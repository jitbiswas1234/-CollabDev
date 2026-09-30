<?php
/**
 * logout.php – End the user's session and redirect to login.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

logout(); // destroys session and redirects to login.php
