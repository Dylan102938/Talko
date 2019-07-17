<?php

    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "talko";

    $conn = mysqli_connect($host, $user, $pass, $dbname);
    if ($conn -> error) {
        die ("Connect failed: ".$conn -> $error);
    }