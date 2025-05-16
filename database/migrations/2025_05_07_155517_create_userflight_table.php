<?php

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
        Schema::create('user_flight', function(Blueprint $table){
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('flight_id')->constrained('flights')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
        public function down(): void
        {
            Schema::table('user_flight', function (Blueprint $table) {
                $table->dropColumn('notified');
            });
        }
};
