<?php

$conn = mysqli_connect("localhost", "root", "", "eventify");

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

/* ================= USER REGISTER ================= */

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if email already exists
    $check_sql = "SELECT * FROM register WHERE email=?";

    $check_stmt = $conn->prepare($check_sql);

    $check_stmt->bind_param("s", $email);

    $check_stmt->execute();

    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {

        echo "<h2 style='color:red;text-align:center;margin-top:100px;font-family:Poppins,sans-serif;'>
        Email already registered
        </h2>";

        exit();
    }

    // Insert user
    $sql = "INSERT INTO register(name,email,password) VALUES(?,?,?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {

        show_success_popup(
            "Registration Successful",
            "Welcome to Eventify",
            "user.html"
        );

    } else {

        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

/* ================= ADMIN LOGIN ================= */

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_login'])) {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // IMPORTANT: admin table use ho rahi hy
    $sql = "SELECT * FROM admin WHERE email=? AND password=?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ss", $email, $password);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        show_success_popup(
            "Login Successful",
            "Welcome back Admin",
            "index.html"
        );

    } else {

        echo "<h2 style='color:red;text-align:center;margin-top:100px;font-family:Poppins,sans-serif;'>
        Invalid Email or Password
        </h2>";
    }

    $stmt->close();
}

$conn->close();

/* ================= POPUP FUNCTION ================= */

function show_success_popup($title, $message, $redirect_url) {

echo <<<EOT

<!DOCTYPE html>
<html>
<head>

<title>$title</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
height:100vh;
display:flex;
justify-content:center;
align-items:center;
background:radial-gradient(circle at top,#020617,#0f172a);
}

.popup{
background:white;
padding:35px;
width:380px;
border-radius:20px;
text-align:center;
border:3px solid #06b6d4;
box-shadow:0 0 20px rgba(6,182,212,0.4);
}

.popup h2{
color:#2563eb;
margin-bottom:10px;
}

.popup p{
color:#475569;
margin-bottom:25px;
}

.popup button{
padding:12px 25px;
border:none;
border-radius:30px;
background:linear-gradient(135deg,#6366f1,#38bdf8);
color:white;
font-size:15px;
cursor:pointer;
transition:0.3s;
}

.popup button:hover{
transform:scale(1.05);
}

</style>

</head>

<body>

<div class="popup">

<h2>$title</h2>

<p>$message</p>

<button onclick="window.location.href='$redirect_url'">

OK

</button>

</div>

</body>
</html>

EOT;

}

?>