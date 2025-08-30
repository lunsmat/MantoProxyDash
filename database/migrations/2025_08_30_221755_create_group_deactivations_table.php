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
        Schema::create('group_deactivations', function (Blueprint $table) {
            $table->id();

            $table->integer('user_id');
            $table->integer('group_id');

            $table->text('deactivation_datetime')->nullable();
            $table->text('reactivation_datetime')->nullable();
            $table->text('reason')->nullable();

            $table->boolean('deactivation_occurred')->default(false);
            $table->boolean('reactivation_occurred')->default(false);

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('group_id')
                ->references('id')->on('groups')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_deactivations');
    }
};
