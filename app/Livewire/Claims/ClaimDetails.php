<?php

namespace App\Livewire\Claims;

use App\Models\Claim;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class ClaimDetails extends Component
{
    public Claim $claim;
    public $showProcessModal = false;
    public $showRejectModal = false;
    public $processRemarks = '';
    public $rejectionReason = '';

    public function mount($id)
    {
        $this->claim = Claim::with(['user.department', 'user.manager', 'documents', 'approver'])
            ->findOrFail($id);

        // Check authorization
        $user = Auth::user();

        // HR/Payroll can view all claims
        if ($user->isHRAdmin() || $user->isPayroll()) {
            return;
        }

        // Approvers can view their team's claims
        if ($user->isApprover() && $this->claim->user->manager_id === $user->id) {
            return;
        }

        // Staff can view their own claims
        if ($user->isStaff() && $this->claim->user_id === $user->id) {
            return;
        }

        abort(403, 'Unauthorized access to this claim.');
    }

    public function processClaim()
    {
        // Only HR Admin can process approved claims
        if (!Auth::user()->isHRAdmin() || $this->claim->status !== 'approved') {
            session()->flash('error', 'Cannot process this claim.');
            return;
        }

        $this->claim->update([
            'status' => 'processed',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
            'process_remarks' => $this->processRemarks,
        ]);

        session()->flash('success', 'Claim processed successfully!');
        $this->showProcessModal = false;
        $this->processRemarks = '';
    }

    public function markAsPaid()
    {
        // Only Payroll can mark processed claims as paid
        if (!Auth::user()->isPayroll() || $this->claim->status !== 'processed') {
            session()->flash('error', 'Cannot mark this claim as paid.');
            return;
        }

        $this->claim->update([
            'status' => 'paid',
            'paid_by' => Auth::id(),
            'paid_at' => now(),
        ]);

        session()->flash('success', 'Claim marked as paid successfully!');
    }

    public function approveClaim()
    {
        // Only approvers can approve pending claims from their team
        if (!Auth::user()->isApprover() ||
            $this->claim->status !== 'pending_approval' ||
            $this->claim->user->manager_id !== Auth::id()) {
            session()->flash('error', 'Cannot approve this claim.');
            return;
        }

        $this->claim->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_remarks' => $this->processRemarks,
        ]);

        session()->flash('success', 'Claim approved successfully!');
        $this->processRemarks = '';
    }

    public function rejectClaim()
    {
        // Only approvers can reject pending claims from their team
        if (!Auth::user()->isApprover() ||
            $this->claim->status !== 'pending_approval' ||
            $this->claim->user->manager_id !== Auth::id()) {
            session()->flash('error', 'Cannot reject this claim.');
            return;
        }

        if (empty($this->rejectionReason)) {
            session()->flash('error', 'Rejection reason is required.');
            return;
        }

        $this->claim->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $this->rejectionReason,
        ]);

        session()->flash('success', 'Claim rejected.');
        $this->showRejectModal = false;
        $this->rejectionReason = '';
    }    public function render()
    {
        return view('livewire.claims.claim-details');
    }
}
