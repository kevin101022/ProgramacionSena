<?php
require_once __DIR__ . '/../Conexion.php';

class UsuarioCoordinadorModel
{
    private $numero_documento;
    private $nombre_coordinador;
    private $correo;
    private $password;
    private $estado;
    private $centro_formacion_id;
    private $db;

    public function __construct($numero_documento = null, $nombre_coordinador = null, $correo = null, $password = null, $estado = 1, $centro_formacion_id = null)
    {
        $this->numero_documento = $numero_documento;
        $this->nombre_coordinador = $nombre_coordinador;
        $this->correo = $correo;
        $this->password = $password;
        $this->estado = $estado;
        $this->centro_formacion_id = $centro_formacion_id;
        $this->db = Conexion::getConnect();
    }

    public function create()
    {
        try {
            $query = "INSERT INTO usuario_coordinador (numero_documento, coord_nombre_coordinador, coord_correo, coord_password, estado, centro_formacion_id) 
                      VALUES (:numero_documento, :nombre, :correo, :password, :estado, :centro_id)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':numero_documento', $this->numero_documento);
            $stmt->bindParam(':nombre', $this->nombre_coordinador);
            $stmt->bindParam(':correo', $this->correo);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':estado', $this->estado);
            $stmt->bindParam(':centro_id', $this->centro_formacion_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en UsuarioCoordinadorModel::create: " . $e->getMessage());
            return false;
        }
    }

    public function getAll($centro_id = null)
    {
        $query = "SELECT numero_documento, coord_nombre_coordinador, coord_correo, estado, centro_formacion_id 
                  FROM usuario_coordinador";

        if ($centro_id) {
            $query .= " WHERE centro_formacion_id = :centro_id OR centro_formacion_id IS NULL";
        }
        $query .= " ORDER BY coord_nombre_coordinador ASC";

        $stmt = $this->db->prepare($query);
        if ($centro_id) {
            $stmt->execute([':centro_id' => $centro_id]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function read($numero_documento)
    {
        $query = "SELECT u.numero_documento, u.coord_nombre_coordinador, u.coord_correo, u.estado, 
                         c.coord_id, c.coord_descripcion as coordinacion_asignada
                  FROM usuario_coordinador u
                  LEFT JOIN coordinacion c ON c.coordinador_actual = u.numero_documento
                  WHERE u.numero_documento = :numero_documento";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':numero_documento', $numero_documento);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getActivosDisponibles($centro_id = null)
    {
        // Trae a los coordinadores activos que NO están asignados a ninguna coordinación
        $query = "SELECT u.numero_documento, u.coord_nombre_coordinador, u.coord_correo 
                  FROM usuario_coordinador u
                  WHERE u.estado = 1 AND u.numero_documento NOT IN (
                      SELECT coordinador_actual FROM coordinacion WHERE coordinador_actual IS NOT NULL
                  )";

        if ($centro_id) {
            $query .= " AND (u.centro_formacion_id = :centro_id OR u.centro_formacion_id IS NULL)";
        }
        $query .= " ORDER BY u.coord_nombre_coordinador ASC";

        $stmt = $this->db->prepare($query);
        if ($centro_id) {
            $stmt->execute([':centro_id' => $centro_id]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        try {
            $query = "UPDATE usuario_coordinador 
                      SET coord_nombre_coordinador = :nombre, 
                          coord_correo = :correo";

            if (!empty($this->password)) {
                $query .= ", coord_password = :password";
            }

            if ($this->estado !== null) {
                $query .= ", estado = :estado";
            }

            $query .= " WHERE numero_documento = :numero_documento";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':nombre', $this->nombre_coordinador);
            $stmt->bindParam(':correo', $this->correo);

            if (!empty($this->password)) {
                $stmt->bindParam(':password', $this->password);
            }
            if ($this->estado !== null) {
                $stmt->bindParam(':estado', $this->estado);
            }

            $stmt->bindParam(':numero_documento', $this->numero_documento);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en UsuarioCoordinadorModel::update: " . $e->getMessage());
            return false;
        }
    }

    public function toggleEstado($nuevo_estado)
    {
        $query = "UPDATE usuario_coordinador SET estado = :estado WHERE numero_documento = :numero_documento";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estado', $nuevo_estado);
        $stmt->bindParam(':numero_documento', $this->numero_documento);
        return $stmt->execute();
    }
}
