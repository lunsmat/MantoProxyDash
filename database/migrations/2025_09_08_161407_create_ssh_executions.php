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
        Schema::create('ssh_executions', function (Blueprint $table) {
            $table->id();

            $table->enum("status", ["pending", "in_progress", "completed", "failed", "partial_failure"])->default("pending");
            $table->string("script_path")->nullable();
            $table->string("command")->nullable();

            $table->text("output")->nullable();

            $table->string("object_type");
            $table->string("object_id");

            $table->integer("user_id")->nullable();
            $table->integer("parent_id")->nullable();
            $table->integer("ssh_user_id");

            $table->index(['object_type', 'object_id']);
            $table->index('status');

            $table->foreign('parent_id')->references('id')->on('ssh_executions')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('ssh_user_id')->references('id')->on('ssh_users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ssh_executions');
    }
};
