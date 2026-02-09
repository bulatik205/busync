<?php
session_start();
require_once '../config/config.php';
define('BASE_PATH', getBackPath(__DIR__));
define('LOCALHOST_API_PATH', BASE_PATH . API_PATH);

if (verifyAuth($pdo) === false) {
    header('Location: ' . BASE_PATH . 'login/');
    exit;
}

if (verificationBusiness($pdo, $_SESSION['user_id']) === false) {
    header('Location: ' . BASE_PATH . 'profile/reg/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuSync | Ваш профиль</title>
    <link rel="stylesheet" href="../sources/css/pages/profile/index.css">
</head>

<body>
    <main>
        <div class="left">
            <nav>
                <button><img src="../sources/images/system/home.png" alt="">Главная</button>
                <button><img src="../sources/images/system/shop.png" alt="">Товары</button>
                <button><img src="../sources/images/system/deal.png" alt="">Сделки</button>
                <button><img src="../sources/images/system/contacts.png" alt="">Конткты</button>
            </nav>
        </div>

        <div class="right">
            <div class="right--top--panel">
                <form class="right--top--panel--search">
                    <input type="text" required placeholder="Поиск по аккаунту">
                    <button>Поиск</button>
                </form>

                <div class="right--top--panel--profile">
                    <button>
                        <?php if (isset($userInfo['username'])): ?>
                            <?php echo htmlspecialchars($userInfo['username']) ?>
                        <?php else: ?>
                            Профиль
                        <?php endif ?>
                    </button>
                </div>
            </div>

            <div class="right--center--panel">
                <div class="profile--header">
                    <h1>Ваши настройки</h1>
                </div>

                <div class="profile--body">
                    <div class="custom-fields">
                        <fieldset class="profile--body--content">
                            <legend>Имя</legend>
                            <input type="text" name="first_name" id="first_name">
                        </fieldset>

                        <fieldset class="profile--body--content">
                            <legend>Фамилия</legend>
                            <input type="text" name="last_name" id="last_name">
                        </fieldset>

                        <fieldset class="profile--body--content">
                            <legend>Отчество</legend>
                            <input type="text" name="father_name" id="father_name">
                        </fieldset>

                        <fieldset class="profile--body--content">
                            <legend>Тип бизнеса</legend>

                            <div class="profile--body--content--radio">
                                <input type="radio" name="business_type" id="business_type_ip">
                                <label for="business_type_ip">ИП</label>
                            </div>

                            <div class="profile--body--content--radio">
                                <input type="radio" name="business_type" id="business_type_ooo">
                                <label for="business_type_ooo">ООО</label>
                            </div>

                            <div class="profile--body--content--radio">
                                <input type="radio" name="business_type" id="business_type_sz">
                                <label for="business_type_sz">Самозанятый</label>
                            </div>

                            <div class="profile--body--content--radio">
                                <input type="radio" name="business_type" id="business_type_other">
                                <label for="business_type_other">Другой</label>
                            </div>
                        </fieldset>

                        <fieldset class="profile--body--content">
                            <legend>Сайт бизнеса</legend>
                            <input type="url" name="business_site" id="business_site">
                        </fieldset>

                        <fieldset class="profile--body--content">
                            <legend>Телефон бизнеса</legend>
                            <input type="number" name="phone" id="phone">
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const API_PATH = '<?php echo LOCALHOST_API_PATH ?>';
    </script>
</body>

</html>