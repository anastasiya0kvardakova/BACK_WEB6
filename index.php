<?php

header('Content-Type: text/html; charset=UTF-8');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages = array();
  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    // Выводим сообщение пользователю.
    $messages[] = 'Спасибо, результаты сохранены.';
    // Если в куках есть пароль, то выводим сообщение.
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf(
        'Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['pass'])
      );
    }
  }

  // Складываем признак ошибок в массив.
  $errors = array();
  $errors['name'] = !empty($_COOKIE['name_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['birth'] = !empty($_COOKIE['birth_error']);
  $errors['gender'] = !empty($_COOKIE['gender_error']);
  $errors['limbs'] = !empty($_COOKIE['limbs_error']);
  $errors['select'] = !empty($_COOKIE['select_error']);
  $errors['bio'] = !empty($_COOKIE['bio_error']);
  $errors['policy'] = !empty($_COOKIE['policy_error']);

  // TODO: аналогично все поля.

  // Выдаем сообщения об ошибках.
  if ($errors['name']) {
    setcookie('name_error', '', 100000);
    $messages[] = '<div class="error">Введите имя.</div>';
  }
  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    $messages[] = '<div class="error">Введите верный email.</div>';
  }
  if ($errors['birth']) {
    setcookie('birth_error', '', 100000);
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
  if ($errors['select']) {
    setcookie('select_error', '', 100000);
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
  // TODO: тут выдать сообщения об ошибках в других полях.

  // Складываем предыдущие значения полей в массив, если есть.
  // При этом санитизуем все данные для безопасного отображения в браузере.
  $values = array();
  $values['name'] = empty($_COOKIE['name_value']) ? '' : strip_tags($_COOKIE['name_value']);
  $values['email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
  $values['birth'] = empty($_COOKIE['birth_value']) ? '' : strip_tags($_COOKIE['birth_value']);
  $values['gender'] = empty($_COOKIE['gender_value']) ? '' : strip_tags($_COOKIE['gender_value']);
  $values['limbs'] = empty($_COOKIE['limbs_value']) ? '' : strip_tags($_COOKIE['limbs_value']);
  $values['select'] = empty($_COOKIE['select_value']) ? '' : strip_tags($_COOKIE['select_value']);
  $values['bio'] = empty($_COOKIE['bio_value']) ? '' : strip_tags($_COOKIE['bio_value']);
  $values['policy'] = empty($_COOKIE['policy_value']) ? '' : strip_tags($_COOKIE['policy_value']);
  // TODO: аналогично все поля.

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if (empty($errors) && !empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {
    try {
      $user = 'u47572';
      $pass = '4532025';
      $member = $_SESSION['login'];
      $db = new PDO('mysql:host=localhost;dbname=u47572', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
      $stmt = $db->prepare("SELECT * FROM members WHERE login = ?");
      $stmt->execute(array($member));
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $values['name'] = $result['name'];
      $values['email'] = $result['email'];
      $values['birth'] = $result['date'];
      $values['gender'] = $result['gender'];
      $values['limbs'] = $result['limbs'];
      $values['bio'] = $result['bio'];
      $values['policy'] = $result['policy'];

      $powers = $db->prepare("SELECT distinct name from powersowners join superpowers2 pow on power_id = pow.id where owner_id = ?");
      $powers->execute(array($member));
      $result = $powers->fetchAll(PDO::FETCH_ASSOC);
      $values['select'] = implode(',', $result);
    } catch (PDOException $e) {
      print('Error : ' . $e->getMessage());
      exit();
    }
    printf('<div>Вход с логином %s, uid %d</div>', $_SESSION['login'], $_SESSION['uid']);
  }
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
  $errors = FALSE;
  // проверка поля имени
  if (!preg_match('/^[a-z0-9_\s]+$/i', $_POST['name'])) {
    setcookie('name_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('name_value', $_POST['name'], time() + 12 * 30 * 24 * 60 * 60);
  }

  // проверка поля email
  if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('email_value', $_POST['email'], time() + 12 * 30 * 24 * 60 * 60);
  }

  // проверка поля даты рождения
  $birth = explode('-', $_POST['birth']);
  $age = (int)date('Y') - (int)$birth[0];
  if ($age > 100 || $age < 0) {
    setcookie('birth_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('birth_value', $_POST['birth'], time() + 12 * 30 * 24 * 60 * 60);
  }

  // проверка поля пола
  if (empty($_POST['gender'])) {
    setcookie('gender_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('gender_value', $_POST['gender'], time() + 12 * 30 * 24 * 60 * 60);
  }

  // проверка поля количества конечностей
  if (empty($_POST['limbs'])) {
    setcookie('limbs_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('limbs_value', $_POST['limbs'], time() + 12 * 30 * 24 * 60 * 60);
  }

  // проверка поля суперспособностей
  if (empty($_POST['select'])) {
    setcookie('select_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('select_value', implode(',', $_POST['select']), time() + 12 * 30 * 24 * 60 * 60);
  }

  // проверка поля биографии
  if (empty($_POST['bio'])) {
    setcookie('bio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('bio_value', $_POST['bio'], time() + 12 * 30 * 24 * 60 * 60);
  }

  // проверка поля политики обработки данных 
  if (empty($_POST['policy'])) {
    setcookie('policy_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  } else {
    setcookie('policy_value', $_POST['policy'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if ($errors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  } else {
    setcookie('name_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('birth_error', '', 100000);
    setcookie('gender_error', '', 100000);
    setcookie('limbs_error', '', 100000);
    setcookie('select_error', '', 100000);
    setcookie('bio_error', '', 100000);
    setcookie('policy_error', '', 100000);
  }

  $user = 'u47572';
  $pass = '4532025';
  $name = $_POST['name'];
  $email = $_POST['email'];
  $date = $_POST['birth'];
  $gender = $_POST['gender'];
  $limbs = $_POST['limbs'];
  $bio = $_POST['bio'];
  $policy = $_POST['policy'];
  $powers = $_POST['select'];
  $member = $_SESSION['login'];

  $db = new PDO('mysql:host=localhost;dbname=u47572', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
  // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
  if (!empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {
    try {
      $stmt = $db->prepare("UPDATE members SET name = ?, email = ?, date = ?, gender = ?, limbs = ?, bio = ?, policy = ? WHERE login = ?");
      $stmt->execute(array($name, $email, $date, $gender, $limbs, $bio, $policy, $member));

      $stmt = $db->prepare("SELECT id FROM members WHERE login = ?");
      $stmt->execute(array($member));
      $user_id = $stmt->fetch(PDO::FETCH_ASSOC);

      $superpowers = $db->prepare("DELETE FROM powersowners WHERE owner_id = ?");
      $superpowers->execute(array($user_id['id']));

      foreach ($powers as $value) {
        $stmt = $db->prepare("SELECT id from superpowers2 WHERE name = ?");
        $stmt->execute(array($value));
        $power_id = $stmt->fetch(PDO::FETCH_ASSOC);

        $superpowers = $db->prepare("INSERT INTO powersowners SET power_id = ?, owner_id = ? ");
        $superpowers->execute(array($power_id['id'], $user_id['id']));
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
      $stmt = $db->prepare("INSERT INTO members SET login = ?, pass = ?, name = ?, email = ?, date = ?, gender = ?, limbs = ?, bio = ?, policy = ?");
      $stmt->execute(array($login, $hash, $name, $email, $date, $gender, $limbs, $bio, $policy));
      $user_id = $db->lastInsertId();
      foreach ($powers as $value) {
        $stmt = $db->prepare("SELECT id from superpowers2 WHERE name = ?");
        $stmt->execute(array($value));
        $power_id = $stmt->fetch(PDO::FETCH_ASSOC);

        $superpowers = $db->prepare("INSERT INTO powersowners SET power_id = ?, owner_id = ? ");
        $superpowers->execute(array($power_id['id'], $user_id));
      }
    } catch (PDOException $e) {
      print('Error : ' . $e->getMessage());
      exit();
    }
  }

  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: ./');
}
