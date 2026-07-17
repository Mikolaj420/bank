# System bankowości internetowej

Projekt zaliczeniowy aplikacji bankowej napisanej w Laravel 10.  
Aplikacja umożliwia logowanie i rejestrację użytkowników, obsługę różnych ról, wykonywanie przelewów między klientami, podgląd salda i historii operacji oraz administrację użytkownikami.

## Najważniejsze funkcje

- logowanie i rejestracja użytkowników,
- 4 role: klient, pracownik, kierownik, administrator,
- różne dashboardy po zalogowaniu w zależności od roli,
- saldo konta i historia przelewów,
- wykonywanie przelewów między klientami,
- akceptacja większych przelewów przez kierownika,
- panel administracyjny do zarządzania użytkownikami,
- zgłoszenia supportowe,
- walidacja formularzy i testy aplikacji.

## Technologie

- PHP 8.x
- Laravel 10
- MariaDB / MySQL
- Blade
- HTML5, CSS3, czysty JavaScript
- architektura MVC

## Role w systemie

| Rola | Opis |
| --- | --- |
| Klient | Widzi swoje konto, saldo, historię przelewów i może wykonywać przelewy oraz dodawać zgłoszenia. |
| Pracownik | Obsługuje zgłoszenia oraz przegląda dane klientów i ich rachunki. |
| Kierownik | Zatwierdza lub odrzuca przelewy wymagające akceptacji oraz ma dostęp do raportów. |
| Administrator | Zarządza użytkownikami i rolami w panelu administracyjnym. |

Po zalogowaniu każdy użytkownik trafia pod adres `/dashboard`, ale widzi inny widok w zależności od swojej roli.

## Wymagania

- PHP 8.1 lub nowszy
- Composer 2.x
- MariaDB 10.4+ lub MySQL 8.0+

Aplikacja korzysta domyślnie z plikowych sesji i cache, więc poza bazą danych nie wymaga dodatkowych usług.

## Uruchomienie projektu

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Domyślny adres aplikacji: `http://127.0.0.1:8000`

## Konfiguracja bazy danych

Najpierw utwórz bazę, np.:

```sql
CREATE DATABASE internet_banking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Następnie uzupełnij dane w pliku `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=internet_banking
DB_USERNAME=root
DB_PASSWORD=twoje_haslo
```

Jeśli chcesz postawić bazę od zera jeszcze raz:

```bash
php artisan migrate:fresh --seed
```

## Dane testowe

Wszystkie konta mają hasło: `password`

| Rola | E-mail |
| --- | --- |
| Administrator | `administrator@bank.test` |
| Kierownik | `kierownik@bank.test` |
| Pracownik | `pracownik@bank.test` |
| Klient | `jan.kowalski@bank.test` |
| Klient | `anna.nowak@bank.test` |
| Klient | `piotr.wisniewski@bank.test` |

Seeder dodaje też przykładowe przelewy, w tym jeden oczekujący na akceptację kierownika.

## Założenia projektu

Projekt został przygotowany zgodnie z wymaganiami zadania:
- zastosowanie Laravel i wzorca MVC,
- minimum trzy poziomy uprawnień,
- różne widoki po zalogowaniu zależnie od roli,
- panel administratora z CRUD użytkowników,
- walidacja formularzy,
- logowanie i rejestracja użytkowników z hashowaniem haseł,
- funkcjonalności typowe dla aplikacji bankowej: saldo, przelewy, historia operacji.

Dodatkowo w projekcie znalazły się:
- własna reguła walidacji PESEL,
- paginacja,
- eksport historii przelewów do CSV,
- testy funkcjonalne i jednostkowe.

## Krótko o architekturze

Projekt korzysta z modeli Eloquent, kontrolerów, widoków Blade, Form Requestów i Policy.  
Logika przelewów została wydzielona do osobnego serwisu `TransferService`, żeby kontrolery były prostsze i czytelniejsze.

Autoryzacja jest sprawdzana na dwóch poziomach:
- przez middleware ról na trasach,
- przez Policy/Gate w kontrolerach.

## Testy

Uruchomienie testów:

```bash
php artisan test
```

Przykładowe testy obejmują:
- poprawne wykonanie przelewu,
- odrzucenie przelewu przy braku środków,
- akceptację i odrzucenie przelewu wymagającego zgody kierownika,
- kontrolę dostępu do zasobów zależnie od roli.

## Struktura projektu

Najważniejsze katalogi:
- `app/Models` – modele,
- `app/Http/Controllers` – kontrolery,
- `app/Http/Requests` – walidacja formularzy,
- `app/Policies` – autoryzacja,
- `app/Services` – logika biznesowa przelewów,
- `resources/views` – widoki Blade,
- `routes/web.php` – trasy aplikacji,
- `database/migrations` i `database/seeders` – struktura i dane startowe,
- `tests` – testy jednostkowe i funkcjonalne.

## Uwagi

Projekt nie używa Vite ani npm — frontend opiera się na Blade, własnym CSS i minimalnym JavaScript.  
Do repozytorium nie należy dodawać katalogów `vendor` i `node_modules`.