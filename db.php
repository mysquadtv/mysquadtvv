<?php
$host = '127.127.127.127';
$db = 'mycinema';
$user = 'root';
$password = '';

// Підключення до бази даних
$conn = new mysqli($host, $user, $password, $db);

// Перевірка з'єднання
if ($conn->connect_error) {
    die("Помилка з'єднання: " . $conn->connect_error);
}
?>

