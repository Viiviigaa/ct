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
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = 587; 
        $mail->CharSet    = 'UTF-8';

        // --- Remitente ---
        // Usamos el mismo correo de Gmail para evitar que se marque como Spam
        $mail->setFrom(getenv('SMTP_EMAIL'), 'Tu App');

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
        echo 'El mensaje ha sido enviado correctamente. Revise su bandeja de entrada.';
        echo "<strong><a href='login.php'>Iniciar sesión</a></strong>";

    } catch (Exception $e) {
        echo "Error al enviar el mensaje: {$mail->ErrorInfo}";
    }
}