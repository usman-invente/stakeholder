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
        Schema::table('stakeholders', function (Blueprint $table) {
            $table->string('dcg_contact_person')->nullable()->after('organization');
            $table->string('method_of_engagement')->nullable()->after('dcg_contact_person');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stakeholders', function (Blueprint $table) {
            $table->dropColumn('dcg_contact_person');
            $table->dropColumn('method_of_engagement');
        });
    }
};
