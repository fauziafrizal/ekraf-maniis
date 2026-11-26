<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->boolean('is_ekraf_admin')->default(false));
        Schema::create('creative_listings', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 20);
            $table->string('name', 150);
            $table->string('category', 100);
            $table->string('village', 100)->nullable();
            $table->text('description');
            $table->unsignedBigInteger('price')->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->boolean('published')->default(false);
            $table->timestamps();
            $table->index(['kind', 'published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creative_listings');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_ekraf_admin'));
    }
};
