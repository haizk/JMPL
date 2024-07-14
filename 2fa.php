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
    $checkResult = $authenticator->verifyCode($secret, $code, 2);
    if ($checkResult) {
        header("location:welcome.php");
        exit();
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
    <title>2FA Verification - JMPL</title>
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
</head>

<body>
    <div class="stars"></div>
    <section class="vh-100">
        <div class="container h-100">
            <div class="row h-100 justify-content-center align-items-center">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5 card-container justify-content-center align-items-center">
                    <div class="card border-5 text-white">
                        <div class="card-body p-5 text-center content-container">
                            <div class="mb-md-5 mt-md-4 mb-0 content-scroll">
                                <h2 class="fw-bold mb-2 text-uppercase">2FA Verification</h2>
                                <p class="text-white mb-5">Check your Authenticator App for the code.</p>

                                <div class="d-flex justify-content-center mb-4">
                                    <img src="img/welcome.png" alt="welcome image" class="img-fluid" style="width: 100px;" />
                                </div>
                                <?php if ($errorMessage != "") : ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                <?php endif; ?>
                                <form action="" method="post">
                                    <div class="form-outline form-white mb-3">
                                        <input type="text" name="2fa" id="2fa" class="form-control form-control-lg" required>
                                        <label class="form-label pt-2" for="2fa">Enter 2FA Code</label>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-outline-light btn-lg px-5" style="width: 200px;"><b>Verify</b></button>
                                </form>

                                <p class="mt-4"><a class="btn btn-light btn-lg px-5" href="logout.php" style="width: 200px;"><b style="color: #ff3300;">Relogin</b></a></p>
                            </div>
                            <div>
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