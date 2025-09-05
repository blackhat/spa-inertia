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
        Schema::create('products', function (Blueprint $table) {
            $table->id()->comment('ไอดีสินค้า');
            $table->string('name')->comment('ชื่อสินค้า');
            $table->foreignId('user_id')->constrained()->comment('ไอดีผู้ใช้');
            $table->foreignId('category_id')->constrained()->comment('ไอดีแคตตาลอต');
            $table->string('brand')->comment('แบรนด์สินค้า');
            $table->integer('price')->check('price >= 0')->comment('ราคาสินค้า');
            $table->decimal('weight', 8, 2)->comment('น้ำหนักกรัม');
            $table->string('image')->nullable()->comment('รูปภาพสินค้า');
            $table->text('description')->nullable()->comment('คำบรรยายสินค้า');
            $table->timestamps();
            $table->comment('ตารางสินค้า');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
