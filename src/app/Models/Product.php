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
        'category_id',
        'condition',
        'description',
        'price',
        'status',
    ];
// 画像とのリレーション（1対多）
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
// カテゴリとのリレーション（多対1）
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
// 出品者とのリレーション（多対1）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

// コメントとのリレーション（1対多）
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
