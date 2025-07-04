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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained("users")->onDelete('cascade'); // لو testimonial من يوزر
            $table->string('name'); // أو ممكن تسيبه فارغ لو من الأدمن
            $table->text('content');
            $table->unsignedTinyInteger('rating')->default(5)->comment('Rating from 1 to 5');
            $table->unsignedTinyInteger('approved')->default(0)->comment('0: pending, 1: approved, 2: rejected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
