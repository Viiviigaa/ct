<?php
function enviarEmail($email, $asunto, $body, $attach)
{   
    require './PHPMailer-master/src/Exception.php';
    require './PHPMailer-master/src/PHPMailer.php';
    require './PHPMailer-master/src/SMTP.php';
    $recipients = $email;
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Mailer = "SMTP";
    $mail->SMTPAuth = true;
    $mail->isHTML(true);
    $mail->SMTPAutoTLS = false;
    $mail->Port = 25;
    $mail->CharSet = 'UTF-8';
    $mail->Host = "localhost";
    $mail->Username = "victor";
    $mail->Password = "12345678";
    $mail->setFrom('victor@informaticascarlatti.es');

    if (isset($attach)) {
        $mail->addAttachment($attach);
    }

    if (is_array($email)) {
        foreach ($recipients as $email) {
            $mail->addAddress($email);
        }
    } else {
        $mail->addAddress($email);
    }
    $mail->Subject = $asunto;
    $mail->Body = $body;

    if (!$mail->send()) {
        echo $mail->ErrorInfo;
    } else {
        echo 'El mensaje ha sido enviado correctamente. Revise su bandeja de entrada.';
        echo "<strong><a href='login.php'>Iniciar sesión</a></strong>";
    }
}
