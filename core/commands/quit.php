<?php
requirePermission($user, "command.quit");

$user->setOnline(0);
sendMessage("🚪 Wylogowałeś się z czatu.");
sendMessage("🚪 [b]{$user->nick}[/b] wylogował się.", getOnlineUsers(new Room($user->room)), "PUSH", "NORMAL", true);
$user->setRoom(null);
die;
