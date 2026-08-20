<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, MustVerifyEmailTrait;

    protected $fillable = [
        'role_id', 'name', 'email', 'phone', 'password', 'status', 'email_verified_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function agent()
    {
        return $this->hasOne(Agent::class);
    }

    public function owner()
    {
        return $this->hasOne(Owner::class);
    }

    public function barber()
    {
        return $this->hasOne(Barber::class);
    }

    public function isRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }
}
