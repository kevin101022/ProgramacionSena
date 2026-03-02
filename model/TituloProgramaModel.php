<?php
require_once dirname(__DIR__) . '/Conexion.php';

class TituloProgramaModel
{

    private $titpro_id;
    private $titpro_nombre;
    private $centro_formacion_cent_id;
    private $db;

    public function __construct($titpro_id = null, $titpro_nombre = null, $centro_formacion_cent_id = null)
    {
        $this->setTitproId($titpro_id);
        $this->setTitproNombre($titpro_nombre);
        $this->setCentroFormacionId($centro_formacion_cent_id);
        $this->db = Conexion::getConnect();
    }

    // Getters
    public function getTitproId()
    {
        return $this->titpro_id;
    }
    public function getTitproNombre()
    {
        return $this->titpro_nombre;
    }

    // Setters
    public function setTitproId($titpro_id)
    {
        $this->titpro_id = $titpro_id;
    }
    public function setTitproNombre($titpro_nombre)
    {
        $this->titpro_nombre = $titpro_nombre;
    }
    public function getCentroFormacionId()
    {
        return $this->centro_formacion_cent_id;
    }
    public function setCentroFormacionId($id)
    {
        $this->centro_formacion_cent_id = $id;
    }

    // CRUD
    public function getNextId()
    {
        $query = "SELECT COALESCE(MAX(titpro_id), 0) + 1 FROM TITULO_PROGRAMA";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function create()
    {
        if (!$this->titpro_id) {
            $this->titpro_id = $this->getNextId();
        }
        $query = "INSERT INTO TITULO_PROGRAMA (titpro_id, titpro_nombre, centro_formacion_cent_id) VALUES (:titpro_id, :titpro_nombre, :centro_formacion_cent_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':titpro_id', $this->titpro_id);
        $stmt->bindParam(':titpro_nombre', $this->titpro_nombre);
        $stmt->bindParam(':centro_formacion_cent_id', $this->centro_formacion_cent_id);

        if ($stmt->execute()) {
            return $this->titpro_id;
        }
        return null;
    }

    public function read()
    {
        $sql = "SELECT * FROM TITULO_PROGRAMA WHERE titpro_id = :titpro_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':titpro_id' => $this->titpro_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll($cent_id = null)
    {
        $sql = "SELECT * FROM TITULO_PROGRAMA";
        $params = [];
        if ($cent_id) {
            $sql .= " WHERE centro_formacion_cent_id = :cent_id OR centro_formacion_cent_id IS NULL";
            $params[':cent_id'] = $cent_id;
        }
        $sql .= " ORDER BY titpro_nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = "UPDATE TITULO_PROGRAMA SET titpro_nombre = :titpro_nombre, centro_formacion_cent_id = :centro_formacion_cent_id WHERE titpro_id = :titpro_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':titpro_nombre', $this->titpro_nombre);
        $stmt->bindParam(':centro_formacion_cent_id', $this->centro_formacion_cent_id);
        $stmt->bindParam(':titpro_id', $this->titpro_id);
        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM TITULO_PROGRAMA WHERE titpro_id = :titpro_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':titpro_id', $this->titpro_id);
        return $stmt->execute();
    }
}
