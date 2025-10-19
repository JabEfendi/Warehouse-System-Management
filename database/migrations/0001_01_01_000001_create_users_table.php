<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('email', 191)->unique();
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('avatar_url', 255)->nullable();

            // role (kalau punya tabel roles)
            $table->foreignId('role_id')->constrained('roles')->cascadeOnUpdate();

            // status & verifikasi
            $table->enum('status', ['active','inactive','suspended','pending'])
                  ->default('pending')
                  ->index();
            $table->timestamp('email_verified_at')->nullable();

            // self-reference: siapa yang memverifikasi user ini
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete(); // kalau verifier dihapus, nilai jadi null

            // login/audit ringan
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->unsignedInteger('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();

            $table->rememberToken(); // sinkron dengan model
            $table->timestamps();
            $table->softDeletes();

            // index tambahan
            $table->index(['role_id', 'status']);
            $table->index(['email_verified_at', 'last_login_at']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
