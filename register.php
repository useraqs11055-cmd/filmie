<?php
session_start();
include __DIR__ . "/db.php";

$error = "";

if(isset($_POST['register'])){

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);

    // check if email exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check) > 0){
        $error = "Email already exists";
    }
    else{

        $sql = "INSERT INTO users (username, email, password, user_type)
                VALUES ('$username', '$email', '$password', '$user_type')";

        if(mysqli_query($conn, $sql)){
            header("Location: login.php");
            exit;
        } else {
            $error = "Error creating account";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Register - Filmie</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#ffd6ea,#ffeaf4,#ffd9f0);
}

.register-box{
    width:380px;
    background:rgba(255,255,255,0.92);
    padding:30px;
    border-radius:25px;
    box-shadow:0 10px 30px rgba(255,105,180,0.25);
    text-align:center;
}

h1{
    color:#ff4fa3;
    margin-bottom:5px;
}

.subtitle{
    color:#777;
    font-size:13px;
    margin-bottom:20px;
}

input, select{
    width:100%;
    padding:12px;
    margin-top:10px;
    border:none;
    border-radius:12px;
    background:#fff0f7;
    outline:none;
}

input:focus, select:focus{
    background:white;
    box-shadow:0 0 0 3px rgba(255,79,163,0.2);
}

button{
    width:100%;
    margin-top:15px;
    padding:12px;
    border:none;
    border-radius:14px;
    background:linear-gradient(135deg,#ff4fa3,#ff7ebd);
    color:white;
    font-weight:600;
    cursor:pointer;
}

button:hover{
    transform:scale(1.02);
}

.error{
    background:#ffe1ea;
    color:#d63384;
    padding:10px;
    border-radius:12px;
    margin-bottom:10px;
    font-size:13px;
}

a{
    display:block;
    margin-top:10px;
    color:#ff4fa3;
    text-decoration:none;
    font-size:13px;
}

</style>

</head>
<body>

<div class="register-box">

<h1>🎬 Filmie</h1>
<p class="subtitle">Create your cute movie account ✨</p>

<?php if($error){ ?>
<div class="error"><?= $error ?></div>
<?php } ?>

<form method="POST">

<input type="text" name="username" placeholder="Username" required>

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password" required>

<select name="user_type">
    <option value="regular">Regular User</option>
    <option value="premium">Premium User 👑</option>
</select>

<button type="submit" name="register">Create Account ✨</button>

</form>

<a href="login.php">Already have an account? Login</a>

</div>

</body>
</html>