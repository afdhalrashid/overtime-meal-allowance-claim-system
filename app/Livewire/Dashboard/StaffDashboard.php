<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Claim;
use Carbon\Carbon;

class StaffDashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        
        // Get dashboard statistics
        $totalClaims = $user->claims()->count();
        $pendingClaims = $user->claims()->where('status', 'pending_approval')->count();
        $approvedClaims = $user->claims()->where('status', 'approved')->count();
        $currentMonthHours = $user->claims()
            ->whereMonth('duty_date', now()->month)
            ->whereYear('duty_date', now()->year)
            ->sum('overtime_hours');
        
        // Get recent claims
        $recentClaims = $user->claims()
            ->with(['documents'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Calculate days until deadline for current month
        $deadlineDay = 9; // 9th of next month
        $nextDeadline = now()->addMonth()->startOfMonth()->addDays($deadlineDay - 1);
        $daysUntilDeadline = now()->diffInDays($nextDeadline, false);
        
        return view('livewire.dashboard.staff-dashboard', [
            'totalClaims' => $totalClaims,
            'pendingClaims' => $pendingClaims,
            'approvedClaims' => $approvedClaims,
            'currentMonthHours' => $currentMonthHours,
            'recentClaims' => $recentClaims,
            'daysUntilDeadline' => $daysUntilDeadline,
        ])->layout('layouts.app-claim');
    }
}
