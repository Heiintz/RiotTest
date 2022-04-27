<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRotation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rotation', function (Blueprint $table) {
            Schema::create('rotation', function (Blueprint $table) {
                $table->id('rotation_id');
                $table->json('data', 150);
                $table->timestamp('created_at')->useCurrent();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rotation', function (Blueprint $table) {
            //
        });
    }
}
