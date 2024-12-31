<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database_name = "fmq_cctv_ver2";

$db = mysqli_connect($hostname, $username, $password, $database_name);

if ($db->connect_error){
    echo "Error nih Brow";
    die("error");
}

?>