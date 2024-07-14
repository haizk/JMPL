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

// Check if post ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: blog.php");
    exit();
}

$post_id = intval($_GET['id']);

// Fetch the specific post
$query = "SELECT * FROM blog_posts WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: blog.php");
    exit();
}

$post = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - JMPL Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .card-container {
            height: 100%;
            display: flex;
            align-items: center;
        }

        .content-container {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .content-scroll {
            flex-grow: 1;
            overflow-y: auto;
        }

        .blog-post {
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .blog-post-title {
            color: white;
            margin-bottom: 15px;
        }

        .blog-post-content {
            color: #e0e0e0;
            font-size: 1.1em;
            line-height: 1.6;
        }

        .blog-post-meta {
            color: #b0b0b0;
            font-size: 0.9em;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="stars"></div>
    <section class="vh-100">
        <div class="container h-100">
            <div class="row h-100 justify-content-center align-items-center">
                <div class="col-12 col-md-10 col-lg-8 card-container justify-content-center align-items-center">
                    <div class="card border-5 text-white">
                        <div class="card-body p-5 pt-3 text-center content-container">
                            <div class="mb-md-5 mt-md-4 content-scroll">
                                <div class="blog-post">
                                    <h2 class="blog-post-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                                    <div class="blog-post-content">
                                        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                                    </div>
                                    <p class="blog-post-meta">Posted on <?php echo htmlspecialchars($post['created_at']); ?></p>
                                </div>
                                <p class="mt-5">
                                    <a class="btn btn-light btn-lg px-5 me-2" href="blog.php" style="width: 200px;">
                                        <b style="color: #ff3300;">Back to Blog</b>
                                    </a>
                                    <a class="btn btn-light btn-lg px-5" href="welcome.php" style="width: 200px;">
                                        <b style="color: #ff3300;">Go to Home</b>
                                    </a>
                                </p>
                            </div>
                            <div>
                                <p class="mb-0 text-white">Thank you for reading our blog!</p>
                                <small>M0521030</small>
                                <small>Hezkiel Bram Setiawan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>