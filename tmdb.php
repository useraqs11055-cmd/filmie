<?php

function tmdbRequest($endpoint){

    $token = "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJlNTczMTc2MGE5MWI2ZmY0M2UxZGRjMmRhMmQ3Mzk2ZCIsIm5iZiI6MTc3OTYxNjEwNC4zMTksInN1YiI6IjZhMTJjOTY4MjE4ZGNhNzA0YzZmMDJmNSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.PHbJJHOq95odxXEjoRmmpZofe-9W8QCBq5IdSOFcHwc";

    $url = "https://api.themoviedb.org/3" . $endpoint;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if($httpCode != 200){
        return null;
    }

    return json_decode($response, true);
}

?>