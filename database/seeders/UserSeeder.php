<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use App\Services\TransferService;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::pluck('id', 'name');

        // --- Personel banku (bez rachunków) ---
        $this->createUser($roles[Role::ADMIN], 'Adam', 'Administrator', 'administrator@bank.test', '9001011238');
        $this->createUser($roles[Role::MANAGER], 'Krystyna', 'Kierownik', 'kierownik@bank.test', '8502022341');
        $this->createUser($roles[Role::EMPLOYEE], 'Paweł', 'Pracownik', 'pracownik@bank.test', '8803033450');

        // --- Klienci wraz z rachunkami i saldem początkowym ---
        $jan = $this->createClient($roles[Role::CLIENT], 'Jan', 'Kowalski', 'jan.kowalski@bank.test', '9004044564', 5000);
        $anna = $this->createClient($roles[Role::CLIENT], 'Anna', 'Nowak', 'anna.nowak@bank.test', '9205055675', 20000);
        $piotr = $this->createClient($roles[Role::CLIENT], 'Piotr', 'Wiśniewski', 'piotr.wisniewski@bank.test', '8506066787', 1500);

        // --- Przykładowe przelewy realizowane przez serwis (spójne salda + historia) ---
        $service = app(TransferService::class);

        $service->transfer($jan->fresh('account'), $anna->account->number, '250.00', 'Zwrot za obiad');
        $service->transfer($anna->fresh('account'), $jan->account->number, '1000.00', 'Pożyczka');
        $service->transfer($piotr->fresh('account'), $jan->account->number, '100.00', 'Prezent urodzinowy');

        // Przelew powyżej progu — trafia do kolejki akceptacji kierownika (status: oczekujący).
        $service->transfer($anna->fresh('account'), $piotr->account->number, '11000.00', 'Zakup samochodu');
    }

    private function createUser(int $roleId, string $firstName, string $lastName, string $email, string $peselPrefix): User
    {
        return User::create([
            'role_id' => $roleId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'pesel' => $this->pesel($peselPrefix),
            'password' => 'password',
        ]);
    }

    private function createClient(int $roleId, string $firstName, string $lastName, string $email, string $peselPrefix, float $balance): User
    {
        $user = $this->createUser($roleId, $firstName, $lastName, $email, $peselPrefix);

        $user->account()->create([
            'number' => Account::generateUniqueNumber(),
            'balance' => $balance,
            'currency' => config('bank.currency', 'PLN'),
        ]);

        return $user->load('account');
    }

    /**
     * Dokłada poprawną cyfrę kontrolną do 10-cyfrowego prefiksu, tworząc prawidłowy PESEL.
     */
    private function pesel(string $tenDigits): string
    {
        $weights = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3];
        $sum = 0;

        for ($i = 0; $i < 10; $i++) {
            $sum += ((int) $tenDigits[$i]) * $weights[$i];
        }

        return $tenDigits.((10 - ($sum % 10)) % 10);
    }
}
