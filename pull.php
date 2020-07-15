<?php

/**
 * firechat
 *
 * @author  workonfire aka Buty935
 * @version 2.0.0-alpha
 */

define('VERSION', '2.0.0-alpha');

require "core/util.php";
require_once "api/PushConnection.php";
require_once "api/MessageBuilder.php";
require_once "core/User.php";
require_once "core/Room.php";
require_once "core/Group.php";

$config = require "config.php";
$PushConnection = new PushConnection($bot_number, $config['BotAPI']['login'], $config['BotAPI']['password']);

$db = new mysqli($config['database']['host'],
    $config['database']['credentials']['username'],
    $config['database']['credentials']['password'],
    $config['database']['database_name']
);

$user = new User($gg_number);

if (!$user->exists) {
    if ($PushConnection->isBot($gg_number))
        finalMessage("⛔ Rejestracja zakończyła się niepowodzeniem.", null, "PULL", "ERROR");
    registerNewUser($gg_number);
    sendMessage("Użytkownik {$gg_number} zarejestrował się na czacie.", getOnlineUsers(new Room('main')), "PUSH");
    finalMessage("Rejestracja przebiegła pomyślnie. Użyj komendy /join, by się zalogować.", null, "PULL", "INFO");
}

if ($user->online == 0) {
    $command = $command == "j" ? "join" : $command;
    if ($command != "join") finalMessage("Nie jesteś zalogowany. Użyj komendy /join, by się zalogować.",
        null, "PULL", "WARN");
}
if ($message[0] != '/' AND $message[0] != '.' AND $user->online == 1)
    sendMessage("{$user->group->prefix}{$user->nick}: {$message}", getOnlineUsers(new Room($user->room)), "PUSH");
else {
    $command_shortcuts = include "core/command_shortcuts.php";
    if (in_array($command, $command_shortcuts)) $command = array_search($command, $command_shortcuts);
    $command_path = "core/commands/{$command}.php";
    if (file_exists($command_path)) include $command_path;
    else finalMessage("Komenda /{$command} nie istnieje.", null, "PULL", "WARN");
}