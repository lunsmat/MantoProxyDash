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
        DB::statement("
            CREATE OR REPLACE VIEW device_filters AS
                SELECT
                    devices.id as device_id,
                    devices.mac_address,
                    url_filters.filters
                FROM devices, url_filters
                WHERE devices.id IN (
                    SELECT url_filter_devices.device_id FROM url_filter_devices WHERE url_filter_devices.url_filter_id = url_filters.id
                ) OR devices.id IN (
                    SELECT
                        group_devices.device_id
                    FROM
                        group_devices
                    JOIN url_filter_groups ON url_filter_groups.group_id = group_devices.group_id
                    WHERE url_filter_groups.url_filter_id = url_filters.id
                );
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS device_filters");
    }
};
