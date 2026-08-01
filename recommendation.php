<?php
session_start();
include __DIR__ . "/db.php";
require __DIR__ . "/tmdb.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* =========================
   GET USER LIKED MOVIES
========================= */
$liked = mysqli_query($conn, "
    SELECT tmdb_id
    FROM Ratings
    WHERE UserID = $user_id
    AND Rating >= 4
");

$genre_count = [];
$rated_movies = [];

while($r = mysqli_fetch_assoc($liked)){

    if(empty($r['tmdb_id'])) continue;

    $rated_movies[] = $r['tmdb_id'];

    $movie = tmdbRequest("/movie/" . $r['tmdb_id']);

    if(isset($movie['genres']) && is_array($movie['genres'])){

        foreach($movie['genres'] as $g){

            if(isset($g['id'])){

                $id = $g['id'];

                if(!isset($genre_count[$id])){
                    $genre_count[$id] = 0;
                }

                /* weight = stronger signal for liked movies */
                $genre_count[$id] += 1;
            }
        }
    }
}

/* =========================
   SORT GENRES BY PREFERENCE
========================= */
arsort($genre_count);

$genre_ids = array_keys($genre_count);
$genre_ids = array_slice($genre_ids, 0, 3); // top 3 genres



$movies = [];

/* =========================
   MAIN RECOMMENDATION (HYBRID FIX)
========================= */

if(count($genre_ids) > 0){

    $genre_string = implode(",", $genre_ids);

    /* 1. GENRE-BASED MOVIES */
    $genre_data = tmdbRequest("
        /discover/movie?with_genres=$genre_string
        &sort_by=popularity.desc
        &vote_count.gte=50
        &language=en-US
        &page=1
    ");

    $genre_movies = $genre_data['results'] ?? [];

    /* 2. SIMILAR MOVIES FROM BEST RATED ONE */
    $similar_movies = [];

    if(!empty($rated_movies)){

        $top_movie_id = $rated_movies[0]; // first good rated movie

        $similar_data = tmdbRequest("/movie/$top_movie_id/similar");

        $similar_movies = $similar_data['results'] ?? [];
    }

    /* 3. MERGE BOTH */
    $movies = array_merge($genre_movies, $similar_movies);
}

/* =========================
   FALLBACK (UNCHANGED)
========================= */
if(empty($movies)){

    $data = tmdbRequest("/movie/popular?language=en-US&page=1");
    $movies = $data['results'] ?? [];
}

/* =========================
   REMOVE RATED MOVIES (UNCHANGED)
========================= */
$filtered = [];

foreach($movies as $m){

    if(!in_array($m['id'], $rated_movies)){
        $filtered[] = $m;
    }
}

$movies = $filtered;

/* =========================
   ADD VARIATION (KEEP THIS)
========================= */
shuffle($movies); 
?>

<!DOCTYPE html>
<html>
<head>
<title>Recommendations</title>

<style>

body{
    margin:0;
    font-family:Arial;
    background:linear-gradient(135deg,#ffe0f0,#ffd6ea,#fff0f7);
    color:#111;
}

.container{
    padding:30px;
    max-width:1000px;
    margin:auto;
}

.back{
    display:inline-block;
    margin-bottom:20px;
    padding:10px 15px;
    background:#ff4fa3;
    color:white;
    text-decoration:none;
    border-radius:12px;
    font-weight:bold;
}

h1{
    color:#ff4fa3;
}

.card{
    display:flex;
    gap:15px;
    margin-bottom:20px;
    background:#0f0f0f;
    padding:15px;
    border-radius:18px;
    align-items:center;
    box-shadow:0 10px 25px rgba(0,0,0,0.3);
    transition:0.3s;
}

.card:hover{
    transform:scale(1.02);
}

img{
    width:90px;
    border-radius:12px;
}

h3{
    margin:0;
    color:#ffd6ea;
}

p{
    margin:5px 0 0 0;
    color:#aaa;
    font-size:13px;
}

.empty{
    color:#777;
    text-align:center;
    margin-top:40px;
}

</style>

</head>

<body>

<div class="container">

<a href="index.php" class="back">⬅ Back</a>

<h1>🔥 Recommended Movies</h1>

<?php if(count($movies) == 0){ ?>
    <div class="empty">Start rating movies to get better recommendations ⭐</div>
<?php } ?>

<?php foreach($movies as $m){ ?>

<div class="card">

    <img src="https://image.tmdb.org/t/p/w200<?= $m['poster_path'] ?? '' ?>">

    <div>
        <h3><?= htmlspecialchars($m['title'] ?? 'Unknown') ?></h3>

        <p>⭐ <?= $m['vote_average'] ?? 'N/A' ?></p>

        <p style="color:#ff4fa3; font-weight:bold;">
            ⭐ Because you liked similar movies
        </p>
    </div>

</div>

<?php } ?>

</div>

</body>
</html>