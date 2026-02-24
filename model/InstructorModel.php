<?php
require_once dirname(__DIR__) . '/Conexion.php';
require_once __DIR__ . '/SchemaResilienceTrait.php';

class InstructorModel
{
    use SchemaResilienceTrait;

    private $inst_id;
    private $inst_nombres;
    private $inst_apellidos;
    private $inst_correo;
    private $inst_telefono;
    private $cent_id;
    private $inst_password;
    private $db;

    public function __construct($inst_id = null, $inst_nombres = null, $inst_apellidos = null, $inst_correo = null, $inst_telefono = null, $cent_id = null, $inst_password = null)
    {
        $this->inst_id = $inst_id;
        $this->inst_nombres = $inst_nombres;
        $this->inst_apellidos = $inst_apellidos;
        $this->inst_correo = $inst_correo;
        $this->inst_telefono = $inst_telefono;
        $this->inst_password = $inst_password;
        $this->cent_id = $cent_id;
        $this->db = Conexion::getConnect();
    }

    // Getters
    public function getInstId()
    {
        return $this->inst_id;
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
    public function setInstId($inst_id)
    {
        $this->inst_id = $inst_id;
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
    public function getNextId()
    {
        $query = "SELECT COALESCE(MAX(inst_id), 0) + 1 FROM INSTRUCTOR";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function create()
    {
        $retryLogic = function () {
            if (!$this->inst_id) {
                $this->inst_id = $this->getNextId();
            }
            $query = "INSERT INTO INSTRUCTOR (inst_id, inst_nombres, inst_apellidos, inst_correo, inst_telefono, inst_password, CENTRO_FORMACION_cent_id) 
            VALUES (:inst_id, :inst_nombres, :inst_apellidos, :inst_correo, :inst_telefono, :inst_password, :cent_id)";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':inst_id', $this->inst_id);
            $stmt->bindParam(':inst_nombres', $this->inst_nombres);
            $stmt->bindParam(':inst_apellidos', $this->inst_apellidos);
            $stmt->bindParam(':inst_correo', $this->inst_correo);
            $stmt->bindParam(':inst_password', $this->inst_password);

            $telefono = !empty($this->inst_telefono) ? $this->inst_telefono : null;
            $stmt->bindParam(':inst_telefono', $telefono);

            $centId = !empty($this->cent_id) ? $this->cent_id : null;
            $stmt->bindParam(':cent_id', $centId);

            $stmt->execute();
            return $this->inst_id;
        };

        try {
            return $retryLogic();
        } catch (PDOException $e) {
            return $this->handleTruncation($e, 'instructor', [
                'inst_nombres' => $this->inst_nombres,
                'inst_apellidos' => $this->inst_apellidos,
                'inst_correo' => $this->inst_correo,
                'inst_password' => $this->inst_password
            ], $retryLogic);
        }
    }

    public function read()
    {
        $sql = "SELECT i.inst_id, i.inst_nombres, i.inst_apellidos, i.inst_correo, i.inst_telefono, 
                       i.CENTRO_FORMACION_cent_id as cent_id, i.inst_password,
                       c.cent_nombre 
                FROM INSTRUCTOR i 
                LEFT JOIN CENTRO_FORMACION c ON i.CENTRO_FORMACION_cent_id = c.cent_id 
                WHERE i.inst_id = :inst_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':inst_id' => $this->inst_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $sql = "SELECT i.inst_id, i.inst_nombres, i.inst_apellidos, i.inst_correo, i.inst_telefono, 
                       i.CENTRO_FORMACION_cent_id as cent_id, i.inst_password,
                       c.cent_nombre 
                FROM INSTRUCTOR i 
                LEFT JOIN CENTRO_FORMACION c ON i.CENTRO_FORMACION_cent_id = c.cent_id 
                ORDER BY i.inst_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByCentro($cent_id)
    {
        $sql = "SELECT i.inst_id, i.inst_nombres, i.inst_apellidos, i.inst_correo, i.inst_telefono, 
                       i.CENTRO_FORMACION_cent_id as cent_id, i.inst_password,
                       c.cent_nombre 
                FROM INSTRUCTOR i 
                LEFT JOIN CENTRO_FORMACION c ON i.CENTRO_FORMACION_cent_id = c.cent_id 
                WHERE i.CENTRO_FORMACION_cent_id = :cent_id
                ORDER BY i.inst_id DESC";
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
                      WHERE inst_id = :inst_id";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':inst_nombres', $this->inst_nombres);
            $stmt->bindParam(':inst_apellidos', $this->inst_apellidos);
            $stmt->bindParam(':inst_correo', $this->inst_correo);
            $stmt->bindParam(':inst_password', $this->inst_password);

            $telefono = !empty($this->inst_telefono) ? $this->inst_telefono : null;
            $stmt->bindParam(':inst_telefono', $telefono);

            $centId = !empty($this->cent_id) ? $this->cent_id : null;
            $stmt->bindParam(':cent_id', $centId);

            $stmt->bindParam(':inst_id', $this->inst_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en InstructorModel::update: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete()
    {
        $query = "DELETE FROM INSTRUCTOR WHERE inst_id = :inst_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':inst_id', $this->inst_id);
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
                WHERE a.INSTRUCTOR_inst_id = :inst_id
                ORDER BY a.asig_fecha_ini DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':inst_id' => $this->inst_id]);
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
                WHERE ic.INSTRUCTOR_inst_id = :inst_id
                ORDER BY comp.comp_nombre_corto ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':inst_id' => $this->inst_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
