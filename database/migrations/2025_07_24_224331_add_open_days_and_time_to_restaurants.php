<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /** Run the migrations. */
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('open_days')->nullable();
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
        });
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('open_days');
            $table->dropColumn('open_time');
            $table->dropColumn('close_time');
        });
    }
};
