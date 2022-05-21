<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

if (!empty($_SESSION['login'])) {
    header('Location: ./');
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['logout'])) {
        session_destroy();
        $_SESSION['login'] = '';
        header('Location: ./?logout=1');
    }
    if (!empty($_GET['error'])) {
        if ($_GET['error'] == 'login') {
            print('<div>Пользователя с таким логином не существует</div>');
        } else {
            print('<div>Неверный пароль</div>');
        }
    }
?>

    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
        <link rel="stylesheet" href="./style.css" />
        <style>
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
            }
        </style>
        <title>Логин</title>
    </head>

    <body>
        <form action="" method="POST" class="login">
            <div>
                Логин:<input name="login" />
            </div>
            <div>
                Пароль:<input name="pass" />
            </div>
            <input type="submit" value="Войти" />
        </form>
        </div>
    </body>

<?php
} else {
    $user = 'u47584';
    $pass = '3864156';
    $db = new PDO('mysql:host=localhost;dbname=u47584', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

    $user_login = $_POST['login'];
    $pass_hash = md5($_POST['pass']);

    try {
        $stmt = $db->prepare("SELECT * FROM clients2 WHERE login = ?");
        $stmt->execute(array($user_login));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    if (empty($result['pass'])) {
        header('Location: ?error=login');
    } else if ($result['pass'] == $pass_hash) {

        $_SESSION['login'] = $_POST['login'];
        $_SESSION['uid'] = $result['id'];

        header('Location: ./');
    } else {
        header('Location: ?error=pass');
    }
}
