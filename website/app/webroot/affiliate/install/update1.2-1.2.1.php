<?php
//CONNECT TO DATABASE
include_once('../auth/db-connect.php');
/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

/* check if server is alive */
if ($mysqli->ping()) {
    printf ("Our connection is ok!<br>");
} else {
    printf ("Error: %s\n", $mysqli->error);
}

//ALTER TABLE
$sql = "ALTER TABLE  `".$prefix."rl_settings` ADD  `gc_secret` VARCHAR( 255 ) NOT NULL AFTER  `gc_key` ;";

if ($mysqli->query($sql) === TRUE) {
    echo "RL Settings table modified successfully<br>";
} else {
    echo "Error modifiying table: " . $mysqli->error;
}

$mysqli->close();
echo 'Update Complete... <a href="../index">Click here to go to your site.';