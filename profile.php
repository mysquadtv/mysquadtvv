<?php
session_start();
include 'db.php'; // Підключення до бази даних

// Перевірка, чи користувач увійшов у систему
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Отримання даних користувача
$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$avatar = !empty($user['avatar']) ? $user['avatar'] : 'default-avatar.png'; // Перевірка наявності аватара

$message = ""; // Ініціалізація змінної повідомлення

// Обробка завантаження аватара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $uploadDir = 'avatars/';
    $avatarName = basename($_FILES['avatar']['name']);
    $avatarPath = $uploadDir . $avatarName;

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatarPath)) {
        $updateQuery = "UPDATE users SET avatar = ? WHERE username = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ss", $avatarPath, $username);
        $stmt->execute();
        $message = "Аватар успішно завантажено.";
    } else {
        $message = "Помилка завантаження аватара.";
    }

    // Перенаправлення після обробки
    header("Location: profile.php?message=" . urlencode($message));
    exit();
}

// Обробка видалення аватара
if (isset($_POST['delete_avatar'])) {
    $defaultAvatar = 'default-avatar.png';
    $updateQuery = "UPDATE users SET avatar = ? WHERE username = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ss", $defaultAvatar, $username);
    $stmt->execute();
    $avatar = $defaultAvatar; // Оновити відображення аватара
    $message = "Аватар успішно видалено.";

    // Перенаправлення після обробки
    header("Location: profile.php?message=" . urlencode($message));
    exit();
}

// Обробка завантаження відео
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video'])) {
    $title = $_POST['title'] ?? 'Untitled';
    $description = $_POST['description'] ?? '';
    $uploadDir = 'uploads/';
    $videoName = basename($_FILES['video']['name']);
    $videoPath = $uploadDir . $videoName;
    $uploaded_at = date('Y-m-d H:i:s');
    $user_id = $user['id'];
    $image_path = ''; // Додати порожнє значення або шлях до стандартного зображення, якщо потрібно

    if (move_uploaded_file($_FILES['video']['tmp_name'], $videoPath)) {
        $query = "INSERT INTO videos (title, description, video_path, uploaded_at, user_id, image_path) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $title, $description, $videoPath, $uploaded_at, $user_id, $image_path);
        if ($stmt->execute()) {
            $message = "Відео успішно завантажено.";
        } else {
            $message = "Помилка: " . $stmt->error;
        }
    } else {
        $message = "Помилка завантаження відео.";
    }

    // Перенаправлення після обробки
    header("Location: profile.php?message=" . urlencode($message));
    exit();
}

// Виведення повідомлення з GET параметра
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профіль користувача</title>
    <style>
        body {
            background-color: #2c2c2c;
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
            overflow-x: hidden;
            display: flex;
            justify-content: center;
            transition: background-color 0.5s;
        }

        .message-container {
            background-color: #444;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            animation: fadeIn 0.5s;
            width: 100%;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .profile-container {
            max-width: 400px;
            margin: 0 20px;
            padding: 15px;
            background-color: #333;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.7);
            transition: box-shadow 0.3s, transform 0.3s;
        }

        img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }

        img:hover {
            transform: scale(1.1) rotate(10deg);
        }

        input[type="file"], input[type="text"], textarea {
            margin: 10px 0;
            width: 100%;
            border-radius: 5px;
        }

        button {
            background-color: #ff7518;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 10px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #e36414;
            transform: scale(1.05);
        }

        .form-container {
            display: none;
        }

        .home-button {
            margin-bottom: 20px;
        }

        .home-button a {
            color: #fff;
            text-decoration: none;
            background-color: #444;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .home-button a:hover {
            background-color: #555;
        }

        .delete-button {
            background-color: #d9534f;
        }

        .delete-button:hover {
            background-color: #c9302c;
        }

        .button-container {
            margin: 20px 0;
        }
    </style>
    <script>
        function toggleAvatarForm() {
            const formContainer = document.getElementById('avatarForm');
            formContainer.style.display = 'block';
            document.getElementById('avatarInput').click();
        }

        function toggleVideoForm() {
            const formContainer = document.getElementById('videoForm');
            formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="message-container">
        <?php if (!empty($message)): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </div>

    <div class="profile-container">
        <div class="home-button">
            <a href="index.php">На головну</a>
        </div>

        <h1>Привіт, <?php echo htmlspecialchars($username); ?>!</h1>
        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar">

        <div class="button-container">
            <?php if ($avatar === 'default-avatar.png'): ?>
                <button onclick="toggleAvatarForm()">Завантажити аватар</button>
            <?php else: ?>
                <form action="profile.php" method="POST" style="display: inline;">
                    <button type="submit" name="delete_avatar" class="delete-button">Видалити аватар</button>
                </form>
            <?php endif; ?>
        </div>

        <div id="avatarForm" class="form-container" style="display: none;">
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <input type="file" id="avatarInput" name="avatar" accept="image/*" required style="display: none;" onchange="this.form.submit();">
            </form>
        </div>

        <div class="button-container">
            <button onclick="toggleVideoForm()">Завантажити відео</button>
        </div>

        <div id="videoForm" class="form-container">
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="Назва відео" required>
                <textarea name="description" placeholder="Опис відео"></textarea>
                <input type="file" name="video" accept="video/*" required>
                <button type="submit">Завантажити відео</button>
            </form>
        </div>
    </div>
</body>
</html>
