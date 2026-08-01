<?php
session_start();
include __DIR__ . "/db.php";
require __DIR__ . "/tmdb.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$movie_id = $_GET['tmdb_id'] ?? null;

if(!$movie_id){
    die("Movie not found");
}

/* =========================
   TMDB DATA
========================= */
$movie = tmdbRequest("/movie/$movie_id");
$credits = tmdbRequest("/movie/$movie_id/credits");
$videos = tmdbRequest("/movie/$movie_id/videos");

/* DIRECTOR */
$director = "Unknown";
if(isset($credits['crew'])){
    foreach($credits['crew'] as $c){
        if($c['job'] == "Director"){
            $director = $c['name'];
            break;
        }
    }
}

/* TRAILER */
$trailer = null;
if(isset($videos['results'])){
    foreach($videos['results'] as $v){
        if($v['type'] == "Trailer" && $v['site'] == "YouTube"){
            $trailer = $v['key'];
            break;
        }
    }
}

/* =========================
   ADD TO WATCHLIST (SAFE)
========================= */
if(isset($_POST['add_watchlist'])){

    mysqli_query($conn, "
        INSERT INTO watchlist (UserID, tmdb_id)
        VALUES ($user_id, $movie_id)
        ON DUPLICATE KEY UPDATE tmdb_id = tmdb_id
    ");
}

/* =========================
   SAVE RATING + REVIEW (FIXED)
========================= */
if(isset($_POST['submit_review'])){

    $rating = (int)($_POST['rating'] ?? 0);
    $review = trim($_POST['review'] ?? '');

    if($rating < 1) $rating = 1;
    if($rating > 10) $rating = 10;

    /* CHECK EXISTING RATING */
    $check = mysqli_query($conn, "
        SELECT RatingID
        FROM Ratings
        WHERE UserID = $user_id
        AND tmdb_id = $movie_id
        LIMIT 1
    ");

    if(mysqli_num_rows($check) > 0){

        mysqli_query($conn, "
            UPDATE Ratings
            SET Rating = $rating
            WHERE UserID = $user_id
            AND tmdb_id = $movie_id
        ");

    } else {

        mysqli_query($conn, "
            INSERT INTO Ratings (UserID, tmdb_id, Rating)
            VALUES ($user_id, $movie_id, $rating)
        ");
    }

    /* =========================
       REVIEW (FIXED - NO DUPLICATES)
    ========================= */

    $review_safe = mysqli_real_escape_string($conn, $review);

    $check_review = mysqli_query($conn, "
        SELECT review_id
        FROM reviews
        WHERE UserID = $user_id
        AND tmdb_id = $movie_id
        LIMIT 1
    ");

    if(mysqli_num_rows($check_review) > 0){

        mysqli_query($conn, "
            UPDATE reviews
            SET review_text = '$review_safe'
            WHERE UserID = $user_id
            AND tmdb_id = $movie_id
        ");

    } else {

        mysqli_query($conn, "
            INSERT INTO reviews (review_text, UserID, tmdb_id)
            VALUES ('$review_safe', $user_id, $movie_id)
        ");
    }
}

/* =========================
   GET REVIEWS
========================= */
$reviews = mysqli_query($conn, "
    SELECT * FROM reviews
    WHERE tmdb_id = $movie_id
    ORDER BY review_id DESC
");

/* =========================
   GET USER RATING
========================= */
$user_rating = null;

$res = mysqli_query($conn, "
    SELECT Rating
    FROM Ratings
    WHERE UserID = $user_id
    AND tmdb_id = $movie_id
    LIMIT 1
");

if($res && mysqli_num_rows($res) > 0){
    $row = mysqli_fetch_assoc($res);
    $user_rating = $row['Rating'];
}
?>

<!DOCTYPE html>
<html>
<head>

<title><?= htmlspecialchars($movie['title']) ?></title>

<style>

body{
    margin:0;
    font-family:Arial;
    background:#111;
    color:white;
}

.bg{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background-image:url("https://image.tmdb.org/t/p/original<?= $movie['backdrop_path'] ?>");
    background-size:cover;
    background-position:center;
    filter:blur(15px) brightness(0.4);
    transform:scale(1.1);
    z-index:0;
}

.content{
    position:relative;
    z-index:2;
    display:flex;
    gap:30px;
    padding:40px;
}

.poster img{
    width:280px;
    border-radius:15px;
}

.info{
    max-width:650px;
}

h1{
    color:#ff4fa3;
}

.tag{
    display:inline-block;
    background:rgba(255,255,255,0.2);
    padding:6px 10px;
    border-radius:12px;
    margin:4px;
}

.back{
    display:inline-block;
    margin-bottom:15px;
    padding:8px 14px;
    background:#ff4fa3;
    color:white;
    text-decoration:none;
    border-radius:10px;
}

.review-box{
    margin-top:20px;
    background:rgba(255,255,255,0.08);
    padding:15px;
    border-radius:12px;
}

input, textarea{
    width:100%;
    padding:8px;
    margin-top:8px;
    border-radius:8px;
    border:none;
}

button{
    margin-top:10px;
    padding:10px;
    background:#ff4fa3;
    color:white;
    border:none;
    border-radius:10px;
    cursor:pointer;
}

.single-review{
    background:white;
    color:black;
    padding:10px;
    border-radius:10px;
    margin-top:10px;
}

</style>

</head>

<body>

<div class="bg"></div>

<div class="content">

<div class="poster">
    <img src="https://image.tmdb.org/t/p/w500<?= $movie['poster_path'] ?>">
</div>

<div class="info">

<a href="index.php" class="back">⬅ Back</a>

<h1><?= htmlspecialchars($movie['title']) ?></h1>

<p><?= htmlspecialchars($movie['overview']) ?></p>

<p>
<span class="tag">⭐ TMDB: <?= $movie['vote_average'] ?></span>
<span class="tag">📅 <?= $movie['release_date'] ?></span>
<span class="tag">🎬 <?= $director ?></span>
<span class="tag">⏱ <?= $movie['runtime'] ?> min</span>

<?php if($user_rating !== null){ ?>
    <span class="tag">⭐ Your Rating: <?= $user_rating ?>/10</span>
<?php } else { ?>
    <span class="tag">⭐ Not Rated Yet</span>
<?php } ?>

</p>

<form method="POST">
    <button type="submit" name="add_watchlist">➕ Add to Watchlist</button>
</form>

<?php if($trailer){ ?>
<iframe width="100%" height="320"
src="https://www.youtube.com/embed/<?= $trailer ?>"
frameborder="0" allowfullscreen></iframe>
<?php } ?>

<div class="review-box">

<h3>⭐ Rate & Review</h3>

<form method="POST">

<input type="number" name="rating" min="1" max="10" required>

<textarea name="review" placeholder="Write your review..." required></textarea>

<button type="submit" name="submit_review">Submit</button>

</form>

</div>

<div class="review-box">

<h3>💬 Reviews</h3>

<?php while($r = mysqli_fetch_assoc($reviews)){ ?>

<div class="single-review">
<?= htmlspecialchars($r['review_text']) ?>
</div>

<?php } ?>

</div>

</div>

</div>

</body>
</html>