<?php
require_once dirname(__DIR__) . '/Conexion.php';
require_once __DIR__ . '/SchemaResilienceTrait.php';

class CentroFormacionModel
{
    use SchemaResilienceTrait;

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
        $retryLogic = function () {
            if (!$this->cent_id) {
                $this->cent_id = $this->getNextId();
            }
            $query = "INSERT INTO CENTRO_FORMACION (cent_id, cent_nombre) VALUES (:cent_id, :cent_nombre)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cent_id', $this->cent_id);
            $stmt->bindParam(':cent_nombre', $this->cent_nombre);
            return $stmt->execute();
        };

        try {
            return $retryLogic();
        } catch (PDOException $e) {
            return $this->handleTruncation($e, 'centro_formacion', [
                'cent_nombre' => $this->cent_nombre
            ], $retryLogic);
        }
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
}
