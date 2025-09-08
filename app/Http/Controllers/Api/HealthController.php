<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class HealthController extends Controller
{
    public function check()
    {
        $status = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'services' => []
        ];

        // Database check
        try {
            DB::connection()->getPdo();
            $userCount = User::count();
            $status['services']['database'] = [
                'status' => 'healthy',
                'total_users' => $userCount,
                'verified_users' => User::where('is_verified', true)->count(),
                'unverified_users' => User::where('is_verified', false)->count(),
            ];
        } catch (\Exception $e) {
            $status['services']['database'] = [
                'status' => 'unhealthy',
                'error' => 'Database connection failed'
            ];
            $status['status'] = 'degraded';
        }

        // Mail service check
        try {
            $mailConfig = config('mail');
            $status['services']['mail'] = [
                'status' => 'configured',
                'driver' => $mailConfig['default'],
                'host' => $mailConfig['mailers'][$mailConfig['default']]['host'] ?? 'N/A',
                'from_address' => $mailConfig['from']['address'],
                'from_name' => $mailConfig['from']['name'],
            ];
        } catch (\Exception $e) {
            $status['services']['mail'] = [
                'status' => 'misconfigured',
                'error' => 'Mail configuration error'
            ];
            $status['status'] = 'degraded';
        }

        // Passport OAuth check
        try {
            $oauthClients = DB::table('oauth_clients')->count();
            $status['services']['oauth'] = [
                'status' => 'healthy',
                'clients_configured' => $oauthClients
            ];
        } catch (\Exception $e) {
            $status['services']['oauth'] = [
                'status' => 'unhealthy',
                'error' => 'OAuth tables not found'
            ];
            $status['status'] = 'degraded';
        }

        $httpCode = $status['status'] === 'healthy' ? 200 : 503;
        
        return response()->json($status, $httpCode);
    }

    public function emailStats()
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'verified_users' => User::where('is_verified', true)->count(),
                'unverified_users' => User::where('is_verified', false)->count(),
                'verification_rate' => 0,
                'users_by_type' => [
                    'admin' => User::where('type', 'admin')->count(),
                    'patient' => User::where('type', 'patient')->count(),
                    'doctor' => User::where('type', 'doctor')->count(),
                ],
                'recent_registrations' => User::where('created_at', '>=', now()->subDays(7))->count(),
                'recent_verifications' => User::where('email_verified_at', '>=', now()->subDays(7))->count(),
            ];

            if ($stats['total_users'] > 0) {
                $stats['verification_rate'] = round(
                    ($stats['verified_users'] / $stats['total_users']) * 100, 
                    2
                );
            }

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve email statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
