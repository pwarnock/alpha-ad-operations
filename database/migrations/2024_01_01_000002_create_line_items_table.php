<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('status', ['draft', 'active', 'paused', 'completed'])->default('draft');
            $table->integer('impressions_goal')->default(0);
            $table->integer('impressions_delivered')->default(0);
            $table->integer('clicks_goal')->default(0);
            $table->integer('clicks_delivered')->default(0);
            $table->decimal('budget', 10, 2);
            $table->decimal('spent', 10, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('ad_size', ['728x90', '300x250', '160x600', '320x50', '300x600', '970x250', 'custom']);
            $table->string('ad_zone');
            $table->decimal('rate', 8, 2);
            $table->enum('pricing_model', ['CPM', 'CPC', 'CPA', 'Flat'])->default('CPM');
            $table->json('targeting')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['campaign_id', 'status']);
            $table->index(['status', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_items');
    }
};