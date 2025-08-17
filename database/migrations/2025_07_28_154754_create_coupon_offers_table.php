<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /** Run the migrations. */
    public function up(): void
    {
        Schema::create('coupon_offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('item_description')->nullable();
            $table->string('validity_period')->nullable();
            $table->string('terms')->nullable();
            $table->timestamps();
        });
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::dropIfExists('coupon_offers');
    }
};
