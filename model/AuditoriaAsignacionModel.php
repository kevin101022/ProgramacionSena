<?php
require_once dirname(__DIR__) . '/Conexion.php';

class AuditoriaAsignacionModel
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::getConnect();
    }

    public function getAll($cent_id = null, $coord_id = null, $usuario_id = null)
    {
        $sql = "SELECT DISTINCT au.id_auditoria, au.fecha_hora, au.documento_usuario_accion, au.correo_usuario, au.tipo_accion,
                       au.instructor_inst_id, au.asig_fecha_ini, au.asig_fecha_fin, au.ficha_fich_id, 
                       au.ambiente_amb_id, au.competencia_comp_id, au.asig_id,
                       i.inst_nombres, i.inst_apellidos, am.amb_nombre, c.comp_nombre_corto, s.sede_nombre,
                       COALESCE(au.nombre_usuario_accion, u_coord.coord_nombre_coordinador, u_cent.cent_nombre, (COALESCE(u_inst_acc.inst_nombres, '') || ' ' || COALESCE(u_inst_acc.inst_apellidos, '')), au.correo_usuario, 'Sistema') as nombre_responsable,
                       area.coord_descripcion as area_nombre, area.coord_id as area_id
                FROM auditoria_asignacion au
                LEFT JOIN instructor i ON au.instructor_inst_id = i.numero_documento
                LEFT JOIN ambiente am ON au.ambiente_amb_id = am.amb_id
                LEFT JOIN competencia c ON au.competencia_comp_id = c.comp_id
                LEFT JOIN sede s ON am.SEDE_sede_id = s.sede_id
                LEFT JOIN ficha f ON au.ficha_fich_id = f.fich_id
                LEFT JOIN COORDINACION area ON f.COORDINACION_coord_id = area.coord_id
                -- Joins para resolver el responsable (Persona que hace la acción)
                LEFT JOIN usuario_coordinador u_coord ON au.documento_usuario_accion = u_coord.numero_documento
                LEFT JOIN CENTRO_FORMACION u_cent ON au.documento_usuario_accion = CAST(u_cent.cent_id AS BIGINT)
                LEFT JOIN instructor u_inst_acc ON au.documento_usuario_accion = u_inst_acc.numero_documento
                WHERE 1=1";

        if ($cent_id !== null && $cent_id !== '') {
            $sql .= " AND (i.CENTRO_FORMACION_cent_id = :cent_id 
                        OR f.COORDINACION_coord_id IN (SELECT coord_id FROM COORDINACION WHERE centro_formacion_cent_id = :cent_id)
                        OR au.documento_usuario_accion = :cent_id
                        OR au.documento_usuario_accion IN (SELECT numero_documento FROM usuario_coordinador WHERE centro_formacion_id = :cent_id))";
        }

        if ($coord_id !== null && $coord_id !== '') {
            $sql .= " AND f.COORDINACION_coord_id = :coord_id";
        }

        if ($usuario_id !== null && $usuario_id !== '') {
            $sql .= " AND au.documento_usuario_accion = :usuario_id";
        }

        $sql .= " ORDER BY au.fecha_hora DESC";

        $stmt = $this->db->prepare($sql);

        if ($cent_id !== null && $cent_id !== '') {
            $stmt->bindParam(':cent_id', $cent_id);
        }
        if ($coord_id !== null && $coord_id !== '') {
            $stmt->bindParam(':coord_id', $coord_id);
        }
        if ($usuario_id !== null && $usuario_id !== '') {
            $stmt->bindParam(':usuario_id', $usuario_id);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT au.*, i.inst_nombres, i.inst_apellidos, am.amb_nombre, c.comp_nombre_corto, s.sede_nombre,
                       COALESCE(au.nombre_usuario_accion, u_coord.coord_nombre_coordinador, u_cent.cent_nombre, (COALESCE(u_inst_acc.inst_nombres, '') || ' ' || COALESCE(u_inst_acc.inst_apellidos, '')), au.correo_usuario, 'Sistema') as nombre_responsable,
                       area.coord_descripcion as area_nombre
                FROM auditoria_asignacion au
                LEFT JOIN instructor i ON au.instructor_inst_id = i.numero_documento
                LEFT JOIN ambiente am ON au.ambiente_amb_id = am.amb_id
                LEFT JOIN competencia c ON au.competencia_comp_id = c.comp_id
                LEFT JOIN sede s ON am.SEDE_sede_id = s.sede_id
                LEFT JOIN ficha f ON au.ficha_fich_id = f.fich_id
                LEFT JOIN COORDINACION area ON f.COORDINACION_coord_id = area.coord_id
                -- Joins para resolver el responsable
                LEFT JOIN usuario_coordinador u_coord ON au.documento_usuario_accion = u_coord.numero_documento
                LEFT JOIN CENTRO_FORMACION u_cent ON au.documento_usuario_accion = CAST(u_cent.cent_id AS BIGINT)
                LEFT JOIN instructor u_inst_acc ON au.documento_usuario_accion = u_inst_acc.numero_documento
                WHERE au.id_auditoria = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
