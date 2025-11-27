<?php
$hostname = "localhost";
$serverusername = "root";
$serverpassword = "";
$database = "peshkatari_db2";
$connect = mysqli_connect($hostname, $serverusername, $serverpassword, $database);
if (!$connect) {
    die("Connection was not successful");
}
