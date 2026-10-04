<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    public function repairTickets()
    {
        return $this->hasMany(RepairTicket::class, 'user_id');
    }

    public function isSystemAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomerUser(): bool
    {
        return $this->role === 'user';
    }
}
