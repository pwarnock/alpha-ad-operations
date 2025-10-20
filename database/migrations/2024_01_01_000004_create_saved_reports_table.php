<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('report_type', ['advertiser_performance', 'inventory', 'campaign_delivery', 'revenue']);
            $table->json('configuration');
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'report_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_reports');
    }
};