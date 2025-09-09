<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE OR REPLACE FUNCTION refresh_device_data_view()
            RETURNS TRIGGER AS $$
            BEGIN
                -- Refresh the materialized view on any INSERT, UPDATE, DELETE
                REFRESH MATERIALIZED VIEW device_data;
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP FUNCTION IF EXISTS refresh_device_data_view();");
    }
};
