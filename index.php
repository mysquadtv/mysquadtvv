<?php
session_start();
include 'db.php'; // Ваш файл з підключенням до бази даних

// Отримання інформації про користувача, якщо він авторизований
$user_avatar = 'default-avatar.png'; // Значення за замовчуванням
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $query = "SELECT avatar FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $user_avatar = $user['avatar'] ? $user['avatar'] : 'default-avatar.png';
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Головна сторінка</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap');

        body {
            background-color: #242424; /* Темніший фон */
            color: #e0e0e0; /* Світліший текст */
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #444; /* Темний заголовок */
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        }

        .logo {
            text-decoration: none;
            font-size: 28px; /* Збільшений розмір для логотипу */
            color: #ff6f61; /* Яскравий колір для логотипу */
            font-weight: 600;
        }

        nav {
            display: flex;
            gap: 15px;
            align-items: center; /* Вирівнювання по центру */
        }

        nav a {
            color: #e0e0e0; /* Світлий текст для навігації */
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #ff6f61; /* Фон при наведенні */
            color: #ffffff; /* Білий текст при наведенні */
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #ff6f61;
            object-fit: cover;
        }

        .clock {
            color: #e0e0e0; /* Колір тексту годинника */
            font-size: 16px; /* Розмір тексту годинника */
            font-weight: 600; /* Жирний шрифт для годинника */
        }

        .container {
            flex: 1;
            display: flex;
            padding: 20px;
            justify-content: center; /* Центрування контенту */
        }

        .content {
            width: 70%;
            padding: 20px;
            background-color: #333; /* Фон для контенту */
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
            margin-right: 20px; /* Відступ праворуч для бокової панелі */
        }

        .sidebar {
            width: 25%; /* Фіксована ширина для бокової панелі */
            padding: 20px;
            background-color: #444; /* Фон для бокової панелі */
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        }

        h2 {
            color: #ff6f61; /* Яскравий заголовок для категорій */
            margin-bottom: 15px;
            font-size: 24px; /* Збільшений розмір заголовка */
        }

        .categories {
            display: flex;
            flex-direction: column;
            gap: 10px; /* Відступ між категоріями */
        }

        .category {
            background-color: #555; /* Фон для категорій */
            color: #ffffff;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s;
            cursor: pointer; /* Зміна курсору для вказівників */
        }

        .category:hover {
            background-color: #ff6f61; /* Фон при наведенні */
            color: #ffffff; /* Білий текст при наведенні */
        }

        footer {
            padding: 15px;
            text-align: center;
            background-color: #444; /* Темний фон для футера */
            color: #e0e0e0;
            margin-top: auto; /* Футер завжди внизу */
        }
    </style>
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
        }

        setInterval(updateClock, 1000); // Оновлення годинника кожну секунду
        window.onload = updateClock; // Показати годинник відразу при завантаженні
    </script>
</head>
<body>

<header>
    <a href="index.php" class="logo">Онлайн Кінотеатр</a>
    <nav>
        <div id="clock" class="clock"></div>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="profile.php">Профіль</a>
            <a href="logout.php">Вийти</a>
            <img src="<?php echo htmlspecialchars($user_avatar); ?>" alt="Avatar" class="user-avatar">
        <?php else: ?>
            <a href="login.php">Увійти</a>
            <a href="register.php">Зареєструватися</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <div class="content">
        <h2>Ласкаво просимо до нашого онлайн кінотеатру!</h2>
        <p>Тут ви знайдете безліч фільмів, які можете переглядати в будь-який час.</p>
    </div>

    <div class="sidebar">
        <h2>Категорії</h2>
        <div class="categories">
            <div class="category" onclick="window.location.href='new_releases.php'">Новинки</div>
            <div class="category">Драми</div>
            <div class="category">Комедії</div>
            <div class="category">Фантастика</div>
            <div class="category">Пригоди</div>
            <div class="category">Аніме</div>
        </div>
    </div>
</div>

<footer>
    <p>© 2024 Онлайн Кінотеатр. Усі права захищені.</p>
</footer>

</body>
</html>
