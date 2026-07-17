{{-- Wspólne pola formularza użytkownika. Zmienne: $roles, opcjonalnie $user, $passwordRequired. --}}
@php($user = $user ?? null)
@php($passwordRequired = $passwordRequired ?? false)

<div class="form-group">
    <label for="role_id">Rola</label>
    <select id="role_id" name="role_id" class="{{ $errors->has('role_id') ? 'is-invalid' : '' }}" required>
        @foreach ($roles as $role)
            <option value="{{ $role->id }}" @selected(old('role_id', $user?->role_id) == $role->id)>{{ $role->label }}</option>
        @endforeach
    </select>
    @error('role_id') <div class="error-text">{{ $message }}</div> @enderror
</div>

<div class="form-row">
    <div class="form-group">
        <label for="first_name">Imię</label>
        <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user?->first_name) }}"
               class="{{ $errors->has('first_name') ? 'is-invalid' : '' }}" required>
        @error('first_name') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label for="last_name">Nazwisko</label>
        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user?->last_name) }}"
               class="{{ $errors->has('last_name') ? 'is-invalid' : '' }}" required>
        @error('last_name') <div class="error-text">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-group">
    <label for="email">Adres e-mail</label>
    <input type="email" id="email" name="email" value="{{ old('email', $user?->email) }}"
           class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required>
    @error('email') <div class="error-text">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="pesel">PESEL</label>
    <input type="text" id="pesel" name="pesel" value="{{ old('pesel', $user?->pesel) }}" inputmode="numeric" maxlength="11"
           class="{{ $errors->has('pesel') ? 'is-invalid' : '' }}" required>
    @error('pesel') <div class="error-text">{{ $message }}</div> @enderror
</div>

<div class="form-row">
    <div class="form-group">
        <label for="password">Hasło</label>
        <input type="password" id="password" name="password"
               class="{{ $errors->has('password') ? 'is-invalid' : '' }}" {{ $passwordRequired ? 'required' : '' }}>
        <div class="hint">{{ $passwordRequired ? 'Minimum 8 znaków.' : 'Pozostaw puste, aby nie zmieniać hasła.' }}</div>
        @error('password') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation">Powtórz hasło</label>
        <input type="password" id="password_confirmation" name="password_confirmation" {{ $passwordRequired ? 'required' : '' }}>
    </div>
</div>
