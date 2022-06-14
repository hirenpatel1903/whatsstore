<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'stores', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('store_theme')->nullable();
            $table->string('domains')->nullable();
            $table->string('enable_storelink')->nullable();
            $table->string('enable_subdomain')->nullable();
            $table->string('subdomain')->nullable();
            $table->string('enable_domain')->default('off');
            $table->longText('about')->nullable();
            $table->string('tagline')->nullable();
            $table->string('slug')->nullable();
            $table->string('lang', 5)->default('en');
            $table->longText('storejs')->nullable();
            $table->string('currency')->default('$');
            $table->string('currency_code')->default('USD');
            $table->string('currency_symbol_position')->default('pre')->nullable();
            $table->string('currency_symbol_space')->default('without')->nullable();
            $table->string('whatsapp')->nullable()->default('#');
            $table->string('facebook')->nullable()->default('#');
            $table->string('instagram')->nullable()->default('#');
            $table->string('twitter')->nullable()->default('#');
            $table->string('youtube')->nullable()->default('#');
            $table->string('google_analytic')->nullable();
            $table->string('facebook_pixel')->nullable();
            $table->string('footer_note')->nullable();
           
            $table->string('enable_shipping')->default('on');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('country')->nullable();
            $table->string('logo')->nullable();
            $table->string('invoice_logo')->nullable();
            $table->string('is_stripe_enabled')->default('off');
            $table->text('stripe_key')->nullable();
            $table->text('stripe_secret')->nullable();
            $table->string('is_paypal_enabled')->default('off');
            $table->text('paypal_mode')->nullable();
            $table->text('paypal_client_id')->nullable();
            $table->text('paypal_secret_key')->nullable();
            $table->text('mail_driver')->nullable();
            $table->text('mail_host')->nullable();
            $table->text('mail_port')->nullable();
            $table->text('mail_username')->nullable();
            $table->text('mail_password')->nullable();
            $table->text('mail_encryption')->nullable();
            $table->text('mail_from_address')->nullable();
            $table->text('mail_from_name')->nullable();
            $table->integer('is_active')->default(1);
            $table->integer('created_by')->default(0);
            $table->string('enable_whatsapp')->default('off');
            $table->string('whatsapp_number')->nullable();
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
        Schema::dropIfExists('stores');
    }
}
