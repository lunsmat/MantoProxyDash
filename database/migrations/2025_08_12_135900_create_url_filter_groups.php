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
        Schema::create('url_filter_groups', function (Blueprint $table) {
            $table->id();

            $table->integer('group_id');
            $table->integer('url_filter_id');

            $table->foreign('group_id')
                ->references('id')->on('groups')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('url_filter_id')
                ->references('id')->on('url_filters')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_filter_groups');
    }
};
