<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EmailVerificationRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->input('email');
        
        if (!$email) {
            return $next($request);
        }
        
        $key = 'email_verification_rate_limit:' . $email;
        $attempts = Cache::get($key, 0);
        
        // Allow maximum 10 attempts per 15 minutes
        if ($attempts >= 10) {
            return response()->json([
                'message' => 'Too many verification attempts. Please wait before requesting another code.',
                'retry_after' => '15 minutes'
            ], 429);
        }
        
        // Increment attempts and set expiry to 15 minutes
        Cache::put($key, $attempts + 1, now()->addMinutes(15));
        
        return $next($request);
    }
}
