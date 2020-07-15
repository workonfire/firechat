<?php

if ($user->online == 1)
    finalMessage("Jesteś już zalogowany!", null, "PULL", "WARN");
else {
    $room = new Room(count($arguments) == 1 ? "main" : $arguments[1]);
    if (!$room->exists) finalMessage("Ten pokój nie istnieje.", null, "PULL", "WARN");
    if (!empty(getOnlineUsers($room))) sendMessage("🚪 [b]{$user->nick}[/b] zalogował się.", getOnlineUsers($room),
        "PUSH", "NORMAL", true);
    $user->setRoom($room);
    $user->setOnline();
    $topic = $room->topic == '' ? "brak" : $room->topic;
    sendMessage("🚪 Dołączyłeś do pokoju #{$user->room}.\n\nTemat: {$topic}\n\nObecni użytkownicy:");
    foreach (getOnlineUsers($room) as $gg_number) {
        $online_user = new User($gg_number);
        echo "| {$online_user->group->prefix}{$online_user->nick}\n";
    }
    die;
}
