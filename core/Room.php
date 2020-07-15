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

    /**
     * Ustawia nazwę pokoju.
     *
     * @param string $name nazwa
     */
    function setName($name) {
        extract($GLOBALS);
        $db->query("UPDATE `rooms` SET `name` = '{$name}' WHERE `name` = {$this->name}");
        $this->name = $name;
    }

    /**
     * Ustawia właściciela pokoju.
     *
     * @param User $user użytkownik
     */
    function setOwner($user) {
        extract($GLOBALS);
        $db->query("UPDATE `rooms` SET `owner` = {$user->gg_number} WHERE `name` = {$this->name}");
        $this->owner = $user;
    }

    /**
     * Ustawia temat pokoju.
     *
     * @param string $topic temat
     */
    function setTopic($topic) {
        extract($GLOBALS);
        $db->query("UPDATE `rooms` SET `topic` = '{$topic}' WHERE `name` = {$this->name}");
        $this->topic = $topic;
    }
}