<!DOCTYPE html>
<html lang="">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8" />
    <link rel="stylesheet" href="./style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
    <title>Zadanie 6</title>
</head>

<body>
    <?php
    if (!empty($messages)) {
        print('<div id="messages">');
        foreach ($messages as $message) {
            print($message);
        }
        print('</div>');
    }
    ?>
    <div class="form-container">
        <form method="POST" action="">
            <a href="login.php?logout=1" <?php (empty($_SESSION['uid'])) ? print('style="display:none"') : print('style="display:inline-block"'); ?>>Выйти</a>
            <div class="name-block">
                <span class="input-group-text block-title">Имя</span>
                <input type="text" class="form-control" name="name" placeholder="Ваше имя" <?php if ($errors['name']) {
                                                                                                print 'class="error"';
                                                                                            } ?> value="<?php print $values['name']; ?>" />
            </div>
            <div class="email-block">
                <span class="input-group-text block-title">Email</span>
                <input type="text" class="form-control" name="email" placeholder="example@mail.ru" <?php if ($errors['email']) {
                                                                                                        print 'class="error"';
                                                                                                    } ?> value="<?php print $values['email']; ?>" />
            </div>
            <div class="birth-block">
                <span class="input-group-text block-title">Дата рождения</span>
                <input type="date" class="form-control" name="date" <?php if ($errors['date']) {
                                                                        print 'class="error"';
                                                                    } ?> value="<?php print $values['date']; ?>" />
            </div>
            <div id="gender-block">
                <span class="input-group-text block-title">Пол</span>
                <div class="radios">
                    <div class="male-radio">
                        <input class="form-check-input" type="radio" name="gender" value="m" <?php if ($values['gender'] == 'm') {
                                                                                                    print 'checked';
                                                                                                }; ?> />
                        <label class="form-check-label" for="male">Мужской</label>
                    </div>
                    <div class="female-radio">
                        <input class="form-check-input" type="radio" name="gender" value="f" <?php if ($values['gender'] == 'f') {
                                                                                                    print 'checked';
                                                                                                }; ?> />
                        <label class="form-check-label" for="female">Женский</label>
                    </div>
                </div>
            </div>
            <div id="limbs-block">
                <span class="input-group-text block-title">Конечности</span>
                <div class="radios">
                    <div class="limbs-radio">
                        <input class="form-check-input" type="radio" name="limbs" value="1" <?php if ($values['limbs'] == '1') {
                                                                                                print 'checked';
                                                                                            }; ?> />
                        <label class="form-check-label">1</label>
                    </div>
                    <div class="limbs-radio">
                        <input class="form-check-input" type="radio" name="limbs" value="2" <?php if ($values['limbs'] == '2') {
                                                                                                print 'checked';
                                                                                            }; ?> />
                        <label class="form-check-label">2</label>
                    </div>
                    <div class="limbs-radio">
                        <input class="form-check-input" type="radio" name="limbs" value="3" <?php if ($values['limbs'] == '3') {
                                                                                                print 'checked';
                                                                                            }; ?> />
                        <label class="form-check-label">3</label>
                    </div>
                    <div class="limbs-radio">
                        <input class="form-check-input" type="radio" name="limbs" value="4" <?php if ($values['limbs'] == '4') {
                                                                                                print 'checked';
                                                                                            }; ?> />
                        <label class="form-check-label">Все</label>
                    </div>
                </div>
            </div>
            <div class="superpowers-block">
                <div class="block-title">Суперспособности</div>
                <select class="form-select form-select-lg mb-2" name="powers[]" multiple>
                    <option value="inf" <?php $arr = explode(',', $values['powers']);
                                        if ($arr != '') {
                                            foreach ($arr as $value) {
                                                if ($value == "inf") {
                                                    print 'selected';
                                                }
                                            }
                                        }
                                        ?>>Локи(бессмертие)</option>
                    <option value="through" <?php $arr = explode(',', $values['powers']);
                                            if ($arr != '') {
                                                foreach ($arr as $value) {
                                                    if ($value == "through") {
                                                        print 'selected';
                                                    }
                                                }
                                            }
                                            ?>>Вижн(сквозь стены)</option>
                    <option value="levitation" <?php $arr = explode(',', $values['powers']);
                                                if ($arr != '') {
                                                    foreach ($arr as $value) {
                                                        if ($value == "levitation") {
                                                            print 'selected';
                                                        }
                                                    }
                                                }
                                                ?>>Доктор Стрэндж(левитация)</option>
                </select>
            </div>
            <div class="input-group">
                <textarea class="form-control" placeholder="Расскажите о себе..." name="bio" <?php if ($errors['bio']) {
                                                                                                    print 'class="error"';
                                                                                                } ?>><?php print $values['bio']; ?></textarea>
            </div>
            <div class="form-check" id="policy">
                <input class="form-check-input" type="checkbox" value="y" id="policy" name="policy" checked />
                <label class="form-check-label" for="policy">Ознакомлен с <a href="./login.php">политикой конфиденциальности</a>.</label>
            </div>
            <button class="btn btn-primary" type="submit" id="send-btn">Отправить</button>
        </form>
    </div>
</body>

</html>
