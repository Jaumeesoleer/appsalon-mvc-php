<?php
 
namespace Controllers;

use MVC\Router;

class citaController{
    public static function index(Router $router){
        session_start();
        isAuth();

        $router->render('cita/index',[
            'id' => $_SESSION['id'],
            'nombre' => $_SESSION['nombre']
        ]);
    }
}