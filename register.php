<?php
session_start();
include 'db.php'; // Ваш файл з підключенням до бази даних

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Хешування пароля
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Перевірка, чи існує користувач з таким ім'ям
    $query = "SELECT * FROM users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Додавання нового користувача
        $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            header("Location: profile.php");
            exit();
        } else {
            echo "<script>alert('Сталася помилка. Спробуйте ще раз.');</script>";
        }
    } else {
        echo "<script>alert('Користувач з таким ім\'ям вже існує.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Реєстрація</title>
    <style>
        body {
            background-color: #1e1e1e; /* Темний фон */
            color: #ffffff; /* Білий текст */
            font-family: 'Courier New', Courier, monospace; /* Шрифт Хелловіна */
            overflow: hidden;
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
            background: rgba(255, 255, 255, 0.6); /* Колір дощу */
            bottom: 100%;
            animation: fall linear;
            opacity: 0.6;
        }

        @keyframes fall {
            0% { transform: translateY(0); }
            100% { transform: translateY(100vh); }
        }

        .form-container {
            background-color: rgba(50, 50, 50, 0.9); /* Темніший фон для форми */
            padding: 20px;
            text-align: center;
            z-index: 1;
            width: 300px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            border-radius: 10px; /* Краще закруглені кути */
        }

        h2 {
            margin: 0 0 20px;
            color: #ffcc00; /* Жовтий заголовок */
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px; /* Заокруглені кути */
            background-color: #333333; /* Темний фон полів */
            color: #ffffff; /* Білий текст */
        }

        button {
            padding: 10px;
            background-color: #ff5733; /* Яскраво червоний */
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px; /* Заокруглені кути */
            width: 100%;
        }

        button:hover {
            background-color: #c70039; /* Трохи темніший червоний при наведенні */
        }

        .links {
            margin-top: 15px;
        }

        .link {
            color: #007bff; /* Синій колір для посилань */
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline; /* Підкреслення при наведенні */
        }
    </style>
</head>
<body>

<div class="container">
    <div class="rain" id="rain"></div>
    <div class="form-container">
        <h2>Реєстрація</h2>
        <form action="register.php" method="POST">
            <input type="text" name="username" placeholder="Ім'я користувача" required>
            <input type="email" name="email" placeholder="Електронна пошта" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Зареєструватися</button>
        </form>
        <div class="links">
            <a class="link" href="login.php">Увійти в акаунт</a><br>
            <a class="link" href="index.php">Повернутися на головну</a>
        </div>
    </div>
</div>

<script>
    function createRain() {
        const rainContainer = document.getElementById('rain');
        const numDrops = 20; // Зменшено число крапель

        for (let i = 0; i < numDrops; i++) {
            const drop = document.createElement('div');
            drop.className = 'drop';
            drop.style.left = Math.random() * 100 + 'vw'; // Випадкова позиція по горизонталі
            drop.style.animationDuration = Math.random() * 0.5 + 0.5 + 's'; // Випадкова тривалість анімації
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
