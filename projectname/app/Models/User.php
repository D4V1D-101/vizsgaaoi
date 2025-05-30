<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'age',
        'salary',
        'is_active',
        'birth_date',
        'last_login_at',
        'bio',
        'preferences',
        'role'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'salary' => 'decimal:2',
        'preferences' => 'json'
    ];

    // Kapcsolat a posts táblával (egy user több postot írhat)
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
