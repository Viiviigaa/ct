<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function enviarEmail($email, $asunto, $body, $attach = null)
{   
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_EMAIL');   
        $mail->Password   = getenv('SMTP_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465; 
        $mail->CharSet    = 'UTF-8';

        // --- Remitente ---
        // Usamos el mismo correo de Gmail para evitar que se marque como Spam
        $mail->setFrom(getenv('SMTP_EMAIL'), 'Canary Travel');

        // --- Destinatarios ---
        if (is_array($email)) {
            foreach ($email as $direccion) {
                $mail->addAddress($direccion);
            }
        } else {
            $mail->addAddress($email);
        }

        // --- Adjuntos ---
        if ($attach !== null) {
            $mail->addAttachment($attach);
        }

        // --- Contenido ---
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('PHPMailer error: ' . $mail->ErrorInfo);
        return false;
    }
}