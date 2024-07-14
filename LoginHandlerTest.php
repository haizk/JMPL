<?php

use PHPUnit\Framework\TestCase;

class LoginHandler
{
    private $conn;
    private $recaptchaSecret;

    public function __construct($dbHost, $dbUser, $dbPass, $dbName, $recaptchaSecret)
    {
        $this->conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
        $this->recaptchaSecret = $recaptchaSecret;
    }

    public function checkLogin($user, $pass, $captchaResponse = null)
    {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE username=?");
        $stmt->bind_param('s', $user);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            if (password_verify($pass, $row['pass'])) {
                if ($_SESSION['attempt'] > 2) {
                    $captcha = $this->verifyCaptcha($captchaResponse);
                    if ($captcha->success && $captcha->score >= 0.5) {
                        $_SESSION['attempt'] = 0;
                        $_SESSION['user'] = $user;
                        return $row['secret'] != null ? '2fa' : 'welcome';
                    } else {
                        $_SESSION['attempt']++;
                        return 'Invalid Captcha';
                    }
                } else {
                    $_SESSION['attempt'] = 0;
                    $_SESSION['user'] = $user;
                    return $row['secret'] != null ? '2fa' : 'welcome';
                }
            } else {
                $_SESSION['attempt']++;
                return 'Invalid password';
            }
        } else {
            $_SESSION['attempt']++;
            return 'Username not found';
        }
    }

    private function verifyCaptcha($captchaResponse)
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $this->recaptchaSecret,
            'response' => $captchaResponse,
        ];

        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        return json_decode($response);
    }
}

class LoginHandlerTest extends TestCase
{
    private $loginHandler;

    protected function setUp(): void
    {
        // Simulate a database connection
        $dbHost = 'localhost';
        $dbUser = 'root';
        $dbPass = '';
        $dbName = 'jmpl';
        $recaptchaSecret = 'dummy_secret';

        // Create an instance of the class
        $this->loginHandler = new LoginHandler($dbHost, $dbUser, $dbPass, $dbName, $recaptchaSecret);
    }

    public function testCheckLoginSuccessful()
    {
        $_SESSION['attempt'] = 0;

        // Mock user data
        $user = 'haizk';
        $pass = 'haizk1234';

        // Assume the password in the database is hashed using password_hash
        $hashedPass = password_hash($pass, PASSWORD_DEFAULT);

        // Mock database result
        $this->mockDatabaseResult($user, $hashedPass);

        $result = $this->loginHandler->checkLogin($user, $pass);

        $this->assertEquals('welcome', $result);
        $this->assertEquals($user, $_SESSION['user']);
    }

    public function testCheckLoginInvalidPassword()
    {
        $_SESSION['attempt'] = 0;

        // Mock user data
        $user = 'haizk';
        $pass = 'wrong';

        // Assume the password in the database is hashed using password_hash
        $hashedPass = password_hash($pass, PASSWORD_DEFAULT);

        // Mock database result
        $this->mockDatabaseResult($user, $hashedPass);

        $result = $this->loginHandler->checkLogin($user, $pass);

        $this->assertEquals('Invalid password', $result);
        $this->assertEquals(1, $_SESSION['attempt']);
    }

    public function testCheckLoginUsernameNotFound()
    {
        $_SESSION['attempt'] = 0;

        // Mock user data
        $user = 'nonExistentUser';
        $pass = 'testPass';

        // Mock database result to return no rows
        $this->mockDatabaseResult(null, null);

        $result = $this->loginHandler->checkLogin($user, $pass);

        $this->assertEquals('Username not found', $result);
        $this->assertEquals(1, $_SESSION['attempt']);
    }

    private function mockDatabaseResult($user, $pass)
    {
        $mysqli = $this->createMock(mysqli::class);
        $stmt = $this->createMock(mysqli_stmt::class);
        $result = $this->createMock(mysqli_result::class);

        $stmt->method('get_result')->willReturn($result);
        $mysqli->method('prepare')->willReturn($stmt);

        if ($user !== null) {
            $result->method('fetch_assoc')->willReturn(['username' => $user, 'pass' => $pass]);
        } else {
            $result->method('fetch_assoc')->willReturn(null);
        }

        // Use reflection to set the private $conn property
        $reflection = new \ReflectionClass($this->loginHandler);
        $property = $reflection->getProperty('conn');
        $property->setAccessible(true);
        $property->setValue($this->loginHandler, $mysqli);
    }
}
