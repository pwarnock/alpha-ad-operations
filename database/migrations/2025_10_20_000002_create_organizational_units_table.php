<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizational_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->foreignId('parent_id')->nullable()->constrained('organizational_units')->onDelete('cascade');
            $table->integer('level')->default(0);
            $table->string('path')->nullable(); // Dot notation path: root.child.grandchild
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['organization_id', 'slug']);
            $table->index(['organization_id', 'parent_id', 'level']);
            $table->index(['organization_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizational_units');
    }
};