<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Restaurant;
use App\Models\Review;



class Restaurant extends Model
{
    use HasFactory, Sortable;

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_restaurant');
    }

    public function regular_holidays()
    {
        return $this->belongsToMany(RegularHoliday::class, 'regular_holiday_restaurant'); 
    }

    // 定義可能なカスタムソート
    public $sortable = [ 
        'rating', 'popular','lowest_price'
    ]; 

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}