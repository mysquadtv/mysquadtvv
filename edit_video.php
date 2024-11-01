<?php
session_start();
include 'db.php'; // Підключення до бази даних

// Перевірка, чи користувач увійшов у систему
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Отримання user_id з бази даних
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
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Збереження відео
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["video"]["name"]);
    move_uploaded_file($_FILES["video"]["tmp_name"], $target_file);

    // Додавання відео до бази даних
    $insertQuery = "INSERT INTO videos (title, description, video_path, user_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sssi", $title, $description, $target_file, $user_id);
    
    if ($stmt->execute()) {
        echo "Відео успішно завантажено.";
    } else {
        echo "Помилка: " . $stmt->error;
    }
}

// Видалення відео
if (isset($_GET['delete_video_id'])) {
    $delete_video_id = $_GET['delete_video_id'];

    // Видалення відео з бази даних
    $deleteQuery = "DELETE FROM videos WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("ii", $delete_video_id, $user_id);

    if ($stmt->execute()) {
        echo "Відео успішно видалено.";
        header("Location: " . $_SERVER['PHP_SELF']); // Перенаправлення на ту ж саму сторінку
        exit();
    } else {
        echo "Помилка: " . $stmt->error;
    }
}

// Перевірка, чи надано video_id для редагування
$video = null; // Ініціалізація змінної для відео
if (isset($_GET['video_id'])) {
    $video_id = $_GET['video_id'];

    // Отримання відео для редагування
    $query = "SELECT * FROM videos WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $video_id, $user_id);
    $stmt->execute();
    $video = $stmt->get_result()->fetch_assoc();

    if (!$video) {
        echo "Відео не знайдено або у вас немає прав для редагування.";
        exit();
    }
}

// Обробка редагування відео
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_video'])) {
    $video_id = $_POST['video_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Оновлення відео в базі даних
    $updateQuery = "UPDATE videos SET title = ?, description = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssii", $title, $description, $video_id, $user_id);
    
    if ($stmt->execute()) {
        echo "Відео успішно оновлено.";
        header("Location: " . $_SERVER['PHP_SELF']); // Перенаправлення на ту ж саму сторінку
        exit();
    } else {
        echo "Помилка: " . $stmt->error;
    }
}

// Отримання відео користувача
$query = "SELECT * FROM videos WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$videos = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ваші відео</title>
    <style>
        body {
            background-color: #2c2c2c;
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
        }

        .video-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #444;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
            margin-bottom: 20px;
        }

        h1, h2 {
            color: #ff7518;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        a {
            color: #ff7518;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        input, textarea {
            width: 100%;
            margin: 10px 0;
            border: 1px solid #ff7518;
            border-radius: 5px;
            padding: 10px;
            background-color: #222;
            color: #fff;
        }

        button {
            background-color: #ff7518;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #e36414;
        }
    </style>
</head>
<body>
    <div class="video-container">
        <h1>Ваші відео</h1>
        <?php while ($video_item = $videos->fetch_assoc()): ?>
            <div>
                <h2><?php echo htmlspecialchars($video_item['title']); ?></h2>
                <p><?php echo htmlspecialchars($video_item['description']); ?></p>
                <video width="300" controls>
                    <source src="<?php echo htmlspecialchars($video_item['video_path']); ?>" type="video/mp4">
                    Ваш браузер не підтримує відео.
                </video>
                <br>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?video_id=<?php echo $video_item['id']; ?>">Редагувати</a> |
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete_video_id=<?php echo $video_item['id']; ?>" style="color: red;">Видалити</a>
            </div>
            <hr>
        <?php endwhile; ?>
        
        <h2>Завантажити нове відео</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Назва відео" required>
            <textarea name="description" placeholder="Опис відео"></textarea>
            <input type="file" name="video" accept="video/mp4" required>
            <button type="submit">Завантажити відео</button>
        </form>

        <?php if (isset($video)): ?>
            <h2>Редагувати відео</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <input type="hidden" name="video_id" value="<?php echo htmlspecialchars($video['id']); ?>">
                <input type="text" name="title" placeholder="Назва відео" value="<?php echo htmlspecialchars($video['title']); ?>" required>
                <textarea name="description" placeholder="Опис відео"><?php echo htmlspecialchars($video['description']); ?></textarea>
                <button type="submit" name="edit_video">Оновити відео</button>
            </form>
        <?php endif; ?>

        <br>
        <a href="index.php">Повернутись на головну сторінку</a>
    </div>
</body>
</html>
