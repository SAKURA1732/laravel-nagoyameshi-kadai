<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\RegularHoliday;
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
    public function test_admin_login_screen_can_access_admin_restaurant_index(): void
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

    /*3ログイン済みの管理者は店舗を登録できる*/
    public function test_admin_can_access_admin_restaurants_store()
    {
        $user = User::factory()->create();
        $adminUser = Admin::factory()->create();
        $restaurant = Restaurant::factory()->make()->toArray();
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.store'));
        $response->assertStatus(200);
    }

    /*店舗にカテゴリを正しく設定できない（管理者以外の場合）*/
    public function test_user_cannot_setting_admin_restaurants()
    {
        $user = User::factory()->create();
        $restaurant = [
            'name' => 'テスト',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
            'category_ids' => $categories
        ];
        $categories = [];
        for ($i = 0; $i < 3; $i++) {
            $categories[] = Category::create(['name' => 'Category ' . $i]);
        }
        $categoryIds = array_map(fn($category) => $category->id, $categories);
   
        $response = $this->actingAs($user)->post(route('admin.restaurants.store'), $restaurant);
    
        // 'category_ids' キーを持たないようにする
        unset($restaurant['category_ids']);
    
        $this->assertDatabaseMissing('restaurants', $restaurant);
        $response->assertRedirect(route('admin.login'));
    }

    /*店舗にカテゴリを正しく設定できる（管理者の場合）*/
    public function test_adminUser_can_setting_admin_restaurants()
    {
        $adminUser = Admin::factory()->create();
    
        $categories = [];
        for ($i = 0; $i < 3; $i++) {
            $categories[] = Category::create(['name' => 'Category ' . $i]);
        }
        $categoryIds = array_map(fn($category) => $category->id, $categories);
 
    
        $restaurantData = [
            'name' => 'テスト',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
            'category_ids' => $categoryIds
        ];
    
        $response = $this->actingAs($adminUser, 'admin')->post(route('admin.restaurants.store'), $restaurantData);
    
        unset($restaurantData['category_ids']);
        $this->assertDatabaseHas('restaurants', $restaurantData);
    
        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseHas('category_restaurant', [
                'category_id' => $categoryId,
            ]);
        }
    
        $response->assertRedirect(route('admin.restaurants.index'));
    }

    /*店舗に定休日を正しく設定できない（管理者以外の場合）*/
    public function test_user_cannot_setting_admin_holidays()
    {
        $restaurant = Restaurant::factory()->make()->toArray();
        $user = User::factory()->create();
        $categories = [];
        for ($i = 0; $i < 3; $i++) {
            $categories[] = Category::create(['name' => 'Category ' . $i]);
        }
        $categoryIds = array_map(fn($category) => $category->id, $categories);
        

    

        $regularholidays = RegularHoliday::factory()->count(3)->create();
        $regular_holiday_ids = $regularholidays->pluck('id')->toArray();
    
        $restaurant->categories()->sync($category_ids);
        $restaurant->regular_holidays()->sync($regular_holiday_ids);
    
        $updateData = [
            'name' => '更新されたレストラン名',
            'description' => '更新された説明',
            'lowest_price' => 1200,
            'highest_price' => 6000,
            'postal_code' => '1111111',
            'address' => '更新された住所',
            'opening_time' => '11:00:00',
            'closing_time' => '21:00:00',
            'seating_capacity' => 60,
            'category_ids' => $category_ids,
            'regularholiday_ids' => $regular_holiday_ids,
        ];
        
        $response = $this->actingAs($admin, 'admin')->put(route('admin.restaurants.update', $restaurant), $updateData);
    
        // Assert basic data update
        unset($updateData['category_ids']);
        unset($updateData['regularholiday_ids']);
        unset($restaurant['updated_at'], $restaurant['created_at']);
        $this->assertDatabaseHas('restaurants', array_merge(['id' => $restaurant->id], $updateData));
    
        // Assert the relations in the database
        foreach ($category_ids as $category_id) {
            $this->assertDatabaseHas('category_restaurant', [
                'restaurant_id' => $restaurant->id,
                'category_id' => $category_id,
            ]);
        }
    
        foreach ($regular_holiday_ids as $regular_holiday_id) {
            $this->assertDatabaseHas('restaurant_regular_holiday', [
                'restaurant_id' => $restaurant->id,
                'regular_holiday_id' => $regular_holiday_id,
            ]);
        }
    
        $response->assertRedirect(route('admin.restaurants.show', $restaurant));
    }

    /*店舗に定休日を正しく設定できる（管理者の場合）*/

    

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

