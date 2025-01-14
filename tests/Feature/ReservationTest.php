<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class ReservationTest extends TestCase
{
    use RefreshDatabase;


    // indexアクション（予約一覧ページ）
    // 未ログインのユーザーは会員側の予約一覧ページにアクセスできない
    public function test_guest_cannot_access_reservation_index()
    {
        $response = $this->get(route('reservations.index'));
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側の予約一覧ページにアクセスできない
    // ログイン済みの有料会員は会員側の予約一覧ページにアクセスできる
    // ログイン済みの管理者は会員側の予約一覧ページにアクセスできない

    // createアクション（予約ページ）
    // 未ログインのユーザーは会員側の予約ページにアクセスできない
    
    public function test_guest_cannot_access_reservation_create()
    {
        /*店舗のダミーデータを作成*/
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.1.reservations.create'));
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側の予約ページにアクセスできない
    // ログイン済みの有料会員は会員側の予約ページにアクセスできる
    // ログイン済みの管理者は会員側の予約ページにアクセスできない
    // storeアクション（予約機能）
    // 未ログインのユーザーは予約できない
    // ログイン済みの無料会員は予約できない
    // ログイン済みの有料会員は予約できる
    public function test_premium_user_can_store_reservation()
{
    $user = User::factory()->create();
    $user->newSubscription('premium_plan', '1QgQro01oFaBB')->create('pm_card_visa'); // 有料プランを設定
    $this->actingAs($user);

    $this->assertTrue($user->subscribed('premium_plan'));

    $restaurant = Restaurant::factory()->create();
    // 現在の日時を使用して、予約日と時間を分けて送信
    $now = Carbon::now();
    $response = $this->post(route('restaurants.reservations.store', $restaurant), [
        'reservation_date' => $now->format('Y-m-d'),
        'reservation_time' => $now->format('H:i'),
        'number_of_people' => 2,
    ]);

    $response->assertStatus(200);
}
    // ログイン済みの管理者は予約できない
    // destroyアクション（予約キャンセル機能）
    // 未ログインのユーザーは予約をキャンセルできない
    // ログイン済みの無料会員は予約をキャンセルできない
    // ログイン済みの有料会員は他人の予約をキャンセルできない
    // ログイン済みの有料会員は自身の予約をキャンセルできる
    // ログイン済みの管理者は予約をキャンセルできない

}