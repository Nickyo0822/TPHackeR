<?php

use App\Models\Profiles;
use App\Models\Rights;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profilerights', function (Blueprint $table) {
            $table->foreignIdFor(Profiles::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Rights::class)->constrained()->cascadeOnDelete();
            $table->boolean('isRightActive')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profilerights');
    }
};
