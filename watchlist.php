<?php
session_start();
include __DIR__ . "/db.php";
require __DIR__ . "/tmdb.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn, "
    SELECT tmdb_id 
    FROM watchlist 
    WHERE UserID = $user_id
");
?>

<!DOCTYPE html>
<html>
<head>
<title>My Watchlist</title>

<style>

/* PASTEL BACKGROUND */
body{
    margin:0;
    font-family:Arial;
    background:linear-gradient(135deg,#ffe0f0,#ffd6ea,#fff0f7);
    color:#111;
}

/* CONTAINER */
.container{
    padding:30px;
    max-width:1000px;
    margin:auto;
}

/* BACK BUTTON */
.back{
    display:inline-block;
    margin-bottom:20px;
    padding:10px 15px;
    background:#ff4fa3;
    color:white;
    text-decoration:none;
    border-radius:12px;
    transition:0.3s;
    font-weight:bold;
}

.back:hover{
    transform:scale(1.05);
    box-shadow:0 10px 25px rgba(255,79,163,0.3);
}

/* TITLE */
h1{
    color:#ff4fa3;
    margin-bottom:25px;
}

/* CARD (NETFLIX STYLE) */
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
    box-shadow:0 15px 35px rgba(255,79,163,0.25);
}

/* POSTER */
img{
    width:90px;
    border-radius:12px;
    object-fit:cover;
}

/* TEXT */
h3{
    margin:0;
    color:#ffd6ea;
}

p{
    margin:5px 0 0 0;
    color:#aaa;
}

/* EMPTY STATE (optional safety) */
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

<h1>📌 My Watchlist</h1>

<?php if(mysqli_num_rows($result) == 0){ ?>
    <div class="empty">No movies in your watchlist yet 🎬</div>
<?php } ?>

<?php while($row = mysqli_fetch_assoc($result)){

    $movie = tmdbRequest("/movie/" . $row['tmdb_id']);

    if(!$movie || !isset($movie['poster_path'])) continue;
?>

<div class="card">
    <img src="https://image.tmdb.org/t/p/w200<?= $movie['poster_path'] ?>">
    <div>
        <h3><?= htmlspecialchars($movie['title']) ?></h3>
        <p>⭐ <?= $movie['vote_average'] ?></p>
    </div>
</div>

<?php } ?>

</div>

</body>
</html>