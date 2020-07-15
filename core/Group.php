<?php

class Group {
    public string $name;
    public array $permissions;
    public string $prefix;

    function __construct($group_name) {
        extract($GLOBALS);
        $group_record = $db->query("SELECT * FROM `groups` WHERE `name` = '{$group_name}'");
        if ($group_record->num_rows != 0) {
            $group_record = $group_record->fetch_assoc();
            $this->name = $group_record['name'];
            $this->permissions = explode(',', $group_record['permissions']);
            $this->prefix = $group_record['prefix'];
        }
    }

    /**
     * Sprawdza, czy dana permisja istnieje w grupie.
     *
     * @param string $permission nazwa uprawnienia
     * @return bool true, jeśli istnieje
     */
    function isPermission($permission) {
        if ($this->name != null) return in_array($permission, $this->permissions);
        else return false;
    }

    /**
     * Dodaje uprawnienie do obecnej grupy.
     *
     * @param string $permission nazwa uprawnienia
     */
    function addPermission($permission) {
        if (!$this->isPermission($permission)) {
            array_push($this->permissions, $permission);
            $queued_permission_list = implode(',', $this->permissions);
            extract($GLOBALS);
            $db->query("UPDATE `groups` SET `permissions` = '{$queued_permission_list}' WHERE `name` = '{$this->name}'");
        }
        else debugMessage("Uprawnienie {$permission} istnieje już w grupie {$this->name}, aczkolwiek próbowano je ponownie przypisać.");
    }

    /**
     * Usuwa uprawnienie z obecnej grupy.
     *
     * @param string $permission nazwa uprawnienia
     */
    function removePermission($permission) {
        if ($this->isPermission($permission)) {
            if (($key = array_search($permission, $this->permissions)) != false)
                unset($this->permissions[$key]);
            $queued_permission_list = implode(',', $this->permissions);
            extract($GLOBALS);
            $db->query("UPDATE `groups` SET `permissions` = '{$queued_permission_list}' WHERE `name` = '{$this->name}'");
        }
        else debugMessage("Uprawnienie {$permission} nie istnieje w grupie {$this->name}, aczkolwiek próbowano je usunąć.");
    }
}