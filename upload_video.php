<?php
session_start();
include 'db.php'; // Підключення до бази даних

// Перевірка, чи користувач увійшов у систему
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Отримання даних користувача з сесії
$username = $_SESSION['username'];
$query = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

// Обробка завантаження відео
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video'])) {
    $title = $_POST['title'] ?? 'Без назви';
    $description = $_POST['description'] ?? '';

    $uploadDir = 'uploads/';
    $videoName = basename($_FILES['video']['name']);
    $videoPath = $uploadDir . $videoName;

    // Перевірка розміру файлу (максимум 100 МБ)
    if ($_FILES['video']['size'] > 3000 * 1024 * 1024) {
        echo "Розмір відео не повинен перевищувати 100 МБ.";
        exit();
    }

    // Перевірка формату файлу
    $allowedTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/mkv'];
    $fileType = mime_content_type($_FILES['video']['tmp_name']);

    if (!in_array($fileType, $allowedTypes)) {
        echo "Неправильний формат файлу. Дозволені тільки MP4, AVI, MOV, MKV.";
        exit();
    }

    // Завантаження файлу на сервер
    if (move_uploaded_file($_FILES['video']['tmp_name'], $videoPath)) {
        // Додавання інформації про відео в базу даних
        $query = "INSERT INTO videos (title, description, video_path, user_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $title, $description, $videoPath, $user_id);

        if ($stmt->execute()) {
            echo "Відео успішно завантажено.";
        } else {
            echo "Помилка: " . $stmt->error;
        }
    } else {
        echo "Помилка завантаження відео.";
    }
}
?>
