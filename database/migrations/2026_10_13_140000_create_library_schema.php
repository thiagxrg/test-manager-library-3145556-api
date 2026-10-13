<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS library');

        Schema::create('category', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 120)->unique();
        });

        Schema::create('book', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 250);
            $table->string('author', 200);
            $table->char('isbn', 13);
            $table->uuid('category_id');
            $table->integer('total_copies');
            $table->integer('available_copies');
            $table->string('cover_key', 512)->nullable();
            $table->timestampTz('deleted_at')->nullable();

            $table->foreign('category_id')->references('id')->on('category')->restrictOnDelete();
        });

        DB::statement('CREATE UNIQUE INDEX uq_book_isbn_active ON library.book (isbn) WHERE deleted_at IS NULL');
        DB::statement('CREATE INDEX ix_book_category_title_active ON library.book (category_id, title) WHERE deleted_at IS NULL');
        DB::statement('ALTER TABLE library.book ADD CONSTRAINT ck_book_available_range CHECK (available_copies >= 0 AND available_copies <= total_copies)');
        DB::statement('ALTER TABLE library.book ADD CONSTRAINT ck_book_total_positive CHECK (total_copies >= 1)');

        Schema::create('member', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('full_name', 200);
            $table->string('document_number', 30)->unique();
        });

        Schema::create('user', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username', 120)->unique();
            $table->string('password_hash', 512);
            $table->string('role', 40);
        });

        DB::statement("ALTER TABLE library.\"user\" ADD CONSTRAINT ck_user_role CHECK (role IN ('admin', 'librarian'))");

        Schema::create('loan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('member_id');
            $table->uuid('loaned_by_user_id');
            $table->timestampTz('loaned_at');

            $table->foreign('member_id')->references('id')->on('member')->restrictOnDelete();
            $table->foreign('loaned_by_user_id')->references('id')->on('user')->restrictOnDelete();
        });

        DB::statement('CREATE INDEX ix_loan_loaned_at ON library.loan (loaned_at)');

        Schema::create('loan_item', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_id');
            $table->uuid('book_id');
            $table->string('book_title', 250);
            $table->char('isbn', 13);
            $table->string('category_name', 120);
            $table->timestampTz('due_at');
            $table->timestampTz('returned_at')->nullable();

            $table->foreign('loan_id')->references('id')->on('loan')->cascadeOnDelete();
            $table->foreign('book_id')->references('id')->on('book')->restrictOnDelete();
            $table->unique(['loan_id', 'book_id']);
        });

        DB::statement('CREATE INDEX ix_loan_item_book_id ON library.loan_item (book_id)');
        DB::statement('CREATE INDEX ix_loan_item_due_at_open ON library.loan_item (due_at) WHERE returned_at IS NULL');

        DB::table('category')->insert([
            ['id' => '11111111-1111-4111-8111-111111111111', 'name' => 'Novel'],
            ['id' => '22222222-2222-4222-8222-222222222222', 'name' => 'Science'],
            ['id' => '33333333-3333-4333-8333-333333333333', 'name' => 'Technology'],
            ['id' => '44444444-4444-4444-8444-444444444444', 'name' => 'History'],
            ['id' => '55555555-5555-4555-8555-555555555555', 'name' => "Children's"],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_item');
        Schema::dropIfExists('loan');
        Schema::dropIfExists('user');
        Schema::dropIfExists('member');
        Schema::dropIfExists('book');
        Schema::dropIfExists('category');
    }
};
