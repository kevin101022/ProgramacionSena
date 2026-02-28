<?php
require_once dirname(__DIR__) . '/Conexion.php';

class InstructorModel
{

    private $numero_documento;
    private $inst_nombres;
    private $inst_apellidos;
    private $inst_correo;
    private $inst_telefono;
    private $cent_id;
    private $inst_password;
    private $db;

    public function __construct($numero_documento = null, $inst_nombres = null, $inst_apellidos = null, $inst_correo = null, $inst_telefono = null, $cent_id = null, $inst_password = null)
    {
        $this->numero_documento = $numero_documento;
        $this->inst_nombres = $inst_nombres;
        $this->inst_apellidos = $inst_apellidos;
        $this->inst_correo = $inst_correo;
        $this->inst_telefono = $inst_telefono;
        $this->inst_password = $inst_password;
        $this->cent_id = $cent_id;
        $this->db = Conexion::getConnect();
    }

    // Getters
    public function getNumeroDocumento()
    {
        return $this->numero_documento;
    }
    public function getInstNombres()
    {
        return $this->inst_nombres;
    }
    public function getInstApellidos()
    {
        return $this->inst_apellidos;
    }
    public function getInstCorreo()
    {
        return $this->inst_correo;
    }
    public function getInstTelefono()
    {
        return $this->inst_telefono;
    }
    public function getInstPassword()
    {
        return $this->inst_password;
    }
    public function getCentId()
    {
        return $this->cent_id;
    }

    // Setters
    public function setNumeroDocumento($numero_documento)
    {
        $this->numero_documento = $numero_documento;
    }
    public function setInstNombres($inst_nombres)
    {
        $this->inst_nombres = $inst_nombres;
    }
    public function setInstApellidos($inst_apellidos)
    {
        $this->inst_apellidos = $inst_apellidos;
    }
    public function setInstCorreo($inst_correo)
    {
        $this->inst_correo = $inst_correo;
    }
    public function setInstTelefono($inst_telefono)
    {
        $this->inst_telefono = $inst_telefono;
    }
    public function setInstPassword($inst_password)
    {
        $this->inst_password = $inst_password;
    }
    public function setCentId($cent_id)
    {
        $this->cent_id = $cent_id;
    }

    // CRUD
    public function create()
    {
        $query = "INSERT INTO INSTRUCTOR (numero_documento, inst_nombres, inst_apellidos, inst_correo, inst_telefono, inst_password, CENTRO_FORMACION_cent_id, estado) 
        VALUES (:numero_documento, :inst_nombres, :inst_apellidos, :inst_correo, :inst_telefono, :inst_password, :cent_id, 1)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':numero_documento', $this->numero_documento);
        $stmt->bindParam(':inst_nombres', $this->inst_nombres);
        $stmt->bindParam(':inst_apellidos', $this->inst_apellidos);
        $stmt->bindParam(':inst_correo', $this->inst_correo);
        $stmt->bindParam(':inst_password', $this->inst_password);

        $telefono = !empty($this->inst_telefono) ? $this->inst_telefono : null;
        $stmt->bindParam(':inst_telefono', $telefono);

        $centId = !empty($this->cent_id) ? $this->cent_id : null;
        $stmt->bindParam(':cent_id', $centId);

        $stmt->execute();
        return $this->numero_documento;
    }

    public function read()
    {
        $sql = "SELECT i.numero_documento as inst_id, i.inst_nombres, i.inst_apellidos, i.inst_correo, i.inst_telefono, 
                       i.CENTRO_FORMACION_cent_id as cent_id, i.inst_password,
                       c.cent_nombre 
                FROM INSTRUCTOR i 
                LEFT JOIN CENTRO_FORMACION c ON i.CENTRO_FORMACION_cent_id = c.cent_id 
                WHERE i.numero_documento = :numero_documento AND i.estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':numero_documento' => $this->numero_documento]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $sql = "SELECT i.numero_documento as inst_id, i.inst_nombres, i.inst_apellidos, i.inst_correo, i.inst_telefono, 
                       i.CENTRO_FORMACION_cent_id as cent_id, i.inst_password,
                       c.cent_nombre 
                FROM INSTRUCTOR i 
                LEFT JOIN CENTRO_FORMACION c ON i.CENTRO_FORMACION_cent_id = c.cent_id 
                WHERE i.estado = 1
                ORDER BY i.numero_documento DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByCentro($cent_id)
    {
        $sql = "SELECT i.numero_documento as inst_id, i.inst_nombres, i.inst_apellidos, i.inst_correo, i.inst_telefono, 
                       i.CENTRO_FORMACION_cent_id as cent_id, i.inst_password,
                       c.cent_nombre 
                FROM INSTRUCTOR i 
                LEFT JOIN CENTRO_FORMACION c ON i.CENTRO_FORMACION_cent_id = c.cent_id 
                WHERE i.CENTRO_FORMACION_cent_id = :cent_id AND i.estado = 1
                ORDER BY i.numero_documento DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cent_id' => $cent_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        try {
            $query = "UPDATE INSTRUCTOR 
                      SET inst_nombres = :inst_nombres, 
                          inst_apellidos = :inst_apellidos, 
                          inst_correo = :inst_correo, 
                          inst_telefono = :inst_telefono, 
                          inst_password = :inst_password,
                          CENTRO_FORMACION_cent_id = :cent_id 
                      WHERE numero_documento = :numero_documento";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':inst_nombres', $this->inst_nombres);
            $stmt->bindParam(':inst_apellidos', $this->inst_apellidos);
            $stmt->bindParam(':inst_correo', $this->inst_correo);
            $stmt->bindParam(':inst_password', $this->inst_password);

            $telefono = !empty($this->inst_telefono) ? $this->inst_telefono : null;
            $stmt->bindParam(':inst_telefono', $telefono);

            $centId = !empty($this->cent_id) ? $this->cent_id : null;
            $stmt->bindParam(':cent_id', $centId);

            $stmt->bindParam(':numero_documento', $this->numero_documento);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en InstructorModel::update: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete()
    {
        $query = "UPDATE INSTRUCTOR SET estado = 0 WHERE numero_documento = :numero_documento";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':numero_documento', $this->numero_documento);
        $stmt->execute();
        return $stmt;
    }

    public function getAsignacionesByInstructor()
    {
        $sql = "SELECT a.asig_id, a.asig_fecha_ini, a.asig_fecha_fin,
                       f.fich_id, f.fich_jornada,
                       p.prog_denominacion,
                       comp.comp_nombre_corto as comp_nombre,
                       amb.amb_nombre,
                       s.sede_nombre
                FROM ASIGNACION a
                LEFT JOIN FICHA f ON a.FICHA_fich_id = f.fich_id
                LEFT JOIN PROGRAMA p ON f.PROGRAMA_prog_id = p.prog_codigo
                LEFT JOIN COMPETENCIA comp ON a.COMPETENCIA_comp_id = comp.comp_id
                LEFT JOIN AMBIENTE amb ON a.AMBIENTE_amb_id = amb.amb_id
                LEFT JOIN SEDE s ON amb.SEDE_sede_id = s.sede_id
                WHERE a.INSTRUCTOR_inst_id = :numero_documento
                ORDER BY a.asig_fecha_ini DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':numero_documento' => $this->numero_documento]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCompetenciasByInstructor()
    {
        $sql = "SELECT ic.inscomp_id, ic.inscomp_vigencia as hab_vigencia,
                       comp.comp_id, comp.comp_nombre_corto as comp_nombre, comp.comp_nombre_unidad_competencia as comp_descripcion,
                       p.prog_codigo, p.prog_denominacion
                FROM INSTRU_COMPETENCIA ic
                LEFT JOIN COMPETENCIA comp ON ic.COMPETxPROGRAMA_COMPETENCIA_comp_id = comp.comp_id
                LEFT JOIN PROGRAMA p ON ic.COMPETxPROGRAMA_PROGRAMA_prog_id = p.prog_codigo
                WHERE ic.INSTRUCTOR_inst_id = :numero_documento
                ORDER BY comp.comp_nombre_corto ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':numero_documento' => $this->numero_documento]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFichasLider()
    {
        $sql = "SELECT f.fich_id, f.fich_jornada, f.fich_fecha_ini_lectiva, f.fich_fecha_fin_lectiva,
                       p.prog_denominacion, tp.titpro_nombre,
                       c.coord_descripcion as coord_nombre, s.sede_nombre
                FROM FICHA f
                INNER JOIN PROGRAMA p ON f.PROGRAMA_prog_id = p.prog_codigo
                INNER JOIN TITULO_PROGRAMA tp ON p.TIT_PROGRAMA_titpro_id = tp.titpro_id
                LEFT JOIN COORDINACION c ON f.COORDINACION_coord_id = c.numero_documento
                LEFT JOIN (
                    SELECT FICHA_fich_id, MAX(ASIG_ID) as asig_id_max 
                    FROM ASIGNACION 
                    GROUP BY FICHA_fich_id
                ) a_max ON f.fich_id = a_max.FICHA_fich_id
                LEFT JOIN ASIGNACION a ON a_max.asig_id_max = a.ASIG_ID
                LEFT JOIN AMBIENTE amb ON a.AMBIENTE_amb_id = amb.amb_id
                LEFT JOIN SEDE s ON amb.SEDE_sede_id = s.sede_id
                WHERE f.INSTRUCTOR_inst_id_lider = :numero_documento
                ORDER BY f.fich_fecha_ini_lectiva DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':numero_documento' => $this->numero_documento]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
