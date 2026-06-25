<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\UserPermissions;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'permissions'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_USER = 'user';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissions ?? [], true);
    }

    public function canViewAllForms(): bool
    {
        return $this->isSuperAdmin() || $this->hasPermission(UserPermissions::VIEW_ALL_FORMS);
    }

    public function canEditAnyForm(): bool
    {
        return $this->isSuperAdmin() || $this->hasPermission(UserPermissions::EDIT_ANY_FORM);
    }

    public function canDeleteAnyForm(): bool
    {
        return $this->isSuperAdmin() || $this->hasPermission(UserPermissions::DELETE_ANY_FORM);
    }

    public function canViewAnyResponses(): bool
    {
        return $this->isSuperAdmin() || $this->hasPermission(UserPermissions::VIEW_ANY_RESPONSES);
    }

    public function canExportAnyResponses(): bool
    {
        return $this->isSuperAdmin() || $this->hasPermission(UserPermissions::EXPORT_ANY_RESPONSES);
    }

    public function canDeleteAnyResponses(): bool
    {
        return $this->isSuperAdmin() || $this->hasPermission(UserPermissions::DELETE_ANY_RESPONSES);
    }

    public function ownsForm(Form $form): bool
    {
        return $form->user_id === $this->id;
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => __('app.roles.super_admin'),
            default => __('app.roles.admin'),
        };
    }

    public function forms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Form::class);
    }
}
