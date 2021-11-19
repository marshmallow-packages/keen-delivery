<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('deliverable');
            $table->string('carrier_shipping_id')->nullable()->default(null);
            $table->string('track_and_trace_id')->nullable()->default(null);
            $table->string('track_and_trace_url')->nullable()->default(null);
            $table->string('carrier')->nullable()->default(null);
            $table->string('service')->nullable()->default(null);
            $table->unsignedInteger('amount')->nullable()->default(null);
            $table->string('reference')->nullable()->default(null);
            $table->string('company_name')->nullable()->default(null);
            $table->string('contact_person')->nullable()->default(null);
            $table->string('street')->nullable()->default(null);
            $table->string('number')->nullable()->default(null);
            $table->string('number_addition')->nullable()->default(null);
            $table->string('zip_code')->nullable()->default(null);
            $table->string('city')->nullable()->default(null);
            $table->string('country')->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->text('comment')->nullable()->default(null);
            $table->float('weight')->nullable()->default(null);
            $table->json('extra_data')->nullable()->default(null);
            $table->json('payload')->nullable()->default(null);
            $table->longText('response')->nullable()->default(null);
            $table->longText('label_encoded')->nullable()->default(null);

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
};
