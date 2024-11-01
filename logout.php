<?php
session_start();
session_destroy(); // Знищити всі дані сесії
header("Location: index.php"); // Переходити на головну сторінку
exit();

