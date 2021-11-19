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
        Schema::create('kiyoh_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kiyoh_id')->nullable()->default(null);
            $table->unsignedBigInteger('product_id')->nullable()->default(null);
            $table->unsignedBigInteger('cluster_id')->nullable()->default(null);
            $table->string('product_code')->nullable()->default(null);
            $table->string('cluster_code')->nullable()->default(null);
            $table->string('product_name')->nullable()->default(null);
            $table->string('image_url')->nullable()->default(null);
            $table->string('source_url')->nullable()->default(null);
            $table->boolean('active')->default(true);

            $table->unique(['product_id']);
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
        Schema::dropIfExists('kiyoh_products');
    }
};
