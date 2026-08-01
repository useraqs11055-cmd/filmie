<?php
session_start();
include __DIR__ . "/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$user_type = $_SESSION['user_type'] ?? 'regular';

$r = mysqli_query($conn, "SELECT COUNT(*) as total FROM Ratings WHERE UserID=$user_id");
$rating_count = mysqli_fetch_assoc($r)['total'] ?? 0;

$w = mysqli_query($conn, "SELECT COUNT(*) as total FROM watchlist WHERE UserID=$user_id");
$watchlist_count = mysqli_fetch_assoc($w)['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>

<title>Profile</title>

<style>

/* PASTEL BACKGROUND */
body{
    margin:0;
    font-family:Arial;
    background:linear-gradient(135deg,#ffe0f0,#ffd6ea,#fff0f7);
}

/* NAV */
.nav{
    background:#ff4fa3;
    padding:15px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    color:white;
    flex-wrap:wrap;
}

/* NAV LINKS */
.nav a{
    color:white;
    text-decoration:none;
    margin-left:10px;
    padding:8px 12px;
    background:rgba(255,255,255,0.2);
    border-radius:12px;
    transition:0.3s;
}

.nav a:hover{
    background:white;
    color:#ff4fa3;
}

/* MAIN CARD */
.card{
    width:380px;
    margin:60px auto;
    background:#0f0f0f;
    padding:30px;
    border-radius:22px;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,0.25);
    color:white;
}

/* NAME */
h2{
    margin-bottom:10px;
    color:#ffd6ea;
}

/* BADGE */
.badge{
    display:inline-block;
    padding:6px 14px;
    border-radius:20px;
    background:#ff4fa3;
    color:white;
    font-weight:bold;
    margin-bottom:15px;
}

/* STATS */
.stat{
    background:rgba(255,255,255,0.08);
    padding:12px;
    margin-top:12px;
    border-radius:12px;
    color:#ddd;
    transition:0.3s;
}

.stat:hover{
    transform:scale(1.03);
    background:rgba(255,79,163,0.15);
}

/* SMALL LABEL */
.small{
    color:#aaa;
    font-size:13px;
    margin-top:5px;
}

</style>

</head>

<body>

<div class="nav">

<div>👤 Filmie Profile</div>

<div>
<a href="index.php">Home</a>
<a href="mood.php">Mood</a>
<a href="recommendation.php">Recommendations</a>
<a href="watchlist.php">Watchlist</a>
</div>

</div>

<div class="card">

<h2><?= htmlspecialchars($username) ?></h2>

<div class="badge"><?= ucfirst($user_type) ?> User</div>

<div class="stat">
🎬 Rated Movies: <?= $rating_count ?>
<div class="small">Movies you have reviewed</div>
</div>

<div class="stat">
📌 Watchlist: <?= $watchlist_count ?>
<div class="small">Saved for later</div>
</div>

</div>

</body>
</html>