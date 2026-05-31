<?php
$conn = mysqli_connect("localhost", "root", "", "eventify");

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

/* ================= USER REGISTER ================= */

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pw = $_POST['password'];

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM register WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows > 0) {

        echo "<h2 style='color:red;text-align:center;margin-top:100px;'>
        Email already registered
        </h2>";

        exit;
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO register(name, email, password) VALUES (?, ?, ?)");

    $stmt->bind_param("sss", $name, $email, $pw);

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
    $password = $_POST['password'];

    $sql = "SELECT * FROM register WHERE email=? AND password=?";

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

        echo "<h2 style='color:red;text-align:center;margin-top:100px;'>
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

<style>

body{
display:flex;
justify-content:center;
align-items:center;
height:100vh;
background:#0f172a;
font-family:Arial;
}

.popup{
background:white;
padding:30px;
border-radius:15px;
text-align:center;
width:350px;
}

button{
padding:10px 20px;
border:none;
background:#06b6d4;
color:white;
border-radius:20px;
cursor:pointer;
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