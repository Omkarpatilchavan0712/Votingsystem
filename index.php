<?php
session_start();
$conn = mysqli_connect("localhost","root","","voting");
if(!$conn) die("DB Error");

/* REGISTER */
if(isset($_POST['register'])){
 $name=$_POST['name'];
 $email=$_POST['email'];
 $pass=md5($_POST['password']);
 mysqli_query($conn,"INSERT INTO users(name,email,password) VALUES('$name','$email','$pass')");
}

/* LOGIN */
if(isset($_POST['login'])){
 $email=$_POST['email'];
 $pass=md5($_POST['password']);
 $q=mysqli_query($conn,"SELECT * FROM users WHERE email='$email' AND password='$pass'");
 if(mysqli_num_rows($q)>0){
   $_SESSION['user']=mysqli_fetch_assoc($q);
 }
}

/* LOGOUT */
if(isset($_GET['logout'])){
 session_destroy();
 header("Location:index.php");
}

/* VOTE */
if(isset($_POST['vote'])){
 $cid=$_POST['candidate'];
 $uid=$_SESSION['user']['id'];
 mysqli_query($conn,"UPDATE candidates SET votes=votes+1 WHERE id=$cid");
 mysqli_query($conn,"UPDATE users SET voted=1 WHERE id=$uid");
 $_SESSION['user']['voted']=1;
}

/* ADD CANDIDATE (ADMIN) */
if(isset($_POST['add_candidate'])){
 $n=$_POST['cname'];
 $p=$_POST['photo'];
 mysqli_query($conn,"INSERT INTO candidates(name,photo) VALUES('$n','$p')");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>College Online Voting</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{
min-height:100vh;
background:linear-gradient(120deg,#667eea,#764ba2,#6b8cff);
background-size:300% 300%;
animation:bg 10s infinite alternate;
display:flex;
justify-content:center;
align-items:center;
}
@keyframes bg{
0%{background-position:0% 50%}
100%{background-position:100% 50%}
}
.card{
width:95%;
max-width:400px;
background:rgba(255,255,255,.2);
backdrop-filter:blur(15px);
padding:30px;
border-radius:20px;
box-shadow:0 20px 40px rgba(0,0,0,.3);
animation:zoom 1s ease;
}
@keyframes zoom{
from{opacity:0;transform:scale(.8)}
to{opacity:1;transform:scale(1)}
}
h1,h2{text-align:center;color:#fff;margin-bottom:15px}
input{
width:100%;
padding:12px;
margin:8px 0;
border-radius:8px;
border:none;
outline:none;
}
button{
width:100%;
padding:12px;
margin-top:10px;
border:none;
border-radius:8px;
background:linear-gradient(135deg,#6a11cb,#2575fc);
color:white;
font-size:16px;
font-weight:bold;
cursor:pointer;
transition:.3s;
}
button:hover{
transform:scale(1.05);
box-shadow:0 10px 25px rgba(0,0,0,.3);
}
.link{
text-align:center;
margin-top:15px;
}
.link a{
color:#fff;
text-decoration:none;
font-weight:bold;
}
.back{
margin-top:15px;
text-align:center;
}
</style>
</head>

<body>

<div class="card">

<?php if(!isset($_SESSION['user'])){ ?>

<?php if(!isset($_GET['register'])){ ?>
<!-- LOGIN PAGE -->
<h1>🗳️ College Voting</h1>
<h2>Login</h2>
<form method="post">
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button name="login">Login</button>
</form>

<div class="link">
<a href="?register=1">New user? Register here</a>
</div>

<?php } else { ?>
<!-- REGISTER PAGE -->
<h1>📝 Create Account</h1>
<h2>Register</h2>
<form method="post">
<input type="text" name="name" placeholder="Full Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button name="register">Register</button>
</form>

<div class="back">
<a href="index.php" style="color:white;text-decoration:none">← Back to Login</a>
</div>

<?php } ?>

<?php } else { ?>

<!-- AFTER LOGIN -->
<h2>Welcome, <?=$_SESSION['user']['name']?></h2>

<?php if($_SESSION['user']['role']!='admin' && $_SESSION['user']['voted']==0){ ?>
<form method="post">
<?php
$c=mysqli_query($conn,"SELECT * FROM candidates");
while($row=mysqli_fetch_assoc($c)){
echo "<label style='color:#fff;display:block;margin:8px 0'>
<input type='radio' name='candidate' value='{$row['id']}' required>
 {$row['name']}
</label>";
}
?>
<button name="vote">Submit Vote</button>
</form>

<?php } else { ?>
<h2>Thank you for voting ✅</h2>
<?php } ?>

<div class="back">
<a href="?logout=1" style="color:white;text-decoration:none">Logout</a>
</div>

<?php } ?>

</div>
</body>
</html>
