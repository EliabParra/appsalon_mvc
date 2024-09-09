<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $email;
    public $nombre;
    public $token;

    protected $font;
    protected $styles;

    public function __construct($email, $nombre, $token) {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
        
        $this->font = "<link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;700;900&display=swap' rel='stylesheet'>";

        $this->styles = "<style>
                        html {font-size: 62.5%; box-sizing: border-box; height: 100%;}
                        body {font-family: 'Poppins', sans-serif; display: flex; flex-direction: column;}
                        h1 {font-size: 4rem; text-align: center;}
                        p, a {text-align: center; font-size: 2rem;}
                        a {text-decoration: none; padding: 1rem 2rem; color: #fff; background-color: #0da6f3; border-radius: 1rem; font-weight: bold; margin-bottom: 4rem;}
                        #texto-mini {font-size: 1.5rem; font-weight: bold;}
                        </style>";
    }

    public function enviarConfirmacion() {
        //crear el obj de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Confirma tu Cuenta';

        //set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "
            <html>
                <head>
                    {$this->font}
                    {$this->styles}
                </head>
                <body>
                    <h1>Hola " . $this->nombre . "</h1>
                    <p>Has creado tu cuenta en AppSalon, solo debes confirmarla presionando el siguiente enlace</p>
                    <p><strong>Presiona aquí: </strong></p>
                    <a href='{$_ENV['APP_URL']}/confirmar-cuenta?token=". $this->token ."'>Confirmar Cuenta</a>
                    <p id='texto-mini'>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>
                </body>
            </html>
        ";

        $mail->Body = $contenido;

        //enviar el email
        $mail->send();
    }

    public function enviarInstrucciones() {
        //crear el obj de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Reestablece tu Password';

        //set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "
            <html>
                <head>
                    {$this->font}
                    {$this->styles}
                </head>
                <body>
                    <h1>Hola " . $this->nombre . "</h1>
                    <p>Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo</p>
                    <p><strong>Presiona aquí: </strong></p>
                    <a href='{$_ENV['APP_URL']}/reestablecer?token=". $this->token ."'>Reestablecer Password</a>
                    <p id='texto-mini'>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>
                </body>
            </html>
        ";

        $mail->Body = $contenido;

        //enviar el email
        $mail->send();
    }
}