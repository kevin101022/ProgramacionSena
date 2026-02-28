<?php
require_once dirname(__DIR__) . '/Conexion.php';

class CentroFormacionModel
{

    private $cent_id;
    private $cent_nombre;
    private $db;

    public function __construct($cent_id = null, $cent_nombre = null)
    {
        $this->cent_id = $cent_id;
        $this->cent_nombre = $cent_nombre;
        $this->db = Conexion::getConnect();
    }

    // Getters
    public function getCentId()
    {
        return $this->cent_id;
    }
    public function getCentNombre()
    {
        return $this->cent_nombre;
    }

    // Setters
    public function setCentId($cent_id)
    {
        $this->cent_id = $cent_id;
    }
    public function setCentNombre($cent_nombre)
    {
        $this->cent_nombre = $cent_nombre;
    }

    public function getNextId()
    {
        $query = "SELECT COALESCE(MAX(cent_id), 0) + 1 FROM CENTRO_FORMACION";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function create()
    {
        if (!$this->cent_id) {
            $this->cent_id = $this->getNextId();
        }
        $query = "INSERT INTO CENTRO_FORMACION (cent_id, cent_nombre) VALUES (:cent_id, :cent_nombre)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cent_id', $this->cent_id);
        $stmt->bindParam(':cent_nombre', $this->cent_nombre);
        return $stmt->execute();
    }

    public function read()
    {
        $query = "SELECT * FROM CENTRO_FORMACION WHERE cent_id = :cent_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':cent_id' => $this->cent_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $query = "SELECT * FROM CENTRO_FORMACION ORDER BY cent_nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = "UPDATE CENTRO_FORMACION SET cent_nombre = :cent_nombre WHERE cent_id = :cent_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cent_nombre', $this->cent_nombre);
        $stmt->bindParam(':cent_id', $this->cent_id);
        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM CENTRO_FORMACION WHERE cent_id = :cent_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cent_id', $this->cent_id);
        return $stmt->execute();
    }

    public function getInstructores()
    {
        $sql = "SELECT inst_id, inst_nombres, inst_apellidos, inst_correo, inst_telefono 
                FROM INSTRUCTOR 
                WHERE CENTRO_FORMACION_cent_id = :cent_id 
                ORDER BY inst_nombres ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cent_id' => $this->cent_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCoordinaciones()
    {
        $sql = "SELECT coord_id, coord_descripcion 
                FROM COORDINACION 
                WHERE CENTRO_FORMACION_cent_id = :cent_id 
                ORDER BY coord_descripcion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cent_id' => $this->cent_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
