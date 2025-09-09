<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ssh_users', function (Blueprint $table) {
            $table->id();

            $table->integer("port")->default(22);
            $table->string("username")->unique();
            $table->string("password")->nullable();
            $table->enum("authentication_method", ["password", "key"])->default("password");
            $table->string("public_key_file_path")->nullable();
            $table->string("private_key_file_path")->nullable();
            $table->string("passphrase")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ssh_users');
    }
};
