<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Review;

class ReviewTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_notsubecribed_login_user_cannot_access_to_subscribe_edit(): void
    // 2ログイン済みの無料会員はレビュー編集ページにアクセスできない
    {
        $user = User::factory()->create();
        $this->actingAs($user);//テストユーザーでログイン
        $restaurant = Restaurant::factory()->create();
        
        // レビューを作成
        $review = Review::factory()->create();

        // ここで有料プランに加入していないことを確認
        // $this->assertFalse($user->subscribed('premium_plan'));
        $response = $this->get(route('restaurants.reviews.edit', [
            $restaurant->id,$review->id,
        ]));
        $this->assertDatabaseHas('reviews',[
            'id' => $review->id, 
        'content' => $review->content, 
        'score' => $review->score,
        ]);

        // $response->assertStatus(403);
        $response->assertRedirect(route('subscription.create'));

    }}
/*
    public function test_subecribed_login_user_cannot_edit_otherusers_review(): void
    // 3ログイン済みの有料会員は会員側の他人のレビュー編集ページにアクセスできない
    {
        $user = User::factory()->create(['subscribed'=>true]);//テストユーザー作成
        $user->newSubscription('premium_plan', 'price_1QhAnhGDRzc6XHkpSQ1oylKM')->create('pm_card_visa'); // プランに加入
        $this->actingAs($user);//テストユーザーでログイン
        $otherUser = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id, 
        ]);
        
       // 編集リクエストを送信
        $response = $this->get(route('restaurants.reviews.edit', [
            $restaurant,$review
        ]));
        $response->assertStatus(403);
    }

    public function test_login_subscribed_user_can_edit_own_review(): void
    // 4ログイン済みの有料会員は会員側の自身のレビュー編集ページにアクセスできる
    {
        $user = User::factory()->create(['subscribed'=>true]);//テストユーザー作成
        $this->actingAs($user);//テストユーザーでログイン
        $user->newSubscription('premium_plan', 'price_1QhAnhGDRzc6XHkpSQ1oylKM')->create('pm_card_visa'); // プランに加入
        $restaurant = Restaurant::factory()->create();
        // レビューを作成
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id, 
        ]);
        
        // レビュー編集リクエスト
        $response = $this->put(route('restaurants.reviews.edit', [$restaurant->id, $review->id]));
    
        // 200ステータスを期待
        $response->assertStatus(200);
    } 
}

*/