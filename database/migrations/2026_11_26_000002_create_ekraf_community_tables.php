<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekraf_documents', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 20);
            $table->string('number', 100);
            $table->string('title', 200);
            $table->date('document_date');
            $table->text('body');
            $table->string('file_path')->nullable();
            $table->timestamps();
            $table->unique(['kind', 'number']);
        });
        Schema::create('ekraf_credentials', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique();
            $table->string('kind', 20);
            $table->string('number', 100)->unique();
            $table->string('recipient', 150);
            $table->string('title', 200);
            $table->date('issued_on');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('ekraf_posts', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 30);
            $table->string('title', 150);
            $table->string('display_name', 100);
            $table->text('body');
            $table->string('url', 500)->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('published')->default(false);
            $table->timestamps();
        });
        Schema::create('ekraf_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creative_listing_id')->constrained()->cascadeOnDelete();
            $table->string('display_name', 80);
            $table->unsignedTinyInteger('rating');
            $table->text('body');
            $table->boolean('published')->default(false);
            $table->timestamps();
        });
        Schema::create('ekraf_feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('rating');
            $table->text('body')->nullable();
            $table->timestamps();
        });
        Schema::create('ekraf_polls', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->json('options');
            $table->boolean('open')->default(false);
            $table->timestamps();
        });
        Schema::create('ekraf_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekraf_poll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('choice');
            $table->timestamps();
            $table->unique(['ekraf_poll_id', 'user_id']);
        });
        Schema::table('creative_listings', function (Blueprint $table) {
            $table->string('photo_path')->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->foreignId('business_id')->nullable()->constrained('creative_listings')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('creative_listings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('business_id');
            $table->dropColumn(['photo_path', 'latitude', 'longitude']);
        });
        foreach (['ekraf_votes', 'ekraf_polls', 'ekraf_feedback', 'ekraf_reviews', 'ekraf_posts', 'ekraf_credentials', 'ekraf_documents'] as $name) {
            Schema::dropIfExists($name);
        }
    }
};
