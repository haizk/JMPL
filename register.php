<?php
session_start();

if (isset($_SESSION["user"])) {
    header("Location: welcome.php");
    exit();
}

$errorMessage = "";

if (isset($_POST["submit"])) {
    $user = htmlspecialchars($_POST["user"]);
    $pass = htmlspecialchars($_POST["pass"]);
    $email = htmlspecialchars($_POST["email"]);
    $conn = mysqli_connect("localhost", "root", "", "jmpl");

    $errorMessage = register($conn, $user, $pass, $email);
}

function register($conn, $user, $pass, $email)
{
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $user, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION["attempt"]++;
        return "Username or email already exists.";
    }

    if (strlen($pass) < 8) {
        $_SESSION["attempt"]++;
        return "Password must be at least 8 characters long.";
    }

    $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO user (username, pass, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user, $hashedPass, $email);

    if ($stmt->execute()) {
        $_SESSION["attempt"] = 0;
        $_SESSION["user"] = $user;
        header("Location: welcome.php");
        exit();
    } else {
        $_SESSION["attempt"]++;
        return "Registration failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
                                    <form id="register-form" action="" method="post">
                                        <div class="d-flex align-items-center pb-1">
                                            <span class="h1 fw-bold mb-0">Register</span>
                                        </div>
                                        <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Create your account</h5>
                                        <div class="form-outline mb-4">
                                            <label class="form-label" for="email">Email</label>
                                            <input type="email" name="email" id="email" class="form-control form-control-lg" required />
                                        </div>
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
                                            <button class="btn btn-light btn-lg btn-block" type="submit" name="submit"><b style="color: #ff3300;">Register</b></button>
                                        </div>
                                        <p class="pb-lg-2 small">Already have an account? <a href="index.php" class="text-white"><b>Login here</b></a></p>
                                        <small>M0521023</small>
                                        <small>Gentur Sahadewa</small>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-5 p-2 d-flex align-items-center">
                                <img src="img/image.png" alt="register form" class="img-fluid" />
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