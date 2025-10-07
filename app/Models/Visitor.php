<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Visitor extends Model
{
    protected $fillable = [
        'ip_address',
        'user_agent',
        'page_url',
        'referer',
        'visit_date',
        'visit_time',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'visit_time' => 'datetime',
    ];

    /**
     * Get visitor statistics for the last N days
     */
    public static function getDailyStats($days = 7)
    {
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();

        return self::select(
            'visit_date',
            DB::raw('COUNT(*) as total_visits'),
            DB::raw('COUNT(DISTINCT ip_address) as unique_visitors')
        )
        ->whereBetween('visit_date', [$startDate, $endDate])
        ->groupBy('visit_date')
        ->orderBy('visit_date', 'desc')
        ->get();
    }

    /**
     * Get today's visitor statistics
     */
    public static function getTodayStats()
    {
        $today = now()->toDateString();
        
        return self::select(
            DB::raw('COUNT(*) as total_visits'),
            DB::raw('COUNT(DISTINCT ip_address) as unique_visitors')
        )
        ->whereDate('visit_date', $today)
        ->first();
    }

    /**
     * Get visitor statistics for a specific date range
     */
    public static function getDateRangeStats($startDate, $endDate)
    {
        return self::select(
            DB::raw('COUNT(*) as total_visits'),
            DB::raw('COUNT(DISTINCT ip_address) as unique_visitors'),
            DB::raw('DATE(visit_date) as visit_date')
        )
        ->whereBetween('visit_date', [$startDate, $endDate])
        ->groupBy('visit_date')
        ->orderBy('visit_date', 'desc')
        ->get();
    }

    /**
     * Record a visitor
     */
    public static function recordVisit($request)
    {
        return self::create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'page_url' => $request->fullUrl(),
            'referer' => $request->header('referer'),
            'visit_date' => now()->toDateString(),
            'visit_time' => now()->toTimeString(),
        ]);
    }
}
