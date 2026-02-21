<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Kolom yang boleh diisi via mass assignment
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // Sembunyikan kolom sensitif saat serialisasi (toArray/toJson)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Cast tipe data otomatis
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed', // Password otomatis di-hash saat disimpan
        ];
    }
}

