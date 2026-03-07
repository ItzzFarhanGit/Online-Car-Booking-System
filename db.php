<?php
$connect = mysqli_connect("localhost", "root", "", "car_booking");

if(!$connect){
    die("Connection Failed: " . mysqli_connect_error());
}
// echo 'Connection Success';
?>
