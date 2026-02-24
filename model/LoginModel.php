<?php

require_once 'Conexion.php';

class LoginModel
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::getConnect();
    }

    public function findCoordinatorByEmail($email)
    {
        $sql = "SELECT coord_id as id, coord_nombre_coordinador as nombre, coord_password as password, CENTRO_FORMACION_cent_id as centro_id 
                FROM coordinacion 
                WHERE coord_correo = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findInstructorByEmail($email)
    {
        $sql = "SELECT inst_id as id, inst_nombres as nombre, inst_apellidos as apellidos, inst_password as password, CENTRO_FORMACION_cent_id as centro_id 
                FROM instructor 
                WHERE inst_correo = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && isset($result['apellidos'])) {
            $result['nombre'] = trim($result['nombre'] . ' ' . $result['apellidos']);
        }
        return $result;
    }

    public function findCoordinaciones()
    {
        $sql = "SELECT coord_id, coord_descripcion, CENTRO_FORMACION_cent_id 
                FROM coordinacion 
                ORDER BY coord_descripcion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCentrosFormacion()
    {
        $sql = "SELECT cent_id, cent_nombre FROM centro_formacion ORDER BY cent_nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarCoordinador($id_coordinacion, $nombre, $correo, $password_hash)
    {
        $sql = "UPDATE coordinacion 
                SET coord_nombre_coordinador = :nombre, 
                    coord_correo = :correo, 
                    coord_password = :password 
                WHERE coord_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id_coordinacion, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
