<?php
require_once dirname(__DIR__) . '/Conexion.php';

class CompetenciaProgramaModel
{
    private $programa_prog_id; // FK a PROGRAMA (prog_codigo)
    private $competencia_comp_id; // FK a COMPETENCIA (comp_id)
    private $db;

    public function __construct($programa_prog_id = null, $competencia_comp_id = null)
    {
        $this->programa_prog_id = $programa_prog_id;
        $this->competencia_comp_id = $competencia_comp_id;
        $this->db = Conexion::getConnect();
    }

    public function create()
    {
        $query = "INSERT INTO COMPETxPROGRAMA (PROGRAMA_prog_id, COMPETENCIA_comp_id) 
                  VALUES (:prog_id, :comp_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':prog_id', $this->programa_prog_id);
        $stmt->bindParam(':comp_id', $this->competencia_comp_id);
        return $stmt->execute();
    }

    public function getCompetenciasByPrograma($progId)
    {
        $sql = "SELECT c.comp_id, c.comp_nombre_corto, c.comp_nombre_unidad_competencia, c.comp_horas,
                       cp.PROGRAMA_prog_id as programa_prog_id
                FROM COMPETENCIA c
                INNER JOIN COMPETxPROGRAMA cp ON c.comp_id = cp.COMPETENCIA_comp_id
                WHERE cp.PROGRAMA_prog_id = :prog_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':prog_id' => $progId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // El resto de métodos no parecen usarse masivamente pero deberían seguir el mismo patrón
    public function syncProgramas($compId, $programaIds)
    {
        try {
            $this->db->beginTransaction();

            // 1. Obtener programas actuales
            $currentProgs = $this->getProgramasByCompetencia($compId);
            $currentIds = array_column($currentProgs, 'prog_codigo');

            // 2. Calcular diferencias
            $toInsert = array_diff($programaIds, $currentIds);
            $toDelete = array_diff($currentIds, $programaIds);

            // 3. Insertar nuevos
            if (!empty($toInsert)) {
                $insQuery = "INSERT INTO COMPETxPROGRAMA (COMPETENCIA_comp_id, PROGRAMA_prog_id) VALUES (:comp_id, :prog_id)";
                $stmt = $this->db->prepare($insQuery);
                foreach ($toInsert as $progId) {
                    $stmt->execute([':comp_id' => $compId, ':prog_id' => $progId]);
                }
            }

            // 4. Eliminar desmarcados (Manejo de FK)
            if (!empty($toDelete)) {
                $delQuery = "DELETE FROM COMPETxPROGRAMA WHERE COMPETENCIA_comp_id = :comp_id AND PROGRAMA_prog_id = :prog_id";
                $stmt = $this->db->prepare($delQuery);
                foreach ($toDelete as $progId) {
                    try {
                        $stmt->execute([':comp_id' => $compId, ':prog_id' => $progId]);
                    } catch (PDOException $e) {
                        // Si falla por FK (1451), lanzamos excepción para avisar al usuario
                        if ($e->getCode() == '23000' && strpos($e->getMessage(), '1451') !== false) {
                            throw new Exception("No se puede desvincular el programa $progId porque tiene instructores o asignaciones asociadas.");
                        }
                        throw $e;
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }
    public function deleteAllByCompetencia($compId)
    {
        $delQuery = "DELETE FROM COMPETxPROGRAMA WHERE COMPETENCIA_comp_id = :comp_id";
        return $this->db->prepare($delQuery)->execute([':comp_id' => $compId]);
    }

    public function getProgramasByCompetencia($compId)
    {
        $sql = "SELECT p.prog_codigo, p.prog_denominacion, p.prog_tipo, p.TIT_PROGRAMA_titpro_id as titpro_id,
                       cp.COMPETENCIA_comp_id as competencia_comp_id,
                       t.titpro_nombre 
                FROM PROGRAMA p
                INNER JOIN COMPETxPROGRAMA cp ON p.prog_codigo = cp.PROGRAMA_prog_id
                LEFT JOIN TITULO_PROGRAMA t ON p.TIT_PROGRAMA_titpro_id = t.titpro_id
                WHERE cp.COMPETENCIA_comp_id = :comp_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':comp_id' => $compId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
