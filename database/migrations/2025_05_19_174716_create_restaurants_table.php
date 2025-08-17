<?php

declare(strict_types=1);

use App\Enums\OfferType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /** Run the migrations. */
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cuisine_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('offer', OfferType::toArray());
            $table->string('location')->nullable();
            $table->float('lat')->nullable();
            $table->float('lng')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('website_url')->nullable();
            $table->string('menu_url')->nullable();
            $table->boolean('menu_from_file')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('coupon_per_user')->default(3);
            $table->timestamps();

            $table->index('name');
            $table->index('lat');
            $table->index('lng');
            $table->index('description');
            $table->index('city_id');
            $table->index('venue_id');
            $table->index('cuisine_id');
            $table->index('location');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
