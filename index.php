<?php
$servername = "localhost";
$username = "mtipikina";
$password = "neto1539";
$dbname = "mtipikina";
session_start();
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

if (isset($_POST['add'])) {
    if (isset($_POST['newTask'])) {
        $data = $conn->prepare('INSERT INTO `task`(`description`, `is_done`, `date_added`, `author`,`assign`,`user_id`,`assigned_user_id`) VALUES (:descr, :done, :datead, :author, :assign, :usid, :asusid)');
        $data->bindParam(':descr', $fieldData);
        $data->bindParam(':done', $isdone);
        $data->bindParam(':datead', date("y.m.d.H:i:s"));
        $data->bindParam(':author', $_SESSION['login']);
        $data->bindParam(':assign', $_SESSION['login']);
        $data->bindParam(':usid', $_SESSION['id']);
        $data->bindParam(':asusid', $_SESSION['id']);
        $fieldData = $_POST['newTask'];
        $isdone    = "В процессе";
        $data->execute();
    }
}
    if(isset($_GET['delete'])){
        $del = $_GET['delete'];
        echo $del;
        $datadel = $conn->prepare('DELETE FROM `task` WHERE id = :id');
        $datadel->bindParam(':id', $del);
        $datadel->execute();
    }

    if(isset($_GET['done'])){
        $isdone = "Выполнено";
        $datadone = $conn->prepare('UPDATE `task` SET `is_done`=:done WHERE id = :id');
        $datadone->execute(array(
            ':done' => $isdone,
            ':id' => $_GET['done']
        ));
    }

if (isset($_POST['get'])) {
    $a = $_POST['sel_name'];
    $data = $conn->query("SELECT * FROM task left join user ON user.login='$a' where task.author= '$a'");
    foreach($data as $rows) {
        echo '<pre>';
        print_r($rows);
        $datadone = $conn->prepare('UPDATE `task` SET `assign`=:assign WHERE id = :id');
        $datadone->execute(array(
            ':assign' => $isdone,
            ':id' => $_GET['done']
        ));
    }
}


if (isset($_POST['exit'])) {
    header('Location: go.php');
}
}
catch(PDOException $e)
{
    die("Error: " . $e->getMessage());
}
?>

<h1> Список дел на сегодня </h1>
<form method="post" action="" enctype="multipart/form-data">
    <input type="text" placeholder="Новая задача" name="newTask">
    <input type="submit" name="add" value="Добавить"><br/><br/>
</form>

<table border="1", cellpadding="10", width="100%">
    <tr>
        <td align="center"> Описание задачи </td>
        <td align="center"> Дата добавления </td>
        <td align="center"> Статус </td>
        <td align="center">  </td>
        <td align="center"> Ответственный </td>
        <td align="center"> Автор </td>
        <td align="center"> Закрепить задачу за пользователем  </td>
    </tr>

    <?php
    $a = $_SESSION['id'];
    $data = $conn->query("SELECT * FROM `task` left join `user` ON user.id=task.assigned_user_id where task.user_id = '$a'");
    foreach($data as $rows) {
        ?>
            <tr>
                <td align="center"><?php echo $rows['description'] ?></td>
                <td align="center"><?php echo $rows['date_added'] ?></td>
                <td align="center"><?php echo $rows['is_done'] ?></td>
                <td align="center"><?php echo '<a href="index.php?delete=' . $rows['0'] . '">Удалить</a> <br/>'.'<a href="index.php?done=' . $rows['0'] . '">Выполнить</a>'?></td>
                <td align="center"><?php echo $rows['assign'] ?></td>
                <td align="center"><?php echo $rows['author'] ?></td>
                <td align="center">
                    <form method="post" action="" enctype="multipart/form-data">
                        <?php echo "<select name = 'sel_name'>";
                        $data1 = $conn->query('SELECT * FROM `user`');
                        foreach($data1 as $rows1) {
                            $a = $rows1['login'];
                            $b = $rows1['id'];
                            echo "<option value = '$a' > $a </option>";
                        }
                        echo "</select>"; ?>
                        <input type="submit" name="get" value="Переложить ответственность">
                    </form>
            </tr>
        <?php
   }
    ?>
</table>

<form method="post" action="" enctype="multipart/form-data">
    <input type="submit" name="exit" value="Выйти"><br/><br/>
</form>