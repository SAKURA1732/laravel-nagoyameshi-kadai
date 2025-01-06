<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    // 会社概要ページ(indexアクション)
    // 1.未ログインのユーザーは管理者側の会社概要ページにアクセスできない
    public function test_guest_cannot_access_admin_company_index()
    {
        $response = $this->get(route('admin.company.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーは管理者側の会社概要ページにアクセスできない
    public function test_user_cannot_access_admin_company_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.company.index'));

        $response->assertRedirect(route('admin.login'));
    }

// 3.ログイン済みの管理者は管理者側の会社概要ページにアクセスできる
public function test_adminUser_can_access_admin_company_index()
{
    $adminUser = Admin::factory()->create();
    $company = Company::factory()->create();

    $response = $this->actingAs($adminUser, 'admin')->get(route('admin.company.index'));

    $response->assertStatus(200);
}

}
