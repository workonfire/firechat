<?php

class User {
    public bool $exists;
    public int $gg_number;
    public string $nick;
    public string $room;
    public int $online;
    public Group $group;

    function __construct($gg_number) {
        extract($GLOBALS);
        $user_record = $db->query("SELECT * FROM `users` WHERE `gg` = {$gg_number}");
        $this->exists = $user_record->num_rows == 1;
        $user = $user_record->fetch_assoc();
        $this->gg_number = $this->exists ? $user['gg'] : 0;
        $this->nick = $this->exists ? $user['nick'] : '';
        $this->room = $this->exists ? $user['room'] : '';
        $this->online = $this->exists ? $user['online'] : 0;
        $room_group_query = $db->query("SELECT * FROM `room_groups` WHERE `room` = '{$this->room}' AND `gg` = {$this->gg_number}");
        if ($room_group_query->num_rows != 0) $this->group = new Group($room_group_query->fetch_assoc()['user_group']);
        else $this->group = new Group('default');
    }

    /**
     * Ustawia wartość "online" użytkownika. Domyślnie 1.
     * 1 = zalogowany
     * 0 = niezalogowany
     *
     * @param int $online wartość
     */
    function setOnline($online = 1) {
        extract($GLOBALS);
        $db->query("UPDATE `users` SET `online` = {$online} WHERE `gg` = {$this->gg_number}");
        $this->online = $online;
    }

    /**
     * Loguje użytkownika do wybranego pokoju.
     *
     * @param Room $room pokój
     */
    function setRoom($room) {
        $room_name = $room == null ? '' : $room->name;
        extract($GLOBALS);
        $db->query("UPDATE `users` SET `room` = '{$room_name}' WHERE `gg` = {$this->gg_number}");
        $this->room = $room_name;
    }

    /**
     * Ustawia nick użytkownikowi.
     *
     * @param string $nick
     */
    function setNick($nick) {
        extract($GLOBALS);
        $db->query("UPDATE `users` SET `nick` = '{$nick}' WHERE `gg` = {$this->gg_number}");
        $this->nick = $nick;
    }

    /**
     * Sprawdza, czy użytkownik posiada wskazane uprawnienie.
     *
     * @param string $permission nazwa uprawnienia
     * @return bool true, jeśli autoryzacja przebiegła pomyślnie
     */
    function hasPermission($permission) {
        return $this->group->isPermission($permission);
    }

    /**
     * Usuwa użytkownika z bazy danych.
     */
    function unregister() {
        extract($GLOBALS);
        $db->query("DELETE FROM `users` WHERE `gg` = {$this->gg_number}");
        $db->query("DELETE FROM `room_groups` WHERE `gg` = {$this->gg_number}");
        $db->query("DELETE FROM `rooms` WHERE `owner` = {$this->gg_number}");
    }
}