<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create cars table
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('plate_number')->unique();
            $table->year('year');
            $table->string('color');
            $table->integer('seats');
            $table->decimal('daily_rate', 10, 2);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['available', 'rented', 'maintenance'])->default('available');
            $table->timestamps();
        });

        // Create bookings table
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('returned_at')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            // ✅ ป้องกันการจองซ้ำในช่วงเวลาเดียวกัน
            $table->unique(['car_id', 'start_date', 'end_date']);
        });

        // Create payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id', 100)->unique(); // ✅ เผื่อให้รองรับ UUID หรือ Payment Gateway
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('payment_method');
            $table->timestamps();
        });

        // Create reviews table
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('rating'); // ✅ เปลี่ยนเป็น unsignedTinyInteger
            $table->text('comment')->nullable();
            $table->timestamps();

            // ✅ ป้องกันการรีวิวซ้ำจากผู้ใช้เดิมใน Booking เดียวกัน
            $table->unique(['user_id', 'booking_id', 'car_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('cars');
    }
};
