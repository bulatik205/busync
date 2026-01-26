<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация | BuSync</title>
    <link rel="stylesheet" href="../sources/css/pages/login-reg/index.css">
</head>

<body>
    <main>
        <div class="main--body">
            <div class="main--body--header">
                <h1>Регистрация</h1>
            </div>

            <form action="" method="post">
                <div class="form--element">
                    <label for="username">Логин</label>
                    <input type="text" name="username" id="username" required minlength="4" maxlength="128">
                </div>

                <div class="form--element">
                    <label for="password">Пароль</label>
                    <input type="password" name="password" id="password" required minlength="6" maxlength="128">
                </div>

                <button type="submit">Регистрация</button>
            </form>

            <p>Есть аккаунт? <a href="../login/">Войти</a></p>
            <p>Забыли пароль? <a href="../ops/">Восстановить</a></p>
        </div>
    </main>
</body>

</html>