<?php

use App\Enums\TimePeriod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('pair');
            $table->decimal('price', 16, 4);
            $table->string('email');
            $table->integer('percentage')->default(0);
            $table->enum('period', Arr::pluck(TimePeriod::cases(),'value'));
            $table->dateTime('valid_from');
            $table->boolean('has_expired')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
