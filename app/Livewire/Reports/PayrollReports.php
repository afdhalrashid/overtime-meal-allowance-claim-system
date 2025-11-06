<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Claim;
use App\Models\User;
use App\Models\Department;
use Carbon\Carbon;

#[Layout('layouts.app')]
class PayrollReports extends Component
{
    public $dateFrom;
    public $dateTo;
    public $departmentFilter = '';
    public $statusFilter = 'processed';
    public $selectedPeriod = 'this_month';

    public function mount()
    {
        $this->setDateRange();
    }

    public function updatedSelectedPeriod()
    {
        $this->setDateRange();
    }

    private function setDateRange()
    {
        switch ($this->selectedPeriod) {
            case 'this_month':
                $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->dateFrom = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_quarter':
                $this->dateFrom = now()->startOfQuarter()->format('Y-m-d');
                $this->dateTo = now()->endOfQuarter()->format('Y-m-d');
                break;
            case 'last_quarter':
                $this->dateFrom = now()->subQuarter()->startOfQuarter()->format('Y-m-d');
                $this->dateTo = now()->subQuarter()->endOfQuarter()->format('Y-m-d');
                break;
            case 'this_year':
                $this->dateFrom = now()->startOfYear()->format('Y-m-d');
                $this->dateTo = now()->endOfYear()->format('Y-m-d');
                break;
        }
    }

    public function render()
    {
        $query = Claim::query()
            ->with(['user.department'])
            ->whereBetween('duty_date', [$this->dateFrom, $this->dateTo])
            ->whereIn('status', ['processed', 'paid']);

        if ($this->departmentFilter) {
            $query->whereHas('user', function ($q) {
                $q->where('department_id', $this->departmentFilter);
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $claims = $query->get();

        // Payroll Summary
        $totalForPayroll = $claims->sum('total_amount');
        $totalClaims = $claims->count();
        $processedAmount = $claims->where('status', 'processed')->sum('total_amount');
        $paidAmount = $claims->where('status', 'paid')->sum('total_amount');
        $processedCount = $claims->where('status', 'processed')->count();
        $paidCount = $claims->where('status', 'paid')->count();

        // Claims ready for payment
        $readyForPayment = Claim::with(['user.department'])
            ->where('status', 'processed')
            ->whereBetween('duty_date', [$this->dateFrom, $this->dateTo])
            ->orderBy('processed_at')
            ->get();

        // Payment by Department
        $paymentByDepartment = $claims->groupBy('user.department.name')->map(function ($departmentClaims) {
            return [
                'total_amount' => $departmentClaims->sum('total_amount'),
                'claim_count' => $departmentClaims->count(),
                'processed_amount' => $departmentClaims->where('status', 'processed')->sum('total_amount'),
                'paid_amount' => $departmentClaims->where('status', 'paid')->sum('total_amount'),
                'employees' => $departmentClaims->groupBy('user_id')->count()
            ];
        });

        // Employee Payment Summary
        $employeePayments = $claims->groupBy('user_id')->map(function ($userClaims) {
            $user = $userClaims->first()->user;
            return [
                'employee_id' => $user->id,
                'name' => $user->name,
                'department' => $user->department->name ?? 'N/A',
                'total_amount' => $userClaims->sum('total_amount'),
                'claim_count' => $userClaims->count(),
                'processed_amount' => $userClaims->where('status', 'processed')->sum('total_amount'),
                'paid_amount' => $userClaims->where('status', 'paid')->sum('total_amount'),
                'overtime_hours' => $userClaims->sum('overtime_hours'),
                'meal_allowance' => $userClaims->sum('meal_allowance_amount')
            ];
        })->sortByDesc('total_amount');

        // Monthly Payment Trends
        $monthlyPayments = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthClaims = Claim::whereYear('duty_date', $month->year)
                               ->whereMonth('duty_date', $month->month)
                               ->whereIn('status', ['processed', 'paid'])
                               ->get();

            $monthlyPayments[] = [
                'month' => $month->format('M Y'),
                'processed' => $monthClaims->where('status', 'processed')->sum('total_amount'),
                'paid' => $monthClaims->where('status', 'paid')->sum('total_amount'),
                'total' => $monthClaims->sum('total_amount')
            ];
        }

        return view('livewire.reports.payroll-reports', [
            'totalForPayroll' => $totalForPayroll,
            'totalClaims' => $totalClaims,
            'processedAmount' => $processedAmount,
            'paidAmount' => $paidAmount,
            'processedCount' => $processedCount,
            'paidCount' => $paidCount,
            'readyForPayment' => $readyForPayment,
            'paymentByDepartment' => $paymentByDepartment,
            'employeePayments' => $employeePayments,
            'monthlyPayments' => $monthlyPayments,
            'departments' => Department::all(),
        ]);
    }

    public function markAsPaid($claimId)
    {
        $claim = Claim::find($claimId);
        if ($claim && $claim->status === 'processed') {
            $claim->update([
                'status' => 'paid',
                'paid_by' => auth()->id(),
                'paid_at' => now()
            ]);

            session()->flash('success', "Claim {$claim->claim_number} marked as paid.");
        }
    }

    public function markMultipleAsPaid($claimIds)
    {
        $claims = Claim::whereIn('id', $claimIds)->where('status', 'processed')->get();

        foreach ($claims as $claim) {
            $claim->update([
                'status' => 'paid',
                'paid_by' => auth()->id(),
                'paid_at' => now()
            ]);
        }

        session()->flash('success', count($claims) . ' claims marked as paid.');
    }

    public function exportPayrollData()
    {
        $query = Claim::query()
            ->with(['user.department'])
            ->whereBetween('duty_date', [$this->dateFrom, $this->dateTo])
            ->whereIn('status', ['processed', 'paid']);

        if ($this->departmentFilter) {
            $query->whereHas('user', function ($q) {
                $q->where('department_id', $this->departmentFilter);
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $claims = $query->get();

        // Prepare CSV data
        $csvData = [];
        $csvData[] = [
            'Claim Number',
            'Employee Name',
            'Department',
            'Duty Date',
            'Work Type',
            'Overtime Hours',
            'Meal Allowance',
            'Total Amount',
            'Status',
            'Processed Date',
            'Paid Date',
            'Processed By',
            'Paid By'
        ];

        foreach ($claims as $claim) {
            $processedBy = '';
            if ($claim->processed_by) {
                $processor = User::find($claim->processed_by);
                $processedBy = $processor ? $processor->name : '';
            }

            $paidBy = '';
            if ($claim->paid_by) {
                $payer = User::find($claim->paid_by);
                $paidBy = $payer ? $payer->name : '';
            }

            $csvData[] = [
                $claim->claim_number,
                $claim->user->name,
                $claim->user->department->name ?? 'N/A',
                $claim->duty_date,
                $claim->work_type,
                $claim->overtime_hours ?? 0,
                $claim->meal_allowance_amount ?? 0,
                $claim->total_amount,
                ucfirst($claim->status),
                $claim->processed_at ? $claim->processed_at->format('Y-m-d H:i:s') : '',
                $claim->paid_at ? $claim->paid_at->format('Y-m-d H:i:s') : '',
                $processedBy,
                $paidBy
            ];
        }

        // Generate CSV content
        $csvContent = '';
        foreach ($csvData as $row) {
            $csvContent .= '"' . implode('","', $row) . '"' . "\n";
        }

        // Set headers for download
        $fileName = 'payroll_report_' . $this->dateFrom . '_to_' . $this->dateTo . '.csv';

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
