<?php

namespace Root\Html;

class Utils {
    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }
}