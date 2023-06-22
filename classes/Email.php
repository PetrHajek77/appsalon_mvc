<?php 

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $nombre;
    public $email;
    public $token;

    public function __construct($nombre, $email, $token){

        $this->nombre = $nombre;
        $this->email = $email;
        $this->token = $token;

    }

    public function enviarConfirmacion(){
        // Crear objeto de email
        $email = new PHPMailer();
        $email->isSMTP();
        $email->Host = 'sandbox.smtp.mailtrap.io';
        $email->SMTPAuth = true;
        $email->Port = 2525;
        $email->Username = '5d083034d142d8';
        $email->Password = '970dbeae54f337';

        $email->setFrom('appsalon@appsalon.com');
        $email->addAddress('appsalon@appsalon.com', 'AppSalon.com');     
        $email->Subject = 'Confirma tu cuenta';  

        // Set HTML
        $email->isHTML(TRUE);
        $email->CharSet = 'UTF-8';
        
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has creado tu cuenta en AppSalon, solo debes confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aqui: <a href='http://localhost:3000/confirmar-cuenta?token=" . $this->token . "'>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";
        $email->Body = $contenido;

        // Enviar el mail
        $email->send();

    }

    public function enviarInstrucciones(){
        // Crear objeto de email
        $email = new PHPMailer();
        $email->isSMTP();
        $email->Host = 'sandbox.smtp.mailtrap.io';
        $email->SMTPAuth = true;
        $email->Port = 2525;
        $email->Username = '5d083034d142d8';
        $email->Password = '970dbeae54f337';

        $email->setFrom('appsalon@appsalon.com');
        $email->addAddress('appsalon@appsalon.com', 'AppSalon.com');     
        $email->Subject = 'Reestablece tu password';  

        // Set HTML
        $email->isHTML(TRUE);
        $email->CharSet = 'UTF-8';
        
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
        $contenido .= "<p>Presiona aqui: <a href='http://localhost:3000/recuperar?token=" . $this->token . "'>Reestablecer Password</a></p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";
        $email->Body = $contenido;

        // Enviar el mail
        $email->send();
    }
 
}