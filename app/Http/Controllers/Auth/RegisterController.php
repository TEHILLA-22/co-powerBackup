<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Address;
use App\Models\AuditLog;
use App\Mail\RegistrationPendingMail;
use App\Mail\NewRegistrationNotificationMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        // Get countries directly from config (no cache needed for this simple list)
        $countries = collect(config('countries'))->map(function ($name, $code) {
            return ['code' => $code, 'name' => $name];
        })->values()->toArray();

        return view('auth.register', compact('countries'));
    }

    /**
     * Handle registration
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Create user with pending approval
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'mobile' => $validated['mobile'],
                'company_name' => $validated['company_name'],
                'company_registration_number' => $validated['company_registration_number'] ?? null,
                'vat_number' => $validated['vat_number'] ?? null,
                'is_approved' => false,
                'language' => app()->getLocale(),
                'currency' => 'GBP',
                'timezone' => 'UTC',
            ]);

            // Create address
            Address::create([
                'user_id' => $user->id,
                'address_type' => 'both',
                'is_default' => true,
                'recipient_name' => $user->full_name,
                'company_name' => $user->company_name,
                'address_line_1' => $validated['address_line_1'],
                'address_line_2' => $validated['address_line_2'] ?? null,
                'city' => $validated['city'],
                'state_province' => $validated['state_province'] ?? null,
                'postal_code' => $validated['postal_code'],
                'country_code' => $validated['country_code'],
                'phone' => $user->phone,
                'email' => $user->email,
            ]);

            DB::commit();

            // Queue emails
            Mail::to($user->email)->queue(new RegistrationPendingMail($user));

            $adminEmails = config('b2b.admin_notification_emails', ['admin@copower.com']);
            Mail::to($adminEmails)->queue(new NewRegistrationNotificationMail($user));

            // Log the user in
            auth()->login($user);

            event(new Registered($user));

            return redirect()->route('auth.pending-approval')
                ->with('success', 'Your account has been created! Please wait for admin approval.');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Registration failed: ' . $e->getMessage(), [
                'email' => $validated['email'] ?? null,
                'company' => $validated['company_name'] ?? null,
            ]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['registration' => 'Registration failed. Please try again.']);
        }
    }

    /**
     * Show pending approval page
     */
    public function pendingApproval()
    {
        $user = auth()->user();

        if ($user && $user->is_approved) {
            return redirect()->route('customer.dashboard');
        }

        return view('auth.pending-approval');
    }
}