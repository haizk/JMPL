<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

$user = htmlspecialchars($_SESSION["user"], ENT_QUOTES, 'UTF-8');
$conn = mysqli_connect("localhost", "root", "", "jmpl");

if (!$conn) {
    die("Connection failed: " . htmlspecialchars(mysqli_connect_error(), ENT_QUOTES, 'UTF-8'));
}

$stmt = $conn->prepare("SELECT * FROM user WHERE username=?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query failed: " . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8'));
}

$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .card-container {
            height: 100%;
            display: flex;
            align-items: center;
        }

        .welcome-card {
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
    </style>
    <script>
        function confirmDeactivate() {
            if (confirm("Are you sure you want to deactivate 2FA? This action cannot be undone.")) {
                document.getElementById("deactivateForm").submit();
            }
        }
    </script>
</head>

<body>
    <div class="stars"></div>
    <section class="vh-100">
        <div class="container h-100">
            <div class="row h-100 justify-content-center align-items-center">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5 card-container">
                    <div class="card border-5 text-white">
                        <div class="card-body p-5 text-center content-container">
                            <div class="mb-md-5 mt-md-4 content-scroll">
                                <h2 class="fw-bold mb-2 text-uppercase">Welcome, <?php echo htmlspecialchars($user); ?>!</h2>
                                <p class="text-white mb-5">Welcome Legion in most-likely vulnerable website.<br>Hope you enjoy.</p>

                                <div class="d-flex justify-content-center mb-4">
                                    <img src="img/welcome.png" alt="welcome image" class="img-fluid" style="width: 100px;" />
                                </div>

                                <?php if ($row["secret"] == null) : ?>
                                    <p class="text-white mb-4"><a class="btn btn-danger btn-lg" href="activate2fa.php">Activate 2FA</a></p>
                                <?php else : ?>
                                    <p class="text-white mb-4">
                                        <a class="btn btn-warning btn-lg" href="#" onclick="confirmDeactivate()">Deactivate 2FA</a>
                                    </p>
                                    <form id="deactivateForm" action="deactivate2fa.php" method="post" style="display: none;">
                                        <input type="hidden" name="confirm_deactivate" value="1">
                                    </form>
                                <?php endif; ?>

                                <p><a class="btn btn-outline-light btn-lg px-5" href="logout.php">Log out</a></p>
                            </div>
                            <div>
                                <p class="mb-0 text-white">All users are suggested to activate Two-Factor Authentication</p>
                                <small>M0521030</small>
                                <small>Hezkiel Bram Setiawan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>