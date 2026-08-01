<?php
session_start();
include __DIR__ . "/db.php";

$error = "";

if(isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit;
}

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND password=?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result && $result->num_rows > 0){

        $user = $result->fetch_assoc();

        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];

        header("Location: index.php");
        exit;

    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Filmie Login</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    height:100vh;
    overflow:hidden;
    background:#050505;
}

.bg-slider{
    position:fixed;
    inset:0;
    z-index:0;
}

.bg-slide{
    position:absolute;
    width:100%;
    height:100%;
    background-size:cover;
    background-position:center;
    filter:blur(20px) brightness(0.35) saturate(1.2);
    opacity:0;
    transition:opacity 1.5s ease-in-out, transform 1.5s ease;
    transform:scale(1.1);
}

.bg-slide.active{
    opacity:1;
    transform:scale(1.05);
}

.overlay{
    position:fixed;
    inset:0;
    background:radial-gradient(circle at center, rgba(255,79,163,0.18), rgba(0,0,0,0.9));
    z-index:1;
}

.glow-orb{
    position:fixed;
    width:250px;
    height:250px;
    border-radius:50%;
    background:radial-gradient(circle, rgba(255,79,163,0.35), transparent 70%);
    filter:blur(25px);
    animation:float 8s infinite ease-in-out;
    opacity:0.6;
    z-index:1;
}

.orb1{ top:10%; left:15%; }
.orb2{ bottom:10%; right:10%; animation-delay:2s; }
.orb3{ top:60%; left:70%; animation-delay:4s; }

@keyframes float{
    0%{ transform:translateY(0px) scale(1); }
    50%{ transform:translateY(-30px) scale(1.1); }
    100%{ transform:translateY(0px) scale(1); }
}

.container{
    display:flex;
    height:100vh;
    position:relative;
    z-index:2;
}

.left-panel{
    flex:1;
    display:flex;
    flex-direction:column;
    justify-content:center;
    padding:80px;
    color:white;
}

.left-panel h1{
    font-size:70px;
    letter-spacing:8px;
    color:#ff4fa3;
}

.tagline{
    font-size:26px;
    margin-top:10px;
    color:#ff4fa3;
}

.desc{
    margin-top:15px;
    color:#bbb;
    max-width:400px;
}

.mini-stats{
    margin-top:25px;
    display:flex;
    gap:12px;
}

.mini-stats div{
    padding:10px 14px;
    border-radius:12px;
    background:rgba(255,79,163,0.1);
    border:1px solid rgba(255,79,163,0.2);
    color:#ddd;
    font-size:13px;
}

.right-panel{
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
}

.box{
    width:360px;
    background:rgba(10,10,10,0.75);
    backdrop-filter:blur(18px);
    padding:45px;
    border-radius:28px;
    border:1px solid rgba(255,79,163,0.2);
    box-shadow:0 0 40px rgba(255,79,163,0.25);
    text-align:center;
    animation:fadeIn 1s ease;
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(20px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

.logo{
    font-size:42px;
    margin-bottom:10px;
}

h2{
    color:#ff4fa3;
}

.subtitle{
    color:#aaa;
    font-size:13px;
    margin-bottom:20px;
}

input{
    width:100%;
    padding:12px;
    margin-top:12px;
    border-radius:14px;
    border:none;
    outline:none;
    background:rgba(255,79,163,0.08);
    color:white;
}

input::placeholder{
    color:#bbb;
}

button{
    width:100%;
    padding:12px;
    margin-top:18px;
    border:none;
    border-radius:14px;
    background:linear-gradient(135deg,#ff4fa3,#ff2e7a);
    color:white;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:scale(1.05);
    box-shadow:0 0 25px rgba(255,79,163,0.5);
}

.error{
    background:rgba(255,79,163,0.15);
    color:#ff6fae;
    padding:10px;
    border-radius:10px;
    margin-bottom:10px;
    font-size:13px;
}

.register-text{
    margin-top:15px;
    color:#aaa;
    font-size:13px;
}

.posters img{
    position:absolute;
    width:120px;
    border-radius:12px;
    opacity:0.25;
    animation:float 6s infinite ease-in-out;
}

.posters img:nth-child(1){
    top:15%;
    left:45%;
}

.posters img:nth-child(2){
    top:65%;
    left:10%;
    animation-delay:2s;
}

.posters img:nth-child(3){
    top:70%;
    left:75%;
    animation-delay:4s;
}

</style>

</head>

<body>

<div class="bg-slider" id="bgSlider"></div>

<div class="overlay"></div>

<div class="glow-orb orb1"></div>
<div class="glow-orb orb2"></div>
<div class="glow-orb orb3"></div>

<div class="posters">

    <img src="https://image.tmdb.org/t/p/w300/9Gtg2DzBhmYamXBS1hKAhiwbBKS.jpg">

    <img src="https://image.tmdb.org/t/p/w300/5YZbUmjbMa3ClvSW1Wj3D6XGolb.jpg">

    <img src="https://image.tmdb.org/t/p/w300/3bhkrj58Vtu7enYsRolD1fZdja1.jpg">

</div>

<div class="container">

    <div class="left-panel">

        <h1>FILMIE</h1>

        <div class="tagline">
            Your movies world ✨
        </div>

        <p class="desc">
            Discover movies, rate them, and build your cinema universe.
        </p>

        <div class="mini-stats">
            <div>🎬 Movies</div>
            <div>⭐ Reviews</div>
            <div>🍿 Watchlist</div>
        </div>

    </div>

    <div class="right-panel">

        <div class="box">

            <div class="logo">🎬🍿</div>

            <h2>Welcome Back</h2>

            <p class="subtitle">
                Login to continue
            </p>

            <?php if($error){ ?>

                <div class="error">
                    <?= $error ?>
                </div>

            <?php } ?>

            <form method="POST">

                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email"
                    required
                >

                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password"
                    required
                >

                <button type="submit" name="login">
                    Login
                </button>

            </form>

            <p class="register-text">
                Don't have an account?
            </p>

            <a href="register.php">
                <button type="button">
                    Create Account
                </button>
            </a>

        </div>

    </div>

</div>

<script>

const images = [

"https://image.tmdb.org/t/p/original/9Gtg2DzBhmYamXBS1hKAhiwbBKS.jpg",

"https://image.tmdb.org/t/p/original/5YZbUmjbMa3ClvSW1Wj3D6XGolb.jpg",

"https://image.tmdb.org/t/p/original/3bhkrj58Vtu7enYsRolD1fZdja1.jpg"

];

let i = 0;

const slider = document.getElementById("bgSlider");

images.forEach((img, index)=>{

    let div = document.createElement("div");

    div.className = "bg-slide";

    div.style.backgroundImage = `url(${img})`;

    if(index === 0){
        div.classList.add("active");
    }

    slider.appendChild(div);

});

const slides = document.querySelectorAll(".bg-slide");

setInterval(()=>{

    slides[i].classList.remove("active");

    i = (i + 1) % slides.length;

    slides[i].classList.add("active");

},4000);

</script>

</body>
</html>