<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /** Run the migrations. */
    public function up(): void
    {
        Schema::create('cuisines', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();

            $table->index('name');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::dropIfExists('cuisines');
    }
};
