<?php
session_start();
include 'db.php'; // Ваш файл з підключенням до бази даних

// Отримання фільмів
$query = "SELECT * FROM movies"; // Замініть 'movies' на назву вашої таблиці з фільмами
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Голосування за фільми</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
        }
        .movie {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
        }
        button {
            padding: 5px 10px;
        }
    </style>
</head>
<body>

<h1>Голосуйте за фільми</h1>

<?php while ($movie = $result->fetch_assoc()): ?>
    <div class="movie">
        <h2><?php echo htmlspecialchars($movie['title']); ?></h2>
        <form method="POST" action="process_vote.php">
            <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
            <button type="submit" name="vote" value="1">Голосувати за</button>
        </form>
    </div>
<?php endwhile; ?>

</body>
</html>
