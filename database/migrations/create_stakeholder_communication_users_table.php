<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stakeholder_communication_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stakeholder_communication_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // Add foreign keys with custom shorter names
            $table->foreign('stakeholder_communication_id', 'comm_users_comm_id_foreign')
                ->references('id')
                ->on('stakeholder_communications')
                ->onDelete('cascade');

            $table->foreign('user_id', 'comm_users_user_id_foreign')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stakeholder_communication_users');
    }
};