```php
<?php
session_start();
include __DIR__ . "/db.php";
require __DIR__ . "/tmdb.php";

if(!isset($_SESSION['user_id'])){
    header("Location: intro.php");
    exit;
}

/* SEARCH LOGIC */
$q = $_GET['q'] ?? null;

if($q){
    $data = tmdbRequest("/search/movie?query=" . urlencode($q));
} else {
    $page = rand(1, 5);
    $data = tmdbRequest("/movie/popular?page=$page");
}

$movies = $data['results'] ?? [];

/* =========================
   BECAUSE YOU LIKED
========================= */

$user_id = $_SESSION['user_id'];

$liked_movie = null;
$recommended_movies = [];

$liked_query = mysqli_query($conn,"
    SELECT tmdb_id
    FROM Ratings
    WHERE UserID='$user_id'
    AND Rating >= 4
    ORDER BY RatingID DESC
    LIMIT 1
");

if($liked_row = mysqli_fetch_assoc($liked_query)){

    $liked_movie = tmdbRequest("/movie/" . $liked_row['tmdb_id']);

    if($liked_movie && isset($liked_movie['genres'][0]['id'])){

        $genre_id = $liked_movie['genres'][0]['id'];

        $rec_data = tmdbRequest(
            "/discover/movie?with_genres=$genre_id&sort_by=popularity.desc"
        );

        $recommended_movies = $rec_data['results'] ?? [];
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Filmie Home</title>

<style>

/* =========================
   BACKGROUND
========================= */
body{
    margin:0;
    font-family:Arial;
    min-height:100vh;
    background:linear-gradient(135deg,#ffd6ea,#ffeaf6,#ffe0f0);
    position:relative;
    overflow-x:hidden;
}

/* FLOATING GLOW */
body::before,
body::after{
    content:"";
    position:fixed;
    width:300px;
    height:300px;
    border-radius:50%;
    filter:blur(70px);
    z-index:-1;
}

body::before{
    top:5%;
    left:0;
    background:rgba(255,79,163,0.25);
}

body::after{
    bottom:5%;
    right:0;
    background:rgba(255,182,193,0.25);
}

/* =========================
   FLOATING SPARKLES
========================= */

.sparkle{
    position:fixed;
    font-size:28px;
    opacity:0.16;
    z-index:-1;
    animation:float 6s ease-in-out infinite;
}

.s1{ top:18%; left:5%; }
.s2{ top:70%; left:10%; animation-delay:1s; }
.s3{ top:25%; right:7%; animation-delay:2s; }
.s4{ bottom:12%; right:14%; animation-delay:3s; }
.s5{ top:50%; right:3%; animation-delay:1.5s; }

@keyframes float{
    0%{ transform:translateY(0px); }
    50%{ transform:translateY(-18px); }
    100%{ transform:translateY(0px); }
}

/* =========================
   NAVBAR
========================= */
.nav{
    background:#050505;
    padding:16px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    color:white;
    flex-wrap:wrap;
    position:sticky;
    top:0;
    z-index:10;

    border-bottom:2px solid rgba(255,79,163,0.25);

    box-shadow:
    0 0 20px rgba(255,79,163,0.15),
    0 10px 30px rgba(0,0,0,0.45);
}

.logo{
    font-weight:bold;
    font-size:24px;
    color:#ffd6ea;

    text-shadow:
    0 0 10px rgba(255,79,163,0.8),
    0 0 20px rgba(255,79,163,0.4);
}

.nav a{
    color:#ffd6ea;
    text-decoration:none;
    margin-left:8px;
    padding:9px 15px;
    background:rgba(255,79,163,0.12);
    border:1px solid rgba(255,79,163,0.25);
    border-radius:999px;
    transition:0.25s;
    font-size:13px;
    display:inline-block;

    box-shadow:
    0 0 10px rgba(255,79,163,0.18);
}

.nav a:hover{
    background:#ff4fa3;
    color:white;

    transform:translateY(-2px) scale(1.06);

    box-shadow:
    0 0 15px rgba(255,79,163,0.8),
    0 0 30px rgba(255,79,163,0.35);
}

.nav form{
    display:flex;
    gap:8px;
    align-items:center;
}

.nav input{
    padding:10px 14px;
    border-radius:999px;
    border:1px solid rgba(255,79,163,0.4);
    outline:none;
    width:190px;

    background:#111;
    color:white;

    box-shadow:
    0 0 12px rgba(255,79,163,0.18);

    transition:0.3s;
}

.nav input::placeholder{
    color:#ffb6d2;
}

.nav input:focus{

    box-shadow:
    0 0 15px rgba(255,79,163,0.8),
    0 0 35px rgba(255,79,163,0.4);

    border-color:#ff4fa3;
}

.nav button{
    border:none;
    padding:10px 14px;
    border-radius:999px;

    background:#ff4fa3;
    color:white;

    font-weight:bold;
    cursor:pointer;

    transition:0.3s;

    box-shadow:
    0 0 15px rgba(255,79,163,0.6);
}

.nav button:hover{

    transform:scale(1.08);

    box-shadow:
    0 0 20px rgba(255,79,163,1),
    0 0 40px rgba(255,79,163,0.5);
}

.nav a[href="logout.php"]{
    background:#ff4fa3;
    color:white;

    box-shadow:
    0 0 15px rgba(255,79,163,0.7);
}

/* =========================
   HEADER
========================= */
.page-title{
    text-align:center;
    padding-top:25px;
    color:#ff4fa3;
    font-size:28px;
    font-weight:bold;
}

.subtitle{
    text-align:center;
    color:#777;
    margin-top:5px;
    margin-bottom:25px;
}

/* =========================
   MOVIE CAROUSEL
========================= */

.carousel-section{
    padding:10px 30px 45px;
}

.carousel-header{
    font-size:24px;
    font-weight:bold;
    color:#ff4fa3;
    margin-bottom:20px;

    text-shadow:
    0 0 10px rgba(255,79,163,0.5);
}

.carousel-wrapper{
    position:relative;
    display:flex;
    align-items:center;
}

.carousel-track{
    display:flex;
    gap:24px;
    overflow-x:auto;
    scroll-behavior:smooth;

    padding:25px 20px;
    width:100%;

    scrollbar-width:none;
}

.carousel-track::-webkit-scrollbar{
    display:none;
}

.carousel-card{
    position:relative;

    min-width:240px;
    height:360px;

    border-radius:24px;
    overflow:hidden;

    flex-shrink:0;

    text-decoration:none;

    transition:
    transform 0.45s ease,
    opacity 0.45s ease,
    filter 0.45s ease,
    box-shadow 0.45s ease;

    opacity:0.7;

    transform:scale(0.9);

    filter:
    brightness(0.55)
    saturate(0.7);

    box-shadow:
    0 10px 30px rgba(0,0,0,0.4);
}

.carousel-card img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.carousel-overlay{
    position:absolute;
    bottom:0;
    left:0;
    width:100%;

    padding:18px;

    background:
    linear-gradient(
        to top,
        rgba(0,0,0,0.96),
        rgba(0,0,0,0.15)
    );

    color:white;
}

.carousel-overlay h4{
    margin:0;
    color:#ffd6ea;
    font-size:17px;
}

.carousel-overlay p{
    margin-top:6px;
    color:#ff9bc8;
    font-weight:bold;
}

.carousel-track:hover .carousel-card{

    opacity:0.35;

    transform:scale(0.84);

    filter:
    brightness(0.45)
    saturate(0.5);
}

.carousel-track .carousel-card:hover{

    opacity:1;

    transform:
    scale(1.12)
    translateY(-12px);

    filter:
    brightness(1)
    saturate(1.15);

    z-index:10;

    box-shadow:
    0 0 25px rgba(255,79,163,0.75),
    0 0 70px rgba(255,79,163,0.4),
    0 30px 70px rgba(0,0,0,0.6);
}

.arrow{
    position:absolute;
    z-index:15;

    width:54px;
    height:54px;

    border:none;
    border-radius:50%;

    background:rgba(0,0,0,0.72);

    color:#ff4fa3;

    font-size:28px;
    font-weight:bold;

    cursor:pointer;

    backdrop-filter:blur(10px);

    transition:0.3s;

    box-shadow:
    0 0 18px rgba(255,79,163,0.35);
}

.arrow:hover{

    transform:scale(1.12);

    background:#ff4fa3;
    color:white;

    box-shadow:
    0 0 25px rgba(255,79,163,0.8),
    0 0 55px rgba(255,79,163,0.45);
}

.left-arrow{
    left:-8px;
}

.right-arrow{
    right:-8px;
}

/* =========================
   MOVIE GRID
========================= */
.grid{
    display:flex;
    flex-wrap:wrap;
    gap:22px;
    padding:30px;
    justify-content:center;
}

.card{
    width:185px;
    background:#0f0f0f;
    border-radius:18px;
    overflow:hidden;
    text-decoration:none;
    color:white;
    box-shadow:0 10px 25px rgba(0,0,0,0.25);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-10px) scale(1.03);
    box-shadow:0 18px 40px rgba(255,79,163,0.35);
}

.card img{
    width:100%;
    height:270px;
    object-fit:cover;
}

.card h3{
    font-size:14px;
    padding:12px;
    color:#ffd6ea;
}

/* =========================
   FOOTER
========================= */
.footer{
    margin-top:40px;
    padding:30px 20px;
    text-align:center;

    background:#050505;
    color:#ffb6d2;

    border-top:2px solid rgba(255,79,163,0.25);

    box-shadow:
    0 -5px 20px rgba(255,79,163,0.12);
}

.footer h3{
    margin:0;
    color:#ff4fa3;
    font-size:22px;

    text-shadow:
    0 0 10px rgba(255,79,163,0.5);
}

.footer p{
    margin-top:10px;
    font-size:13px;
    color:#ffcce0;
}

</style>

</head>

<body>

<div class="sparkle s1">✨</div>
<div class="sparkle s2">💖</div>
<div class="sparkle s3">🍿</div>
<div class="sparkle s4">🎬</div>
<div class="sparkle s5">✨</div>

<div class="nav">

<div class="logo">
🎬 Filmie
</div>

<form method="GET" action="index.php">
    <input type="text" name="q" placeholder="Search movies...">
    <button type="submit">🔍</button>
</form>

<div>
<a href="index.php">Home</a>
<a href="mood.php">Mood</a>
<a href="recommendation.php">Recommendations</a>
<a href="watchlist.php">Watchlist</a>
<a href="profile.php">Profile</a>
<a href="logout.php">Logout ⎋</a>
</div>

</div>

<div class="page-title">
🍿 Discover Movies
</div>

<div class="subtitle">
Search, rate, and build your Filmie taste ✨
</div>

<?php if($liked_movie && !empty($recommended_movies)){ ?>

<div class="carousel-section">

<div class="carousel-header">
💖 Because you liked <?= htmlspecialchars($liked_movie['title']) ?>
</div>

<div class="carousel-wrapper">

<button class="arrow left-arrow" onclick="scrollCarousel(-1)">
❮
</button>

<div class="carousel-track" id="movieCarousel">

<?php foreach(array_slice($recommended_movies,0,12) as $rm){

    if(empty($rm['poster_path'])) continue;
?>

<a class="carousel-card"
href="movie.php?tmdb_id=<?= $rm['id'] ?>">

<img src="https://image.tmdb.org/t/p/w500<?= $rm['poster_path'] ?>">

<div class="carousel-overlay">
    <h4><?= htmlspecialchars($rm['title']) ?></h4>

    <p>
    ⭐ <?= round($rm['vote_average'],1) ?>
    </p>
</div>

</a>

<?php } ?>

</div>

<button class="arrow right-arrow" onclick="scrollCarousel(1)">
❯
</button>

</div>

</div>

<?php } ?>

<div class="grid">

<?php foreach($movies as $m){

if(empty($m['poster_path'])) continue;
?>

<a class="card"
href="movie.php?tmdb_id=<?= $m['id'] ?>">

<img src="https://image.tmdb.org/t/p/w500<?= $m['poster_path'] ?>">

<h3><?= htmlspecialchars($m['title']) ?></h3>

</a>

<?php } ?>

</div>

<div class="footer">

<h3>🎬 Filmie</h3>

<p>
Your cute movie recommendation world ✨
</p>

<p>
Movie Recommendation System Project
</p>

</div>

<script>

function scrollCarousel(direction){

    const carousel =
    document.getElementById("movieCarousel");

    carousel.scrollBy({
        left: direction * 500,
        behavior: "smooth"
    });
}

</script>

</body>
</html>
```
