<?php

namespace App\Livewire\Reports;

use App\Models\Claim;
use App\Models\User;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ApproverReports extends Component
{
    public $selectedPeriod = 'this_month';
    public $departmentFilter = '';
    public $statusFilter = '';
    public $dateFrom;
    public $dateTo;

    public function mount()
    {
        $this->updateDateRange();
    }

    public function updatedSelectedPeriod()
    {
        $this->updateDateRange();
    }

    private function updateDateRange()
    {
        $now = Carbon::now();

        switch ($this->selectedPeriod) {
            case 'this_month':
                $this->dateFrom = $now->startOfMonth()->toDateString();
                $this->dateTo = $now->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $this->dateFrom = $now->subMonth()->startOfMonth()->toDateString();
                $this->dateTo = $now->endOfMonth()->toDateString();
                break;
            case 'this_quarter':
                $this->dateFrom = $now->startOfQuarter()->toDateString();
                $this->dateTo = $now->endOfQuarter()->toDateString();
                break;
            case 'last_quarter':
                $this->dateFrom = $now->subQuarter()->startOfQuarter()->toDateString();
                $this->dateTo = $now->endOfQuarter()->toDateString();
                break;
            case 'this_year':
                $this->dateFrom = $now->startOfYear()->toDateString();
                $this->dateTo = $now->endOfYear()->toDateString();
                break;
        }
    }

    public function exportApprovalData()
    {
        // Placeholder for export functionality
        session()->flash('message', 'Export functionality coming soon!');
    }

    public function render()
    {
        $baseQuery = Claim::whereBetween('created_at', [$this->dateFrom, $this->dateTo]);

        // Filter by department if approver manages specific departments
        if ($this->departmentFilter) {
            $baseQuery->whereHas('user', function ($query) {
                $query->where('department_id', $this->departmentFilter);
            });
        }

        // Filter by status
        if ($this->statusFilter) {
            $baseQuery->where('status', $this->statusFilter);
        }

        // Get departments user can approve for
        $departments = Department::all();

        // Approval Statistics
        $totalClaims = (clone $baseQuery)->count();
        $pendingClaims = (clone $baseQuery)->where('status', 'pending')->count();
        $approvedClaims = (clone $baseQuery)->where('status', 'approved')->count();
        $rejectedClaims = (clone $baseQuery)->where('status', 'rejected')->count();
        $totalAmount = (clone $baseQuery)->whereIn('status', ['approved', 'processed', 'paid'])->sum('total_amount');

        // Claims requiring attention (pending for more than 3 days)
        $urgentClaims = Claim::where('status', 'pending')
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo])
            ->count();

        // Processing time analytics
        $avgProcessingTime = Claim::whereNotNull('approved_at')
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo])
            ->get()
            ->filter(function($claim) {
                return $claim->approved_at && $claim->created_at;
            })
            ->avg(function($claim) {
                return Carbon::parse($claim->created_at)->diffInHours(Carbon::parse($claim->approved_at));
            }) ?? 0;

        // Approval rate
        $approvalRate = $totalClaims > 0 ? ($approvedClaims / $totalClaims) * 100 : 0;

        // Claims by status
        $claimsByStatus = [
            'pending' => $pendingClaims,
            'approved' => $approvedClaims,
            'rejected' => $rejectedClaims
        ];

        // Recent pending claims
        $pendingApprovals = Claim::where('status', 'pending')
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo])
            ->with(['user', 'user.department'])
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();

        // Approval trends by department
        $approvalByDepartment = Claim::join('users', 'claims.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->whereBetween('claims.created_at', [$this->dateFrom, $this->dateTo])
            ->selectRaw('
                departments.name as department_name,
                COUNT(*) as total_claims,
                SUM(CASE WHEN claims.status = "approved" THEN 1 ELSE 0 END) as approved_claims,
                SUM(CASE WHEN claims.status = "rejected" THEN 1 ELSE 0 END) as rejected_claims,
                SUM(CASE WHEN claims.status = "pending" THEN 1 ELSE 0 END) as pending_claims,
                SUM(CASE WHEN claims.status = "approved" THEN claims.total_amount ELSE 0 END) as approved_amount
            ')
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('total_claims', 'desc')
            ->get()
            ->map(function ($item) {
                $item->approval_rate = $item->total_claims > 0 ?
                    ($item->approved_claims / $item->total_claims) * 100 : 0;

                // Calculate average processing hours manually
                $deptClaims = Claim::join('users', 'claims.user_id', '=', 'users.id')
                    ->where('users.department_id', function($query) use ($item) {
                        $query->select('departments.id')
                            ->from('departments')
                            ->where('departments.name', $item->department_name)
                            ->limit(1);
                    })
                    ->whereNotNull('claims.approved_at')
                    ->whereBetween('claims.created_at', [$this->dateFrom, $this->dateTo])
                    ->get(['claims.created_at', 'claims.approved_at']);

                $item->avg_processing_hours = $deptClaims->filter(function($claim) {
                    return $claim->approved_at && $claim->created_at;
                })->avg(function($claim) {
                    return Carbon::parse($claim->created_at)->diffInHours(Carbon::parse($claim->approved_at));
                }) ?? 0;
                return $item;
            });

        // Top claimants requiring frequent approval
        $frequentClaimants = Claim::join('users', 'claims.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->whereBetween('claims.created_at', [$this->dateFrom, $this->dateTo])
            ->selectRaw('
                users.name as user_name,
                departments.name as department_name,
                COUNT(*) as total_claims,
                SUM(CASE WHEN claims.status = "approved" THEN 1 ELSE 0 END) as approved_claims,
                SUM(CASE WHEN claims.status = "rejected" THEN 1 ELSE 0 END) as rejected_claims,
                SUM(claims.total_amount) as total_amount,
                AVG(claims.overtime_hours) as avg_overtime_hours
            ')
            ->groupBy('users.id', 'users.name', 'departments.name')
            ->orderBy('total_claims', 'desc')
            ->take(10)
            ->get()
            ->map(function ($item) {
                $item->approval_rate = $item->total_claims > 0 ?
                    ($item->approved_claims / $item->total_claims) * 100 : 0;
                return $item;
            });

        // Claims requiring immediate attention (by urgency)
        $urgentApprovals = Claim::where('status', 'pending')
            ->with(['user', 'user.department'])
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get()
            ->map(function ($claim) {
                $claim->days_pending = Carbon::parse($claim->created_at)->diffInDays(now());
                $claim->urgency_level = $claim->days_pending >= 7 ? 'high' :
                    ($claim->days_pending >= 3 ? 'medium' : 'low');
                return $claim;
            });

        return view('livewire.reports.approver-reports', [
            'departments' => $departments,
            'totalClaims' => $totalClaims,
            'pendingClaims' => $pendingClaims,
            'approvedClaims' => $approvedClaims,
            'rejectedClaims' => $rejectedClaims,
            'totalAmount' => $totalAmount,
            'urgentClaims' => $urgentClaims,
            'avgProcessingTime' => round($avgProcessingTime, 1),
            'approvalRate' => round($approvalRate, 1),
            'claimsByStatus' => $claimsByStatus,
            'pendingApprovals' => $pendingApprovals,
            'approvalByDepartment' => $approvalByDepartment,
            'frequentClaimants' => $frequentClaimants,
            'urgentApprovals' => $urgentApprovals
        ]);
    }
}
