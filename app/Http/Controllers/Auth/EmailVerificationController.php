<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
            return redirect()->route('customer.dashboard');
        }
        
        // Generate OTP if not exists or expired
        if (!$user->otp_code || ($user->otp_expires_at && now()->greaterThan($user->otp_expires_at))) {
            $otp = $user->generateOtp();
            Mail::to($user->email)->queue(new OtpVerificationMail($user, $otp));
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
            return redirect()->route('customer.dashboard')
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
            return redirect()->route('customer.dashboard');
        }

        // Check if too many attempts
        $attempts = $user->otp_attempts ?? 0;
        if ($attempts >= 5) {
            return back()->withErrors(['otp' => 'Too many OTP requests. Please try again later.']);
        }

        $otp = $user->generateOtp();
        Mail::to($user->email)->queue(new OtpVerificationMail($user, $otp));

        return back()->with('success', 'New OTP sent to your email.');
    }
}