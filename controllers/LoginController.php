<?php
namespace Controllers;

use Model\Usuario;
use MVC\Router;
use Classes\Email;

class LoginController{
    public static function login(Router $router) {

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                //comprobar que existe usuario
                $usuario = Usuario::where('email', $auth->email);

                if($usuario){
                    if ($usuario->comprobarPasswordYVerificado($auth->password)){
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['apellido'] = $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        if($usuario->admin === "1"){
                            $_SESSION['admin'] = $usuario->admin ?? null;

                            header('Location: /admin');
                        }else{
                            header('Location: /cita');
                        }
                    }
                }else{
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);

    }
    public static function logout() {
        session_start();
        $_SESSION = [];
        header('Location:/');
    }
    public static function olvide(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === "1"){
                    $usuario->crearToken();
                    $usuario->guardar();

                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    Usuario::setAlerta('exito', 'Revisa tu email');
                }else{
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }
    public static function recuperar(Router $router) {
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token no válido');
            $error = true;
        }
    
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $password = new Usuario($_POST);

            $alertas = $password->validarPassword();

            if(empty($alertas)){
                $usuario->password = null;

                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

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
    public static function crear(Router $router) {
        $usuario = new Usuario;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            //revisar alerta vacia
            if(empty($alertas['error'])){
                // verificar que el usuario no esta registrado
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows){
                    //usuario registrado
                    $alertas = Usuario::getAlertas();
                }else{
                    //usuario no registrado

                    //hash password
                    $usuario->hashPassword();

                    //generar token
                    $usuario->crearToken();

                    //enviar el mail
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    //crear usuario
                    $resultado = $usuario->guardar();

                    if($resultado){
                        header('Location: /mensaje');
                    }
                }
            }
            
        }
        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje');
    }
    
    public static function confirmar(Router $router) {
        $alertas = [];
        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if(empty($usuario) || $token == ''){
            //mensaje error
            Usuario::setAlerta('error', 'Token no válido');        
        }else{
            //usuario confirmado
            $usuario->confirmado = "1";
            $usuario->token = '';
            $usuario->guardar();
            
            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');
        }

        //Obtener alertas
        $alertas = Usuario::getAlertas();

        //Renderizar vista
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }

}