<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Rating;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'user_name',
        'email',
        'password',
        'profile_image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function registeredAddress()
    {
        return $this->hasOne(Address::class)->where('type', 'registered');
    }

    public function shippingAddress()
    {
        return $this->hasOne(Address::class)->where('type', 'shipping');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites', 'user_id', 'product_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'buyer_id');
    }

    public function sales()
    {
        return $this->hasMany(Purchase::class, 'seller_id');
    }

    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'to_user_id');
    }

    /**
     * 自分が行った評価（自分が評価した側）
     */
    public function givenRatings()
    {
        return $this->hasMany(Rating::class, 'from_user_id');
    }

    /**
     * 評価の平均値を算出 (FN005対応)
     * まだ評価がない場合は null または 0 を返す
     */
    public function getAverageRatingAttribute()
    {
    // 小数点第1位で四捨五入する（例：3.45 -> 3.5）
    $avg = $this->receivedRatings()->avg('rating');
        return $avg ? round($avg, 1) : null;
    }
}

