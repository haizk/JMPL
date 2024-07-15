<?php
session_start();

if (isset($_SESSION["user"])) {
    header("Location: welcome.php");
    exit();
}

if (!isset($_SESSION["attempt"])) {
    $_SESSION["attempt"] = 0;
}

$errorMessage = "";

if (isset($_GET["user"]) && isset($_GET["pass"])) {
    $user = $_GET["user"]; // Removed htmlspecialchars
    $pass = $_GET["pass"]; // Removed htmlspecialchars
    $conn = mysqli_connect("localhost", "root", "", "jmpl");

    // Unsafe query, vulnerable to SQL Injection
    $sql = "SELECT * FROM user WHERE username = '$user'";
    $result = mysqli_query($conn, $sql);
    $result = mysqli_fetch_assoc($result);

    if ($result) {
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
    <title>Login - JMPL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
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
                                    <form id="login-form" action="" method="get">
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