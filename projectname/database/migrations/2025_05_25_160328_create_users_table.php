<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // bigint unsigned primary key
            $table->string('name', 100);
            $table->string('email')->unique();
            $table->integer('age')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('birth_date')->nullable();
            $table->datetime('last_login_at')->nullable();
            $table->text('bio')->nullable();
            $table->json('preferences')->nullable();
            $table->enum('role', ['admin', 'user', 'moderator'])->default('user');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
