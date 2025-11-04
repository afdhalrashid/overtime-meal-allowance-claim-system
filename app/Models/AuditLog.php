<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'event',
        'old_values',
        'new_values',
        'user_id',
        'user_agent',
        'ip_address',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    /**
     * Get the user who performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the auditable model.
     */
    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Create an audit log entry.
     */
    public static function log(
        string $auditableType,
        int $auditableId,
        string $event,
        array $oldValues,
        array $newValues,
        string $notes
    ): void {
        static::create([
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => auth()->id(),
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
            'notes' => $notes,
        ]);
    }
}
