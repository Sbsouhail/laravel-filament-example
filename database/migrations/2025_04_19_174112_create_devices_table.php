<?php

declare(strict_types=1);

use App\Enums\PlatformType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /** Run the migrations. */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('identifier')->unique();
            $table->string('fcm_token')->nullable();
            $table->string('ip_address')->nullable();
            $table->enum('platform', PlatformType::toArray());
            $table->timestamps();

            $table->index('user_id');
            $table->index('identifier');
            $table->index('fcm_token');
            $table->index('ip_address');
            $table->index('platform');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
