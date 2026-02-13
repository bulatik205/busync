<?php
session_start();
require_once '../config/config.php';
require_once '../modules/user/getUser.php';
define('BASE_PATH', getBackPath(__DIR__));

require_once '../modules/user/getApiTokens.php';

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

$getApiTokens = new getApiTokens($_SESSION['user_id'] ?? null, $pdo);
$getApiTokensResult = $getApiTokens->get();
$apiTokens = $getApiTokensResult['keys'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/busync/api/v1/getItems/?limit=50");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "API-key: " . $apiTokens[0]['api_key']
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$getItemsResult = json_decode(curl_exec($ch), true);

$itemsFields = $getItemsResult['fields'];
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
                <fieldset class="item--body--content success" id="items-success"></fieldset>

                <fieldset class="item--body--content error" id="items-error"></fieldset>

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

                        <button onclick="createItem()">Создать</button>
                    </div>
                </div>

                <div class="items">
                    <div class="items--body">
                        <h2>Все товары</h2>

                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th>Артикул</th>
                                    <th>Категория</th>
                                    <th>Остаток</th>
                                    <th>Розница</th>
                                    <th>Себестоимость</th>
                                    <th>Производитель</th>
                                    <th>Единица измерения</th>
                                    <th>Статус</th>
                                    <th>Описание</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($itemsFields) && is_array($itemsFields)): ?>
                                    <?php foreach ($itemsFields as $item): ?>
                                        <tr ondblclick="this.classList.toggle('highlight')" style="cursor: pointer;">
                                            <td><?php echo htmlspecialchars($item['id'] ?? '—') ?></td>
                                            <td><?php echo htmlspecialchars($item['item_name'] ?? '—') ?></td>
                                            <td><?php echo htmlspecialchars($item['item_art'] ?? '—') ?></td>
                                            <td><?php echo htmlspecialchars($item['item_category'] ?? '—') ?></td>
                                            <td><?php echo htmlspecialchars($item['item_remain'] ?? '—') ?></td>
                                            <td><?php echo htmlspecialchars($item['item_retail'] ?? '—') ?></td>
                                            <td><?php echo htmlspecialchars($item['item_cost'] ?? '—') ?></td>
                                            <td><?php echo htmlspecialchars($item['item_manufacturer'] ?? '—') ?></td>
                                            <td><?php echo htmlspecialchars($item['item_unit'] ?? '—') ?></td>
                                            <td><?php echo htmlspecialchars($item['item_status'] ?? '—') ?></td>
                                            <td><?php echo htmlspecialchars($item['item_description'] ?? '—') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" style="text-align: center; padding: 40px;">Товаров пока нет</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../sources/js/items/ajax.js"></script>
    <script>
        const API_KEY = '<?php echo htmlspecialchars($apiTokens[0]['api_key']) ?>';
    </script>
</body>
</html>