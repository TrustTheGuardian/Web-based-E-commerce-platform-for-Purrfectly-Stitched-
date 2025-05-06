<?php
$token = $_GET['token'] ?? '';
?>

<form action="update_password.php" method="POST">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

    <label>New Password</label>
    <input type="password" name="password" required><br>

    <label>Confirm Password</label>
    <input type="password" name="repassword" required><br>

    <button type="submit">Reset Password</button>
</form>