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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->string('contract_title');
            $table->string('contract_id')->unique();
            $table->date('start_date');
            $table->date('expiry_date');
            $table->enum('renewal_terms', ['auto-renew', 'manual'])->default('manual');
            $table->decimal('contract_value', 15, 2)->nullable();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('document_path')->nullable();
            $table->enum('status', ['active', 'expiring', 'expired', 'renewed'])->default('active');
            $table->string('contract_owner');
            $table->string('contract_owner_email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
