<?php
session_start();
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $db_pass);
    if ($stmt->fetch() && $password === $db_pass) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        header("Location: ../products.php");
        exit();
    } else {
        $error = "Invalid login.";
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html><html><body>
<h2>Login</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    <input name="username" placeholder="Username" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button>Login</button>
</form>
</body></html>
