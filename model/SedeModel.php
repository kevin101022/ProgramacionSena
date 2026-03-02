<?php
require_once dirname(__DIR__) . '/Conexion.php';

class SedeModel
{

    private $sede_id;
    private $sede_nombre;
    private $centro_formacion_id;
    private $db;

    public function __construct($sede_id = null, $sede_nombre = null, $centro_formacion_id = null)
    {
        $this->setSedeId($sede_id);
        $this->setSedeNombre($sede_nombre);
        $this->setCentroFormacionId($centro_formacion_id);
        $this->db = Conexion::getConnect();
    }

    // Getters
    public function getSedeId()
    {
        return $this->sede_id;
    }
    public function getSedeNombre()
    {
        return $this->sede_nombre;
    }
    public function getCentroFormacionId()
    {
        return $this->centro_formacion_id;
    }

    // Setters
    public function setSedeId($sede_id)
    {
        $this->sede_id = $sede_id;
    }
    public function setSedeNombre($sede_nombre)
    {
        $this->sede_nombre = $sede_nombre;
    }
    public function setCentroFormacionId($centro_formacion_id)
    {
        $this->centro_formacion_id = $centro_formacion_id;
    }

    // CRUD helpers
    public function getNextId()
    {
        $query = "SELECT COALESCE(MAX(sede_id), 0) + 1 FROM SEDE";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function create()
    {
        if (!$this->sede_id) {
            $this->sede_id = $this->getNextId();
        }
        $query = "INSERT INTO SEDE (sede_id, sede_nombre, CENTRO_FORMACION_cent_id) 
        VALUES (:sede_id, :sede_nombre, :cent_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':sede_id', $this->sede_id);
        $stmt->bindParam(':sede_nombre', $this->sede_nombre);
        $stmt->bindParam(':cent_id', $this->centro_formacion_id);
        $stmt->execute();
        return $this->sede_id;
    }
    public function read()
    {
        $sql = "SELECT sede_id, sede_nombre, CENTRO_FORMACION_cent_id FROM SEDE WHERE sede_id = :sede_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sede_id' => $this->sede_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll($cent_id = null)
    {
        $sql = "SELECT sede_id, sede_nombre, CENTRO_FORMACION_cent_id FROM SEDE";
        if ($cent_id) {
            $sql .= " WHERE CENTRO_FORMACION_cent_id = :cent_id";
            $sql .= " ORDER BY sede_nombre ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cent_id' => $cent_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $sql .= " ORDER BY sede_nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function update()
    {
        $query = "UPDATE SEDE SET sede_nombre = :sede_nombre, CENTRO_FORMACION_cent_id = :cent_id WHERE sede_id = :sede_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':sede_nombre', $this->sede_nombre);
        $stmt->bindParam(':cent_id', $this->centro_formacion_id);
        $stmt->bindParam(':sede_id', $this->sede_id);
        $stmt->execute();
        return $stmt;
    }
    public function delete()
    {
        $query = "DELETE FROM SEDE WHERE sede_id = :sede_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':sede_id', $this->sede_id);
        $stmt->execute();
        return $stmt;
    }

    public function getFichasBySede()
    {
        $sql = "SELECT f.fich_id, f.fich_jornada,
                       f.fich_fecha_ini_lectiva, f.fich_fecha_fin_lectiva,
                       p.prog_denominacion, 
                       tp.titpro_nombre,
                       i.inst_nombres, 
                       i.inst_apellidos
                FROM FICHA f
                INNER JOIN PROGRAMA p ON f.PROGRAMA_prog_id = p.prog_codigo
                INNER JOIN TITULO_PROGRAMA tp ON p.TIT_PROGRAMA_titpro_id = tp.titpro_id
                LEFT JOIN INSTRUCTOR i ON f.INSTRUCTOR_inst_id_lider = i.numero_documento
                INNER JOIN (
                    SELECT FICHA_fich_id, AMBIENTE_amb_id 
                    FROM ASIGNACION 
                    GROUP BY FICHA_fich_id, AMBIENTE_amb_id
                ) a ON f.fich_id = a.FICHA_fich_id
                INNER JOIN AMBIENTE amb ON a.AMBIENTE_amb_id = amb.amb_id
                WHERE amb.SEDE_sede_id = :sede_id
                ORDER BY f.fich_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sede_id' => $this->sede_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProgramasBySede()
    {
        $sql = "SELECT DISTINCT p.prog_codigo, p.prog_denominacion, 
                       tp.titpro_nombre, tp.titpro_nivel
                FROM PROGRAMA p
                INNER JOIN TITULO_PROGRAMA tp ON p.TIT_PROGRAMA_titpro_id = tp.titpro_id
                INNER JOIN FICHA f ON f.PROGRAMA_prog_id = p.prog_codigo
                INNER JOIN (
                    SELECT FICHA_fich_id, AMBIENTE_amb_id 
                    FROM ASIGNACION 
                    GROUP BY FICHA_fich_id, AMBIENTE_amb_id
                ) a ON f.fich_id = a.FICHA_fich_id
                INNER JOIN AMBIENTE amb ON a.AMBIENTE_amb_id = amb.amb_id
                WHERE amb.SEDE_sede_id = :sede_id
                ORDER BY p.prog_denominacion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sede_id' => $this->sede_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
