<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientFundsException;
use App\Models\Account;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransferService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_transfer_below_threshold_moves_funds_and_records_transaction(): void
    {
        $sender = $this->makeClient(1000);
        $recipient = $this->makeClient(0);

        $transaction = app(TransferService::class)
            ->transfer($sender->fresh('account'), $recipient->account->number, '250.00', 'Test');

        $this->assertSame('750.00', $sender->account->fresh()->balance);
        $this->assertSame('250.00', $recipient->account->fresh()->balance);
        $this->assertSame(Transaction::STATUS_COMPLETED, $transaction->status);
        $this->assertDatabaseHas('transactions', [
            'reference' => $transaction->reference,
            'amount' => '250.00',
            'status' => Transaction::STATUS_COMPLETED,
        ]);
    }

    public function test_transfer_over_balance_is_rejected(): void
    {
        $sender = $this->makeClient(100);
        $recipient = $this->makeClient(0);

        $this->expectException(InsufficientFundsException::class);

        app(TransferService::class)
            ->transfer($sender->fresh('account'), $recipient->account->number, '500.00', null);
    }

    public function test_large_transfer_requires_approval_and_credits_on_approve(): void
    {
        $sender = $this->makeClient(20000);
        $recipient = $this->makeClient(0);
        $manager = User::factory()->role(Role::MANAGER)->create();

        $service = app(TransferService::class);
        $transaction = $service->transfer($sender->fresh('account'), $recipient->account->number, '15000.00', 'Duży przelew');

        // Środki nadawcy zablokowane, odbiorca jeszcze nie zasilony.
        $this->assertSame(Transaction::STATUS_PENDING, $transaction->status);
        $this->assertSame('5000.00', $sender->account->fresh()->balance);
        $this->assertSame('0.00', $recipient->account->fresh()->balance);

        $service->approve($transaction, $manager);

        $this->assertSame(Transaction::STATUS_COMPLETED, $transaction->fresh()->status);
        $this->assertSame('15000.00', $recipient->account->fresh()->balance);
    }

    public function test_rejected_transfer_refunds_the_sender(): void
    {
        $sender = $this->makeClient(20000);
        $recipient = $this->makeClient(0);
        $manager = User::factory()->role(Role::MANAGER)->create();

        $service = app(TransferService::class);
        $transaction = $service->transfer($sender->fresh('account'), $recipient->account->number, '12000.00', null);

        $service->reject($transaction, $manager);

        $this->assertSame(Transaction::STATUS_REJECTED, $transaction->fresh()->status);
        $this->assertSame('20000.00', $sender->account->fresh()->balance);
        $this->assertSame('0.00', $recipient->account->fresh()->balance);
    }

    private function makeClient(float $balance): User
    {
        $user = User::factory()->create();

        $user->account()->create([
            'number' => Account::generateUniqueNumber(),
            'balance' => $balance,
            'currency' => 'PLN',
        ]);

        return $user->load('account');
    }
}
