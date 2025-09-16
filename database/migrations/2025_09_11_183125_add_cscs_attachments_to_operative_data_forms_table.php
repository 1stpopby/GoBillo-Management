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
        Schema::table('operative_data_forms', function (Blueprint $table) {
            $table->string('cscs_card_front_image')->nullable()->after('cscs_card_expiry');
            $table->string('cscs_card_back_image')->nullable()->after('cscs_card_front_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operative_data_forms', function (Blueprint $table) {
            $table->dropColumn(['cscs_card_front_image', 'cscs_card_back_image']);
        });
    }
};
