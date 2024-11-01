<?php
session_start();
include 'db.php'; // Ваш файл з підключенням до бази даних

// Отримання ID відео з URL
if (isset($_GET['id'])) {
    $video_id = $_GET['id'];

    // Запит до бази даних для отримання інформації про відео
    $query = "SELECT * FROM videos WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $video_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $video = $result->fetch_assoc();

    if (!$video) {
        echo "Відео не знайдено.";
        exit;
    }
} else {
    echo "ID відео не вказано.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($video['title']); ?></title>
    <style>
        body {
            background-color: #242424;
            color: #e0e0e0;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        h1 {
            color: #ff6f61; /* Колір заголовка */
        }
        video {
            width: 100%;
            max-width: 600px; /* Максимальна ширина відеоплеєра */
            border-radius: 10px;
            margin-bottom: 20px; /* Відступ знизу */
        }
        p {
            max-width: 600px; /* Максимальна ширина опису */
            text-align: center;
        }
    </style>
</head>
<body>

<h1><?php echo htmlspecialchars($video['title']); ?></h1>
<video controls>
    <source src="<?php echo 'uploads/videos/' . htmlspecialchars($video['video_file']); ?>" type="video/mp4">
    Ваш браузер не підтримує відтворення відео.
</video>
<p><?php echo htmlspecialchars($video['description']); ?></p>

</body>
</html>
