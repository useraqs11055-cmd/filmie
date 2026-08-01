<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "movie_recommendation_db"
);

if(!$conn){
    die("Database connection failed");
}
?>