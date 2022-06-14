<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'user_details', function (Blueprint $table){
            $table->id();
            $table->string('store_id');
            $table->string('name');
            $table->string('phone');
            $table->text('special_instruct')->nullable();
            $table->integer('location_id')->default(0);
            $table->integer('shipping_id')->default(0);
            $table->string('billing_address');
            $table->string('shipping_address')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('user_details');
    }
}
