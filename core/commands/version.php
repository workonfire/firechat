<?php
requirePermission($user, "command.version");

finalMessage("[b]firechat[/b]\n[i]v".VERSION."[/i]\nby workonfire", $user->gg_number, "PUSH", "NORMAL", true);