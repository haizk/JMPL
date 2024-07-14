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

// Pagination
$posts_per_page = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $posts_per_page;

// Get total number of posts
$total_posts_query = "SELECT COUNT(*) as total FROM blog_posts";
$total_posts_result = mysqli_query($conn, $total_posts_query);
$total_posts = mysqli_fetch_assoc($total_posts_result)['total'];
$total_pages = ceil($total_posts / $posts_per_page);

// Get posts for current page
$query = "SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $posts_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - JMPL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .card-container {
            height: 100%;
            display: flex;
            align-items: center;
        }

        .blog-card {
            max-height: 100%;
            overflow-y: auto;
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

        .blog-post-card {
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .blog-post-card:hover {
            background-color: rgba(255, 255, 255, 0.9);
        }

        .blog-post-card * {
            color: white;
            transition: color 0.3s ease;
        }

        .blog-post-card:hover * {
            color: black;
        }

        .text-muted {
            color: #d0d0d0 !important;
        }

        .blog-post-card:hover .text-muted {
            color: #6c757d !important;
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
                                <h2 class="fw-bold mb-4 text-uppercase">Blog Posts</h2>
                                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                    <div class="card mb-4 blog-post-card" onclick="location.href='post.php?id=<?php echo $row['id']; ?>';">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars(substr($row['content'], 0, 200)) . '...'; ?></p>
                                            <p class="card-text"><small class="text-muted">Posted on <?php echo htmlspecialchars($row['created_at']); ?></small></p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                                <p class="mt-5"><a class="btn btn-light btn-lg px-5" href="welcome.php" style="width: 200px;"><b style="color: #ff3300;">Go Back</b></a></p>
                            </div>
                            <div>
                                <p class="mb-0 text-white">Enjoy reading our blog posts!</p>
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