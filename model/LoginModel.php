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
        $sql = "SELECT numero_documento as id, coord_nombre_coordinador as nombre, coord_password as password, CENTRO_FORMACION_cent_id as centro_id 
                FROM coordinacion 
                WHERE coord_correo = :email LIMIT 1";
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

    public function findCoordinaciones()
    {
        $sql = "SELECT numero_documento as coord_id, coord_descripcion, CENTRO_FORMACION_cent_id 
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

    public function registrarCoordinador($id_coordinacion, $nombre, $correo, $password_hash, $documento)
    {
        // En lugar de INSERT, las coordinaciones ya existen creadas por el Centro de Formación.
        // Asignamos al coordinador a esta coordinación (UPDATE) solo si está vacante (coord_correo IS NULL).
        $sql = "UPDATE coordinacion 
                SET coord_nombre_coordinador = :nombre, 
                    coord_correo = :correo, 
                    coord_password = :password,
                    numero_documento = :documento
                WHERE numero_documento = :id AND (coord_correo IS NULL OR coord_correo = '' OR coord_correo = 'N/A')";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
        $stmt->bindParam(':documento', $documento, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id_coordinacion, PDO::PARAM_INT);
        $stmt->execute();

        // Retorna true si se afectó al menos una fila (es decir, la coordinación estaba vacante)
        return $stmt->rowCount() > 0;
    }

    public function getCoordinacionesDisponiblesByCentro($cent_id)
    {
        $sql = "SELECT numero_documento as coord_id, coord_descripcion 
                FROM coordinacion 
                WHERE CENTRO_FORMACION_cent_id = :cent_id 
                AND (coord_correo IS NULL OR coord_correo = '' OR coord_correo = 'N/A')
                ORDER BY coord_descripcion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':cent_id', $cent_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
