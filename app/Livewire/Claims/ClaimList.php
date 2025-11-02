<?php

namespace App\Livewire\Claims;

use App\Models\Claim;
use Livewire\Component;
use Livewire\WithPagination;

class ClaimList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function getClaims()
    {
        $query = Claim::where('user_id', auth()->id())
            ->with(['documents', 'approver'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('claim_number', 'like', '%' . $this->search . '%')
                      ->orWhere('remarks', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(10);
    }

    public function deleteClaim($claimId)
    {
        $claim = Claim::where('id', $claimId)
            ->where('user_id', auth()->id())
            ->where('status', 'draft')
            ->first();

        if ($claim) {
            // Delete associated documents
            foreach ($claim->documents as $document) {
                if (file_exists(storage_path('app/' . $document->file_path))) {
                    unlink(storage_path('app/' . $document->file_path));
                }
            }
            $claim->documents()->delete();
            $claim->delete();

            session()->flash('success', 'Claim deleted successfully.');
        } else {
            session()->flash('error', 'Claim not found or cannot be deleted.');
        }
    }

    public function render()
    {
        return view('livewire.claims.claim-list', [
            'claims' => $this->getClaims()
        ])->layout('layouts.app', ['title' => 'My Claims']);
    }
}
