<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // Menggunakan factory dan notifikasi bawaan Laravel
    use HasFactory, Notifiable;

    // Mengizinkan mass assignment untuk field tertentu
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // Menyembunyikan field sensitif saat serialisasi data
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Mengubah tipe data field tertentu
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Password otomatis di-hash
        ];
    }
}