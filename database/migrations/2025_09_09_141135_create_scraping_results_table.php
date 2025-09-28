<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scraping_results', function (Blueprint $table) {
            $table->id();
            $table->string('portal');           
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('tanggal')->nullable(); 
            $table->string('url')->unique();
            $table->string('hash')->nullable()->index(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scraping_results');
    }
};
