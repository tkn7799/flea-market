<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'buyer_id',
        'seller_id',
        'product_id',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 購入者
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * 出品者
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function messages()
    {
        return $this->hasMany(TransactionMessage::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}