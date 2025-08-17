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
            $table->dropColumn('offer');
            $table->foreignId('coupon_offer_id')->nullable()->constrained();
        });
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('offer')->nullable();
            $table->dropForeign(['coupon_offer_id']);
            $table->dropColumn('coupon_offer_id');
        });
    }
};
