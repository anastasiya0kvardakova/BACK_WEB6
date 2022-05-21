<?php

header('Content-Type: text/html; charset=UTF-8');
session_start();


if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $messages = array();
    if (!empty($_COOKIE['save'])) {

        setcookie('save', '', 100000);
        setcookie('login', '', 100000);
        setcookie('pass', '', 100000);

        $messages[] = 'Спасибо, результаты сохранены.';

        if (!empty($_COOKIE['pass'])) {
            $messages[] = sprintf(
                'Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
                strip_tags($_COOKIE['login']),
                strip_tags($_COOKIE['pass'])
            );
        }
    }


    $errors = array();
    $errors['name'] = !empty($_COOKIE['name_error']);
    $errors['email'] = !empty($_COOKIE['email_error']);
    $errors['date'] = !empty($_COOKIE['date_error']);
    $errors['gender'] = !empty($_COOKIE['gender_error']);
    $errors['limbs'] = !empty($_COOKIE['limbs_error']);
    $errors['powers'] = !empty($_COOKIE['powers_error']);
    $errors['bio'] = !empty($_COOKIE['bio_error']);
    $errors['policy'] = !empty($_COOKIE['policy_error']);

    if ($errors['name']) {
        setcookie('name_error', '', 100000);
        $messages[] = '<div class="error">Введите имя.</div>';
    }
    if ($errors['email']) {
        setcookie('email_error', '', 100000);
        $messages[] = '<div class="error">Введите верный email.</div>';
    }
    if ($errors['date']) {
        setcookie('date_error', '', 100000);
        $messages[] = '<div class="error">Введите корректную дату рождения.</div>';
    }
    if ($errors['gender']) {
        setcookie('gender_error', '', 100000);
        $messages[] = '<div class="error">Выберите пол.</div>';
    }
    if ($errors['limbs']) {
        setcookie('limbs_error', '', 100000);
        $messages[] = '<div class="error">Выберите количество конечностей.</div>';
    }
    if ($errors['powers']) {
        setcookie('powers_error', '', 100000);
        $messages[] = '<div class="error">Выберите суперспособнос(ть/ти).</div>';
    }
    if ($errors['bio']) {
        setcookie('bio_error', '', 100000);
        $messages[] = '<div class="error">Расскажите о себе.</div>';
    }
    if ($errors['policy']) {
        setcookie('policy_error', '', 100000);
        $messages[] = '<div class="error">Ознакомтесь с политикой обработки данных.</div>';
    }

    $values = array();
    $values['name'] = empty($_COOKIE['name_value']) ? '' : strip_tags($_COOKIE['name_value']);
    $values['email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
    $values['date'] = empty($_COOKIE['date_value']) ? '' : strip_tags($_COOKIE['date_value']);
    $values['gender'] = empty($_COOKIE['gender_value']) ? '' : strip_tags($_COOKIE['gender_value']);
    $values['limbs'] = empty($_COOKIE['limbs_value']) ? '' : strip_tags($_COOKIE['limbs_value']);
    $values['powers'] = empty($_COOKIE['powers_value']) ? '' : strip_tags($_COOKIE['powers_value']);
    $values['bio'] = empty($_COOKIE['bio_value']) ? '' : strip_tags($_COOKIE['bio_value']);
    $values['policy'] = empty($_COOKIE['policy_value']) ? '' : strip_tags($_COOKIE['policy_value']);

    if (empty($errors) && !empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {
        try {
            $user = 'u47584';
            $pass = '3864156';
            $member = $_SESSION['login'];
            $db = new PDO('mysql:host=localhost;dbname=u47584', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
            $stmt = $db->prepare("SELECT * FROM clients2 WHERE login = ?");
            $stmt->execute(array($member));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $values['name'] = $result['name'];
            $values['email'] = $result['email'];
            $values['date'] = $result['date'];
            $values['gender'] = $result['gender'];
            $values['limbs'] = $result['limbs'];
            $values['bio'] = $result['bio'];
            $values['policy'] = $result['policy'];

            $powers = $db->prepare("SELECT distinct name from superclients join superpowers3 pow on power_id = pow.id where client_id = ?");
            $powers->execute(array($member));
            $result = $powers->fetch(PDO::FETCH_ASSOC);
            $values['powers'] = $result['powers'];
        } catch (PDOException $e) {
            print('Error : ' . $e->getMessage());
            exit();
        }
        printf('<div>Вход с логином %s, uid %d</div>', $_SESSION['login'], $_SESSION['uid']);
    }
    include('form.php');
} else {
    $errors = FALSE;

    if (!preg_match('/^([a-zA-Z]|[а-яА-Я])/', $_POST['name'])) {
        setcookie('name_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('name_value', $_POST['name'], time() + 12 * 30 * 24 * 60 * 60);
    }


    if (!preg_match('/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}/', $_POST['email'])) {
        setcookie('email_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('email_value', $_POST['email'], time() + 12 * 30 * 24 * 60 * 60);
    }

    $date = explode('-', $_POST['date']);
    $age = (int)date('Y') - (int)$date[0];
    if ($age > 100 || $age < 0) {
        setcookie('date_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('date_value', $_POST['date'], time() + 12 * 30 * 24 * 60 * 60);
    }


    if (empty($_POST['gender'])) {
        setcookie('gender_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('gender_value', $_POST['gender'], time() + 12 * 30 * 24 * 60 * 60);
    }


    if (empty($_POST['limbs'])) {
        setcookie('limbs_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('limbs_value', $_POST['limbs'], time() + 12 * 30 * 24 * 60 * 60);
    }


    if (empty($_POST['powers'])) {
        setcookie('powers_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('powers_value', implode(',', $_POST['powers']), time() + 12 * 30 * 24 * 60 * 60);
    }


    if (empty($_POST['bio'])) {
        setcookie('bio_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('bio_value', $_POST['bio'], time() + 12 * 30 * 24 * 60 * 60);
    }


    if (empty($_POST['policy'])) {
        setcookie('policy_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('policy_value', $_POST['policy'], time() + 12 * 30 * 24 * 60 * 60);
    }

    if ($errors) {

        header('Location: index.php');
        exit();
    } else {
        setcookie('name_error', '', 100000);
        setcookie('email_error', '', 100000);
        setcookie('date_error', '', 100000);
        setcookie('gender_error', '', 100000);
        setcookie('limbs_error', '', 100000);
        setcookie('powers_error', '', 100000);
        setcookie('bio_error', '', 100000);
        setcookie('policy_error', '', 100000);
    }

    $user = 'u47584';
    $pass = '3864156';
    $name = $_POST['name'];
    $email = $_POST['email'];
    $date = $_POST['date'];
    $gender = $_POST['gender'];
    $limbs = $_POST['limbs'];
    $bio = $_POST['bio'];
    $policy = $_POST['policy'];
    $powers = $_POST['powers'];
    $member = $_SESSION['login'];

    $db = new PDO('mysql:host=localhost;dbname=u47584', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

    if (!empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {
        try {
            $stmt = $db->prepare("UPDATE clients2 SET name = ?, email = ?, date = ?, gender = ?, limbs = ?, bio = ?, policy = ? WHERE login = ?");
            $stmt->execute(array($name, $email, $date, $gender, $limbs, $bio, $policy, $member));

            $stmt = $db->prepare("SELECT id FROM clients2 WHERE login = ?");
            $stmt->execute(array($member));
            $client_id = $stmt->fetch(PDO::FETCH_ASSOC);

            $superpowers = $db->prepare("DELETE FROM superclients WHERE client_id = ?");
            $superpowers->execute(array($client_id['id']));

            foreach ($powers as $value) {
                $stmt = $db->prepare("SELECT id from superpowers3 WHERE name = ?");
                $stmt->execute(array($value));
                $power_id = $stmt->fetch(PDO::FETCH_ASSOC);

                $superpowers = $db->prepare("INSERT INTO superclients SET power_id = ?, client_id = ? ");
                $superpowers->execute(array($power_id['id'], $client_id['id']));
            }
        } catch (PDOException $e) {
            print('Error : ' . $e->getMessage());
            exit();
        }
    } else {
        $login = uniqid();
        $password = uniqid();
        $hash = md5($password);

        setcookie('login', $login);
        setcookie('pass', $password);

        try {
            $stmt = $db->prepare("INSERT INTO clients2 SET login = ?, pass = ?, name = ?, email = ?, date = ?, gender = ?, limbs = ?, bio = ?, policy = ?");
            $stmt->execute(array($login, $hash, $name, $email, $date, $gender, $limbs, $bio, $policy));
            $client_id = $db->lastInsertId();
            foreach ($powers as $value) {
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
    setcookie('save', '1');
    header('Location: ./');
}
