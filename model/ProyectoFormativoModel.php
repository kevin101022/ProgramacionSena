<?php
class ProyectoFormativoModel {
    private $db;

    public function __construct() {
        $this->db = Conexion::getConnect();
    }

    public function getAll($centro_id) {
        $stmt = $this->db->prepare("SELECT p.*, prog.prog_denominacion FROM proyecto_formativo p JOIN programa prog ON p.programa_prog_codigo = prog.prog_codigo WHERE p.centro_formacion_cent_id = :centro_id");
        $stmt->execute(['centro_id' => $centro_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($pf_id) {
        $stmt = $this->db->prepare("
            SELECT p.*, prog.prog_denominacion 
            FROM proyecto_formativo p
            JOIN programa prog ON p.programa_prog_codigo = prog.prog_codigo
            WHERE p.pf_id = :pf_id
        ");
        $stmt->execute(['pf_id' => $pf_id]);
        $proyecto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($proyecto) {
            $proyecto['fases'] = $this->getFasesByProyecto($pf_id);
        }
        return $proyecto;
    }

    public function getFasesByProyecto($pf_id) {
        $stmt = $this->db->prepare("SELECT * FROM fase_proyecto WHERE pf_pf_id = :pf_id ORDER BY fase_orden ASC");
        $stmt->execute(['pf_id' => $pf_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByPrograma($prog_codigo) {
        $stmt = $this->db->prepare("SELECT * FROM proyecto_formativo WHERE programa_prog_codigo = :prog_codigo");
        $stmt->execute(['prog_codigo' => $prog_codigo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO proyecto_formativo (pf_codigo, pf_nombre, pf_descripcion, programa_prog_codigo, centro_formacion_cent_id) VALUES (:pf_codigo, :pf_nombre, :pf_descripcion, :programa_prog_codigo, :centro_formacion_cent_id) RETURNING pf_id");
        $stmt->execute([
            'pf_codigo' => $data['pf_codigo'],
            'pf_nombre' => $data['pf_nombre'],
            'pf_descripcion' => $data['pf_descripcion'],
            'programa_prog_codigo' => $data['programa_prog_codigo'],
            'centro_formacion_cent_id' => $data['centro_formacion_cent_id']
        ]);
        return $stmt->fetchColumn();
    }

    public function createFase($data) {
        $stmt = $this->db->prepare("INSERT INTO fase_proyecto (fase_nombre, fase_orden, fase_fecha_ini, fase_fecha_fin, pf_pf_id) VALUES (:fase_nombre, :fase_orden, :fase_fecha_ini, :fase_fecha_fin, :pf_pf_id) RETURNING fase_id");
        $stmt->execute([
            'fase_nombre' => $data['fase_nombre'],
            'fase_orden' => $data['fase_orden'],
            'fase_fecha_ini' => $data['fase_fecha_ini'],
            'fase_fecha_fin' => $data['fase_fecha_fin'],
            'pf_pf_id' => $data['pf_pf_id']
        ]);
        return $stmt->fetchColumn();
    }

    public function createConFases($proyecto, $fases) {
        try {
            $this->db->beginTransaction();

            $pf_id = $this->create($proyecto);

            foreach ($fases as $fase) {
                $fase['pf_pf_id'] = $pf_id;
                $this->createFase($fase);
            }

            $this->db->commit();
            return $pf_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($pf_id, $data) {
        $stmt = $this->db->prepare("UPDATE proyecto_formativo SET pf_codigo = :pf_codigo, pf_nombre = :pf_nombre, pf_descripcion = :pf_descripcion, programa_prog_codigo = :programa_prog_codigo, centro_formacion_cent_id = :centro_formacion_cent_id WHERE pf_id = :pf_id");
        $data['pf_id'] = $pf_id;
        return $stmt->execute($data);
    }

    public function delete($pf_id) {
        $stmt = $this->db->prepare("DELETE FROM proyecto_formativo WHERE pf_id = :pf_id");
        return $stmt->execute(['pf_id' => $pf_id]);
    }

    public function getFichasByProyecto($pf_id) {
        $stmt = $this->db->prepare("SELECT f.* FROM ficha f JOIN ficha_proyecto fp ON f.fich_id = fp.fich_fich_id WHERE fp.pf_pf_id = :pf_id");
        $stmt->execute(['pf_id' => $pf_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function asociarFicha($fich_id, $pf_id) {
        $stmt = $this->db->prepare("INSERT INTO ficha_proyecto (fich_fich_id, pf_pf_id) VALUES (:fich_id, :pf_id)");
        return $stmt->execute(['fich_id' => $fich_id, 'pf_id' => $pf_id]);
    }

    public function desasociarFicha($fich_id, $pf_id) {
        $stmt = $this->db->prepare("DELETE FROM ficha_proyecto WHERE fich_fich_id = :fich_id AND pf_pf_id = :pf_id");
        return $stmt->execute(['fich_id' => $fich_id, 'pf_id' => $pf_id]);
    }

    public function getProyectoByFicha($fich_id) {
        $stmt = $this->db->prepare("SELECT p.* FROM proyecto_formativo p JOIN ficha_proyecto fp ON p.pf_id = fp.pf_pf_id WHERE fp.fich_fich_id = :fich_id");
        $stmt->execute(['fich_id' => $fich_id]);
        $proyecto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($proyecto) {
            $proyecto['fases'] = $this->getFasesByProyecto($proyecto['pf_id']);
        }
        return $proyecto;
    }
}
