<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Claim;
use App\Models\User;
use App\Models\Department;

class HrDashboard extends Component
{
    public $selectedStatus = 'all';
    public $selectedDepartment = 'all';
    public $dateFrom;
    public $dateTo;

    public function processClaim($claimId)
    {
        $claim = Claim::findOrFail($claimId);

        if ($claim->status !== 'approved') {
            session()->flash('error', 'Only approved claims can be processed.');
            return;
        }

        $claim->update([
            'status' => 'processed',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        session()->flash('success', 'Claim processed successfully!');
    }

    public function markAsPaid($claimId)
    {
        $claim = Claim::findOrFail($claimId);

        if ($claim->status !== 'processed') {
            session()->flash('error', 'Only processed claims can be marked as paid.');
            return;
        }

        $claim->update([
            'status' => 'paid',
            'paid_by' => auth()->id(),
            'paid_at' => now(),
        ]);

        session()->flash('success', 'Claim marked as paid!');
    }

    public function render()
    {
        $user = auth()->user();

        // Build query for filtered claims
        $claimsQuery = Claim::with(['user', 'user.department', 'approver']);

        // Apply filters
        if ($this->selectedStatus !== 'all') {
            $claimsQuery->where('status', $this->selectedStatus);
        }

        if ($this->selectedDepartment !== 'all') {
            $claimsQuery->whereHas('user', function($query) {
                $query->where('department_id', $this->selectedDepartment);
            });
        }

        if ($this->dateFrom) {
            $claimsQuery->where('duty_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $claimsQuery->where('duty_date', '<=', $this->dateTo);
        }

        $claims = $claimsQuery->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $totalClaims = Claim::count();
        $pendingApproval = Claim::where('status', 'pending_approval')->count();
        $approvedClaims = Claim::where('status', 'approved')->count();
        $processedClaims = Claim::where('status', 'processed')->count();
        $paidClaims = Claim::where('status', 'paid')->count();

        // Monthly statistics
        $currentMonthAmount = Claim::whereMonth('duty_date', now()->month)
            ->whereYear('duty_date', now()->year)
            ->whereIn('status', ['approved', 'processed', 'paid'])
            ->sum('total_amount');

        $currentMonthHours = Claim::whereMonth('duty_date', now()->month)
            ->whereYear('duty_date', now()->year)
            ->whereIn('status', ['approved', 'processed', 'paid'])
            ->sum('overtime_hours');

        // Get departments for filter
        $departments = Department::where('is_active', true)->get();

        // Claims requiring action (for HR/Payroll roles)
        $actionRequired = [];
        if ($user->isHRAdmin()) {
            $actionRequired = Claim::where('status', 'approved')->count();
        } elseif ($user->isPayroll()) {
            $actionRequired = Claim::where('status', 'processed')->count();
        }

        return view('livewire.dashboard.hr-dashboard', [
            'claims' => $claims,
            'departments' => $departments,
            'totalClaims' => $totalClaims,
            'pendingApproval' => $pendingApproval,
            'approvedClaims' => $approvedClaims,
            'processedClaims' => $processedClaims,
            'paidClaims' => $paidClaims,
            'currentMonthAmount' => $currentMonthAmount,
            'currentMonthHours' => $currentMonthHours,
            'actionRequired' => $actionRequired,
        ])->layout('layouts.app');
    }
}
