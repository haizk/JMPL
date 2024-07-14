<?php

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
