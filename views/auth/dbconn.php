<?php
$host = "localhost";
$name = "root";
$pass = "2006";
$db = "project_db";

$conn = mysqli_connect($host,$name,$pass,$db);

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}


?>