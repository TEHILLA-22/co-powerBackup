<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class EmailVerificationController extends Controller
{
    /**
     * Show OTP verification page
     */
    public function show()
    {
        $user = auth()->user();
        
        // If already verified, redirect
        if ($user->is_verified) {
            return redirect()->route('customer.products');
        }
        
        // Generate OTP if not exists or expired
        if (!$user->otp_code || ($user->otp_expires_at && now()->greaterThan($user->otp_expires_at))) {
            $otp = $user->generateOtp();
            try {
                Mail::to($user->email)->send(new OtpVerificationMail($user, $otp));
            } catch (\Throwable $e) {
                \Log::error('OTP email failed on verify page: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                session()->flash('warning', 'We could not send the verification code right now. Please use the resend button below.');
            }
        }
        
        return view('auth.verify-otp');
    }

    /**
     * Verify OTP
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        $user = auth()->user();
        $result = $user->verifyOtp($validated['otp_code']);

        if ($result['success']) {
            return redirect()->route('customer.products')
                ->with('success', 'Email verified successfully!');
        }

        return back()
            ->withErrors(['otp_code' => $result['message']]);
    }

    /**
     * Resend OTP
     */
    public function resend()
    {
        $user = auth()->user();

        if ($user->is_verified) {
            return redirect()->route('customer.products');
        }

        // Check if too many attempts
        $attempts = $user->otp_attempts ?? 0;
        if ($attempts >= 5) {
            return back()->withErrors(['otp' => 'Too many OTP requests. Please try again later.']);
        }

        // Rate limit: allow one resend per 60 seconds
        if (RateLimiter::tooManyAttempts('otp-resend:' . $user->id, 1)) {
            $seconds = RateLimiter::availableIn('otp-resend:' . $user->id);
            return back()->withErrors(['otp' => "Please wait {$seconds} seconds before requesting another code."]);
        }

        $otp = $user->generateOtp();
        try {
            Mail::to($user->email)->send(new OtpVerificationMail($user, $otp));
            RateLimiter::hit('otp-resend:' . $user->id, 60);
        } catch (\Throwable $e) {
            \Log::error('OTP resend email failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return back()->withErrors(['otp' => 'We could not send the code right now. Please try again in a moment.']);
        }

        return back()->with('success', 'New OTP sent to your email.');
    }
}