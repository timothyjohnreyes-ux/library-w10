<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')
            ->leftJoin('members', 'members.user_id', '=', 'users.id')
            ->where('users.role', 'member')
            ->whereNull('members.id')
            ->select([
                'users.id as user_id',
                'users.name',
                'users.email',
            ])
            ->orderBy('users.id')
            ->eachById(function (object $user): void {
                DB::table('members')->insert([
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }, 100, 'user_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Backfilled member records are retained to avoid deleting user data.
    }
};
