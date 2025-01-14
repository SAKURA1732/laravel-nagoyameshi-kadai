<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Cashier\Billable;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Billable;
    public function reviews(){
        return $this->hasMany(Review::class);
    }

    public function reservations(){
    return $this->hasMany(Reservation::class);
    }

    public function favorite_restaurants() {
        return $this->belongsToMany(Restaurant::class, 'restaurant_user', 'user_id', 'restaurant_id');
    }
}