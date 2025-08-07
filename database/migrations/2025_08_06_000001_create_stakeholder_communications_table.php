<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stakeholder_communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stakeholder_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->comment('User who recorded the communication');
            $table->date('meeting_date');
            $table->time('meeting_time');
            $table->string('meeting_type'); // in-person, video, phone, email
            $table->string('location')->nullable();
            $table->text('attendees');
            $table->text('discussion_points');
            $table->text('action_items')->nullable();
            $table->text('follow_up_notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stakeholder_communications');
    }
};
