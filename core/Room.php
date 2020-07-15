<?php

class Room {
    public bool $exists;
    public string $name;
    public User $owner; //TODO: to coś musi znaczyć
    public string $topic;

    function __construct($name) {
        extract($GLOBALS);
        $room_record = $db->query("SELECT * FROM `rooms` WHERE `name` = '{$name}'");
        $this->exists = $room_record->num_rows == 1;
        if ($this->exists) {
            $room = $room_record->fetch_assoc();
            $this->name = $room['name'];
            $this->owner = new User($room['owner']);
            $this->topic = $room['topic'];
        }
    }

    // TODO: Funkcje setOwner() i takie tam
}