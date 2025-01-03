<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    /*indexアクション（店舗一覧ページ）*/
    /*1.未ログインユーザーは管理者側の店舗一覧にアクセスできない*/
    public function test_guest_login_screen_cannot_access_admin_restaurant_index(): void
    {
        $response = $this->get('/admin/restaurants');
        /*管理者側のログインページにリダイレクトする*/
        $response->assertRedirect('/admin/login');
    }

    /*2.ログイン済み(actingAs)の一般ユーザーは管理者側の店舗一覧にアクセスできない*/
    public function test_user_login_screen_cannot_access_admin_restaurant_index(): void
    {
        /*店舗のダミーデータを作成*/
        $restaurant = Restaurant::factory()->create();

        /*一般ユーザーのダミーデータを作成*/
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get('/admin/restaurants');

        /*管理者側のログインページにリダイレクトする*/
        $response->assertRedirect('/admin/login');
    }

    /*3.ログイン済みの管理者は管理者側の店舗一覧にアクセスできる*/
    public function test_admin_login_screen_can_access_admin_restaurant_show(): void
    {
        $adminUser = Admin::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.index', $adminUser));
        $response->assertStatus(200);
    }

    /*showアクション（店舗詳細ページ）*/
    /*1.未ログインのユーザーは管理者側の店舗詳細ページにアクセスできない*/
    public function test_guest_cannot_access_admin_restaurant_show(): void
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('admin.restaurants.show', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }

    /*2.ログイン済みの一般ユーザーは管理者側の店舗詳細ページにアクセスできない*/
    public function test_user_cannot_access_admin_restaurants_show()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.restaurants.show', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }

    /*3.ログイン済みの管理者は管理者側の店舗詳細ページにアクセスできる*/
    public function test_admin_can_access_admin_restaurants_show()
    {
        $adminUser = Admin::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.show', $restaurant));
        $response->assertStatus(200);
    }

    /*createアクション（店舗登録ページ）*/
    /*1.未ログインのユーザーは管理者側の店舗登録ページにアクセスできない*/
    public function test_guest_cannot_access_admin_restaurants_create()
    {
        $response = $this->get(route('admin.restaurants.create'));
        $response->assertRedirect(route('admin.login'));
    }

    /*2.ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない*/
    public function test_user_cannot_access_admin_restaurants_create()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.restaurants.create'));
        $response->assertRedirect(route('admin.login'));
    }

    /*3.ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる*/
    public function test_admin_can_access_admin_restaurants_create()
    {
        $adminUser = Admin::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.create'));
        $response->assertStatus(200);
    }

    /*storeアクション（店舗登録機能）*/
    /*1.未ログインのユーザーは店舗を登録できない*/
    public function test_guest_cannot_access_admin_restaurants_store()
    {
        $restaurant = Restaurant::factory()->make()->toArray();
        $response = $this->post(route('admin.restaurants.store'), $restaurant);
        $this->assertDatabaseHas('restaurants', $restaurant);
    }
    
    /*2.ログイン済みの一般ユーザーは店舗を登録できない*/
    public function test_user_cannot_access_admin_restaurants_store()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->make()->toArray();

        $response = $this->actingAs($user)->post(route('admin.restaurants.store'), $restaurant);
        $response->assertRedirect(route('admin.login'));
    }

    /*ログイン済みの管理者は店舗を登録できる*/
    public function test_admin_can_access_admin_restaurants_store()
    {
        $user = User::factory()->create();
        $adminUser = Admin::factory()->create();
        $restaurant = Restaurant::factory()->make()->toArray();
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.store'));
        $response->assertStatus(200);
    }

    /*editアクション（店舗編集ページ）*/
    /*1.未ログインのユーザーは管理者側の店舗編集ページにアクセスできない*/
    public function test_guest_cannot_access_admin_restaurants_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('admin.restaurants.edit', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }

    /*2.ログイン済みの一般ユーザーは管理者側の店舗編集ページにアクセスできない*/
    public function test_user_cannot_access_admin_restaurants_edit()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.restaurants.edit', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }

    /*3.ログイン済みの管理者は管理者側の店舗編集ページにアクセスできる*/
    public function test_admin_can_access_admin_restaurants_edit()
    {
        $adminUser = Admin::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.edit', $restaurant));
        $response->assertStatus(200);
    }

    /*updateアクション（店舗更新機能）*/
    /*1.未ログインのユーザーは店舗を更新できない*/
    public function test_guest_cannot_access_admin_restaurants_update()
    {
        $old_restaurant = Restaurant::factory()->create();

        $new_restaurant = [
            'name' => 'テスト更新',
            'description' => 'テスト更新',
            'lowest_price' => 5000,
            'highest_price' => 10000,
            'postal_code' => '1234567',
            'address' => 'テスト更新',
            'opening_time' => '13:00:00',
            'closing_time' => '23:00:00',
            'seating_capacity' => 100
        ];

        $response = $this->patch(route('admin.restaurants.update', $old_restaurant), $new_restaurant);
        $this->assertDatabaseMissing('restaurants', $new_restaurant);
        $response->assertRedirect(route('admin.login'));
    }

    /*2.ログイン済みの一般ユーザーは店舗を更新できない*/
    public function test_user_cannot_access_admin_restaurants_update()
    {
        $user = User::factory()->create();
        $old_restaurant = Restaurant::factory()->create();
        $new_restaurant = [
            'name' => 'テスト更新',
            'description' => 'テスト更新',
            'lowest_price' => 5000,
            'highest_price' => 10000,
            'postal_code' => '1234567',
            'address' => 'テスト更新',
            'opening_time' => '13:00:00',
            'closing_time' => '23:00:00',
            'seating_capacity' => 100
        ];

        $response = $this->actingAs($user)->patch(route('admin.restaurants.update', $old_restaurant), $new_restaurant);
        $this->assertDatabaseMissing('restaurants', $new_restaurant);
        $response->assertRedirect(route('admin.login'));
    }

    /*3.ログイン済みの管理者は店舗を更新できる*/
    public function test_admin_can_access_admin_restaurants_update()
    {
        // 管理者ユーザーと既存のレストランを作成
        $adminUser = Admin::factory()->create();
        $oldRestaurant = Restaurant::factory()->create();

        // 更新するための新しいデータを用意
        $newRestaurantData = [
            'name' => 'テスト',
            'description' => 'テストの説明',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト住所',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
        ];

        // 管理者としてログインし、レストランの更新リクエストを送信
        $response = $this->actingAs($adminUser, 'admin')
                         ->patch(route('admin.restaurants.update', $oldRestaurant), $newRestaurantData);

        // リダイレクト先をassertし、更新が成功したことを確認
        $response->assertRedirect(route('admin.restaurants.index'));

        // データベースの確認
        $this->assertDatabaseHas('restaurants', [
            'id' => $oldRestaurant->id,
            'name' => 'テスト',
            'description' => 'テストの説明'
            // 他のフィールドについても同様に確認可能
        ]);
    }

    /*destroyアクション（店舗削除機能）*/
    /*1.未ログインのユーザーは店舗を削除できない*/
    public function test_guest_cannot_access_admin_restaurants_destroy()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->delete(route('admin.restaurants.destroy', $restaurant));

        $this->assertDatabaseHas('restaurants', ['id' => $restaurant->id]);
        $response->assertRedirect(route('admin.login'));
    }

    /*2.ログイン済みの一般ユーザーは店舗を削除できない*/
    public function test_user_cannot_access_admin_restaurants_destroy()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.restaurants.destroy', $restaurant));

        $this->assertDatabaseHas('restaurants', ['id' => $restaurant->id]);
        $response->assertRedirect(route('admin.login'));
    }

    /*3.ログイン済みの管理者は店舗を削除できる*/
    public function test_admin_can_access_admin_restaurants_destroy()
    {
        $adminUser = Admin::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->delete(route('admin.restaurants.destroy', $restaurant));

        $this->assertDatabaseMissing('restaurants', ['id' => $restaurant->id]);
        $response->assertRedirect(route('admin.restaurants.index'));
    }
}

