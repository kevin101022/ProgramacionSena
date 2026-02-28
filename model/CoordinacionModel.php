<?php
require_once dirname(__DIR__) . '/Conexion.php';

class CoordinacionModel
{

    private $numero_documento;
    private $coord_descripcion;
    private $centro_formacion_cent_id;
    private $coord_nombre_coordinador;
    private $coord_correo;
    private $coord_password;
    private $db;

    public function __construct($numero_documento = null, $coord_descripcion = null, $centro_formacion_cent_id = null, $coord_nombre_coordinador = null, $coord_correo = null, $coord_password = null)
    {
        $this->numero_documento = $numero_documento;
        $this->coord_descripcion = $coord_descripcion;
        $this->centro_formacion_cent_id = $centro_formacion_cent_id;
        $this->coord_nombre_coordinador = $coord_nombre_coordinador;
        $this->coord_correo = $coord_correo;
        $this->coord_password = $coord_password;
        $this->db = Conexion::getConnect();
    }

    // Getters
    public function getNumeroDocumento()
    {
        return $this->numero_documento;
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

    public function create()
    {
        $query = "INSERT INTO COORDINACION (numero_documento, coord_descripcion, CENTRO_FORMACION_cent_id, coord_nombre_coordinador, coord_correo, coord_password, estado) 
                  VALUES (:numero_documento, :descripcion, :cent_id, :coordinador, :correo, :password, 1)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':numero_documento', $this->numero_documento);
        $stmt->bindParam(':descripcion', $this->coord_descripcion);
        $stmt->bindParam(':cent_id', $this->centro_formacion_cent_id);
        $stmt->bindParam(':coordinador', $this->coord_nombre_coordinador);
        $stmt->bindParam(':correo', $this->coord_correo);
        $stmt->bindParam(':password', $this->coord_password);
        return $stmt->execute();
    }

    public function read($cent_id = null)
    {
        $query = "SELECT c.numero_documento as coord_id, c.coord_descripcion, c.CENTRO_FORMACION_cent_id as cent_id, 
                         c.coord_nombre_coordinador, c.coord_correo, c.coord_password,
                         cf.cent_nombre 
                  FROM COORDINACION c 
                  INNER JOIN CENTRO_FORMACION cf ON c.CENTRO_FORMACION_cent_id = cf.cent_id 
                  WHERE c.numero_documento = :numero_documento AND c.estado = 1";

        $params = [':numero_documento' => $this->numero_documento];
        if ($cent_id) {
            $query .= " AND c.CENTRO_FORMACION_cent_id = :cent_id";
            $params[':cent_id'] = $cent_id;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll($cent_id = null)
    {
        $query = "SELECT c.numero_documento as coord_id, c.coord_descripcion, c.CENTRO_FORMACION_cent_id as cent_id, 
                         c.coord_nombre_coordinador, c.coord_correo, c.coord_password,
                         cf.cent_nombre 
                  FROM COORDINACION c 
                  INNER JOIN CENTRO_FORMACION cf ON c.CENTRO_FORMACION_cent_id = cf.cent_id 
                  WHERE c.estado = 1";

        if ($cent_id) {
            $query .= " AND c.CENTRO_FORMACION_cent_id = :cent_id";
        }
        $query .= " ORDER BY c.coord_descripcion ASC";

        $stmt = $this->db->prepare($query);
        if ($cent_id) {
            $stmt->execute([':cent_id' => $cent_id]);
        } else {
            $stmt->execute();
        }
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
                      WHERE numero_documento = :numero_documento";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':descripcion', $this->coord_descripcion);
            $stmt->bindParam(':cent_id', $this->centro_formacion_cent_id);
            $stmt->bindParam(':coordinador', $this->coord_nombre_coordinador);
            $stmt->bindParam(':correo', $this->coord_correo);
            $stmt->bindParam(':password', $this->coord_password);
            $stmt->bindParam(':numero_documento', $this->numero_documento);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en CoordinacionModel::update: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete()
    {
        $query = "UPDATE COORDINACION SET estado = 0 WHERE numero_documento = :numero_documento";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':numero_documento', $this->numero_documento);
        return $stmt->execute();
    }

    public function getProgramas()
    {
        $sql = "SELECT DISTINCT p.prog_codigo, p.prog_denominacion 
                FROM PROGRAMA p 
                INNER JOIN FICHA f ON p.prog_codigo = f.PROGRAMA_prog_id 
                WHERE f.COORDINACION_coord_id = :numero_documento
                ORDER BY p.prog_denominacion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':numero_documento' => $this->numero_documento]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
