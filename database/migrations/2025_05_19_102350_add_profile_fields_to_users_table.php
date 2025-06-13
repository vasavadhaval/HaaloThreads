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
        Schema::table('users', function (Blueprint $table) {
            $table->text('address')->nullable()->after('whatsapp_number');
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable()->after('address');
            $table->string('education')->nullable()->after('blood_group');
            $table->string('occupation')->nullable()->after('education');
            $table->integer('age')->nullable()->after('occupation');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('age');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'blood_group',
                'education',
                'occupation',
                'age',
                'marital_status'
            ]);
        });
    }
};
