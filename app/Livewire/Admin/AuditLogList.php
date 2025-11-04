<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\AuditLog;
use App\Models\User;

#[Layout('layouts.app')]
class AuditLogList extends Component
{
    use WithPagination;

    public $search = '';
    public $eventFilter = '';
    public $userFilter = '';
    public $modelFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 15;

    public function render()
    {
        $query = AuditLog::query()
            ->with(['user'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('notes', 'like', '%' . $this->search . '%')
                  ->orWhere('auditable_type', 'like', '%' . $this->search . '%')
                  ->orWhere('event', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->eventFilter) {
            $query->where('event', $this->eventFilter);
        }

        if ($this->userFilter) {
            $query->where('user_id', $this->userFilter);
        }

        if ($this->modelFilter) {
            $query->where('auditable_type', 'like', '%' . $this->modelFilter . '%');
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $auditLogs = $query->paginate($this->perPage);

        $users = User::orderBy('name')->get();
        $events = AuditLog::distinct()->pluck('event');
        $models = AuditLog::distinct()->pluck('auditable_type');

        return view('livewire.admin.audit-log-list', [
            'auditLogs' => $auditLogs,
            'users' => $users,
            'events' => $events,
            'models' => $models,
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'eventFilter', 'userFilter', 'modelFilter', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEventFilter()
    {
        $this->resetPage();
    }

    public function updatingUserFilter()
    {
        $this->resetPage();
    }

    public function updatingModelFilter()
    {
        $this->resetPage();
    }
}
