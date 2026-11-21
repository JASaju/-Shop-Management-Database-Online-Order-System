<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_requests', function (Blueprint $table) {
            $table->id('order_request_id');
            $table->string('customer_name', 120)->nullable();
            $table->string('phone', 20);
            $table->json('items');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('status', 20)->default('pending'); // pending|called|confirmed|cancelled
            $table->string('note', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_requests');
    }
};
