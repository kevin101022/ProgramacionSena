<?php
require_once dirname(__DIR__) . '/Conexion.php';
require_once __DIR__ . '/SchemaResilienceTrait.php';

class FichaModel
{
    use SchemaResilienceTrait;

    private $fich_id;
    private $programa_prog_id;
    private $instructor_inst_id_lider;
    private $fich_jornada;
    private $coordinacion_coord_id;
    private $fich_fecha_ini_lectiva;
    private $fich_fecha_fin_lectiva;
    private $db;

    public function __construct($fich_id = null, $programa_prog_id = null, $instructor_inst_id_lider = null, $fich_jornada = null, $coordinacion_coord_id = null, $fich_fecha_ini_lectiva = null, $fich_fecha_fin_lectiva = null)
    {
        $this->fich_id = $fich_id;
        $this->programa_prog_id = $programa_prog_id;
        $this->instructor_inst_id_lider = $instructor_inst_id_lider;
        $this->fich_jornada = $fich_jornada;
        $this->coordinacion_coord_id = $coordinacion_coord_id;
        $this->fich_fecha_ini_lectiva = $fich_fecha_ini_lectiva;
        $this->fich_fecha_fin_lectiva = $fich_fecha_fin_lectiva;
        $this->db = Conexion::getConnect();
    }

    // Getters
    public function getFichId()
    {
        return $this->fich_id;
    }
    public function getProgramaProgId()
    {
        return $this->programa_prog_id;
    }
    public function getInstructorIdLider()
    {
        return $this->instructor_inst_id_lider;
    }
    public function getFichJornada()
    {
        return $this->fich_jornada;
    }
    public function getCoordinacionId()
    {
        return $this->coordinacion_coord_id;
    }
    public function getFechaIni()
    {
        return $this->fich_fecha_ini_lectiva;
    }
    public function getFechaFin()
    {
        return $this->fich_fecha_fin_lectiva;
    }

    // Setters
    public function setFichId($id)
    {
        $this->fich_id = $id;
    }
    public function setProgramaProgId($id)
    {
        $this->programa_prog_id = $id;
    }
    public function setInstructorIdLider($id)
    {
        $this->instructor_inst_id_lider = $id;
    }
    public function setFichJornada($j)
    {
        $this->fich_jornada = $j;
    }
    public function setCoordinacionId($id)
    {
        $this->coordinacion_coord_id = $id;
    }
    public function setFechaIni($f)
    {
        $this->fich_fecha_ini_lectiva = $f;
    }
    public function setFechaFin($f)
    {
        $this->fich_fecha_fin_lectiva = $f;
    }

    // CRUD
    public function getNextId()
    {
        $query = "SELECT COALESCE(MAX(fich_id), 0) + 1 FROM FICHA";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function create()
    {
        $retryLogic = function () {
            // Se asume que fich_id es proporcionado manualmente por el usuario
            if (!$this->fich_id) {
                throw new Exception("El número de ficha es obligatorio.");
            }
            $query = "INSERT INTO FICHA (fich_id, PROGRAMA_prog_id, INSTRUCTOR_inst_id_lider, fich_jornada, COORDINACION_coord_id, fich_fecha_ini_lectiva, fich_fecha_fin_lectiva) 
                      VALUES (:fich_id, :prog_id, :inst_id, :jornada, :coord_id, :fecha_ini, :fecha_fin)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':fich_id', $this->fich_id);
            $stmt->bindParam(':prog_id', $this->programa_prog_id);
            $stmt->bindParam(':inst_id', $this->instructor_inst_id_lider);
            $stmt->bindParam(':jornada', $this->fich_jornada);
            $stmt->bindParam(':coord_id', $this->coordinacion_coord_id);
            $stmt->bindParam(':fecha_ini', $this->fich_fecha_ini_lectiva);
            $stmt->bindParam(':fecha_fin', $this->fich_fecha_fin_lectiva);
            return $stmt->execute();
        };

        try {
            return $retryLogic();
        } catch (PDOException $e) {
            return $this->handleTruncation($e, 'ficha', [
                'fich_jornada' => $this->fich_jornada
            ], $retryLogic);
        }
    }

    public function read()
    {
        $sql = "SELECT f.fich_id, f.PROGRAMA_prog_id as programa_prog_id, 
                       f.INSTRUCTOR_inst_id_lider as instructor_inst_id_lider, 
                       f.fich_jornada, f.COORDINACION_coord_id as coordinacion_coord_id,
                       f.fich_fecha_ini_lectiva, f.fich_fecha_fin_lectiva,
                       p.prog_denominacion, 
                       tp.titpro_nombre,
                       i.inst_nombres, 
                       i.inst_apellidos, 
                       c.coord_descripcion as coord_nombre,
                       s.sede_nombre
                FROM FICHA f
                INNER JOIN PROGRAMA p ON f.PROGRAMA_prog_id = p.prog_codigo
                INNER JOIN TITULO_PROGRAMA tp ON p.TIT_PROGRAMA_titpro_id = tp.titpro_id
                INNER JOIN INSTRUCTOR i ON f.INSTRUCTOR_inst_id_lider = i.inst_id
                LEFT JOIN COORDINACION c ON f.COORDINACION_coord_id = c.coord_id
                LEFT JOIN (
                    SELECT FICHA_fich_id, MAX(ASIG_ID) as asig_id_max 
                    FROM ASIGNACION 
                    GROUP BY FICHA_fich_id
                ) a_max ON f.fich_id = a_max.FICHA_fich_id
                LEFT JOIN ASIGNACION a ON a_max.asig_id_max = a.ASIG_ID
                LEFT JOIN AMBIENTE amb ON a.AMBIENTE_amb_id = amb.amb_id
                LEFT JOIN SEDE s ON amb.SEDE_sede_id = s.sede_id
                WHERE f.fich_id = :fich_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':fich_id' => $this->fich_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $sql = "SELECT f.fich_id, f.PROGRAMA_prog_id as programa_prog_id, 
                       f.INSTRUCTOR_inst_id_lider as instructor_inst_id_lider, 
                       f.fich_jornada, f.COORDINACION_coord_id as coordinacion_coord_id,
                       f.fich_fecha_ini_lectiva, f.fich_fecha_fin_lectiva,
                       p.prog_denominacion, 
                       tp.titpro_nombre,
                       i.inst_nombres, 
                       i.inst_apellidos, 
                       c.coord_descripcion as coord_nombre,
                       s.sede_nombre
                FROM FICHA f
                INNER JOIN PROGRAMA p ON f.PROGRAMA_prog_id = p.prog_codigo
                INNER JOIN TITULO_PROGRAMA tp ON p.TIT_PROGRAMA_titpro_id = tp.titpro_id
                INNER JOIN INSTRUCTOR i ON f.INSTRUCTOR_inst_id_lider = i.inst_id
                LEFT JOIN COORDINACION c ON f.COORDINACION_coord_id = c.coord_id
                LEFT JOIN (
                    SELECT FICHA_fich_id, MAX(ASIG_ID) as asig_id_max 
                    FROM ASIGNACION 
                    GROUP BY FICHA_fich_id
                ) a_max ON f.fich_id = a_max.FICHA_fich_id
                LEFT JOIN ASIGNACION a ON a_max.asig_id_max = a.ASIG_ID
                LEFT JOIN AMBIENTE amb ON a.AMBIENTE_amb_id = amb.amb_id
                LEFT JOIN SEDE s ON amb.SEDE_sede_id = s.sede_id
                ORDER BY f.fich_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        try {
            $query = "UPDATE FICHA 
                      SET PROGRAMA_prog_id = :prog_id, 
                          INSTRUCTOR_inst_id_lider = :inst_id, 
                          fich_jornada = :jornada, 
                          COORDINACION_coord_id = :coord_id,
                          fich_fecha_ini_lectiva = :fecha_ini,
                          fich_fecha_fin_lectiva = :fecha_fin
                      WHERE fich_id = :fich_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':prog_id', $this->programa_prog_id);
            $stmt->bindParam(':inst_id', $this->instructor_inst_id_lider);
            $stmt->bindParam(':jornada', $this->fich_jornada);
            $stmt->bindParam(':coord_id', $this->coordinacion_coord_id);
            $stmt->bindParam(':fecha_ini', $this->fich_fecha_ini_lectiva);
            $stmt->bindParam(':fecha_fin', $this->fich_fecha_fin_lectiva);
            $stmt->bindParam(':fich_id', $this->fich_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en FichaModel::update: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete()
    {
        $query = "DELETE FROM FICHA WHERE fich_id = :fich_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fich_id', $this->fich_id);
        return $stmt->execute();
    }
}
