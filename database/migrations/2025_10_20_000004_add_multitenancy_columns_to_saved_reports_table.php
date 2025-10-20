<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saved_reports', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->after('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('organizational_unit_id')->nullable()->after('organization_id')->constrained()->onDelete('cascade');
            
            $table->index(['tenant_id', 'organization_id', 'organizational_unit_id']);
            $table->index(['organization_id', 'report_type']);
            $table->index(['organizational_unit_id', 'report_type']);
        });
    }

    public function down(): void
    {
        Schema::table('saved_reports', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['organizational_unit_id']);
            $table->dropColumn(['tenant_id', 'organization_id', 'organizational_unit_id']);
        });
    }
};