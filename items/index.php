<?php 
session_start();
require_once '../config/config.php';
require_once '../modules/user/getUser.php';
define('BASE_PATH', getBackPath(__DIR__));

if (verifyAuth($pdo) === false) {
    header('Location: ' . BASE_PATH . 'login/');
    exit;
}

if (verificationBusiness($pdo, $_SESSION['user_id']) === false) {
    header('Location: ' . BASE_PATH . 'profile/reg/');
    exit;
}

$getUserClass = new getUser($_SESSION['session_token'] ?? null, $pdo);
$userInfo = $getUserClass->get();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuSync | Товары</title>
    <link rel="stylesheet" href="../sources/css/pages/items/index.css">
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
                    <button onclick="window.location = '../profile/'">
                        <?php if (isset($userInfo['username'])): ?>
                            <?php echo htmlspecialchars($userInfo['username']) ?>
                        <?php else: ?>
                            Профиль
                        <?php endif ?>
                    </button>
                </div>
            </div>

            <div class="right--center--panel">
                <div class="form-add">
                    <div class="form-add--header">
                        <h2>Добавить товар</h2>
                    </div>

                    <div class="form-add--body">
                        <fieldset>
                            <legend>Название товара</legend>
                            <input type="text" id="item_name" minlength="4" maxlength="255" placeholder="Название">
                        </fieldset>

                        <fieldset>
                            <legend>Описание товара</legend>
                            <textarea type="text" id="item_description" minlength="4" maxlength="10000" placeholder="Описание"></textarea>
                        </fieldset>

                        <fieldset>
                            <legend>Артикул</legend>
                            <input type="text" id="item_art" minlength="4" maxlength="255" placeholder="Артикул">
                        </fieldset>

                        <fieldset>
                            <legend>Категория</legend>
                            <input type="text" id="item_category" minlength="4" maxlength="100" placeholder="Категория">
                        </fieldset>

                        <fieldset>
                            <legend>Себестоимость</legend>
                            <input type="number" id="item_cost" minlength="2" maxlength="20" placeholder="Себестоимость, число (до 2 знаков после запятой)">
                        </fieldset>

                        <fieldset>
                            <legend>Розничная цена</legend>
                            <input type="number" id="item_retail" minlength="2" maxlength="20" placeholder="Розничная цена, число (до 2 знаков после запятой)">
                        </fieldset>

                        <fieldset>
                            <legend>Производитель</legend>
                            <input type="text" id="item_manufacturer" minlength="4" maxlength="255" placeholder="Производитель">
                        </fieldset>

                        <fieldset>
                            <legend>Остаток</legend>
                            <input type="number" id="item_remain" minlength="4" maxlength="255" placeholder="Остаток, число">
                        </fieldset>

                        <fieldset>
                            <legend>Единица измерения</legend>
                            <input type="text" id="item_unit" minlength="4" maxlength="255" placeholder="Единица измерения (л., кв. м., куб. см. или др.)">
                        </fieldset>

                        <fieldset>
                            <legend>Статус</legend>
                            <input type="text" id="item_status" minlength="4" maxlength="255" placeholder="Статус, например, 'под заказ'">
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>