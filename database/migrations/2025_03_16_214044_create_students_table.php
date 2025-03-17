<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('students', function (Blueprint $table) {
        $table->id();  // العمود الرئيسي (id)
        $table->string('name');  // عمود الاسم (name)
        $table->string('major');  // عمود التخصص (major)
        $table->string('level');  // عمود المستوى (level)
        $table->integer('age');  // عمود العمر (age)
        $table->timestamps();  // لحفظ التواريخ (created_at, updated_at)
        $table->dropColumn('level');
    });
}

public function down()
{
    Schema::dropIfExists('students');
}  
};
