<?php
require_once dirname(__DIR__) . '/Conexion.php';

class AuditoriaAsignacionModel
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::getConnect();
    }

    public function getAll($cent_id = null, $usuario_id = null)
    {
        $sql = "SELECT au.id_auditoria, au.fecha_hora, au.documento_usuario_accion, au.correo_usuario, au.tipo_accion,
                       au.instructor_inst_id, au.asig_fecha_ini, au.asig_fecha_fin, au.ficha_fich_id, 
                       au.ambiente_amb_id, au.competencia_comp_id, au.asig_id,
                       i.inst_nombres, i.inst_apellidos, am.amb_nombre, c.comp_nombre_corto, s.sede_nombre
                FROM auditoria_asignacion au
                LEFT JOIN instructor i ON au.instructor_inst_id = i.numero_documento
                LEFT JOIN ambiente am ON au.ambiente_amb_id = am.amb_id
                LEFT JOIN competencia c ON au.competencia_comp_id = c.comp_id
                LEFT JOIN sede s ON am.SEDE_sede_id = s.sede_id
                WHERE 1=1";

        if ($cent_id) {
            $sql .= " AND i.CENTRO_FORMACION_cent_id = :cent_id";
        }

        if ($usuario_id) {
            $sql .= " AND au.documento_usuario_accion = :usuario_id";
        }

        $sql .= " ORDER BY au.fecha_hora DESC";

        $stmt = $this->db->prepare($sql);

        if ($cent_id) {
            $stmt->bindParam(':cent_id', $cent_id);
        }
        if ($usuario_id) {
            $stmt->bindParam(':usuario_id', $usuario_id);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT au.*, i.inst_nombres, i.inst_apellidos, am.amb_nombre, c.comp_nombre_corto, s.sede_nombre
                FROM auditoria_asignacion au
                LEFT JOIN instructor i ON au.instructor_inst_id = i.numero_documento
                LEFT JOIN ambiente am ON au.ambiente_amb_id = am.amb_id
                LEFT JOIN competencia c ON au.competencia_comp_id = c.comp_id
                LEFT JOIN sede s ON am.SEDE_sede_id = s.sede_id
                WHERE au.id_auditoria = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
