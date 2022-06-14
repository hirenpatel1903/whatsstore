<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddDetailsFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'user_details', function (Blueprint $table){
            $table->string('email')->nullable()->after('name');
        }
        );
        Schema::table(
            'stores', function (Blueprint $table){
            $table->string('enable_telegram')->default('off')->after('whatsapp_number');
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('add_details_field');
    }
}
