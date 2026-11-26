<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekraf_service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 24)->unique();
            $table->string('secret_hash', 64);
            $table->string('category', 40);
            $table->string('applicant', 100);
            $table->string('contact', 150)->nullable();
            $table->text('description');
            $table->string('status', 24)->default('received')->index();
            $table->timestamps();
        });
        Schema::create('ekraf_service_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekraf_service_request_id')->constrained('ekraf_service_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 24);
            $table->text('internal_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekraf_service_events');
        Schema::dropIfExists('ekraf_service_requests');
    }
};
