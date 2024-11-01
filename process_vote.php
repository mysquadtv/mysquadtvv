<?php
session_start();
include 'db.php'; // Ваш файл з підключенням до бази даних

if (isset($_POST['movie_id']) && isset($_SESSION['user_id'])) {
    $movie_id = $_POST['movie_id'];
    $user_id = $_SESSION['user_id'];

    // Перевірка, чи користувач вже голосував за цей фільм
    $query = "SELECT * FROM movie_votes WHERE movie_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $movie_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Вставка нового голосу
        $query = "INSERT INTO movie_votes (movie_id, user_id, vote_value) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $vote_value = 1; // Голос "так"
        $stmt->bind_param("iii", $movie_id, $user_id, $vote_value);
        if ($stmt->execute()) {
            echo "Ваш голос успішно зараховано!";
        } else {
            echo "Виникла помилка: " . $stmt->error;
        }
    } else {
        echo "Ви вже голосували за цей фільм!";
    }
} else {
    echo "Необхідно авторизуватися для голосування.";
}
?>
<br><a href="vote.php">Повернутися до голосування</a>
