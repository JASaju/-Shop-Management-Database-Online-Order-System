<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The bill_voucher dump has no `users` table, which login needs.
 * Laravel's default users migration normally creates it; this one is a safety
 * net that only creates the table if it does not exist yet, so running
 * `php artisan migrate` is safe in either case.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            return;
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Intentionally empty: never drop a users table that another migration owns.
    }
};
