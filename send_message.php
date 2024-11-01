<?php
session_start();
include 'db.php'; // Підключення до бази даних

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['username'])) {
    $sender = $_SESSION['username'];
    $recipient = $_POST['recipient'];
    $message = $_POST['message'];

    // Перевірка, чи отримувач не порожній
    if (!empty($recipient) && !empty($message)) {
        $query = "INSERT INTO messages (sender, recipient, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $sender, $recipient, $message);
        $stmt->execute();
        
        // Повернення на сторінку профілю
        header("Location: profile.php");
        exit();
    } else {
        echo "<script>alert('Будь ласка, заповніть усі поля.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Надіслати повідомлення</title>
</head>
<body>

<h2>Надіслати повідомлення</h2>
<form action="send_message.php" method="POST">
    <input type="text" name="recipient" placeholder="Отримувач" required>
    <textarea name="message" placeholder="Ваше повідомлення" required></textarea>
    <button type="submit">Надіслати</button>
</form>

<a href="profile.php">Повернутися на профіль</a>

</body>
</html>
