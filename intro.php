<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Welcome to Filmie</title>

<style>

body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    overflow:hidden;

    background:black;

    font-family:Arial;
}

/* GLOW BACKGROUND */
body::before,
body::after{
    content:"";
    position:absolute;
    width:350px;
    height:350px;
    border-radius:50%;
    filter:blur(100px);
}

body::before{
    background:#ff4fa3;
    left:-100px;
    top:-100px;
    opacity:0.4;
}

body::after{
    background:#ff99cc;
    right:-100px;
    bottom:-100px;
    opacity:0.3;
}

/* MAIN LOGO */
.logo{
    position:relative;
    z-index:2;

    font-size:90px;
    font-weight:bold;

    color:#ffd6ea;

    text-shadow:
    0 0 10px #ff4fa3,
    0 0 20px #ff4fa3,
    0 0 40px #ff4fa3;

    opacity:0;

    animation:
    fadeIn 2s forwards,
    glow 2s infinite alternate;
}

/* SUBTITLE */
.subtitle{
    position:absolute;
    margin-top:120px;

    color:#ffb6d2;
    font-size:18px;

    opacity:0;

    animation:fadeSub 2s forwards;
    animation-delay:1.5s;
}

/* FLOATING SPARKLES */
.sparkle{
    position:absolute;
    color:#ffb6d2;
    font-size:22px;

    animation:float 5s linear infinite;
}

/* ANIMATIONS */

@keyframes fadeIn{
    from{
        opacity:0;
        transform:scale(0.8);
    }

    to{
        opacity:1;
        transform:scale(1);
    }
}

@keyframes glow{
    from{
        text-shadow:
        0 0 10px #ff4fa3,
        0 0 20px #ff4fa3;
    }

    to{
        text-shadow:
        0 0 20px #ff4fa3,
        0 0 40px #ff4fa3,
        0 0 60px #ff4fa3;
    }
}

@keyframes fadeSub{
    to{
        opacity:1;
    }
}

@keyframes float{

    0%{
        transform:translateY(100vh) scale(0.5);
        opacity:0;
    }

    50%{
        opacity:1;
    }

    100%{
        transform:translateY(-120vh) scale(1.2);
        opacity:0;
    }
}

</style>

<!-- AUTO REDIRECT -->
<meta http-equiv="refresh" content="3;url=index.php">

</head>

<body>

<div class="logo">
Filmie
</div>

<div class="subtitle">
Your movie world ✨
</div>

<!-- SPARKLES -->
<div class="sparkle" style="left:10%; animation-delay:0s;">✨</div>
<div class="sparkle" style="left:25%; animation-delay:1s;">💖</div>
<div class="sparkle" style="left:40%; animation-delay:2s;">✨</div>
<div class="sparkle" style="left:60%; animation-delay:3s;">🎬</div>
<div class="sparkle" style="left:75%; animation-delay:1.5s;">✨</div>
<div class="sparkle" style="left:90%; animation-delay:2.5s;">🍿</div>

</body>
</html>