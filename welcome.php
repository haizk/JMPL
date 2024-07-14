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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.2/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script>
        function confirmDeactivate() {
            if (confirm("Are you sure you want to deactivate 2FA? This action cannot be undone.")) {
                document.getElementById("deactivateForm").submit();
            }
        }
    </script>
</head>

<body>
    <section class="vh-100">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark text-white" style="border-radius: 1rem;">
                        <div class="d-flex justify-content-center mt-5">
                            <img src="img/welcome.png" alt="login form" class="img-fluid" style="width: 100px;" />
                        </div>
                        <div class="card-body p-5 text-center">
                            <div class="mb-md-5 md-4 pb-5">
                                <h2 class="fw-bold mb-2 text-uppercase">Welcome, <?php echo htmlspecialchars($user); ?>!</h2>
                                <div class="d-flex justify-content-center">
                                    <p class="text-white-50">Welcome Legion in most-likely vulnerable website.<br>Hope you enjoy.</p>
                                </div>
                                <?php if ($row["secret"] == null) : ?>
                                    <p class="text-white-50 mt-5 mb-2"><a class="text-danger" href="activate2fa.php">Activate 2FA</a></p>
                                <?php else : ?>
                                    <p class="text-white-50 mt-5 mb-2">
                                        <a class="text-danger" href="#" onclick="confirmDeactivate()">Deactivate 2FA</a>
                                    <form id="deactivateForm" action="deactivate2fa.php" method="post" style="display: none;">
                                        <input type="hidden" name="confirm_deactivate" value="1">
                                    </form>
                                    </p>
                                <?php endif; ?>
                                <p><a class="btn btn-outline-light btn-lg px-5" href="logout.php">Log out</a></p>
                            </div>
                            <div class="d-flex justify-content-center">
                                <p class="text-white-50">All users are suggested to activate <br>Two-Factor Authentication</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.2/mdb.min.js"></script>
</body>

</html>