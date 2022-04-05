<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\TokenFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'login' => 'admin',
            'password' => Hash::make('admin')
        ]);

        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => 'App\Models\User',
            'tokenable_id' => '1',
            'name' => 'api-token',
            'token' => 'a6b4c03eb907cb344e8aea8cdb41341b28bec380a105a7a126eca2f0fe8bcbe1', // 1|vx4AQKppebZGV7moWenUCcmhwS1uKbxBxnVRQJ6S
            'abilities' => '["*"]',
        ]);
    }
}
