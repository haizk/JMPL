<?php
require 'vendor/autoload.php';
session_start();
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

$authenticator = new PHPGangsta_GoogleAuthenticator();
$secret = "";

if (isset($_SESSION['secret'])) {
    $secret = htmlspecialchars($_SESSION['secret'], ENT_QUOTES, 'UTF-8');
} else {
    $secret = $authenticator->createSecret();
    $_SESSION['secret'] = $secret;
}

ob_start();
var_dump($authenticator->getCode($secret));
ob_end_clean();

if (isset($_POST["submit"])) {
    $code = htmlspecialchars($_POST["2fa"], ENT_QUOTES, 'UTF-8');
    $checkResult = $authenticator->verifyCode($secret, $code, 100);
    if ($checkResult) {
        $user = htmlspecialchars($_SESSION["user"], ENT_QUOTES, 'UTF-8');
        $conn = mysqli_connect("localhost", "root", "", "jmpl");
        if (!$conn) {
            die("Connection failed: " . htmlspecialchars(mysqli_connect_error(), ENT_QUOTES, 'UTF-8'));
        }
        mysqli_query($conn, "UPDATE user SET secret = '$secret' WHERE username = '$user'");
        unset($_SESSION["secret"]);
        header("Location: welcome.php");
        exit();
    } else {
        $error_message = "Invalid code. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activate 2FA - JMPL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="stars"></div>
    <section class="vh-100">
        <div class="container h-100">
            <div class="row h-100 justify-content-center align-items-center">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5 card-container">
                    <div class="card border-5 text-white pb-3 px-4">
                        <div class="card-body text-center content-container">
                            <div class="mb-md-5 mt-md-4 content-scroll">
                                <h2 class="fw-bold mb-2 text-uppercase">Activate 2FA</h2>
                                <p class="text-white mb-3">Scan the QR code to enable Two-Factor Authentication</p>

                                <div class="d-flex justify-content-center mb-4">
                                    <img src="<?php echo htmlspecialchars($authenticator->getQRCodeGoogleUrl('JMPL', $secret), ENT_QUOTES, 'UTF-8'); ?>" alt="QR Code" style="width: 200px;" />
                                </div>

                                <?php if (isset($error_message)) : ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $error_message; ?>
                                    </div>
                                <?php endif; ?>

                                <form action="" method="post">
                                    <div class="form-outline form-white mb-3">
                                        <input type="text" name="2fa" id="2fa" class="form-control form-control-lg" required>
                                        <label class="form-label pt-2" for="2fa">Enter 2FA Code</label>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-outline-light btn-lg px-5" style="width: 200px;"><b>Activate</b></button>
                                </form>
                                <p class="mt-4"><a class="btn btn-light btn-lg px-5" href="welcome.php" style="width: 200px;"><b style="color: #ff3300;">Go Back</b></a></p>
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