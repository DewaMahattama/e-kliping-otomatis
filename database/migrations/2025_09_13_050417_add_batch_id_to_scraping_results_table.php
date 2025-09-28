<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scraping_results', function (Blueprint $table) {
            $table->string('batch_id', 36)->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('scraping_results', function (Blueprint $table) {
            $table->dropColumn('batch_id');
        });
    }
};
