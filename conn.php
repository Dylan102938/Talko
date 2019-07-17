<?php

    //mysql login info
    $host = "localhost";
    $user = "root";
    $pass = "51886688";
    $dbname = "talko";
    
    $conn = mysqli_connect($host, $user, $pass, $dbname);
    if ($conn -> error) {
        die ("Connect failed: ".$conn -> $error);
    }