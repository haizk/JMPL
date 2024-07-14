<?php
session_start();

$errorMessage = "";
$successMessage = "";

if (isset($_POST["submit"])) {
    $email = htmlspecialchars($_POST["email"]);

    $successMessage = "If an account exists for $email, you will receive password reset instructions.";
    $_SESSION["attempt"] = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
                                    <form id="forgot-password-form" action="" method="post">
                                        <div class="d-flex align-items-center pb-1">
                                            <span class="h1 fw-bold mb-0">Forgot Password</span>
                                        </div>
                                        <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Reset your password</h5>
                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="email">Email</label>
                                            <input type="email" name="email" id="email" class="form-control form-control-lg" required />
                                        </div>
                                        <?php
                                        if ($errorMessage != "") {
                                            echo "<div class='alert alert-danger' role='alert'>";
                                            echo htmlspecialchars($errorMessage);
                                            echo "</div>";
                                        }
                                        if ($successMessage != "") {
                                            echo "<div class='alert alert-success' role='alert'>";
                                            echo htmlspecialchars($successMessage);
                                            echo "</div>";
                                        }
                                        ?>
                                        <div class="pt-3 mb-4">
                                            <button class="btn btn-light btn-lg btn-block" type="submit" name="submit"><b style="color: #ff3300;">Reset Password</b></button>
                                        </div>
                                        <p class="pb-lg-2 small">Remember your password? <a href="index.php" class="text-white"><b>Login here</b></a></p>
                                        <small>M0521030</small>
                                        <small>Hezkiel Bram Setiawan</small>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-5 p-2 d-flex align-items-center">
                                <img src="img/image.png" alt="forgot password form" class="img-fluid" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>