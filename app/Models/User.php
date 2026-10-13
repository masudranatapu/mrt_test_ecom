<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Admin panel user (guard: admin).
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    /**
     * Admin panel modules a staff member can be given access to.
     * Super admins always have all of them (and can manage admins).
     */
    public const PERMISSIONS = [
        'orders' => ['label' => 'Orders', 'description' => 'View orders, change status and payment, get new-order alerts'],
        'products' => ['label' => 'Products & Categories', 'description' => 'Add / edit products and categories'],
        'inventory' => ['label' => 'Inventory', 'description' => 'Stock update and stock history'],
        'customers' => ['label' => 'Customers', 'description' => 'View customers, approve / deactivate'],
        'marketing' => ['label' => 'Marketing', 'description' => 'Flash sales and campaigns'],
        'support' => ['label' => 'Support', 'description' => 'Contact messages and complaints'],
        'content' => ['label' => 'Content & Settings', 'description' => 'FAQs and contact / store info'],
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'status',
        'is_super_admin',
        'permissions',
        'password',
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
            'status' => 'boolean',
            'is_super_admin' => 'boolean',
            'permissions' => 'array',
        ];
    }

    public function hasPermission(string $permission): bool
    {
        return $this->is_super_admin || in_array($permission, $this->permissions ?? [], true);
    }

    public function roleLabel(): string
    {
        return $this->is_super_admin ? 'Super Admin' : 'Staff';
    }

    /**
     * Active admins who hold a permission (e.g. who should get new-order alerts).
     */
    public static function withPermission(string $permission)
    {
        return static::active()->get()->filter->hasPermission($permission)->values();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn () => $this->avatar
            ? storage_url($this->avatar)
            : default_avatar());
    }
}
