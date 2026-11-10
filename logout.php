<?php
require_once __DIR__ . '/includes/auth.php';

// clear the session and go back to login
session_destroy();
session_start();
flash('success', 'You have been logged out.');
redirect('login.php');
