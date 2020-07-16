# firechat
Wersja 2.0

Wersja "reborn" starego [firechatu](https://github.com/workonfire/firechat-old).

Póki co jest to sam silnik z **bardzo** okrojonymi funkcjami (choć z dużymi możliwościami), ale na razie jest to **tylko zarys projektu**, do którego mam ogromne plany! 😄

## Funkcje
(póki co lista ta  jest bardzo skromna)
- pokoje rozmów (np. `/join main`)
- system grup - można przypisać użytkownika do grupy, np. `admin`
- system uprawnień - można przypisać uprawnienia do konkretnych grup, np. `command.ban` dla `admin` itp.
- grupa użytkownika jest zależna od pokoju
- dostosowywalne skróty komend, np. można użyć `/ver` zamiast `/version`
- obsługa BBCode w wiadomościach
- opcjonalne wiadomości pożegnalne w `/quit`

## API
API w obecnym stanie nie jest zbyt funkcjonalne, ale obecnie można je wykorzystać w następujący sposób, przykładowo do tworzenia własnych komend.
Przykłady:

#### Pozyskiwanie użytkownika
```php
$user = new User(542177); // numer GG
```

#### Autoryzowanie użytkownika
```php
if ($user->hasPermission("command.wzium"))
    sendMessage("[b]Wzium![/b]", $recipients = $user->gg_number, $mode = "PUSH", $level = "NORMAL", $bbcode = true);
else die("O nie, nie nie! Nie masz uprawnień do wziumowania.");
```

#### Sprawdzanie tematu pokoju
```php
$room = new Room($user->room);
if (!strpos($room->topic, "wzium")) die("W tym miejscu nie możesz wziumować.");
```

#### Wyrzucanie użytkownika
```php
$user->setOnline(false);
$user->setRoom(null);
die("Zostałeś wyrzucony z pokoju.");
```

#### Rejestracja użytkownika i powiadamianie osób online
```php
registerNewUser($gg_number = 123456, $nick = "wzium");
sendMessage("Uwaga! Na czat wszedł wzium!", getOnlineUsers(new Room('main')), "PUSH");
```