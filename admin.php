<?php

$user = 'u47584';
$pass = '3864156';
$db = new PDO('mysql:host=localhost;dbname=u47584', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['delete'])) {
        $stmt = $db->prepare("SELECT * FROM clients2 WHERE login = ?");
        $stmt->execute(array($_POST['delete']));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($result)) {
            print('<p>Ошибка при удалении данных</p>');
        } else {
            $user_id = $result['id'];
            $stmt = $db->prepare("DELETE FROM clients2 WHERE login = ?");
            $stmt->execute(array($_POST['delete']));

            $stmt = $db->prepare("DELETE FROM superclients where client_id = ?");
            $stmt->execute(array($user_id));
            header('Location: ?delete=1');
        }
    } else if (!empty($_POST['edit'])) {
        $user = 'u47584';
        $pass = '3864156';
        $client_id = $_POST['edit'];

        $db = new PDO('mysql:host=localhost;dbname=u47584', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
        $stmt = $db->prepare("SELECT * FROM clients2 WHERE login = ?");
        $stmt->execute(array($client_id));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $values['name'] = $result['name'];
        $values['email'] = $result['email'];
        $values['birth'] = $result['date'];
        $values['gender'] = $result['gender'];
        $values['limbs'] = $result['limbs'];
        $values['bio'] = $result['bio'];
        $values['policy'] = $result['policy'];

        setcookie('client_id', $client_id, time() + 12 * 30 * 24 * 60 * 60);

        $powers = $db->prepare("SELECT distinct name from superclients join superpowers3 pow on power_id = pow.id where client_id = ?");
        $powers->execute(array($client_id));
        $result = $powers->fetchAll(PDO::FETCH_ASSOC);
        $str = "";
        foreach ($result as $power) {
            $str .= $power['name'] . ',';
        }
        $values['superpowers'] = $str;
    } else {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $date = $_POST['date'];
        $gender = $_POST['gender'];
        $limbs = $_POST['limbs'];
        $bio = $_POST['bio'];
        $policy = $_POST['policy'];
        $select = $_POST['powers'];
        $user = 'u47584';
        $pass = '3864156';
        $db = new PDO('mysql:host=localhost;dbname=u47584', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

        $client_id = $_COOKIE['client_id'];

        try {
            $stmt = $db->prepare("SELECT login FROM clients2 WHERE id = ?");
            $stmt->execute(array($client_id));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $db->prepare("UPDATE clients2 SET name = ?, email = ?, date = ?, gender = ?, limbs = ?, bio = ?, policy = ? WHERE login = ?");
            $stmt->execute(array($name, $email, $date, $gender, $limbs, $bio, $policy, $result['login']));

            $superpowers = $db->prepare("DELETE FROM superclients WHERE client_id = ?");
            $superpowers->execute(array($client_id));

            foreach ($select as $value) {
                $stmt = $db->prepare("SELECT id from superpowers3 WHERE name = ?");
                $stmt->execute(array($value));
                $power_id = $stmt->fetch(PDO::FETCH_ASSOC);

                $superpowers = $db->prepare("INSERT INTO superclients SET power_id = ?, client_id = ? ");
                $superpowers->execute(array($power_id['id'], $client_id));
            }
        } catch (PDOException $e) {
            print('Error : ' . $e->getMessage());
            exit();
        }
    }
}

if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
    try {
        $stmt = $db->prepare("SELECT * FROM admins WHERE login = ?");
        $stmt->execute(array($_SERVER['PHP_AUTH_USER']));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }

    if (empty($result['password'])) {
        header('HTTP/1.1 401 Unanthorized');
        header('WWW-Authenticate: Basic realm="My site"');
        print('<h1>401 Неверный логин</h1>');
        exit();
    }

    if ($result['password'] != md5($_SERVER['PHP_AUTH_PW'])) {
        header('HTTP/1.1 401 Unanthorized');
        header('WWW-Authenticate: Basic realm="My site"');
        print('<h1>401 Неверный пароль</h1>');
        exit();
    }

    print('Вы успешно авторизовались и видите защищенные паролем данные.');

    $stmt = $db->prepare("SELECT * FROM clients2");
    $stmt->execute([]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT pow.name AS name, count(*) AS amount FROM superclients JOIN superpowers3 pow ON power_id = pow.id GROUP BY power_id");
    $stmt->execute();
    $powersCount = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    header('HTTP/1.1 401 Unanthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Требуется авторизация</h1>');
    exit();
}
?>
<!DOCTYPE html>
<html lang="">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
    <title>Админка</title>
    <style>
        body {
            padding: 12px;
            display: flex;
            align-items: flex-start;
            justify-content: space-around;
            flex-direction: column;
            background: #fff !important;
        }
    </style>
</head>

<body>
    <table class="table table-hover">
        <tr>
            <th scope="col">Название силы</th>
            <th scope="col">Число обладателей</th>
        </tr>
        <?php
        if (!empty($powersCount)) {
            foreach ($powersCount as $value) {
        ?>
                <tr scope="row">
                    <td><?php echo $value['name'] ?></td>
                    <td><?php echo $value['amount'] ?></td>
                </tr>
        <?php }
        } ?>
    </table>

    <table class="table table-hover">
        <tr>
            <th scope="col">Имя</th>
            <th scope="col">Email</th>
            <th scope="col">Дата рождения</th>
            <th scope="col">Конечности</th>
            <th scope="col">Пол</th>
            <th scope="col">Суперспособности</th>
            <th scope="col">Биография</th>
        </tr>
        <?php
        if (!empty($result)) {
            foreach ($result as $value) {
        ?>
                <tr scope="row">
                    <td><?php echo $value['name'] ?></td>
                    <td><?php echo $value['email'] ?></td>
                    <td><?php echo $value['date'] ?></td>
                    <td><?php echo $value['limbs'] ?></td>
                    <td><?php echo $value['gender'] ?></td>
                    <td>
                        <?php
                        $powers = $db->prepare("SELECT distinct name from superclients join superpowers3 pow on power_id = pow.id where client_id = ?");
                        $powers->execute(array($value['id']));
                        $superpowers = $powers->fetchAll(PDO::FETCH_ASSOC);
                        $str = "";
                        foreach ($superpowers as $power) {
                            $str .= $power['name'] . ';';
                        }
                        echo $str;
                        ?>
                    </td>
                    <td><?php echo $value['bio'] ?></td>
                    <td>
                        <form action="" method="post">
                            <input value="<?php echo $value['id'] ?>" name="edit" type="hidden" />
                            <button id="edit">Edit</button>
                        </form>
                    </td>
                    <td>
                        <form action="" method="post">
                            <input value="<?php echo $value['login'] ?>" name="delete" type="hidden" />
                            <button id="delete">Delete</button>
                        </form>
                    </td>
                </tr>
        <?php
            }
        } else {
            echo "Записи не найдены";
        }
        ?>
    </table>
    <?php if (!empty($_POST['edit'])) {
        include('adminchange.php');
    } ?>
</body>

</html>
