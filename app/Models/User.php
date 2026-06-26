<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'user_type',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Active users — used to populate "assign to" / "sales officer"
     * dropdowns. Every active user is eligible regardless of role, so any
     * new role added later is automatically included with no extra setup.
     */
    public function scopeWithLeadsPermission($query, string $action = 'edit')
    {
        return $query->where('status', 'active');
    }

    /**
     * Whether this user can do {action} on {module} — admins always can;
     * everyone else needs their role to carry the matching permission
     * (e.g. hasPermission('edit', 'leads') checks for 'edit-leads').
     */
    public function hasPermission(string $action, string $module): bool
    {
        if ($this->user_type === 'admin') {
            return true;
        }

        return $this->role && $this->role->permissions->contains('name', "{$action}-{$module}");
    }
}