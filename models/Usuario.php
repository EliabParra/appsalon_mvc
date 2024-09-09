<?php

namespace Model;

class Usuario extends ActiveRecord {
    // DB
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? '0';
        $this->confirmado = $args['confirmado'] ?? '0';
        $this->token = $args['token'] ?? '';
    }

    //mensajes de validacion de la cuenta
    public function validarNuevaCuenta() {
        if(!$this->nombre || !$this->apellido) {
            self::$alertas['error'][] = 'El Nombre y el Apellido son Obligatorios';
        }

        if (!$this->telefono) {
            self::$alertas['error'][] = 'Debe Colocar un Numero de Telefono';
        }

        if (!$this->email) {
            self::$alertas['error'][] = 'Debe Colocar un correo electronico';
        }

        if (!$this->password) {
            self::$alertas['error'][] = 'Debe Colocar un password';
        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }

        return self::$alertas;
    }

    public function validarLogin() {
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }

        if (!$this->password) {
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        return self::$alertas;
    }

    public function validarEmail() {
        if (!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        return self::$alertas;
    }

    public function validarPassword() {
        if (!$this->password) {
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe tener al menos 6 caracteres';
        }

        return self::$alertas;
    }

    //revisa si el usuario existe
    public function existeUsuario() {
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";

        $result = self::$db->query($query);

        if ($result->num_rows) {
            self::$alertas['error'][] = 'El usuario ya esta registrado';
        }

        return $result;
    }

    //hashear password
    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    //crar token unico
    public function crearToken() {
        $this->token = uniqid();
    }

    public function comprobarPasswordAndVerificado($password) {
        $resultado = password_verify($password, $this->password);

        if (!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = 'Password incorrecto o tu cuenta no ha sido confirmada';
        } else {
            return true;
        }
    }
}