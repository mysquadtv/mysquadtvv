<?php
session_start();
include 'db.php'; // Ваш файл з підключенням до бази даних

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Перевірка наявності користувача в базі даних
    $query = "SELECT * FROM users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Перевірка пароля
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id']; // Зберігаємо user_id в сесії

            // Перенаправлення на головну сторінку після успішного входу
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Неправильний пароль.');</script>";
        }
    } else {
        echo "<script>alert('Користувача з таким ім\'ям не існує.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Увійти</title>
    <style>
        body {
            background-color: #1e1e1e;
            color: #ffcc00; /* Жовтий текст для Хелловіна */
            font-family: 'Courier New', Courier, monospace; /* Зловісний шрифт */
            overflow: hidden;
            position: relative;
        }

        .container {
            position: relative;
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .rain {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .drop {
            position: absolute;
            width: 2px;
            height: 20px;
            background: rgba(255, 255, 255, 0.6);
            bottom: 100%;
            animation: fall linear;
            opacity: 0.6;
        }

        @keyframes fall {
            0% { transform: translateY(0); }
            100% { transform: translateY(100vh); }
        }

        .form-container {
            background-color: rgba(30, 30, 30, 0.8);
            padding: 20px;
            text-align: center;
            z-index: 1;
            width: 300px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            border: 2px solid #ffcc00; /* Жовта рамка */
            border-radius: 10px; /* Краї з округленням */
        }

        h2 {
            margin: 0 0 20px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 0;
        }

        button {
            padding: 10px;
            background-color: #ff4500; /* Оранжевий колір для Хелловіна */
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 0;
            width: 100%;
        }

        button:hover {
            background-color: #e63900; /* Темніший оранжевий при наведенні */
        }

        .links {
            margin-top: 15px;
        }

        .link {
            color: #ffcc00; /* Жовтий для посилань */
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="rain" id="rain"></div>
    <div class="form-container">
        <h2>Увійти в акаунт</h2>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Ім'я користувача" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Увійти</button>
        </form>
        <div class="links">
            <a class="link" href="register.php">Зареєструвати акаунт</a><br>
            <a class="link" href="index.php">Повернутися на головну</a>
        </div>
    </div>
</div>

<script>
    function createRain() {
        const rainContainer = document.getElementById('rain');
        const numDrops = 30; // Зменшена кількість крапель

        for (let i = 0; i < numDrops; i++) {
            const drop = document.createElement('div');
            drop.className = 'drop';
            drop.style.left = Math.random() * 100 + 'vw'; // Випадкова позиція по горизонталі
            drop.style.animationDuration = Math.random() * 1 + 1 + 's'; // Випадкова тривалість анімації
            drop.style.animationDelay = Math.random() * 2 + 's'; // Випадкова затримка перед анімацією
            rainContainer.appendChild(drop);

            // Видалити краплю після завершення анімації
            drop.addEventListener('animationend', () => {
                drop.remove();
            });
        }
    }

    setInterval(createRain, 300); // Створюємо нові краплі кожні 300 мс
</script>

</body>
</html>
