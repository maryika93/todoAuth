<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$errors = [];
$servername = "localhost";
$username = "mtipikina";
$passw = "neto1539";
$dbname = "mtipikina";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $passw);

    if (!empty($_POST)) {
        if (isset($_POST['reg'])) {

            if (isset($_POST['login'])) {
                $login = $_POST['login'];
            }
            if (isset($_POST['password'])) {
                $password = md5($_POST['password']);
            }
            $data = $conn->query("SELECT * FROM `user` WHERE login = '$login'");
            foreach ($data as $rows) {
                if (!empty($rows['id'])) {
                    exit("Извините, введённый вами логин уже зарегистрирован. Введите другой логин. </br></br></br> <a href='reg.php'>Вернуться</a>");
                }
            }
            $result = $conn->prepare('INSERT INTO `user`(`login`, `password`) VALUES (:log, :pass)');
            $result->bindParam(':log', $login);
            $result->bindParam(':pass', $password);
            $result->execute();
            $data1 = $conn->query("SELECT * FROM `user` WHERE login = '$login'");
            foreach ($data1 as $rows) {
                $_SESSION['id'] = $rows['id'];
                $_SESSION['login'] = $rows['login'];
            }
            echo "Вы успешно зарегистрированы! Теперь вы можете зайти на сайт.</br></br></br> <a href='index.php'>Главная страница</a>";
            die;
        }

        if (isset($_POST['inp'])) {
            if (isset($_POST['login'])) {
                $login = $_POST['login'];
            }
            if (isset($_POST['password'])) {
                $password = md5($_POST['password']);
            }
            if (empty($login) or empty($password))
            {
                exit ("Вы ввели не всю информацию, вернитесь назад и заполните все поля!");
            }
            $data = $conn->query("SELECT * FROM `user` WHERE login = '$login'");
            foreach ($data as $rows) {
                if (empty($rows['password'])) {
                    exit ("Извините, введённый вами логин или пароль неверный. </br></br></br> <a href='reg.php'>Вернуться</a>");
                }
                else {
                    if ($rows['password'] == $password) {
                        $_SESSION['login'] = $rows['login'];
                        $_SESSION['id']    = $rows['id'];
                        header('Location: index.php');
                    } else {
                        exit ("Извините, введённый вами логин или пароль неверный. </br></br></br> <a href='reg.php'>Вернуться</a>");
                    }
                }
            }
        }
    }
    else {
            echo "Введите данные для регистрации или войдите, если уже регистрировались:";
    }
}
catch(PDOException $e)
{
    die("Error: " . $e->getMessage());
}
foreach ($errors as $error):
    echo $error;
endforeach;

?>


<form method="post" action="" enctype="multipart/form-data">
    <label>Логин</label>
    <input type="text" placeholder="Логин" name="login">
    <label>Пароль</label>
    <input type="password"  placeholder="Пароль" name="password">
    <input type="submit" name="inp" value="Вход">
    <input type="submit" name="reg" value="Регистрация"><br/><br/>
</form>

