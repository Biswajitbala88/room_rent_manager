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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('month')->nullable();
            $table->integer('electricity_units')->default(0)->nullable();
            $table->decimal('electricity_charge', 8, 2)->default(0)->nullable();
            $table->decimal('water_charge', 8, 2)->default(0)->nullable();
            $table->decimal('total_amount', 8, 2)->nullable();
            $table->string('status')->default('unpaid')->nullable(); // paid/unpaid
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
