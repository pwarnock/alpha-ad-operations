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
        Schema::table('line_items', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('organizational_unit_id')->nullable()->after('tenant_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('line_items', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['organizational_unit_id']);
            $table->dropColumn(['tenant_id', 'organizational_unit_id']);
        });
    }
};
