<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email', 
        'password',
        'avatar_url',
        'api_token' 
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function generateToken()
    {

        $token = hash('sha256', Str::random(60));
        $this->api_token = $token;
        $this->save();
        
        return $token;
    }

    public function revokeToken()
    {
        $this->api_token = null;
        $this->save();
    }

    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {

    }

    public function getRememberTokenName()
    {
        return null;
    }

    public function userGames()
    {
        return $this->hasMany(UserGame::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }
}