<?php 
    namespace Classes;
    use PHPMailer\PHPMailer\PHPMailer;

    class Email{

        public $email;
        public $nombre; 
        public $token;

        public function __construct($email, $nombre, $token){
            $this->email = $email;
            $this->nombre = $nombre;
            $this->token = $token;
        }

        public function enviarConfirmacion(){
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Port = $_ENV['EMAIL_PORT'];
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASS'];

            $mail->setFrom('cuentas@appsalon.com');
            $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
            $mail->Subject = 'Confirma tu cuenta';

            // set HTML
            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';

            $contenido = "<html>";
            $contenido .= "<p><strong>Hola " . $this->nombre . "</strong>. Has creado tu cuenta en AppSalon, solo debes confirmarla presionando el siguiente enlace. </p>";
            $contenido .= "<p> Presiona aquí: <a href ='" . $_ENV['APP_URL'] ."/confirmar-cuenta?token=" . $this->token . "'> Confirmar cuenta</a></p>";
            $contenido .= "<p>Si tu no solicitaste esta cuenta puedes ignorar el mensaje</p>";
            $contenido .= "</html>";
            $mail->Body = $contenido;

            // enviar mail

            $mail->send();

        }
        
        public function enviarInstrucciones(){
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Port = $_ENV['EMAIL_PORT'];
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASS'];

            $mail->setFrom('cuentas@appsalon.com');
            $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
            $mail->Subject = 'Restablece tu password';

            // set HTML
            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';

            $contenido = "<html>";
            $contenido .= "<p><strong>Hola " . $this->nombre . "</strong>. Has solicitado restablecer tu password, sigue el siguiente enlace para hacerlo. </p>";
            $contenido .= "<p> Presiona aquí: <a href ='" . $_ENV['APP_URL'] ."/recuperar?token=" . $this->token . "'> Restablecer password</a></p>";
            $contenido .= "<p>Si tu no solicitaste esta cuenta puedes ignorar el mensaje</p>";
            $contenido .= "</html>";
            $mail->Body = $contenido;

            // enviar mail

            $mail->send();
        }
    }
?>