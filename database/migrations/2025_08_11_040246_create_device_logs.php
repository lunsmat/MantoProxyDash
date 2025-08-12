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
        Schema::create('device_logs', function (Blueprint $table) {
            $table->id();

            $table->integer('device_id')->unsigned();
            $table->string('http_method');
            $table->string('http_url');
            $table->text('http_headers');
            $table->text('http_body');
            $table->timestamps();

            $table->foreign('device_id')
                ->references('id')->on('devices')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_logs');
    }
};
