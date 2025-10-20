<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('organizational_unit_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_admin')->default(false);
            
            $table->index(['organization_id', 'is_admin']);
            $table->index(['organizational_unit_id']);
            $table->index(['tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['organizational_unit_id']);
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['organization_id', 'organizational_unit_id', 'tenant_id', 'is_admin']);
        });
    }
};