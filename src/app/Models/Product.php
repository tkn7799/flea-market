<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_name',
        'brand_name',
        'condition',
        'description',
        'price',
        'status',
    ];
// 商品画像
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
// カテゴリ（多対多）
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }
// 出品者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

// お気に入り
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function isFavoritedBy($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }

// コメント
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
