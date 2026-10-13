<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sanctum's own token storage. Kept in `public`, deliberately outside the `library`
 * schema: it is authentication infrastructure, not one of the six business tables
 * documented in data-model.md (D-11 / DECISIONS.md).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public.personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->uuidMorphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public.personal_access_tokens');
    }
};
