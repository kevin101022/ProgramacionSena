<?php
require_once dirname(__DIR__) . '/Conexion.php';
require_once __DIR__ . '/SchemaResilienceTrait.php';

class CoordinacionModel
{
    use SchemaResilienceTrait;

    private $coord_id;
    private $coord_descripcion;
    private $centro_formacion_cent_id;
    private $coord_nombre_coordinador;
    private $coord_correo;
    private $coord_password;
    private $db;

    public function __construct($coord_id = null, $coord_descripcion = null, $centro_formacion_cent_id = null, $coord_nombre_coordinador = null, $coord_correo = null, $coord_password = null)
    {
        $this->coord_id = $coord_id;
        $this->coord_descripcion = $coord_descripcion;
        $this->centro_formacion_cent_id = $centro_formacion_cent_id;
        $this->coord_nombre_coordinador = $coord_nombre_coordinador;
        $this->coord_correo = $coord_correo;
        $this->coord_password = $coord_password;
        $this->db = Conexion::getConnect();
    }

    // Getters
    public function getCoordId()
    {
        return $this->coord_id;
    }
    public function getCoordDescripcion()
    {
        return $this->coord_descripcion;
    }
    public function getCentroFormacionCentId()
    {
        return $this->centro_formacion_cent_id;
    }
    public function getCoordNombreCoordinador()
    {
        return $this->coord_nombre_coordinador;
    }
    public function getCoordCorreo()
    {
        return $this->coord_correo;
    }

    // Setters (Opcionales si se usan en controlador)
    public function setCoordDescripcion($desc)
    {
        $this->coord_descripcion = $desc;
    }

    public function getNextId()
    {
        $query = "SELECT COALESCE(MAX(coord_id), 0) + 1 FROM COORDINACION";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function create()
    {
        $retryLogic = function () {
            if (!$this->coord_id) {
                $this->coord_id = $this->getNextId();
            }
            $query = "INSERT INTO COORDINACION (coord_id, coord_descripcion, CENTRO_FORMACION_cent_id, coord_nombre_coordinador, coord_correo, coord_password) 
                      VALUES (:id, :descripcion, :cent_id, :coordinador, :correo, :password)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $this->coord_id);
            $stmt->bindParam(':descripcion', $this->coord_descripcion);
            $stmt->bindParam(':cent_id', $this->centro_formacion_cent_id);
            $stmt->bindParam(':coordinador', $this->coord_nombre_coordinador);
            $stmt->bindParam(':correo', $this->coord_correo);
            $stmt->bindParam(':password', $this->coord_password);
            return $stmt->execute();
        };

        try {
            return $retryLogic();
        } catch (PDOException $e) {
            return $this->handleTruncation($e, 'coordinacion', [
                'coord_descripcion' => $this->coord_descripcion,
                'coord_nombre_coordinador' => $this->coord_nombre_coordinador,
                'coord_correo' => $this->coord_correo
            ], $retryLogic);
        }
    }

    public function read()
    {
        $query = "SELECT c.coord_id, c.coord_descripcion, c.CENTRO_FORMACION_cent_id as cent_id, 
                         c.coord_nombre_coordinador, c.coord_correo, c.coord_password,
                         cf.cent_nombre 
                  FROM COORDINACION c 
                  INNER JOIN CENTRO_FORMACION cf ON c.CENTRO_FORMACION_cent_id = cf.cent_id 
                  WHERE c.coord_id = :coord_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':coord_id' => $this->coord_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $query = "SELECT c.coord_id, c.coord_descripcion, c.CENTRO_FORMACION_cent_id as cent_id, 
                         c.coord_nombre_coordinador, c.coord_correo, c.coord_password,
                         cf.cent_nombre 
                  FROM COORDINACION c 
                  INNER JOIN CENTRO_FORMACION cf ON c.CENTRO_FORMACION_cent_id = cf.cent_id 
                  ORDER BY c.coord_descripcion ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        try {
            $query = "UPDATE COORDINACION 
                      SET coord_descripcion = :descripcion, 
                          CENTRO_FORMACION_cent_id = :cent_id,
                          coord_nombre_coordinador = :coordinador,
                          coord_correo = :correo,
                          coord_password = :password
                      WHERE coord_id = :coord_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':descripcion', $this->coord_descripcion);
            $stmt->bindParam(':cent_id', $this->centro_formacion_cent_id);
            $stmt->bindParam(':coordinador', $this->coord_nombre_coordinador);
            $stmt->bindParam(':correo', $this->coord_correo);
            $stmt->bindParam(':password', $this->coord_password);
            $stmt->bindParam(':coord_id', $this->coord_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en CoordinacionModel::update: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete()
    {
        $query = "DELETE FROM COORDINACION WHERE coord_id = :coord_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':coord_id', $this->coord_id);
        return $stmt->execute();
    }
}
