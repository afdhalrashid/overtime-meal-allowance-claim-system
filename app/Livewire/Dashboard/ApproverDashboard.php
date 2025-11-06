<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Claim;
use App\Models\User;

class ApproverDashboard extends Component
{
    public function approveClaim($claimId, $remarks = null)
    {
        $claim = Claim::findOrFail($claimId);

        // Verify this user can approve this claim
        if ($claim->user->manager_id !== auth()->id()) {
            session()->flash('error', 'You are not authorized to approve this claim.');
            return;
        }

        $claim->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_remarks' => $remarks,
        ]);

        session()->flash('success', 'Claim approved successfully!');
    }

    public function rejectClaim($claimId, $reason)
    {
        $claim = Claim::findOrFail($claimId);

        // Verify this user can reject this claim
        if ($claim->user->manager_id !== auth()->id()) {
            session()->flash('error', 'You are not authorized to reject this claim.');
            return;
        }

        if (empty($reason)) {
            session()->flash('error', 'Rejection reason is required.');
            return;
        }

        $claim->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);

        session()->flash('success', 'Claim rejected.');
    }

    public function render()
    {
        $user = auth()->user();

        // Get team members
        $teamMembers = User::where('manager_id', $user->id)->get();

        // Get pending claims from team members
        $pendingClaims = Claim::whereIn('user_id', $teamMembers->pluck('id'))
            ->where('status', 'pending_approval')
            ->with(['user', 'documents'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Get approved claims from team members (last 30 days)
        $recentApprovals = Claim::whereIn('user_id', $teamMembers->pluck('id'))
            ->where('status', 'approved')
            ->where('approved_at', '>=', now()->subDays(30))
            ->with(['user'])
            ->orderBy('approved_at', 'desc')
            ->limit(10)
            ->get();

        // Statistics
        $totalPending = $pendingClaims->count();
        $totalApprovedThisMonth = Claim::whereIn('user_id', $teamMembers->pluck('id'))
            ->where('status', 'approved')
            ->whereMonth('approved_at', now()->month)
            ->count();

        $totalOvertimeHoursThisMonth = Claim::whereIn('user_id', $teamMembers->pluck('id'))
            ->whereMonth('duty_date', now()->month)
            ->whereYear('duty_date', now()->year)
            ->sum('overtime_hours');

        return view('livewire.dashboard.approver-dashboard', [
            'teamMembers' => $teamMembers,
            'pendingClaims' => $pendingClaims,
            'recentApprovals' => $recentApprovals,
            'totalPending' => $totalPending,
            'totalApprovedThisMonth' => $totalApprovedThisMonth,
            'totalOvertimeHoursThisMonth' => $totalOvertimeHoursThisMonth,
        ])->layout('layouts.app');
    }
}
