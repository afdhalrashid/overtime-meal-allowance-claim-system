<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Claim;
use App\Models\User;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class HrReports extends Component
{
    public $dateFrom;
    public $dateTo;
    public $departmentFilter = '';
    public $statusFilter = '';
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
            case 'today':
                $this->dateFrom = now()->format('Y-m-d');
                $this->dateTo = now()->format('Y-m-d');
                break;
            case 'this_week':
                $this->dateFrom = now()->startOfWeek()->format('Y-m-d');
                $this->dateTo = now()->endOfWeek()->format('Y-m-d');
                break;
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
            ->whereBetween('duty_date', [$this->dateFrom, $this->dateTo]);

        if ($this->departmentFilter) {
            $query->whereHas('user', function ($q) {
                $q->where('department_id', $this->departmentFilter);
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $claims = $query->get();

        // Overview Statistics
        $totalClaims = $claims->count();
        $totalAmount = $claims->sum('total_amount');
        $avgClaimAmount = $totalClaims > 0 ? $totalAmount / $totalClaims : 0;
        $pendingClaims = $claims->where('status', 'pending_approval')->count();
        $approvedClaims = $claims->where('status', 'approved')->count();
        $rejectedClaims = $claims->where('status', 'rejected')->count();
        $processedClaims = $claims->where('status', 'processed')->count();
        $paidClaims = $claims->where('status', 'paid')->count();

        // Claims by Status
        $claimsByStatus = $claims->groupBy('status')->map->count();

        // Claims by Department
        $claimsByDepartment = $claims->groupBy('user.department.name')->map(function ($departmentClaims) {
            return [
                'count' => $departmentClaims->count(),
                'amount' => $departmentClaims->sum('total_amount')
            ];
        });

        // Monthly Trends (last 12 months)
        $monthlyTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthClaims = Claim::whereYear('duty_date', $month->year)
                               ->whereMonth('duty_date', $month->month)
                               ->get();

            $monthlyTrends[] = [
                'month' => $month->format('M Y'),
                'count' => $monthClaims->count(),
                'amount' => $monthClaims->sum('total_amount')
            ];
        }

        // Top Claimants
        $topClaimants = $claims->groupBy('user_id')->map(function ($userClaims) {
            $user = $userClaims->first()->user;
            return [
                'name' => $user->name,
                'department' => $user->department->name ?? 'N/A',
                'count' => $userClaims->count(),
                'amount' => $userClaims->sum('total_amount')
            ];
        })->sortByDesc('amount')->take(10);

        // Processing Time Analysis
        $processingTimes = $claims->where('status', '!=', 'pending_approval')
                                 ->map(function ($claim) {
                                     if ($claim->approved_at && $claim->submitted_at) {
                                         return $claim->submitted_at->diffInDays($claim->approved_at);
                                     }
                                     return null;
                                 })->filter()->values();

        $avgProcessingTime = $processingTimes->count() > 0 ? $processingTimes->avg() : 0;

        // Department Performance
        $departmentPerformance = Department::with(['users.claims' => function ($query) {
            $query->whereBetween('duty_date', [$this->dateFrom, $this->dateTo]);
        }])->get()->map(function ($department) {
            $departmentClaims = $department->users->flatMap->claims;
            $pendingCount = $departmentClaims->where('status', 'pending_approval')->count();
            $totalCount = $departmentClaims->count();

            return [
                'name' => $department->name,
                'total_claims' => $totalCount,
                'pending_claims' => $pendingCount,
                'total_amount' => $departmentClaims->sum('total_amount'),
                'avg_amount' => $totalCount > 0 ? $departmentClaims->sum('total_amount') / $totalCount : 0,
                'pending_percentage' => $totalCount > 0 ? ($pendingCount / $totalCount) * 100 : 0
            ];
        });

        return view('livewire.reports.hr-reports', [
            'totalClaims' => $totalClaims,
            'totalAmount' => $totalAmount,
            'avgClaimAmount' => $avgClaimAmount,
            'pendingClaims' => $pendingClaims,
            'approvedClaims' => $approvedClaims,
            'rejectedClaims' => $rejectedClaims,
            'processedClaims' => $processedClaims,
            'paidClaims' => $paidClaims,
            'claimsByStatus' => $claimsByStatus,
            'claimsByDepartment' => $claimsByDepartment,
            'monthlyTrends' => $monthlyTrends,
            'topClaimants' => $topClaimants,
            'avgProcessingTime' => round($avgProcessingTime, 1),
            'departmentPerformance' => $departmentPerformance,
            'departments' => Department::all(),
        ]);
    }

    public function exportData()
    {
        $baseQuery = Claim::whereBetween('created_at', [$this->dateFrom, $this->dateTo]);

        // Apply filters
        if ($this->departmentFilter) {
            $baseQuery->whereHas('user', function ($query) {
                $query->where('department_id', $this->departmentFilter);
            });
        }

        if ($this->statusFilter) {
            $baseQuery->where('status', $this->statusFilter);
        }

        // Get claims with relationships
        $claims = $baseQuery->with(['user', 'user.department'])->get();

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
            'Submitted Date',
            'Approved Date',
            'Processed Date',
            'Paid Date',
            'Approved By',
            'Processed By',
            'Paid By',
            'Approval Remarks',
            'Process Remarks',
            'Rejection Reason'
        ];

        foreach ($claims as $claim) {
            $approvedBy = '';
            if ($claim->approved_by) {
                $approver = User::find($claim->approved_by);
                $approvedBy = $approver ? $approver->name : '';
            }

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
                $claim->created_at->format('Y-m-d H:i:s'),
                $claim->approved_at ? $claim->approved_at->format('Y-m-d H:i:s') : '',
                $claim->processed_at ? $claim->processed_at->format('Y-m-d H:i:s') : '',
                $claim->paid_at ? $claim->paid_at->format('Y-m-d H:i:s') : '',
                $approvedBy,
                $processedBy,
                $paidBy,
                $claim->approval_remarks ?? '',
                $claim->process_remarks ?? '',
                $claim->rejection_reason ?? ''
            ];
        }

        // Generate CSV content
        $csvContent = '';
        foreach ($csvData as $row) {
            $csvContent .= '"' . implode('","', $row) . '"' . "\n";
        }

        // Set headers for download
        $fileName = 'hr_report_' . $this->dateFrom . '_to_' . $this->dateTo . '.csv';

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
