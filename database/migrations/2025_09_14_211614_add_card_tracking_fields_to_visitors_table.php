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
        Schema::table('visitors', function (Blueprint $table) {
            $table->boolean('card_returned')->default(false);
            $table->integer('follow_up_count')->default(0);
            $table->timestamp('last_follow_up')->nullable();
            $table->boolean('escalation_email_sent')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn('card_returned');
            $table->dropColumn('follow_up_count');
            $table->dropColumn('last_follow_up');
            $table->dropColumn('escalation_email_sent');
        });
    }
};
