<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Model\Usuario;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                //comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario) {
                    //verificar el password
                    if ($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        //autenticar el usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //redireccionamiento
                        if ($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('location: /admin');
                        } else {
                            header('location: /cita');
                        }
                    }
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout(Router $router) {
        isAuth();
        $_SESSION = [];
        header('location: /');
    }

    public static function olvide(Router $router) {
        $auth = new Usuario;
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $auth->sincronizar($_POST);
            $alertas = $auth->validarEmail();

            if (empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario && $usuario->confirmado === "1") {
                    //generar un token
                    $usuario->crearToken();
                    $usuario->guardar();

                    //enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    Usuario::setAlerta('exito', 'Revisa tu Email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide-password', [
            'alertas' => $alertas,
            'auth' => $auth
        ]);
    }

    public static function reestablecer(Router $router) {
        $alertas = [];
        $error = false;
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            //mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no valido');
            $error = true;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if (empty($alertas)) {
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = '';
                $resultado = $usuario->guardar();

                if ($resultado) {
                    header('location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/reestablecer-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router) {
        $usuario = new Usuario;
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            //revisar que no hayan errores
            if(empty($alertas)) {
                //verificar si no esta registrado
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    //hashear el password
                    $usuario->hashPassword();

                    //generar un token unico
                    $usuario->crearToken();

                    //enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarConfirmacion();

                    //crear el usuario
                    $resultado = $usuario->guardar();
                    if($resultado) {
                        header('location: /mensaje');
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

        if (empty($usuario)) {
            //mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no valido');
        } else {
            //confirmar el usuario
            $usuario->confirmado = '1';
            $usuario->token = '';
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}