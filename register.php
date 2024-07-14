<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION["user"])) {
    header("Location: welcome.php");
    exit();
}

$errorMessage = "";

if (isset($_POST["submit"])) {
    $errorMessage = register($_POST);
}

function register($data)
{
    $conn = mysqli_connect("localhost", "root", "", "jmpl");
    $user = htmlspecialchars($data["user"]);
    $pass = htmlspecialchars($data["pass"]);
    $email = htmlspecialchars($data["email"]);

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $user, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return "Username or email already exists.";
    }

    // Validate password length
    if (strlen($pass) < 8) {
        return "Password must be at least 8 characters long.";
    } else {
        // Hash the password
        $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO user (username, pass, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user, $hashedPass, $email);
        $stmt->execute();
        header("Location: index.php");
        exit();
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
</head>

<body>
    <div class="stars"></div>
    <section class="vh-100">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col col-xl-10">
                    <div class="card">
                        <div class="row g-0">
                            <div class="col-md-6 col-lg-7 d-flex align-items-center">
                                <div class="card-body p-4 p-lg-5 text-black">
                                    <form action="" method="post">
                                        <div class="d-flex align-items-center mb-3 pb-1">
                                            <span class="h1 fw-bold mb-0">JMPL Register Page</span>
                                        </div>
                                        <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Create your account</h5>
                                        <div class="form-outline mb-2">
                                            <label class="form-label" for="email">Email</label>
                                            <input type="email" name="email" id="email" class="form-control form-control-lg" required placeholder="Enter your email" />
                                        </div>
                                        <div class="form-outline mb-2">
                                            <label class="form-label" for="user">Username</label>
                                            <input type="text" name="user" id="user" class="form-control form-control-lg" required placeholder="Enter username" />
                                        </div>
                                        <div class="form-outline mb-2">
                                            <label class="form-label" for="pass">Password</label>
                                            <input type="password" name="pass" id="pass" class="form-control form-control-lg" required placeholder="Enter password" />
                                        </div>
                                        <?php
                                        if ($errorMessage != "") {
                                            echo "<div class='alert alert-danger' role='alert'>" . htmlspecialchars($errorMessage) . "</div>";
                                        }
                                        ?>
                                        <div class="pt-1 mb-4">
                                            <button class="btn btn-dark btn-lg btn-block" type="submit" name="submit">Register Now</button>
                                        </div>
                                        <p class="mb-5 pb-lg-2" style="color: #393f81;">Already have an account? <a href="index.php" style="color: #393f81;">Login here</a></p>
                                        <a href="#!" class="small text-muted">M0521023.</a>
                                        <a href="#!" class="small text-muted">Gentur Sahadewa</a>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-5 d-none d-md-block">
                                <img src="img/image.png" alt="login form" class="img-fluid p-10" style="height:100%;" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>