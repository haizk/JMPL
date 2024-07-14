<?php
require 'vendor/autoload.php';
session_start();
date_default_timezone_set('Asia/Jakarta');
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

$authenticator = new PHPGangsta_GoogleAuthenticator();
$user = htmlspecialchars($_SESSION["user"], ENT_QUOTES, 'UTF-8');
$conn = mysqli_connect("localhost", "root", "", "jmpl");

if (!$conn) {
    die("Connection failed: " . htmlspecialchars(mysqli_connect_error(), ENT_QUOTES, 'UTF-8'));
}

$stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$secret = htmlspecialchars($result["secret"], ENT_QUOTES, 'UTF-8');

$errorMessage = "";

if (isset($_POST["submit"])) {
    $code = htmlspecialchars($_POST["2fa"], ENT_QUOTES, 'UTF-8');
    $checkResult = $authenticator->verifyCode($secret, $code, 2); // Changed window from 100 to 2 for better security
    if ($checkResult) {
        header("location:welcome.php");
    } else {
        $errorMessage = "Invalid 2FA code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.2/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
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
                            <div class="mb-md-4 md-4 pb-5">
                                <h2 class="fw-bold mb-4 text-uppercase">CHECK THE AUTHENTICATOR APP</h2>
                                <form action="" method="post">
                                    <div class="form-group">
                                        <label for="2fa" class="mb-2">Enter Code Below</label>
                                        <input type="text" name="2fa" id="2fa" class="form-control form-control-lg mb-3" required>
                                    </div>
                                    <?php if ($errorMessage != "") : ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <button type="submit" name="submit" class="btn btn-outline-light btn-lg px-5">Login</button>
                                    <p class="mt-2"><a class="text-danger" href="logout.php">Go back</a></p>
                                </form>
                            </div>
                            <div class="d-flex justify-content-center mt-4 pt-1">
                                <p class="text-white-50">We hope you had not erased the 2FA account earlier.<br>Didn't ya?</p>
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