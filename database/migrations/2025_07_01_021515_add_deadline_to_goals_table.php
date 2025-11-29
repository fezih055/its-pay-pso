<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('goals')) {
            Schema::table('goals', function (Blueprint $table) {
                if (!Schema::hasColumn('goals', 'deadline')) {
                    $table->date('deadline')->nullable()->after('priority');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn('deadline');
        });
    }
};
