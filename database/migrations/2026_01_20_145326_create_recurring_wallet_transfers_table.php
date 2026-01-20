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
        Schema::create('recurring_wallet_transfers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('source_id')->constrained('wallets');
            $table->foreignId('target_id')->constrained('wallets');

            $table->date('start_date');
            $table->date('end_date');
            $table->integer('frequency')->unsigned();
            $table->string('email');

            $table->integer('amount')->unsigned();
            $table->string('reason')->nullable();

            $table->timestamps();
        });

        Schema::table('wallet_transfers', function (Blueprint $table) {
            $table->foreignId('recurring_wallet_transfer_id')->constrained('recurring_wallet_transfers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_wallet_transfers');

        Schema::table('wallet_transfers', function (Blueprint $table) {
            $table->dropColumn(['recurring_wallet_transfer_id']);
        });
    }
};
