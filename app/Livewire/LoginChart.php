<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\LoginLog;
use Illuminate\Support\Facades\DB;

class LoginChart extends Component
{

    public function render()
    {
        $now = Carbon::now(); // PH timezone from config/app.php

    // ---------- DAILY: last 7 days ----------
    $daily = LoginLog::select(
        DB::raw('DATE(logged_in_at) as date'),
        DB::raw('COUNT(DISTINCT user_id) as count')
    )
    ->where('logged_in_at', '>=', $now->copy()->subDays(6))
    ->groupBy('date')
    ->orderBy('date')
    ->get();

    // Fill missing days with 0
    $dailyLabels = [];
    $dailyData = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = $now->copy()->subDays($i)->format('Y-m-d');
        $dailyLabels[] = $now->copy()->subDays($i)->format('D'); // Mon, Tue, etc.
        $dailyData[] = $daily->firstWhere('date', $date)->count ?? 0;
    }

    // ---------- WEEKLY: last 4 weeks ----------
    $weeklyLabels = [];
    $weeklyData = [];
    for ($i = 3; $i >= 0; $i--) {
        $start = $now->copy()->subWeeks($i)->startOfWeek(); // Monday
        $end = $now->copy()->subWeeks($i)->endOfWeek(); // Sunday
        $count = LoginLog::whereBetween('logged_in_at', [$start, $end])
                        ->distinct('user_id')
                        ->count('user_id');

        $weeklyLabels[] = 'Week of '.$start->format('M d'); // e.g., Week of Oct 06
        $weeklyData[] = $count;
    }

    // ---------- YEARLY: last 12 months ----------
    $yearlyLabels = [];
    $yearlyData = [];
    for ($i = 11; $i >= 0; $i--) {
        $monthStart = $now->copy()->subMonths($i)->startOfMonth();
        $monthEnd = $now->copy()->subMonths($i)->endOfMonth();
        $count = LoginLog::whereBetween('logged_in_at', [$monthStart, $monthEnd])
                        ->distinct('user_id')
                        ->count('user_id');
        $yearlyLabels[] = $monthStart->format('M'); // Jan, Feb, etc.
        $yearlyData[] = $count;
    }

    $loginData = [
        'daily' => [
            'labels' => $dailyLabels,
            'data' => $dailyData,
        ],
        'weekly' => [
            'labels' => $weeklyLabels,
            'data' => $weeklyData,
        ],
        'yearly' => [
            'labels' => $yearlyLabels,
            'data' => $yearlyData,
        ],
    ];

        return view('livewire.login-chart', compact('loginData'));
    }
}
