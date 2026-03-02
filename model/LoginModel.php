<?php

require_once 'Conexion.php';

class LoginModel
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::getConnect();
    }

    public function findCentroFormacionByEmail($email)
    {
        $sql = "SELECT cent_id as id, cent_nombre as nombre, cent_password as password 
                FROM centro_formacion 
                WHERE cent_correo = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findCoordinatorByEmail($email)
    {
        $sql = "SELECT uc.numero_documento as id, uc.coord_nombre_coordinador as nombre, uc.coord_password as password, uc.centro_formacion_id as centro_id,
                       c.coord_id
                FROM usuario_coordinador uc
                LEFT JOIN COORDINACION c ON c.coordinador_actual = uc.numero_documento AND c.estado = 1
                WHERE uc.coord_correo = :email AND uc.estado = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findInstructorByEmail($email)
    {
        $sql = "SELECT numero_documento as id, inst_nombres as nombre, inst_apellidos as apellidos, inst_password as password, CENTRO_FORMACION_cent_id as centro_id 
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

    // Métodos removidos (auto-registro obsoleto)
}
