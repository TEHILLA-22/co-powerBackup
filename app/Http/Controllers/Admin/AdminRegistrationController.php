<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRegisterRequest;
use App\Models\Admin;
use App\Models\AuditLog;
use App\Mail\AdminOtpMail;
use App\Mail\AdminWelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminRegistrationController extends Controller
{
    /**
     * Show admin registration form
     */
    public function showRegistrationForm()
    {
        // Check if already logged in
        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        // Check if registration is allowed (only if no admins exist)
        $adminCount = Admin::count();
        $registrationAllowed = $adminCount === 0;

        return view('admin.auth.register', compact('registrationAllowed'));
    }

    /**
     * Handle admin registration - Smart check for existing unverified users
     */
    public function register(AdminRegisterRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Check if admin already exists with this email
            $existingAdmin = Admin::where('email', $validated['email'])->first();

            if ($existingAdmin) {
                // If admin exists but is not active (unverified), resend OTP
                if (!$existingAdmin->is_active) {
                    // Generate new OTP
                    $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
                    $otpExpiresAt = now()->addMinutes(10);

                    // Update existing admin with new OTP session
                    session([
                        'admin_otp' => $otp,
                        'admin_otp_expires' => $otpExpiresAt,
                        'admin_register_email' => $existingAdmin->email,
                    ]);

                    // Send OTP email
                    Mail::to($existingAdmin->email)->send(new AdminOtpMail($existingAdmin, $otp));

                    DB::commit();

                    return redirect()->route('admin.verify')
                        ->with('success', 'We found your pending registration. A new OTP has been sent to your email.');
                }

                // If admin is already active, redirect to login
                DB::commit();
                return redirect()->route('admin.login')
                    ->with('info', 'This account is already verified. Please login.');
            }

            // Create new admin
            $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            $otpExpiresAt = now()->addMinutes(10);

            $admin = Admin::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role' => 'admin',
                'is_active' => false, // Not active until OTP verified
                'force_password_change' => true,
                'language' => app()->getLocale(),
                'timezone' => 'UTC',
            ]);

            // Store OTP in session
            session([
                'admin_otp' => $otp,
                'admin_otp_expires' => $otpExpiresAt,
                'admin_register_email' => $admin->email,
            ]);

            // Send OTP email
            Mail::to($admin->email)->send(new AdminOtpMail($admin, $otp));

            DB::commit();

            return redirect()->route('admin.verify')
                ->with('success', 'Please check your email for the OTP verification code.');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Admin registration failed: ' . $e->getMessage(), [
                'email' => $validated['email'] ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['registration' => 'Registration failed. Please try again.']);
        }
    }

    /**
     * Show OTP verification page with resend option
     */
    public function showVerifyForm()
    {
        // Check if OTP session exists
        if (!session()->has('admin_otp') || !session()->has('admin_register_email')) {
            // Check if there's an unverified admin with this email
            $email = session('admin_register_email');
            if ($email) {
                $admin = Admin::where('email', $email)->where('is_active', false)->first();
                if ($admin) {
                    // Resend OTP
                    $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
                    $otpExpiresAt = now()->addMinutes(10);

                    session([
                        'admin_otp' => $otp,
                        'admin_otp_expires' => $otpExpiresAt,
                    ]);

                    Mail::to($admin->email)->send(new AdminOtpMail($admin, $otp));

                    return redirect()->route('admin.verify')
                        ->with('success', 'A new OTP has been sent to your email.');
                }
            }

            return redirect()->route('admin.register')
                ->with('error', 'Session expired. Please register again.');
        }

        // Check if already logged in
        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        // Get the email for display
        $email = session('admin_register_email');

        return view('admin.auth.verify-otp', compact('email'));
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        // Check session
        if (!session()->has('admin_otp') || !session()->has('admin_register_email')) {
            return redirect()->route('admin.register')
                ->with('error', 'Session expired. Please register again.');
        }

        $sessionOtp = session('admin_otp');
        $sessionExpires = session('admin_otp_expires');
        $adminEmail = session('admin_register_email');

        // Check expiry
        if (now()->greaterThan($sessionExpires)) {
            session()->forget(['admin_otp', 'admin_otp_expires']);
            
            // Check if admin exists and is unverified
            $admin = Admin::where('email', $adminEmail)->where('is_active', false)->first();
            if ($admin) {
                // Generate new OTP
                $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
                $otpExpiresAt = now()->addMinutes(10);

                session([
                    'admin_otp' => $otp,
                    'admin_otp_expires' => $otpExpiresAt,
                ]);

                Mail::to($admin->email)->send(new AdminOtpMail($admin, $otp));

                return back()
                    ->with('warning', 'OTP has expired. A new OTP has been sent to your email.')
                    ->withInput();
            }

            session()->forget('admin_register_email');
            return redirect()->route('admin.register')
                ->with('error', 'OTP has expired. Please register again.');
        }

        // Verify OTP
        if ($request->otp_code !== $sessionOtp) {
            // Check attempts
            $attempts = session('admin_otp_attempts', 0) + 1;
            session(['admin_otp_attempts' => $attempts]);

            if ($attempts >= 5) {
                // Generate new OTP after too many failed attempts
                $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
                $otpExpiresAt = now()->addMinutes(10);

                session([
                    'admin_otp' => $otp,
                    'admin_otp_expires' => $otpExpiresAt,
                    'admin_otp_attempts' => 0,
                ]);

                $admin = Admin::where('email', $adminEmail)->first();
                if ($admin) {
                    Mail::to($admin->email)->send(new AdminOtpMail($admin, $otp));
                }

                return back()
                    ->with('warning', 'Too many failed attempts. A new OTP has been sent to your email.')
                    ->withInput();
            }

            return back()
                ->withErrors(['otp_code' => 'Invalid OTP code. Please try again. (' . (5 - $attempts) . ' attempts remaining)'])
                ->withInput();
        }

        // Find and activate admin
        $admin = Admin::where('email', $adminEmail)->first();

        if (!$admin) {
            session()->forget(['admin_otp', 'admin_otp_expires', 'admin_register_email', 'admin_otp_attempts']);
            return redirect()->route('admin.register')
                ->with('error', 'Admin not found. Please register again.');
        }

        // If admin already active, just login
        if ($admin->is_active) {
            session()->forget(['admin_otp', 'admin_otp_expires', 'admin_register_email', 'admin_otp_attempts']);
            auth()->guard('admin')->login($admin);
            return redirect()->route('admin.dashboard')
                ->with('success', 'Welcome back!');
        }

        // Activate admin
        $admin->is_active = true;
        $admin->email_verified_at = now();
        $admin->save();

        // Log registration
        AuditLog::log(
            'admin_register',
            'admin',
            $admin->id,
            null,
            ['email' => $admin->email],
            "Admin {$admin->full_name} registered and verified"
        );

        // Send welcome email
        Mail::to($admin->email)->send(new AdminWelcomeMail($admin));

        // Clear session
        session()->forget(['admin_otp', 'admin_otp_expires', 'admin_register_email', 'admin_otp_attempts']);

        // Auto-login
        auth()->guard('admin')->login($admin);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Account verified and activated successfully! Welcome to Copower Admin.');
    }

    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        // Check session
        if (!session()->has('admin_register_email')) {
            return redirect()->route('admin.register')
                ->with('error', 'Session expired. Please register again.');
        }

        $adminEmail = session('admin_register_email');
        $admin = Admin::where('email', $adminEmail)->first();

        if (!$admin) {
            session()->forget(['admin_otp', 'admin_otp_expires', 'admin_register_email', 'admin_otp_attempts']);
            return redirect()->route('admin.register')
                ->with('error', 'Admin not found. Please register again.');
        }

        // Check if admin is already active
        if ($admin->is_active) {
            session()->forget(['admin_otp', 'admin_otp_expires', 'admin_register_email', 'admin_otp_attempts']);
            auth()->guard('admin')->login($admin);
            return redirect()->route('admin.dashboard')
                ->with('success', 'Your account is already verified. Welcome back!');
        }

        // Rate limiting for OTP resend
        $resendAttempts = session('admin_otp_resend_attempts', 0) + 1;
        session(['admin_otp_resend_attempts' => $resendAttempts]);

        if ($resendAttempts > 5) {
            return back()
                ->with('error', 'Too many OTP requests. Please wait 10 minutes before trying again.');
        }

        // Generate new OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $otpExpiresAt = now()->addMinutes(10);

        session([
            'admin_otp' => $otp,
            'admin_otp_expires' => $otpExpiresAt,
            'admin_otp_attempts' => 0,
        ]);

        // Send OTP email
        Mail::to($admin->email)->send(new AdminOtpMail($admin, $otp));

        return back()->with('success', 'New OTP sent to your email.');
    }

    /**
     * Check registration status (AJAX - for smart form)
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return response()->json([
                'exists' => false,
                'message' => 'Email available for registration.',
            ]);
        }

        if ($admin->is_active) {
            return response()->json([
                'exists' => true,
                'verified' => true,
                'message' => 'This email is already verified. Please login.',
                'redirect' => route('admin.login'),
            ]);
        }

        return response()->json([
            'exists' => true,
            'verified' => false,
            'message' => 'This email has a pending registration. Please verify your account.',
            'redirect' => route('admin.verify'),
        ]);
    }
}