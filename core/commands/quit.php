<?php

$user->setOnline(false);

if (count($arguments) != 1) {
    unset($arguments[0]);
    $goodbye_message = implode(' ', $arguments);
    if (strlen($goodbye_message) > 128) unset($goodbye_message);
}

if (isset($goodbye_message)) $goodbye_message = " Wiadomość pożegnalna: " . $goodbye_message;
else $goodbye_message = '';

sendMessage("🚪 Wylogowałeś się z czatu." . $goodbye_message);
sendMessage("🚪 [b]{$user->nick}[/b] wylogował się." . $goodbye_message, getOnlineUsers(new Room($user->room)),
    "PUSH", "NORMAL", true);

$user->setRoom(null);
die;
