<?php
require __DIR__ . '/includes/config.php';
redirect(empty($_SESSION['uid']) ? 'login.php' : 'home.php');
