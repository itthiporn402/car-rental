<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ผู้ใช้ที่ให้คะแนน
            $table->foreignId('booking_id')->constrained()->onDelete('cascade'); // การจองที่ผู้ใช้ให้คะแนน
            $table->foreignId('car_id')->constrained()->onDelete('cascade'); // รถที่ผู้ใช้ให้คะแนน
            $table->integer('rating'); // rating: 1-5
            $table->text('comment');  // คอมเมนต์ของผู้ใช้
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
