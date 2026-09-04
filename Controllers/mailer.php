<?php
    require_once(__DIR__ . '/../config/constants.php');

    require_once(BASE_PATH . '/vendor/autoload.php');
    require_once(BASE_PATH . '/config/config.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    function sendVerificationEmail(string $toEmail, string $toName, string $token): bool
    {
        $mail = new PHPMailer(true); 

        try {

            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = 'tls';
            $mail->Port       = MAIL_PORT;

            $mail->setFrom('no-reply@Authentication.com', 'My App');
            $mail->addAddress($toEmail, $toName);

            $verifyLink = "http://localhost:8800/Controllers/verify-email.php?token=" . urlencode($token);

            $mail->isHTML(true);
            $mail->Subject = 'Verify your email address';
            $mail->Body    = "Hi $toName,<br><br>Please verify your email by clicking below:<br>"
                            . "<a href=\"$verifyLink\">$verifyLink</a>";

            $mail->send();
            return true;
        } catch (Exception $e) {

            error_log("Mail error: {$mail->ErrorInfo}");
            return false;
        }
    }