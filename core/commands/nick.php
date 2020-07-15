<?php
requirePermission($user, "command.nick");

if (count($arguments) == 1) $warn_message = "Podaj nick.";
else {
    $new_nick = preg_replace("/[^A-Za-z0-9 ]/", '', $arguments[1]);
    if (strlen($new_nick) > 16) $warn_message = "Nick nie może mieć więcej niż 16 znaków.";
    elseif (userExists($new_nick)) $warn_message = "Ktoś już ma taki nick.";
}

if (isset($warn_message)) finalMessage($warn_message, null, "PULL", "WARN");

sendMessage("{$user->nick} zmienił swój nick na {$new_nick}.", getOnlineUsers(new Room($user->room)), "PUSH", "INFO");
$user->setNick($new_nick);
die;