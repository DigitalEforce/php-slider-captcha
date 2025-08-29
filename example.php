<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head><title>Captcha Demo</title></head>
<body>
<h2>Form with NVCaptcha</h2>
<form method="post" action="">
    <input type="text" name="name" placeholder="Your Name" required><br><br>
    <?php include "nvcaptcha/captcha_view.php"; ?><br>
    <button type="submit">Submit</button>
</form>

<?php
if($_SERVER['REQUEST_METHOD']==="POST"){
    if(!empty($_POST['nvc_trap'])){ die("Bot detected!"); } // Honeypot check
    if($_POST['captcha_verified']==="1"){
        echo "<p style='color:green'>Form submitted successfully ✅</p>";
    } else {
        echo "<p style='color:red'>Captcha failed ❌</p>";
    }
}
?>
</body>
</html>
