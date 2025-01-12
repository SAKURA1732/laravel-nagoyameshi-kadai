<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        
        $reviews = $restaurant->reviews()->paginate(3); 

        $user = Auth::user();
        if($user->subscribed('premium_plan')){
            $reviews = Review::orderBy('created_at','desc')->paginate(5);
        }else{
            $reviews = Review::orderBy('created_at','desc')->paginate(3);
        }
        
        return view('reviews.index', compact('restaurant','reviews'));
    }


    
    public function create(Restaurant $restaurant)
    {
        $reviews = $restaurant->reviews;

        return view('reviews.create',compact('reviews','restaurant'));
    }

    public function store(Request $request,$restaurantId)
    {
        
        $request->validate([
           'score' =>'required|numeric|between:1,5',
           'content' =>'required',
        ]);

        $reviews = new Review();
        $reviews->score = $request->input('score');
        $reviews->content = $request->input('content');
        $reviews->restaurant_id = $restaurantId;
        $reviews->user_id = $request->user()->id; 
        $reviews->save();

        return redirect()->route('reviews.index', ['restaurant' => $restaurantId])
        ->with('flash_message', 'レビューを投稿しました');

    }

    public function edit(Restaurant $restaurant, Review $review, User $user)
    {
        $user = Auth::user();
        $user_id = $user->id;

        if(!$user_id === Auth::user()){
            return redirect()->route('reviews.index',['restaurant'=>$restaurant_id])->with('error_message','不正なアクセスです。');
        }else{
            return view('reviews.edit',compact('restaurant','review'));
        }
    }

    public function update(Request $request, Restaurant $restaurant, Review $review)
    {
        $request->validate([
            'score' =>'required|numeric|between:1,5',
            'content' =>'required',
         ]);
    
        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->save();
    
        return redirect()->route('reviews.index', ['restaurant' => $restaurant->id])
            ->with('flash_message', 'レビューを編集しました。');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        
        return redirect()->route('reviews.index', ['restaurant' => $review->restaurant_id])
            ->with('flash_message', 'レビューを削除しました。');
    }
}