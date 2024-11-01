<?php
session_start();
include 'db.php'; // Ваш файл з підключенням до бази даних

// Перевірка, чи користувач увійшов у систему
if (!isset($_SESSION['user_id'])) {
    die("Вам потрібно увійти в систему, щоб залишити коментар.");
}

// Перевірка, чи надіслано коментар
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $video_id = $_POST['video_id'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id']; // Отримуємо user_id з сесії

    // Запит на вставку коментаря
    $query = "INSERT INTO comments (video_id, comment, user_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $video_id, $comment, $user_id);

    if ($stmt->execute()) {
        // Перенаправлення назад до сторінки з відео
        header("Location: new_releases.php"); // Можливо, змініть на відповідну сторінку
        exit();
    } else {
        echo "Помилка: " . $stmt->error;
    }
}
?>

