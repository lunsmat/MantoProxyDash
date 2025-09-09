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
            CREATE TRIGGER refresh_device_data_by_devices
                AFTER INSERT OR UPDATE OR DELETE ON devices
                FOR EACH STATEMENT
                EXECUTE FUNCTION refresh_device_data_view();
        ");

        DB::statement("
            CREATE TRIGGER refresh_device_data_by_group_devices
                AFTER INSERT OR UPDATE OR DELETE ON group_devices
                FOR EACH STATEMENT
                EXECUTE FUNCTION refresh_device_data_view();
        ");

        DB::statement("
            CREATE TRIGGER refresh_device_data_by_url_filter_devices
                AFTER INSERT OR UPDATE OR DELETE ON url_filter_devices
                FOR EACH STATEMENT
                EXECUTE FUNCTION refresh_device_data_view();
        ");

        DB::statement("
            CREATE TRIGGER refresh_device_data_by_url_filter_groups
                AFTER INSERT OR UPDATE OR DELETE ON url_filter_groups
                FOR EACH STATEMENT
                EXECUTE FUNCTION refresh_device_data_view();
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER IF EXISTS refresh_device_data_by_devices ON devices");
        DB::statement("DROP TRIGGER IF EXISTS refresh_device_data_by_group_devices ON group_devices");
        DB::statement("DROP TRIGGER IF EXISTS refresh_device_data_by_url_filter_devices ON url_filter_devices");
        DB::statement("DROP TRIGGER IF EXISTS refresh_device_data_by_url_filter_groups ON url_filter_groups");
    }
};
