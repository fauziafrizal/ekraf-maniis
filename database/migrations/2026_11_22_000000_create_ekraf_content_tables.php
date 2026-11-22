<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_members', function (Blueprint $table): void {
            $table->id();
            $table->string('seed_key')->nullable()->unique();
            $table->string('name');
            $table->string('position');
            $table->string('division')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('subsectors', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('examples')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('programs', function (Blueprint $table): void {
            $table->id();
            $table->string('seed_key')->nullable()->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->text('body');
            $table->text('audience');
            $table->enum('status', ['wacana', 'akan_datang', 'berjalan', 'selesai'])->default('wacana');
            $table->dateTime('starts_at')->nullable();
            $table->string('location')->nullable();
            $table->text('registration_url')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->text('body');
            $table->date('event_date');
            $table->string('cover_path')->nullable();
            $table->string('cover_alt')->nullable();
            $table->string('cover_credit')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('gallery_photos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('activity_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('alt_text');
            $table->text('caption')->nullable();
            $table->string('credit');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_photos');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('subsectors');
        Schema::dropIfExists('board_members');
    }
};
