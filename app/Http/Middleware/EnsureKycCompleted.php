<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycCompleted
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Only enforce KYC check if the email is verified and role is not super_admin
            if ($user->hasVerifiedEmail() && $user->role !== 'super_admin') {
                // Determine if any required KYC fields are empty
                $kycFields = ['first_name', 'last_name', 'phone', 'state', 'lga', 'address'];
                $incomplete = false;

                foreach ($kycFields as $field) {
                    if (empty($user->$field)) {
                        $incomplete = true;
                        break;
                    }
                }

                if ($incomplete) {
                    // Check if current route is not dashboard, kyc.submit, or logout
                    if (!$request->routeIs('dashboard') && !$request->routeIs('kyc.submit') && !$request->routeIs('logout')) {
                        return redirect()->route('dashboard')->with('warning', 'Please complete your KYC profile to continue.');
                    }
                }
            }
        }

        return $next($request);
    }
}
