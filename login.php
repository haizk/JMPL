<?php
session_start();
define('RECAPTCHA_SITE_KEY', '6LeTIZgpAAAAAAdAdcFrEqSkBO7f2qAOcIsfHSMy');
define('RECAPTCHA_SECRET_KEY', '6LeTIZgpAAAAAA7vVfr-boQUwVTfq9Brm8Yp8IR2');

function verifyRecaptcha($recaptcha_response)
{
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $recaptcha_response
    ];
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $json = json_decode($result);
    return $json->success;
}

if (isset($_SESSION["user"])) {
    header("Location: welcome.php");
    exit();
}

if (!isset($_SESSION["attempt"])) {
    $_SESSION["attempt"] = 0;
}

$errorMessage = "";

if (isset($_POST["user"]) && isset($_POST["pass"])) {
    $user = htmlspecialchars($_POST["user"]);
    $pass = htmlspecialchars($_POST["pass"]);
    $conn = mysqli_connect("localhost", "root", "", "jmpl");
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($_SESSION["attempt"] > 2) {
        $recaptcha = $_POST['g-recaptcha-response'];
        if (!verifyRecaptcha($recaptcha)) {
            $errorMessage = "Invalid captcha.";
            $_SESSION["attempt"]++;
        } else if ($result) {
            if (password_verify($pass, $result["pass"])) {
                $_SESSION["attempt"] = 0;
                $_SESSION["user"] = $user;
                if ($result["secret"] != null) {
                    header("location:2fa.php");
                    exit();
                } else {
                    header("location:welcome.php");
                    exit();
                }
            } else {
                $_SESSION["attempt"]++;
                $errorMessage = "Invalid password.";
            }
        } else {
            $_SESSION["attempt"]++;
            $errorMessage = "Username not found.";
        }
    } else if ($result) {
        if (password_verify($pass, $result["pass"])) {
            $_SESSION["attempt"] = 0;
            $_SESSION["user"] = $user;
            if ($result["secret"] != null) {
                header("location:2fa.php");
                exit();
            } else {
                header("location:welcome.php");
                exit();
            }
        } else {
            $_SESSION["attempt"]++;
            $errorMessage = "Invalid password.";
        }
    } else {
        $_SESSION["attempt"]++;
        $errorMessage = "Username not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>
    <div class="stars"></div>
    <section class="vh-100">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col col-xl-10">
                    <div class="card border-5 d-flex justify-content-center align-items-center p-4">
                        <div class="row g-0">
                            <div class="col-md-6 col-lg-7 d-flex align-items-center">
                                <div class="card-body text-white p-2">
                                    <form id="login-form" action="" method="post">
                                        <div class="d-flex align-items-center pb-1">
                                            <span class="h1 fw-bold mb-0">Login</span>
                                        </div>
                                        <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Sign into your account</h5>
                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="user">Username</label>
                                            <input type="text" name="user" id="user" class="form-control form-control-lg" required />
                                        </div>
                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="pass">Password</label>
                                            <input type="password" name="pass" id="pass" class="form-control form-control-lg" required />
                                        </div>
                                        <?php
                                        if ($errorMessage != "") {
                                            echo "<div class='alert alert-danger' role='alert'>";
                                            echo htmlspecialchars($errorMessage);
                                            echo "</div>";
                                        }
                                        ?>
                                        <?php if ($_SESSION["attempt"] >= 3) : ?>
                                            <div class="g-recaptcha py-2" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
                                        <?php endif ?>
                                        <div class="pt-3 mb-4">
                                            <button class="btn btn-light btn-lg btn-block" type="submit" name="submit"><b style="color: #ff3300;">Login</b></button>
                                        </div>
                                        <a class="small text-white" href="forgot.php"><b>Forgot password?</b></a>
                                        <p class="pb-lg-2 small">Don't have an account? <a href="register.php" class="text-white"><b>Register here</b></a></p>
                                        <small>M0521030</small>
                                        <small>Hezkiel Bram Setiawan</small>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-5 p-2 d-flex align-items-center">
                                <img src="img/image.png" alt="login form" class="img-fluid" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>