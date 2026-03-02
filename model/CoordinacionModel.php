<?php
require_once dirname(__DIR__) . '/Conexion.php';

class CoordinacionModel
{
    private $coord_id;
    private $coord_descripcion;
    private $centro_formacion_cent_id;
    private $coordinador_actual;
    private $estado;
    private $db;

    public function __construct($coord_id = null, $coord_descripcion = null, $centro_formacion_cent_id = null, $coordinador_actual = null, $estado = 1)
    {
        $this->coord_id = $coord_id;
        $this->coord_descripcion = $coord_descripcion;
        $this->centro_formacion_cent_id = $centro_formacion_cent_id;
        $this->coordinador_actual = $coordinador_actual;
        $this->estado = $estado;
        $this->db = Conexion::getConnect();
    }

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
    public function getCoordinadorActual()
    {
        return $this->coordinador_actual;
    }

    public function create()
    {
        $query = "INSERT INTO COORDINACION (coord_descripcion, centro_formacion_cent_id, coordinador_actual, estado) 
                  VALUES (:descripcion, :cent_id, :coordinador, :estado)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':descripcion', $this->coord_descripcion);
        $stmt->bindParam(':cent_id', $this->centro_formacion_cent_id);
        $stmt->bindParam(':coordinador', $this->coordinador_actual);
        $stmt->bindParam(':estado', $this->estado);
        return $stmt->execute();
    }

    public function read($cent_id = null)
    {
        $query = "SELECT c.coord_id, c.coord_descripcion, c.centro_formacion_cent_id as cent_id, 
                         c.coordinador_actual as numero_documento,
                         u.coord_nombre_coordinador, u.coord_correo,
                         cf.cent_nombre 
                  FROM COORDINACION c 
                  LEFT JOIN usuario_coordinador u ON c.coordinador_actual = u.numero_documento
                  INNER JOIN CENTRO_FORMACION cf ON c.centro_formacion_cent_id = cf.cent_id 
                  WHERE c.coord_id = :coord_id AND c.estado = 1";

        $params = [':coord_id' => $this->coord_id];
        if ($cent_id) {
            $query .= " AND c.centro_formacion_cent_id = :cent_id";
            $params[':cent_id'] = $cent_id;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll($cent_id = null)
    {
        $query = "SELECT c.coord_id, c.coord_descripcion, c.centro_formacion_cent_id as cent_id, 
                         c.coordinador_actual as numero_documento,
                         COALESCE(u.coord_nombre_coordinador, 'Vacante') as coord_nombre_coordinador, 
                         COALESCE(u.coord_correo, 'N/A') as coord_correo,
                         cf.cent_nombre 
                  FROM COORDINACION c 
                  LEFT JOIN usuario_coordinador u ON c.coordinador_actual = u.numero_documento
                  INNER JOIN CENTRO_FORMACION cf ON c.centro_formacion_cent_id = cf.cent_id 
                  WHERE c.estado = 1";

        if ($cent_id) {
            $query .= " AND c.centro_formacion_cent_id = :cent_id";
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
                          centro_formacion_cent_id = :cent_id,
                          coordinador_actual = :coordinador
                      WHERE coord_id = :coord_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':descripcion', $this->coord_descripcion);
            $stmt->bindParam(':cent_id', $this->centro_formacion_cent_id);
            $stmt->bindParam(':coordinador', $this->coordinador_actual);
            $stmt->bindParam(':coord_id', $this->coord_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en CoordinacionModel::update: " . $e->getMessage());
            throw $e;
        }
    }

    public function desvincular()
    {
        // En esta arquitectura normalizada, desvincular significa asignar NULL al coordinador
        // manteniendo la dependencia activa.
        $query = "UPDATE COORDINACION 
                  SET coordinador_actual = NULL
                  WHERE coord_id = :coord_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':coord_id', $this->coord_id);
        return $stmt->execute();
    }

    public function delete()
    {
        $query = "UPDATE COORDINACION SET estado = 0 WHERE coord_id = :coord_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':coord_id', $this->coord_id);
        return $stmt->execute();
    }

    public function getProgramas()
    {
        // Esta consulta busca todos los programas que tienen al menos una ficha
        // vinculada a esta coordinación específica.
        $sql = "SELECT DISTINCT p.prog_codigo, p.prog_denominacion 
                FROM PROGRAMA p 
                INNER JOIN FICHA f ON p.prog_codigo = f.PROGRAMA_prog_id 
                WHERE f.COORDINACION_coord_id = :coord_id
                ORDER BY p.prog_denominacion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':coord_id' => $this->coord_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
