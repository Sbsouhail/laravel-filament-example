<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /** Run the migrations. */
    public function up(): void
    {
        Schema::table('coupon_offers', function (Blueprint $table) {
            $table->string('button_title')->nullable()->after('terms');
        });
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::table('coupon_offers', function (Blueprint $table) {
            $table->dropColumn('button_title');
        });
    }
};
