<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    // 1つのカテゴリーは複数のレストランを登録できる
    public function restaurants() {
        return $this->belongsToMany(Restaurant::class, 'category_restaurant');
    }
}