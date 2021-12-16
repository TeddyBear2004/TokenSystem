<?php

function getCon(){
    $host = "";
    $username = "";
    $password = "";
    $database = "";

    $con = new mysqli($host, $username, $password, $database);

    if($con->connect_error){
        die("Connection failed: ". $con->connect_error);
    }
    return $con;
}
