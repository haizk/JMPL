<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

$user = htmlspecialchars($_SESSION["user"], ENT_QUOTES, 'UTF-8');
$conn = mysqli_connect("localhost", "root", "", "jmpl");

if (!$conn) {
    die("Connection failed: " . htmlspecialchars(mysqli_connect_error(), ENT_QUOTES, 'UTF-8'));
}

$sql = "UPDATE user SET secret = NULL WHERE username = '$user'";

if (mysqli_query($conn, $sql)) {
    unset($_SESSION['secret']);
    header("Location: welcome.php");
} else {
    echo "Error updating record: " . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8');
}

mysqli_close($conn);
