<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class UserTest extends TestCase
{
    use RefreshDatabase;
 
    /*会員一覧ページ*/
    public function test_guest_login_screen_cannot_access_admin_user_index(): void
    {
        /*未ログインユーザーは管理者側の会員一覧にアクセスできない*/
        $response = $this->get('/admin/users');
        /*管理者側のログインページにリダイレクトする*/
        $response->assertRedirect('/admin/login');
    }

    public function test_user_login_screen_cannot_access_admin_user_index(): void
    {
        /*一般ユーザーのダミーデータを作成*/
        $user = User::factory()->create();
        /*ログイン済み(actingAs)の一般ユーザーは管理者側の会員一覧にアクセスできない*/
        $response = $this->actingAs($user, 'web')->get('/admin/users');
        /*管理者側のログインページにリダイレクトする*/
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_login_screen_can_access_admin_user_index(): void
    {
        $adminUser = Admin::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    /*会員詳細ページ*/
    /*未ログインのユーザーは管理者側の会員詳細ページにアクセスできない*/
    public function test_guest_cannot_access_admin_user_show(): void
    {
        $user = User::factory()->create();
        $response = $this->get(route('admin.users.show', $user));
        $response->assertRedirect(route('admin.login'));
    }

    /*ログイン済みの一般ユーザーは管理者側の会員詳細ページにアクセスできない*/
    public function test_user_cannot_access_admin_user_show(): void
    {
        /*一般ユーザーのダミーデータを作成*/
        $user = User::factory()->create();
        /*ログイン済み(actingAs)の一般ユーザーは管理者側の会員詳細にアクセスできない*/
        $response = $this->actingAs($user)->get(route('admin.users.show', $user));
        /*管理者側のログインページにリダイレクトする*/
        $response->assertRedirect(route('admin.login'));
    }

    /*ログイン済みの管理者は管理者側の会員詳細ページにアクセスできる*/
    public function test_admin_login_screen_can_access_admin_user_show(): void
    {

        $user = User::factory()->create();

        $adminUser = Admin::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.users.show', $user));
        $response->assertStatus(200);
    }
}

