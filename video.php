<?php
session_start();
include 'db.php'; // Ваш файл з підключенням до бази даних

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Перевірка, чи передано video_id
if (isset($_GET['video_id']) && is_numeric($_GET['video_id'])) {
    $video_id = $_GET['video_id'];

    // Отримання відео з бази даних
    $videoQuery = "SELECT * FROM videos WHERE id = ?";
    $videoStmt = $conn->prepare($videoQuery);
    $videoStmt->bind_param("i", $video_id);
    $videoStmt->execute();
    $videoResult = $videoStmt->get_result();

    // Перевірка, чи знайдено відео
    if ($videoResult->num_rows > 0) {
        $video = $videoResult->fetch_assoc();
    } else {
        echo "Відео не знайдено.";
        exit;
    }
} else {
    echo "Некоректний ID відео.";
    exit;
}

// Обробка форми додавання коментаря
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $user_id = $_SESSION['user_id']; // Припустимо, що ID користувача зберігається в сесії
    $comment = $_POST['comment'];

    // Вставка коментаря в базу даних
    $insertQuery = "INSERT INTO comments (video_id, user_id, comment) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    if ($stmt) {
        $stmt->bind_param("iis", $video_id, $user_id, $comment);
        if ($stmt->execute()) {
            // Повернення на ту ж саму сторінку для оновлення коментарів
            header("Location: video.php?video_id=" . $video_id);
            exit;
        } else {
            echo "Помилка при додаванні коментаря: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Помилка підготовки запиту для вставки коментаря: " . $conn->error;
    }
}

// Обробка видалення коментаря
if (isset($_GET['delete_comment_id']) && is_numeric($_GET['delete_comment_id'])) {
    $delete_comment_id = $_GET['delete_comment_id'];

    // Видалення коментаря з бази даних
    $deleteQuery = "DELETE FROM comments WHERE id = ? AND user_id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    if ($deleteStmt) {
        $deleteStmt->bind_param("ii", $delete_comment_id, $_SESSION['user_id']);
        $deleteStmt->execute();
        $deleteStmt->close();
    } else {
        echo "Помилка підготовки запиту для видалення коментаря: " . $conn->error;
    }
}

// Отримання коментарів для відео
$commentsQuery = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE video_id = ?";
$commentsStmt = $conn->prepare($commentsQuery);
$commentsStmt->bind_param("i", $video_id);
$commentsStmt->execute();
$commentsResult = $commentsStmt->get_result();

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($video['title']); ?></title>
    <style>
        body {
            background-color: #1e1e1e;
            color: #f5f5f5;
            font-family: 'Arial', sans-serif;
            padding: 20px;
            text-align: center;
        }
        video {
            width: 100%;
            max-width: 640px;
            height: auto;
            border-radius: 15px;
            margin: 10px 0;
        }
        .comment-container {
            margin-top: 20px;
            background-color: rgba(50, 50, 50, 0.9);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: left;
        }
        .comment {
            margin-bottom: 10px;
        }
        textarea {
            width: 100%;
            height: 60px; /* Стандартна висота */
            padding: 10px;
            border-radius: 5px;
            border: none;
            resize: none; /* Вимкнути можливість зміни розміру */
            background-color: #2c2c2c;
            color: #fff;
        }
        button {
            background-color: #ffcc00;
            color: #2c2c2c;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            margin-top: 10px;
        }
        button:hover {
            background-color: #ffc107;
            transform: scale(1.05); /* Збільшення кнопки при наведенні */
        }
        .close-video-button {
            background-color: #ff4c4c; /* Червоний фон */
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            margin-top: 20px; /* Додати відступ */
            font-size: 16px; /* Змінити розмір шрифту */
        }
        .close-video-button:hover {
            background-color: #ff1a1a; /* Темніший червоний при наведенні */
            transform: scale(1.05); /* Збільшення кнопки при наведенні */
        }
        .close-video-button:active {
            transform: scale(0.95); /* Зменшення кнопки при натисканні */
        }
        .delete-comment-button {
            background-color: #ff4c4c;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-left: 10px;
        }
        .delete-comment-button:hover {
            background-color: #ff1a1a;
        }
        .emoji {
            cursor: pointer;
            font-size: 20px;
            margin: 0 5px;
            transition: transform 0.2s;
        }
        .emoji:hover {
            transform: scale(1.2); /* Збільшення смайлів при наведенні */
        }
    </style>
</head>
<body>

<h1><?php echo htmlspecialchars($video['title']); ?></h1>
<video controls>
    <source src="<?php echo htmlspecialchars($video['video_path']); ?>" type="video/mp4">
    Ваш браузер не підтримує відтворення відео.
</video>
<p>Опис: <?php echo htmlspecialchars($video['description']); ?></p>
<p>Завантажено: <?php echo htmlspecialchars($video['uploaded_at']); ?></p>

<!-- Коментарі -->
<div class="comment-container">
    <h3>Коментарі</h3>
    <div class="comments">
        <?php while ($comment = $commentsResult->fetch_assoc()): ?>
            <div class="comment">
                <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                <span><?php echo htmlspecialchars($comment['comment']); ?></span>
                <?php if ($_SESSION['user_id'] == $comment['user_id']): ?>
                    <a href="?video_id=<?php echo $video_id; ?>&delete_comment_id=<?php echo $comment['id']; ?>" class="delete-comment-button">Видалити</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Форма додавання коментаря -->
    <form method="post">
        <textarea name="comment" placeholder="Ваш коментар..."></textarea>
        <div>
            <span class="emoji" onclick="addEmoji('😊')">😊</span>
            <span class="emoji" onclick="addEmoji('😢')">😢</span>
            <span class="emoji" onclick="addEmoji('😂')">😂</span>
            <span class="emoji" onclick="addEmoji('😡')">😡</span>
            <span class="emoji" onclick="addEmoji('❤️')">❤️</span>
        </div>
        <button type="submit">Додати коментар</button>
    </form>
</div>

<a href="index.php" class="close-video-button">Закрити відео</a>

<script>
function addEmoji(emoji) {
    const textarea = document.querySelector('textarea[name="comment"]');
    textarea.value += emoji; // Додаємо смайлик до текстового поля
}
</script>

</body>
</html>

<?php
$conn->close(); // Закриття підключення до бази даних
?>
