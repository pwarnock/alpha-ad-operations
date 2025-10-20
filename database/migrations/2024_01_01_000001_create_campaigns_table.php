<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertiser_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'active', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->decimal('budget', 10, 2);
            $table->decimal('spent', 10, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('pricing_model', ['CPM', 'CPC', 'CPA', 'Flat'])->default('CPM');
            $table->decimal('rate', 8, 2);
            $table->string('target_url');
            $table->json('targeting')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['advertiser_id', 'status']);
            $table->index(['status', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};