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

// hide var_dump on display
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
    } else {
        echo "Invalid code. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activate 2FA</title>
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
                            <img src="<?php echo htmlspecialchars($authenticator->getQRCodeGoogleUrl('JMPL', $secret), ENT_QUOTES, 'UTF-8'); ?>" alt="QR Code" style="width: 200px;" />
                        </div>
                        <div class="card-body p-5 text-center">
                            <div class="mb-1 md-5 mt-md-4 pb-5">
                                <h2 class="fw-bold text-uppercase">Scan for code</h2>
                                <div class="d-flex justify-content-center mb-3">
                                    <p class="text-white-50">Please open your Google Authenticator<br>to enable Two-Factor Authentication</p>
                                </div>
                                <form action="" method="post">
                                    <div class="form-group mb-4">
                                        <input type="text" name="2fa" id="2fa" class="form-control form-control-lg mb-3" required placeholder="Enter code here">
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-outline-light btn-lg px-5">Activate</button>
                                </form>
                            </div>
                            <div class="d-flex justify-content-center">
                                <p class="text-white">Not sure to activate 2FA? <a href="welcome.php" class="text-danger">Go back</a></p>
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