<?php
require_once dirname(__DIR__) . '/Conexion.php';
require_once __DIR__ . '/SchemaResilienceTrait.php';

class TituloProgramaModel
{
    use SchemaResilienceTrait;

    private $titpro_id;
    private $titpro_nombre;
    private $db;

    public function __construct($titpro_id = null, $titpro_nombre = null)
    {
        $this->setTitproId($titpro_id);
        $this->setTitproNombre($titpro_nombre);
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
        $retryLogic = function () {
            if (!$this->titpro_id) {
                $this->titpro_id = $this->getNextId();
            }
            $query = "INSERT INTO TITULO_PROGRAMA (titpro_id, titpro_nombre) VALUES (:titpro_id, :titpro_nombre)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':titpro_id', $this->titpro_id);
            $stmt->bindParam(':titpro_nombre', $this->titpro_nombre);

            if ($stmt->execute()) {
                return $this->titpro_id;
            }
            return null;
        };

        try {
            return $retryLogic();
        } catch (PDOException $e) {
            return $this->handleTruncation($e, 'titulo_programa', ['titpro_nombre' => $this->titpro_nombre], $retryLogic);
        }
    }

    public function read()
    {
        $sql = "SELECT * FROM TITULO_PROGRAMA WHERE titpro_id = :titpro_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':titpro_id' => $this->titpro_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $sql = "SELECT * FROM TITULO_PROGRAMA ORDER BY titpro_nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = "UPDATE TITULO_PROGRAMA SET titpro_nombre = :titpro_nombre WHERE titpro_id = :titpro_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':titpro_nombre', $this->titpro_nombre);
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
