<?php

$db = @mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT);

require_once "common.php";

if (!$db) {
    exit_message("Failed to establish connection to the database.");
}
