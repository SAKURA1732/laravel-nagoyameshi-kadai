<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Category;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    /*indexアクション（カテゴリ一覧ページ）*/
    /*1.未ログインのユーザーは管理者側のカテゴリ一覧にアクセスできない*/
    public function test_guest_login_screen_cannot_access_admin_category_index(): void
    {
            $response = $this->get('/admin/categories');
            /*管理者側のログインページにリダイレクトする*/
            $response->assertRedirect('/admin/login');
    }

    /*2.ログイン済み(actingAs)の一般ユーザーは管理者側のカテゴリ一覧にアクセスできない*/
    public function test_user_login_screen_cannot_access_admin_category_index(): void
    {
        /*一般ユーザーのダミーデータを作成*/
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get('/admin/categories');

        /*管理者側のログインページにリダイレクトする*/
        $response->assertRedirect('/admin/login');
    }

    /*3.ログイン済みの管理者は管理者側のカテゴリ一覧にアクセスできる*/
    public function test_admin_login_screen_can_access_admin_category_index(): void
    {
        $adminUser = Admin::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.categories.index', $adminUser));
        $response->assertStatus(200);
    }

    /*storeアクション（カテゴリ登録機能）*/
    /*1.未ログインのユーザーはカテゴリを登録できない*/
    public function test_guest_cannot_access_admin_category_store()
    {
         $categoryData = [
             'name' => 'テストカテゴリー',
         ];
    
    // 未ログインの状態でPOSTリクエストを送信
    $response = $this->post(route('admin.categories.store'), $categoryData);
    
    //リクエストがリダイレクトされたことを確認
    $response->assertRedirect('/admin/login');
    
    // カテゴリがデータベースに作成されていないことを確認
    $this->assertDatabaseMissing('categories', $categoryData);
}

    /*2.ログイン済みの一般ユーザーはカテゴリを登録できない*/
    public function test_user_cannot_access_admin_category_store()
    {
        $user = User::factory()->create();
        $categoryData = [
            'name' => 'テストカテゴリー',
        ];

        $response = $this->actingAs($user)->post(route('admin.categories.store'), $categoryData);
        $response->assertRedirect('/admin/login');
   
   // カテゴリがデータベースに作成されていないことを確認
        $this->assertDatabaseMissing('categories', $categoryData);
    }

    /*3ログイン済みの管理者はカテゴリを登録できる*/
    public function test_admin_can_access_admin_category_store()
    {
        $user = User::factory()->create();
        $adminUser = Admin::factory()->create();
        $categoryData = [
            'name' => 'テストカテゴリー',
        ];
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.categories.store'));
        $response->assertStatus(200);
    }

    /*updateアクション（カテゴリ更新機能）*/
    /*1.未ログインのユーザーはカテゴリを更新できない*/
    public function test_guest_cannot_access_admin_category_update()
    {
        // テスト用のカテゴリを作成
        $category = Category::create([
            'name' => 'テストカテゴリー',
        ]);
    
        // 更新データ
        $new_categoryData = [
            'name' => 'テストカテゴリー2',
        ];
    
        // PATCHリクエストを送信（カテゴリIDを指定）
        $response = $this->patch(route('admin.categories.update', $category->id), $new_categoryData);
        
        //カテゴリが更新されていないことを確認
        $this->assertDatabaseMissing('categories', $new_categoryData);
        
        //ログインページにリダイレクトされたことを確認
        $response->assertRedirect('/admin/login');
    }

    /*2.ログイン済みの一般ユーザーはカテゴリを更新できない*/
    public function test_user_cannot_access_admin_category_update()
    {
        // テスト用のカテゴリを作成
        $category = Category::create([
            'name' => 'テストカテゴリー',
        ]);
    
        // 更新データ
        $new_categoryData = [
            'name' => 'テストカテゴリー2',
        ];

        // 一般ユーザーのダミーデータを作成
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('admin.categories.update', $category->id), $new_categoryData);
        $this->assertDatabaseMissing('categories', $new_categoryData);
        $response->assertRedirect(route('admin.login'));
    }

        /*3.ログイン済みの管理者はカテゴリを更新できる*/
        public function test_admin_can_access_admin_category_update()
        {
        // テスト用のカテゴリを作成
        $category = Category::create([
            'name' => 'テストカテゴリー',
        ]);
    
        // 更新データ
        $new_categoryData = [
            'name' => 'テストカテゴリー2',
        ];
        // 管理者のダミーデータを作成
            $adminUser = Admin::factory()->create();
    
        // 管理者としてログインし、カテゴリーの更新リクエストを送信
        $response = $this->actingAs($adminUser, 'admin')->patch(route('admin.categories.update', $category->id), $new_categoryData);
    
        // リダイレクト先をassertし、更新が成功したことを確認
        $response->assertRedirect(route('admin.categories.index'));
    
        // データベースの確認
        $this->assertDatabaseHas('categories', [
                'name' => 'テストカテゴリー2',
            ]);
        }

    /*destroyアクション（カテゴリ削除機能））*/
    /*1.未ログインのユーザーはカテゴリーを削除できない*/
    public function test_guest_cannot_access_admin_category_destroy()
    {
    // テスト用のカテゴリを作成
        $category = Category::create([
        'name' => 'テストカテゴリー',
        ]);

        $response = $this->delete(route('admin.categories.destroy', $category));

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $response->assertRedirect(route('admin.login'));
    }

    /*2.ログイン済みの一般ユーザーはカテゴリーを削除できない*/
    public function test_user_cannot_access_admin_category_destroy()
    {
        // テスト用のカテゴリを作成
        $category = Category::create([
            'name' => 'テストカテゴリー',
        ]);
    
        // 更新データ
        $new_categoryData = [
            'name' => 'テストカテゴリー2',
        ];

        // 一般ユーザーのダミーデータを作成
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.categories.destroy', $category));

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $response->assertRedirect(route('admin.login'));
    }

    /*3.ログイン済みの管理者はカテゴリーを削除できる*/
    public function test_admin_can_access_admin_category_destroy()
    {
        $adminUser = Admin::factory()->create();
        // テスト用のカテゴリを作成
        $category = Category::create([
            'name' => 'テストカテゴリー',
        ]);
    
        // 更新データ
        $new_categoryData = [
            'name' => 'テストカテゴリー2',
        ];

        $response = $this->actingAs($adminUser, 'admin')->delete(route('admin.categories.update', $category->id), $new_categoryData);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $response->assertRedirect(route('admin.categories.index'));
    }
}