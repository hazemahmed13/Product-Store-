<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // ID تلقائي
            $table->string('name'); // اسم المنتج
            $table->text('description')->nullable(); // وصف المنتج (اختياري)
            $table->decimal('price', 10, 2); // سعر المنتج
            $table->integer('stock'); // الكمية المتاحة
            $table->timestamps(); // created_at و updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

