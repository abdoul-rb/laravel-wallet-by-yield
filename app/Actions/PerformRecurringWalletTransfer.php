<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\WalletTransactionType;
use App\Exceptions\InsufficientBalance;
use App\Models\RecurringWalletTransfer;
use App\Models\User;
use App\Models\WalletTransfer;
use Illuminate\Support\Facades\DB;

readonly class PerformRecurringWalletTransfer
{
    public function __construct(protected PerformWalletTransaction $performWalletTransaction, protected PerformWalletTransfer $performWalletTransfer) {}

    /**
     * @throws InsufficientBalance
     */
    public function execute(User $sender, User $recipient, array $data): RecurringWalletTransfer
    {
        return DB::transaction(function () use ($sender, $recipient, $data) {
            $recurringTransfer = RecurringWalletTransfer::create([
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'frequency' => $data['frequency'],
                'email' => $data['email'],
                'amount' => $data['amount'],
                'source_id' => $sender->wallet->id,
                'target_id' => $recipient->wallet->id,
            ]);

            $walletTransfer = $this->performWalletTransfer->execute($sender, $recipient, $data['amount'], $data['reason']);
            $walletTransfer->update([
                'recurring_wallet_transfer_id' => $recurringTransfer->id
            ]);

            $this->performWalletTransaction->execute(
                wallet: $sender->wallet,
                type: WalletTransactionType::DEBIT,
                amount: $data['amount'],
                reason: $data['reason'],
                transfer: $walletTransfer
            );

            $this->performWalletTransaction->execute(
                wallet: $recipient->wallet,
                type: WalletTransactionType::CREDIT,
                amount: $data['amount'],
                reason: $data['reason'],
                transfer: $walletTransfer
            );

            return $recurringTransfer;
        });
    }
}
