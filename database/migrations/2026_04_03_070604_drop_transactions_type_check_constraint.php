<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the constraint if it exists (mostly for postgres/sqlite)
        DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS transactions_type_check');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally recreate if reverting
        DB::statement("ALTER TABLE transactions ADD CONSTRAINT transactions_type_check CHECK (type::text = ANY (ARRAY['income'::character varying, 'expense'::character varying]::text[]))");
    }
};
