<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$settings = include('config.php');

$responseJSON = array('message' => 'Not allowed', 'isSuccess' => false);

header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header("Content-Type: application/json");
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: X-PINGARUNER');
header('Access-Control-Max-Age: 1728000');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $responseJSON['message'] = 'This service accepts POST requests only.';
    echo json_encode($responseJSON);
    exit;
}

try {
    $captcha = null;

    if (isset($_POST['g-recaptcha-response']))
        $captcha = $_POST['g-recaptcha-response'];

    if (!$captcha)
        throw new Exception('Please check the captcha.');
    elseif (
        !(isset($_POST['NameSurname']) && !empty($_POST['NameSurname'])) ||
        !(isset($_POST['EMail']) && !empty($_POST['EMail'])) ||
        !(isset($_POST['Subject']) && !empty($_POST['Subject'])) ||
        !(isset($_POST['Message']) && !empty($_POST['Message']))
    )
        throw new Exception('Please check your form. Some inputs are missing.');

    $response = json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $settings['recaptchaSecret'] . '&response=' . $captcha . '&remoteip=' . $_SERVER['REMOTE_ADDR']), true);

    if (!$response['success'])
        throw new Exception('Your recaptcha has been failed.');

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = $settings['mailHost'];
    $mail->SMTPAuth = $settings['smtpAuth'];
    $mail->SMTPSecure = $settings['smtpSecure'];
    $mail->Username = $settings['senderUsername'];
    $mail->Password = $settings['senderPassword'];
    $mail->Port = $settings['smtpPort'];
    $mail->CharSet = 'UTF-8';

    $mail->setFrom($settings['senderUsername'], $settings['sentFrom']);

    foreach ($settings['receivers'] as $key => $value) {
        $mail->addAddress($key, $value);
    }

    $mail->isHTML(true);
    $mail->setLanguage($settings['language'], __DIR__ . '/vendor/phpmailer/phpmailer/language');
    $mail->Subject = $settings['subject'];

    $date = date('Y-m-d');
    $ip = $_SERVER['REMOTE_ADDR'];

    $body = 'Date: ' . $date . "<br>";
    $body .= 'IP Address: ' . $ip . '<br><br>';
    $body .= 'Name - Surname: ' . $_POST['NameSurname'] . "<br>";
    $body .= 'E-Mail Address: ' . $_POST['EMail'] . "<br>";
    $body .= 'Subject: ' . $_POST['Subject'] . "<br>";
    $body .= 'Message: ' . $_POST['Message'];

    $mail->Body = $body;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->send();

    $responseJSON['message'] = 'Your message has been sent.';
    $responseJSON['isSuccess'] = true;
} catch (Exception $e) {
    $responseJSON['message'] = 'Your message could not be sent. ' . $e->getMessage();
} catch (\Exception $e) {
    $responseJSON['message'] = 'Your message could not be sent. PHP exception: ' . $e->getMessage();
}

echo json_encode($responseJSON);