<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存のユーザーアカウントを確認して存在しない場合のみ作成
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'ユーザー1',
                'kana' => 'ユーザー1',
                'password' => Hash::make('user1pass'),
                'postal_code' => '5716079',
                'address' => '広島県広島市中区',
                'phone_number' => '082-111-1111',
                'email_verified_at' => now(),
            ]
        );

        // 課題レビュー用のユーザーアカウントを確認して存在しない場合のみ作成
        $user2 = User::firstOrCreate(
            ['email' => 'user2@example.com'],
            [
            'name' => 'ユーザー2',
            'kana' => 'ユーザー2',
            'password' => Hash::make('user2pass'),
            'postal_code' => '5716079',
            'address' => '広島県広島市西区',
            'phone_number' => '082-222-2222',
            'email_verified_at' => now(),
        ]);
    }
}
