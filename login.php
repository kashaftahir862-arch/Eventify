<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$conn = mysqli_connect("localhost", "root", "", "eventify");

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $sql = "SELECT * FROM `register` WHERE email=? AND password=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $_SESSION['user_email'] = $user_data['email'];

        show_success_popup(
            "Login Successful",
            "Welcome back to Eventify",
            "events.html"
        );
    } else {
        echo "<h2 style='color:red;text-align:center;margin-top:100px;'>Invalid Email or Password</h2>";
        header("refresh:2; url=userlogin.html");
    }

    $stmt->close();
}

$conn->close();
function show_success_popup($title, $message, $redirect_url) {
echo <<<EOT
<!DOCTYPE html>
<html>
<head>
<title>$title</title>
<style>
body{ display:flex; justify-content:center; align-items:center; height:100vh; background:#0f172a; font-family:Arial; }
.popup{ background:white; padding:30px; border-radius:15px; text-align:center; width:350px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
h2 { color: #0f172a; margin-top: 0; }
p { color: #64748b; margin-bottom: 20px; }
button{ padding:10px 20px; border:none; background:#06b6d4; color:white; border-radius:20px; cursor:pointer; font-weight: bold; width: 100px; }
button:hover { background:#0891b2; }
</style>
</head>
<body>
<div class="popup">
    <h2>$title</h2>
    <p>$message</p>
    <button onclick="window.location.replace('events.html')">Ok</button>
</div>
</body>
</html>
EOT;
}
?>