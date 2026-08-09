<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Address;
use App\Models\AuditLog;
use App\Mail\OtpVerificationMail;
use App\Mail\NewRegistrationNotificationMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
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

            // Create user (email verification follows via OTP)
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
                'customer_tier_id' => 1,
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

            // Generate OTP and send verification email
            $otp = $user->generateOtp();
            Mail::to($user->email)->send(new OtpVerificationMail($user, $otp));

            // Notify admins about the new registration
            $adminEmails = config('b2b.admin_notification_emails', ['admin@copower.com']);
            Mail::to($adminEmails)->queue(new NewRegistrationNotificationMail($user));

            AuditLog::log(
                'register',
                'user',
                $user->id,
                null,
                ['email' => $user->email, 'company' => $user->company_name],
                'New customer registered'
            );

            // Log the user in and send them to OTP verification
            auth()->login($user);

            event(new Registered($user));

            return redirect()->route('auth.verify-otp')
                ->with('success', 'Your account has been created. Please enter the 6-digit code sent to your email.');

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
     * Show OTP verification form
     */
    public function showVerifyOtp()
    {
        $user = auth()->user();

        if ($user && $user->is_verified) {
            return redirect()->route('customer.products');
        }

        if (!$user) {
            return redirect()->route('login');
        }

        $resendAvailable = !$user->otp_expires_at || now()->lessThan($user->otp_expires_at);

        return view('auth.verify-otp', compact('user', 'resendAvailable'));
    }

    /**
     * Verify the OTP code
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'otp_code' => ['required', 'digits:6'],
        ]);

        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->is_verified) {
            return redirect()->route('customer.products');
        }

        $result = $user->verifyOtp($validated['otp_code']);

        if ($result['success']) {
            AuditLog::log(
                'verify-otp',
                'user',
                $user->id,
                ['is_verified' => false],
                ['is_verified' => true],
                'Customer email verified via OTP'
            );

            return redirect()->route('customer.products')
                ->with('success', $result['message']);
        }

        return back()
            ->withErrors(['otp_code' => $result['message']]);
    }

    /**
     * Resend the OTP code
     */
    public function resendOtp(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->is_verified) {
            return redirect()->route('customer.products');
        }

        $otp = $user->generateOtp();
        Mail::to($user->email)->send(new OtpVerificationMail($user, $otp));

        return back()->with('success', 'A new verification code has been sent to your email.');
    }

    /**
     * Show pending approval page (legacy, retained for safety)
     */
    public function pendingApproval()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->is_verified) {
            return redirect()->route('customer.products');
        }

        return redirect()->route('auth.verify-otp');
    }
}