<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="description" content="welkom">
    <meta charset="UTF-8">
    <title>index</title>
</head>
<body>
<form method="post">
    <input type="submit" name="pim" value="aaa">
</form>
<?php
session_start();
if (isset($_POST['pim'])) {
    session_destroy();
    header("location: index.php");
}
echo('welkom ' . $_SESSION['naam'] . '<br>');
if($_SESSION['role'] == 'admin') {
    echo 1;
}
?>
</body>
</html>