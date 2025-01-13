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

    public function categories(){
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function regular_holidays(){
        return $this->belongsToMany(RegularHoliday::class)->withTimestamps();
    }

    public function reviews(){
        return $this->hasMany(Review::class);
    }

    public function reservations(){
    return $this->hasMany(Reservation::class);
    }

    public function popularSortable($query, $direction) {
        return $query->withCount('reservations')->orderBy('reservations_count', $direction);
        }

    // 定義可能なカスタムソート
    public $sortable = [ 
        'rating', 'popular','lowest_price'
    ]; 
}