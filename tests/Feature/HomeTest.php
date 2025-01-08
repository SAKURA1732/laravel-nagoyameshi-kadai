<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    // 1.未ログインのユーザーは会員側のトップページにアクセスできる
    public function test_guest_login_can_access_user_index(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    // 2.ログイン済みの一般ユーザーは会員側のトップページにアクセスできる
    public function test_user_login_can_access_user_index(): void
    {
        /*一般ユーザーのダミーデータを作成*/
        $user = User::factory()->create();
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    // 3.ログイン済みの管理者は会員側のトップページにアクセスできない
    public function test_admin_login_cannot_access_user_index(): void
    {
        $adminUser = Admin::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('home'));

        /*管理者側のトップページにリダイレクトする*/
        $response->assertRedirect('admin/home');
    }

}
