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
        DB::statement("
            CREATE OR REPLACE VIEW device_data AS
                SELECT
                    devices.id,
                    devices.name,
                    devices.mac_address,
                    devices.allow_connection,
                    STRING_AGG(device_filters.filters, E'\n') as filters
                FROM devices
                LEFT JOIN device_filters ON device_filters.device_id = devices.id
                GROUP BY devices.id, devices.name, devices.mac_address, devices.allow_connection;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS device_data");
    }
};
