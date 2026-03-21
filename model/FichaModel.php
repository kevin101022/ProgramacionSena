<?php
require_once dirname(__DIR__) . '/Conexion.php';

class FichaModel
{

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

    // CRUD
    public function getNextId()
    {
        $query = "SELECT COALESCE(MAX(fich_id), 0) + 1 FROM ficha";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function create()
    {
        if (!$this->fich_id) {
            throw new Exception("El número de ficha es obligatorio.");
        }
        $query = "INSERT INTO ficha (fich_id, programa_prog_id, instructor_inst_id_lider, fich_jornada, coordinacion_coord_id, fich_fecha_ini_lectiva, fich_fecha_fin_lectiva) 
                  VALUES (:fich_id, :prog_id, :inst_id, :jornada, :coord_id, :fecha_ini, :fecha_fin)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':fich_id' => $this->fich_id,
            ':prog_id' => $this->programa_prog_id,
            ':inst_id' => $this->instructor_inst_id_lider,
            ':jornada' => $this->fich_jornada,
            ':coord_id' => $this->coordinacion_coord_id,
            ':fecha_ini' => $this->fich_fecha_ini_lectiva,
            ':fecha_fin' => $this->fich_fecha_fin_lectiva
        ]);
        return true;
    }

    public function read($cent_id = null)
    {
        $sql = "SELECT f.fich_id, f.programa_prog_id, 
                       f.instructor_inst_id_lider, 
                       f.fich_jornada, f.coordinacion_coord_id,
                       f.fich_fecha_ini_lectiva, f.fich_fecha_fin_lectiva,
                       p.prog_denominacion, 
                       tp.titpro_nombre,
                       i.inst_nombres, 
                       i.inst_apellidos, 
                       c.coord_descripcion as coord_nombre,
                       s.sede_nombre,
                       (SELECT COUNT(*) FROM competencia cp WHERE cp.programa_prog_id = f.programa_prog_id) as total_comps,
                       (SELECT COUNT(DISTINCT a_sub.competencia_comp_id) FROM asignacion a_sub WHERE a_sub.ficha_fich_id = f.fich_id) as assigned_comps,
                       (SELECT STRING_AGG(DISTINCT sub_i.inst_nombres || ' ' || sub_i.inst_apellidos, ', ') 
                        FROM asignacion a_sub 
                        JOIN instructor sub_i ON a_sub.instructor_inst_id = sub_i.numero_documento 
                        WHERE a_sub.ficha_fich_id = f.fich_id) as instructores_historial
                FROM ficha f
                LEFT JOIN programa p ON f.programa_prog_id = p.prog_codigo
                LEFT JOIN titulo_programa tp ON p.tit_programa_titpro_id = tp.titpro_id
                LEFT JOIN instructor i ON f.instructor_inst_id_lider = i.numero_documento
                LEFT JOIN coordinacion c ON f.coordinacion_coord_id = c.coord_id
                LEFT JOIN (
                    SELECT ficha_fich_id, MAX(asig_id) as asig_id_max 
                    FROM asignacion 
                    GROUP BY ficha_fich_id
                ) a_max ON f.fich_id = a_max.ficha_fich_id
                LEFT JOIN asignacion a ON a_max.asig_id_max = a.asig_id
                LEFT JOIN ambiente amb ON a.ambiente_amb_id = amb.amb_id
                LEFT JOIN sede s ON amb.sede_sede_id = s.sede_id
                WHERE f.fich_id = :fich_id";

        $params = [':fich_id' => $this->fich_id];
        if ($cent_id) {
            $sql .= " AND (c.centro_formacion_cent_id = :cent_id OR i.centro_formacion_cent_id = :cent_id)";
            $params[':cent_id'] = $cent_id;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll($cent_id = null, $coord_id = null)
    {
        $sql = "SELECT f.fich_id, f.programa_prog_id, 
                       f.instructor_inst_id_lider, 
                       f.fich_jornada, f.coordinacion_coord_id,
                       f.fich_fecha_ini_lectiva, f.fich_fecha_fin_lectiva,
                       p.prog_denominacion, 
                       tp.titpro_nombre,
                       i.inst_nombres, 
                       i.inst_apellidos, 
                       c.coord_descripcion as coord_nombre,
                       s.sede_nombre,
                       (SELECT COUNT(*) FROM competencia cp WHERE cp.programa_prog_id = f.programa_prog_id) as total_comps,
                       (SELECT COUNT(DISTINCT a_sub.competencia_comp_id) FROM asignacion a_sub WHERE a_sub.ficha_fich_id = f.fich_id) as assigned_comps,
                       (SELECT STRING_AGG(DISTINCT sub_i.inst_nombres || ' ' || sub_i.inst_apellidos, ', ') 
                        FROM asignacion a_sub 
                        JOIN instructor sub_i ON a_sub.instructor_inst_id = sub_i.numero_documento 
                        WHERE a_sub.ficha_fich_id = f.fich_id) as instructores_historial
                FROM ficha f
                LEFT JOIN programa p ON f.programa_prog_id = p.prog_codigo
                LEFT JOIN titulo_programa tp ON p.tit_programa_titpro_id = tp.titpro_id
                LEFT JOIN instructor i ON f.instructor_inst_id_lider = i.numero_documento
                LEFT JOIN coordinacion c ON f.coordinacion_coord_id = c.coord_id
                LEFT JOIN (
                    SELECT ficha_fich_id, MAX(asig_id) as asig_id_max 
                    FROM asignacion 
                    GROUP BY ficha_fich_id
                ) a_max ON f.fich_id = a_max.ficha_fich_id
                LEFT JOIN asignacion a ON a_max.asig_id_max = a.asig_id
                LEFT JOIN ambiente amb ON a.ambiente_amb_id = amb.amb_id
                LEFT JOIN sede s ON amb.sede_sede_id = s.sede_id";

        $params = [];
        $where = [];

        if ($coord_id) {
            $where[] = "f.coordinacion_coord_id = :coord_id";
            $params[':coord_id'] = $coord_id;
        } elseif ($cent_id) {
            $where[] = "(c.centro_formacion_cent_id = :cent_id OR i.centro_formacion_cent_id = :cent_id)";
            $params[':cent_id'] = $cent_id;
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY f.fich_id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = "UPDATE ficha 
                  SET programa_prog_id = :prog_id, 
                      instructor_inst_id_lider = :inst_id, 
                      fich_jornada = :jornada, 
                      coordinacion_coord_id = :coord_id,
                      fich_fecha_ini_lectiva = :fecha_ini,
                      fich_fecha_fin_lectiva = :fecha_fin
                  WHERE fich_id = :fich_id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':prog_id' => $this->programa_prog_id,
            ':inst_id' => $this->instructor_inst_id_lider,
            ':jornada' => $this->fich_jornada,
            ':coord_id' => $this->coordinacion_coord_id,
            ':fecha_ini' => $this->fich_fecha_ini_lectiva,
            ':fecha_fin' => $this->fich_fecha_fin_lectiva,
            ':fich_id' => $this->fich_id
        ]);
    }

    public function delete()
    {
        $query = "DELETE FROM ficha WHERE fich_id = :fich_id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':fich_id' => $this->fich_id]);
    }

    public function getCompetenciasVistas()
    {
        $query = "SELECT 
                    c.comp_id, 
                    c.comp_nombre_corto, 
                    c.comp_horas as horas_totales,
                    i.inst_nombres, 
                    i.inst_apellidos,
                    COALESCE(SUM(EXTRACT(EPOCH FROM (da.detasig_hora_fin - da.detasig_hora_ini))/3600)::INTEGER, 0) as horas_asignadas
                  FROM asignacion a
                  INNER JOIN competencia c ON a.competencia_comp_id = c.comp_id
                  INNER JOIN instructor i ON a.instructor_inst_id = i.numero_documento
                  LEFT JOIN detallexasignacion da ON da.asignacion_asig_id = a.asig_id
                  WHERE a.ficha_fich_id = :fich_id
                  GROUP BY c.comp_id, c.comp_nombre_corto, c.comp_horas, i.inst_nombres, i.inst_apellidos
                  ORDER BY c.comp_nombre_corto ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':fich_id' => $this->fich_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCompetenciasFaltantes()
    {
        $query = "SELECT 
                    c.comp_id, 
                    c.comp_nombre_unidad_competencia as comp_nombre,
                    c.comp_nombre_corto,
                    c.comp_horas as comp_num_horas
                  FROM competencia c
                  WHERE c.programa_prog_id = (SELECT programa_prog_id FROM ficha WHERE fich_id = :fich_id)
                  AND c.comp_id NOT IN (
                      SELECT competencia_comp_id FROM asignacion WHERE ficha_fich_id = :fich_id
                  )
                  ORDER BY c.comp_nombre_corto ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':fich_id' => $this->fich_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
