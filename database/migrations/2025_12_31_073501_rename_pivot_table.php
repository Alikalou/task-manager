<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('pivot_table_tag_task', 'tag_task');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
