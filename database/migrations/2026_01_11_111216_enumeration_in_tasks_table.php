<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            //

            $table
                ->string('status', 10)
                ->default('to_do')
                ->change();

            $table
                ->string('priortiy', 10)
                ->default('normal')
                ->after('status')
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('priority');

        });
    }
};
