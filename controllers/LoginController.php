<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router){
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Instanciamos el modelo de Usuario y le pasamos lo que usuario escriba en el post(en el formulario)
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                // Comprobar que exista el usuario por el email
                $usuario = Usuario::where('email', $auth->email);
         
                if($usuario){
                    // Verificamos si el password esta correcto y usuario esta confirmado
                    if($usuario->comprobarPasswordAndConfirmado($auth->password)){
                        // Autenticar el usuario
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        // debuguear($_SESSION);

                        // Redireccionamiento
                        // si es admin
                        if($usuario->admin === '1'){
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } 
                        // si no es admin, es cliente  
                        else 
                        {
                            header('Location: /cita');
                        }

                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas,
        ]);

        
    }

    public static function logout(){
        session_start();
        // debuguear($_SESSION);

        $_SESSION = [];
        header('Location: /');
    }

    public static function olvide(Router $router){
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);
                
                if($usuario && $usuario->confirmado === '1'){
                    // Generar un token
                    $usuario->crearToken();
                    $usuario->guardar();
                    // debuguear($usuario);

                    // Enviar el email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();

                    // Alerta de exito
                    Usuario::setAlerta('exito', 'Revisa tu email');
                    
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                    
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router){
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);
        
        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token no valido');
            $error = true;
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)){
                // Eliminamos el password antiguo
                $usuario->password = null;
                // Le asignamos nuevo password
                $usuario->password = $password->password;
                // Hasheamos el nuevo password
                $usuario->hashPassword();
                // Eliminamos el token
                $usuario->token = null;
                // Lo guardamos en la BD
                $resultado = $usuario->guardar();
                if($resultado){
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router){

        $usuario = new Usuario;
       
        // Alertas vacias
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            // $usuario = new Usuario($_POST);
            // debuguear($usuario);

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            // Revisar que alertas este vacio
            if(empty($alertas)){
                // Verificar que el usuario no este registrado
                $resultado = $usuario->existeUsuario();

                // El usuario esta registrado
                if($resultado->num_rows){
                    $alertas = Usuario::getAlertas();
                // El usuario no esta registrado
                } 
                
                else {
                    // No esta registrado
                    // Hashear el password
                    $usuario->hashPassword();
                    
                    // Generar un token unico
                    $usuario->crearToken();

                    // Enviar el email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarConfirmacion();
                    
                    // Crear usuario
                    $resultado = $usuario->guardar();
                    
                    if($resultado){
                        // echo 'guardado correctamente';
                        header('Location: /mensaje');
                    }
                    
                    // debuguear($usuario);
                    
                }
            }

        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('/auth/mensaje');
    }

    public static function confirmar(Router $router){
        $alertas = [];

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            // Mostrar mensaje de error
            // setAlerta es un metodo estatico, no requiere instanciarlo
            Usuario::setAlerta('error', 'Token no valido');
        } else {
            // Modificar confirmado a 1
            $usuario->confirmado = '1';
            // Eliminar el token
            $usuario->token = null;
            // Guardamos los cambios a la base de datos
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        // Obtener alertas
        $alertas = Usuario::getAlertas();

        // Renderizar la vista
        $router->render('/auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }

    
}