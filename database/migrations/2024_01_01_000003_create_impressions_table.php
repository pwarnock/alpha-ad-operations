<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->decimal('revenue', 10, 2)->default(0);
            $table->string('country')->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->unique(['line_item_id', 'date']);
            $table->index(['campaign_id', 'date']);
            $table->index(['date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impressions');
    }
};