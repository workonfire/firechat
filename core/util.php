<?php

$message = file_get_contents("php://input");
$command = explode(' ', strtolower(str_replace(array('/', '.'), '', trim($message))))[0];
$arguments = explode(' ', $message);
$bot_number = $_GET['to'];
$gg_number = $_GET['from'];
$command_shortcuts = [];

/**
 * Wyświetla wiadomość deweloperską, jeśli włączony jest tryb debugowania.
 *
 * @param string $message treść wiadomości
 */
function debugMessage($message) {
    $config = include "config.php";
    if ($config['debug']) echo "[DEBUG] " . $message . "\n\n";
}

/**
 * Wysyła wiadomość do użytkownika.
 *
 * @param string $message wiadomość
 * @param array $recipients tablica z odbiorcami
 * @param string $mode tryb wysyłania wiadomości; dostępne tryby:
 *                     PULL - zwykła wiadomość bez autoryzacji, nie obsługuje niestandardowych odbiorców
 *                     PUSH - wiadomość wysyłana za pośrednictwem serwera BotMastera, wymaga autoryzacji, obsługuje BBCode
 *                     i niestandardową listę odbiorców
 * @param string $level poziom wiadomości, parametr opcjonalny; dostępne poziomy:
 *                      INFO - zwykła wiadomość informacyjna z przedrostkiem "ℹ"
 *                      WARN - wiadomość ostrzegająca użytkownika z przedrostkiem "⚠"
 *                      ERROR - wiadomość informująca użytkownika o błędzie z przedrostkiem "⛔"
 * @param bool $bbcode obsługa znaczników BBCode w wiadomości, domyślnie wyłączona
 */
function sendMessage($message, $recipients = null, $mode = "PULL", $level = '', $bbcode = false) {
    switch ($level) {
        case "INFO": $prefix = 'ℹ'; break;
        case "WARN": $prefix = '⚠'; break;
        case "ERROR": $prefix = '⛔'; break;
        default: $prefix = ''; break;
    }

    if ($prefix != '') $prefix .= ' ';
    $message = $prefix . $message . "\n";

    if ($mode == "PULL") {
        if ($recipients != null)
            debugMessage("Odbiorcy wiadomości zostali podani, pomimo tego, że tryb wysyłania wiadomości \"PULL\" nie obsługuje systemu odbiorców.");
        echo $message;
    }
    elseif ($mode == "PUSH") {
        $MessageBuilder = new MessageBuilder();
        $bbcode ? $MessageBuilder->addBBcode($message) : $MessageBuilder->addText($message);
        $MessageBuilder->setRecipients($recipients);
        extract($GLOBALS);
        $PushConnection->push($MessageBuilder);
        $MessageBuilder->clear();
    }
}

/**
 * Kończy wykonywanie kodu po wykonaniu funkcji nadrzędnej.
 *
 * @see sendMessage()
 */
function finalMessage($message, $recipients = null, $mode = "PULL", $level = '', $bbcode = false) {
    sendMessage($message, $recipients, $mode, $level, $bbcode);
    die;
}

/**
 * Zwraca listę zalogowanych użytkowników z wybranego pokoju (domyślnie #main).
 *
 * @param Room $room pokój
 * @return array lista numerów GG użytkowników
 */
function getOnlineUsers($room) {
    extract($GLOBALS);
    $query = $db->query("SELECT * FROM `users` WHERE `online` = 1 AND `room` = '{$room->name}'");
    $users = [];
    while ($online_users = $query->fetch_assoc()) $users[] = $online_users['gg'];
    return $users;
}

/**
 * Rejestruje nowego użytkownika.
 *
 * @param int $gg_number numer GG
 * @param string $nick nazwa użytkownika
 * @param int $online czy użytkownik ma być zalogowany
 * @param string $global_group główna grupa użytkownika
 * @param string $room pokój, w którym ma znaleźć się użytkownik
 */
function registerNewUser($gg_number, $nick = '', $online = 0, $global_group = 'default', $room = '') {
    if ($nick == '') $nick = "USER_" . rand(10000, 99999);
    extract($GLOBALS);
    $db->query("INSERT INTO `users` (`gg`, `nick`, `online`, `room`) VALUES ({$gg_number}, '{$nick}', {$online}, '{$room}')");
    $db->query("INSERT INTO `room_groups` (`gg`, `room`, `user_group`) VALUES ({$gg_number}, 'main', '{$global_group}')");
}

/**
 * Wymaga posiadania uprawnienia od użytkownika.
 * Jeśli użytkownik takowego uprawnienia nie będzie posiadał, zostanie mu wyświetlony błąd
 * i reszta kodu nie wykona się.
 *
 * @param User $user reprezentacja użytkownika
 * @param string $permission nazwa uprawnienia
 */
function requirePermission($user, $permission) {
    if (!$user->hasPermission($permission)) {
        sendMessage("Nie posiadasz wystarczających uprawnień do wykonania tej komendy.", null, "PULL", "ERROR");
        debugMessage("Brakujące pozwolenie: {$permission}");
        die;
    }
}

/**
 * Sprawdza po nicku, czy dany użytkownik istnieje w bazie danych.
 *
 * @param string $nick nazwa użytkownika
 * @return bool true, jeśli istnieje
 */
function userExists($nick) {
    extract($GLOBALS);
    return $db->query("SELECT * FROM `users` WHERE `nick` LIKE '{$nick}'")->num_rows >= 1;
}