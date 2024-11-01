<?php
session_start();
include 'db.php'; // Ваш файл з підключенням до бази даних

// Отримання відео з категорії "новинки"
$query = "SELECT * FROM videos ORDER BY uploaded_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Нові Відео</title>
    <style>
        body {
            background-color: #1e1e1e;
            color: #f5f5f5;
            font-family: 'Arial', sans-serif;
            padding: 20px;
            text-align: center;
            overflow-x: hidden;
        }

        img.logo {
            width: 200px;
            margin-bottom: 20px;
            animation: fadeIn 1s;
        }

        .main-button {
            background-color: #ffcc00;
            color: #2c2c2c;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2em;
            text-decoration: none;
            margin: 20px auto;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .main-button:hover {
            background-color: #ffc107;
        }

        .video-container {
            display: inline-block;
            margin: 10px;
            background-color: rgba(30, 30, 30, 0.9);
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .video-container.dimmed {
            opacity: 0.5;
        }

        .video-container.expanded {
            opacity: 1;
        }

        video {
            width: 100%;
            max-width: 640px;
            height: auto;
            border-radius: 15px;
            margin: 10px 0;
        }

        .video-title {
            font-size: 1.5em;
            color: #ffcc00;
            margin-bottom: 10px;
            transition: color 0.3s;
            cursor: pointer;
            text-decoration: none;
        }

        .video-title:hover {
            color: #ffc107;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>

<img src="logo.png" alt="Логотип" class="logo">
<a href="index.php" class="main-button">Головна</a>

<?php if ($result->num_rows > 0): ?>
    <?php while ($video = $result->fetch_assoc()): ?>
        <div class="video-container dimmed">
            <!-- Додаємо посилання на окрему сторінку для кожного відео -->
            <a href="video.php?video_id=<?php echo $video['id']; ?>" class="video-title"><?php echo htmlspecialchars($video['title']); ?></a>
            <video controls style="display: none;">
                <source src="<?php echo htmlspecialchars($video['video_path']); ?>" type="video/mp4">
                Ваш браузер не підтримує відтворення відео.
            </video>
            <p>Завантажено: <?php echo htmlspecialchars($video['uploaded_at']); ?></p>
            <p>Опис: <?php echo htmlspecialchars($video['description']); ?></p>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>Немає доступних відео.</p>
<?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>
