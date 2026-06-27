<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        if ($request->user()->verifyOtp($request->code)) {
            event(new Verified($request->user()));
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1')
                ->with('success', 'Email verified successfully.');
        }

        return back()->withErrors(['code' => 'The provided OTP is invalid or has expired.']);
    }
}
