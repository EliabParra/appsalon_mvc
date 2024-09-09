<?php

namespace Controllers;

use Model\AdminCita;
use MVC\Router;

class AdminController {
    public static function index(Router $router) {
        isAdmin();

        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $fechas = explode('-', $fecha);

        if (!checkdate($fechas[1], $fechas[2], $fechas[0])) {
            header('Location: /404');
        }

        //consultar db
        $query = "SELECT citas.id, citas.hora, CONCAT( usuarios.nombre, ' ', usuarios.apellido) as cliente, ";
        $query .= " usuarios.email, usuarios.telefono, servicios.nombre as servicio, servicios.precio  ";
        $query .= " FROM citas  ";
        $query .= " LEFT OUTER JOIN usuarios ";
        $query .= " ON citas.usuarioId=usuarios.id  ";
        $query .= " LEFT OUTER JOIN citas_servicios ";
        $query .= " ON citas_servicios.citaId=citas.id ";
        $query .= " LEFT OUTER JOIN servicios ";
        $query .= " ON servicios.id=citas_servicios.servicioId ";
        $query .= " WHERE fecha =  '{$fecha}' ";

        $citas = AdminCita::SQL($query);

        $router->render('admin/index', [
            'nombre' => $_SESSION['nombre'],
            'citas' => $citas,
            'fecha' => $fecha
        ]);
    }
}