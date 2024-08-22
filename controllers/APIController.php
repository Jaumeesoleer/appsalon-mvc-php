<?php

namespace Controllers;

use Model\Servicio;
use Model\Cita;
use Model\CitaServicio;

class APIController{
    public static function index(){
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    public static function guardar(){

        //almacena la cita y devuelve su Id
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();
        $id = $resultado["id"];

        //almacena la cita y el servicio
        $servicio = $_POST['servicios'];
        $idServicios = explode(',', $servicio);

        foreach ($idServicios as $idServicio){
            $args = [
                'citasId' => $id,
                'serviciosId' => $idServicio
            ];
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();
        }

        $respuesta = [
            'resultado' => $resultado
        ];

        echo json_encode($respuesta);
    }
}
