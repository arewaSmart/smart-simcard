<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $user = $request->user();

        // Auto-generate and send a new OTP if the current one has expired or doesn't exist
        if (!$user->otp_code || !$user->otp_expires_at || $user->otp_expires_at->isPast()) {
            $user->generateOtp();
            $user->sendOtpEmail();
        }

        return view('auth.verify-email');
    }
}
