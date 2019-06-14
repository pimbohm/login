<?php
//
//use App\Kernel;
//use Symfony\Component\Debug\Debug;
//use Symfony\Component\HttpFoundation\Request;
//
//require dirname(__DIR__).'/config/bootstrap.php';
//
//if ($_SERVER['APP_DEBUG']) {
//    umask(0000);
//
//    Debug::enable();
//}
//
//if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
//    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
//}
//
//if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
//    Request::setTrustedHosts([$trustedHosts]);
//}
//
//$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
//$request = Request::createFromGlobals();
//$response = $kernel->handle($request);
//$response->send();
//$kernel->terminate($request, $response);
session_start();
$servername = "localhost";
$username = "root";
$password = "";

try {
//Creating connection for mysql
    $conn = new PDO("mysql:host=$servername;dbname=login", $username, $password);
// set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> Title </title>
    <link rel="stylesheet" href="css/registreren.css">
</head>

<body>
<?php

if(isset($_SESSION['id']))
{
    header("location:hoofdpagina.php");
    return;
}
if(isset($_POST["submit"])){
    $password = $_POST['password'];
    $pswd = $_POST['pswd'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    if ($password == $pswd) {
        $stmt = $conn->prepare("INSERT INTO user (voornaam, tussenvoegsel, achternaam, email, wachtwoord, roles)
    VALUES (:vn, :tv, :an, :email, :ww, :role)");
        $stmt->bindParam(':vn', $_POST["voornaam"]);
        $stmt->bindParam(':tv', $_POST["tussenvoegsel"]);
        $stmt->bindParam(':an', $_POST["achternaam"]);
        $stmt->bindParam(':email', $_POST["email"]);
        $stmt->bindParam(':ww', $hash);
        $stmt->bindParam(':role', $_POST["role"]);

        $stmt->execute();
        echo "<script type= 'text/javascript'>alert('Je account is aangemaakt');</script>";
        header("location:index.php?action=login");
        return;
    } else {
        echo "<script type= 'text/javascript'>alert('wachtwoorden komen niet overeen');</script>";
    }
}

if(isset($_POST['login'])) {
    $pwd = $_POST['pwd'];
    $mail = $_POST['mail'];
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = :mail LIMIT 1");
    $stmt->execute([ ':mail' => $mail ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if( ! $row)
    {
        echo "<script type= 'text/javascript'>alert('Verkeerde gegevens ingevuld');</script>";
    }
    else
    {
        $count = $stmt->rowCount();
        if($count > 0)
        {
            if(password_verify($pwd, $row['wachtwoord']))
            {
                $_SESSION['id'] = $row['id'];
                $_SESSION['naam'] = $row['voornaam'];
                $_SESSION['role'] = $row['roles'];
                header("location:pim.php");
                return;
            }
            else
            {
                echo "<script type= 'text/javascript'>alert('Verkeerde gegevens ingevuld');</script>";
            }
        }
    }
}
if(isset($_GET['action']) == 'login')
{
    ?>

    <h1>Login</h1>
    <hr>
    <form method="post">
        <label>email:</label>
        <input type="text" name="mail" id="mail" required placeholder=""><br>
        <label>password:</label>
        <input type="password" name="pwd" id="pwd" required placeholder=""><br>
        <input type="submit" name="login" value="login"><br>
        <br><br><br><br>
        <br><br><br><br>
        <a href="index.php">Account aanmaken</a>
    </form>
    <?php
}
else {
    ?>
    <h1>Als je een account op deze website aanmaakt kun je een applicatie aanvragen bij incrowd</h1>
    <hr>
    <h1>Account aanmaken</h1>
    * = verplicht
    <form method="post">
        <label>voornaam:</label>
        <input type="text" name="voornaam" id="voornaam" required>*<br>
        <label>tussenvoegsel:</label>
        <input type="text" name="tussenvoegsel" id="tussenvoegsel"><br>
        <label>achternaam:</label>
        <input type="text" name="achternaam" id="achternaam" required>*<br>
        <label>email</label>
        <input type="email" name="email" id="email" required="required">*<br>
        <label>Wachtwoord: </label>
        <input type="password" name="password" required="required">*<br>
        <label>Wachtwoord herhalen: </label>
        <input type="password" name="pswd" required="required">*<br>
        <input type="hidden" name="role" id="role" value="user">
        <input type="submit" value="Account aanmaken " name="submit"><br>
        <br><br><br><br>
        <br><br><br><br>
        <a href="index.php?action=login">Login</a>
    </form>
    <?php
}
?>
</body>

</html>
