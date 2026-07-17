<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Account;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    /**
     * Lista użytkowników z wyszukiwarką (imię, nazwisko, e-mail, numer rachunku)
     * oraz paginacją.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $search = trim((string) $request->query('search', ''));

        $users = User::query()
            ->with(['role', 'account'])
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('account', fn (Builder $q) => $q->where('number', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('admin.users.create', ['roles' => Role::orderBy('id')->get()]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $user = User::create($data);

            // Klientowi automatycznie zakładamy rachunek.
            if ($user->isClient()) {
                $user->account()->create([
                    'number' => Account::generateUniqueNumber(),
                    'balance' => 0,
                    'currency' => config('bank.currency', 'PLN'),
                ]);
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'Użytkownik został utworzony.');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load(['role', 'account']);

        $transactions = $user->account
            ? Transaction::forAccount($user->account->id)->with(['fromAccount.user', 'toAccount.user'])->latest()->limit(10)->get()
            : collect();

        return view('admin.users.show', compact('user', 'transactions'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::orderBy('id')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        // Puste hasło = brak zmiany.
        if (empty($data['password'])) {
            unset($data['password']);
        }

        DB::transaction(function () use ($user, $data) {
            $user->update($data);

            // Jeśli użytkownik stał się klientem, a nie ma jeszcze rachunku — zakładamy go.
            if ($user->isClient() && ! $user->account) {
                $user->account()->create([
                    'number' => Account::generateUniqueNumber(),
                    'balance' => 0,
                    'currency' => config('bank.currency', 'PLN'),
                ]);
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'Dane użytkownika zostały zaktualizowane.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        // Nie pozwalamy usunąć własnego konta.
        if ($request->user()->id === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Nie możesz usunąć własnego konta.');
        }

        // Ochrona historii: konta z transakcjami nie usuwamy (integralność danych).
        if ($user->account && Transaction::forAccount($user->account->id)->exists()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Nie można usunąć użytkownika, którego rachunek ma historię transakcji.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Użytkownik został usunięty.');
    }
}
