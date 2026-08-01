<?php
session_start();
require __DIR__ . "/tmdb.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$moods = [
    "happy" => 35,
    "sad" => 18,
    "scary" => 27,
    "action" => 28,
    "romance" => 10749
];

$type = $_GET['type'] ?? 'happy';
$genre = $moods[$type] ?? 35;

$data = tmdbRequest("/discover/movie?with_genres=$genre");
$movies = $data['results'] ?? [];
?>

<!DOCTYPE html>
<html>
<head>

<title>Mood</title>

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

/* GLOW EFFECTS */
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

/* LOGO */
.logo{
    font-weight:bold;
    font-size:24px;
    color:#ffd6ea;

    text-shadow:
    0 0 10px rgba(255,79,163,0.8),
    0 0 20px rgba(255,79,163,0.4);
}

/* LINKS */
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

/* =========================
   BACK BUTTON
========================= */
.back{
    margin:20px;
}

.back a{
    background:#111;
    color:#ffd6ea;
    padding:12px 18px;
    border-radius:999px;
    text-decoration:none;
    font-weight:bold;

    border:1px solid rgba(255,79,163,0.3);

    box-shadow:
    0 0 15px rgba(255,79,163,0.18);

    transition:0.3s;
}

.back a:hover{
    background:#ff4fa3;
    color:white;

    box-shadow:
    0 0 20px rgba(255,79,163,0.8);
}

/* =========================
   PAGE TITLE
========================= */
.title{
    text-align:center;
    color:#ff4fa3;
    font-size:30px;
    font-weight:bold;
    margin-top:10px;
}

.subtitle{
    text-align:center;
    color:#777;
    margin-top:5px;
    margin-bottom:25px;
}

/* =========================
   MOOD BUTTONS
========================= */
.buttons{
    text-align:center;
    padding:10px 20px;
}

.buttons a{
    background:#111;
    color:#ffd6ea;
    padding:12px 18px;
    margin:8px;
    border-radius:999px;
    display:inline-block;
    text-decoration:none;
    font-weight:bold;

    border:1px solid rgba(255,79,163,0.3);

    transition:0.3s;

    box-shadow:
    0 0 15px rgba(255,79,163,0.15);
}

.buttons a:hover{
    background:#ff4fa3;
    color:white;

    transform:translateY(-3px) scale(1.05);

    box-shadow:
    0 0 20px rgba(255,79,163,0.9),
    0 0 40px rgba(255,79,163,0.35);
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

/* =========================
   MOVIE CARD
========================= */
.card{
    width:185px;
    background:#0f0f0f;
    border-radius:18px;
    overflow:hidden;
    text-decoration:none;
    color:white;

    transition:0.3s;

    box-shadow:
    0 10px 25px rgba(0,0,0,0.25);
}

.card:hover{
    transform:translateY(-10px) scale(1.03);

    box-shadow:
    0 18px 40px rgba(255,79,163,0.35);
}

.card img{
    width:100%;
    height:270px;
    object-fit:cover;
}

.card h3{
    padding:12px;
    font-size:14px;
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

<!-- NAVBAR -->
<div class="nav">

<div class="logo">
🎭 Mood
</div>

<div>
<a href="index.php">Home</a>
<a href="recommendation.php">Recommendations</a>
<a href="watchlist.php">Watchlist</a>
<a href="profile.php">Profile</a>
</div>

</div>

<!-- BACK -->
<div class="back">
    <a href="index.php">⬅ Back to Home</a>
</div>

<!-- TITLE -->
<div class="title">
✨ Pick Your Mood
</div>

<div class="subtitle">
Get movies based on your current vibe 🎬
</div>

<!-- MOOD BUTTONS -->
<div class="buttons">

<a href="mood.php?type=happy">😊 Happy</a>
<a href="mood.php?type=sad">😢 Sad</a>
<a href="mood.php?type=scary">👻 Scary</a>
<a href="mood.php?type=action">🔥 Action</a>
<a href="mood.php?type=romance">💖 Romance</a>

</div>

<!-- MOVIES -->
<div class="grid">

<?php foreach($movies as $m){

if(empty($m['poster_path'])) continue;
?>

<a class="card" href="movie.php?tmdb_id=<?= $m['id'] ?>">

<img src="https://image.tmdb.org/t/p/w500<?= $m['poster_path'] ?>">

<h3>
<?= htmlspecialchars($m['title']) ?>
</h3>

</a>

<?php } ?>

</div>

<!-- FOOTER -->
<div class="footer">

<h3>🎬 Filmie</h3>

<p>
Mood-based movie discovery ✨
</p>

<p>
Movie Recommendation System Project
</p>

</div>

</body>
</html>