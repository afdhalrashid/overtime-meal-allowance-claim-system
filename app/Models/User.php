<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'role',
        'department_id',
        'manager_id',
        'phone',
        'involves_driving',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'involves_driving' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * The model's boot method.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->employee_id)) {
                $user->employee_id = static::generateEmployeeId();
            }
        });
    }

    /**
     * Generate a unique employee ID.
     */
    public static function generateEmployeeId(): string
    {
        $year = now()->year;

        // Get the highest employee ID for the current year
        $lastEmployee = static::where('employee_id', 'like', "EMP{$year}%")
            ->orderBy('employee_id', 'desc')
            ->first();

        if ($lastEmployee) {
            // Extract the sequence number from the last employee ID
            $lastSequence = (int) substr($lastEmployee->employee_id, -4);
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('EMP%s%04d', $year, $sequence);
    }    /**
     * Get the department that the user belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the manager of this user.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the users that this user manages.
     */
    public function managedUsers()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    /**
     * Get the claims submitted by this user.
     */
    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    /**
     * Get the claims approved by this user.
     */
    public function approvedClaims()
    {
        return $this->hasMany(Claim::class, 'approved_by');
    }

    /**
     * Get the claims processed by this user.
     */
    public function processedClaims()
    {
        return $this->hasMany(Claim::class, 'processed_by');
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is a staff member.
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Check if user is an approver.
     */
    public function isApprover(): bool
    {
        return $this->role === 'approver';
    }

    /**
     * Check if user is an HR admin.
     */
    public function isHRAdmin(): bool
    {
        return $this->role === 'hr_admin';
    }

    /**
     * Check if user is a payroll staff.
     */
    public function isPayroll(): bool
    {
        return $this->role === 'payroll';
    }
}
