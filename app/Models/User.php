<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name','username','email','password',
        'role_id','status','avatar_url','verified_by',
    ];

    protected $hidden = [
        'password','remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'locked_until'      => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Siapa yang memverifikasi user ini
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Daftar user yang pernah diverifikasi oleh user (admin) ini
    public function verifiedUsers()
    {
        return $this->hasMany(User::class, 'verified_by');
    }

    // Jika punya tabel roles
    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }
}
