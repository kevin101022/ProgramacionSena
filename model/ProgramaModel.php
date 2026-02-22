<?php
require_once dirname(__DIR__) . '/Conexion.php';
require_once __DIR__ . '/SchemaResilienceTrait.php';

class ProgramaModel
{
    use SchemaResilienceTrait;

    private $prog_codigo;
    private $prog_denominacion;
    private $tit_programa_titpro_id;
    private $prog_tipo;
    private $db;

    public function __construct($prog_codigo = null, $prog_denominacion = null, $tit_programa_titpro_id = null, $prog_tipo = null)
    {
        $this->prog_codigo = $prog_codigo;
        $this->prog_denominacion = $prog_denominacion;
        $this->tit_programa_titpro_id = $tit_programa_titpro_id;
        $this->prog_tipo = $prog_tipo;
        $this->db = Conexion::getConnect();
    }

    // Getters
    public function getProgCodigo()
    {
        return $this->prog_codigo;
    }
    public function getProgDenominacion()
    {
        return $this->prog_denominacion;
    }
    public function getTitProgramaTitproId()
    {
        return $this->tit_programa_titpro_id;
    }
    public function getProgTipo()
    {
        return $this->prog_tipo;
    }

    // Setters
    public function setProgCodigo($prog_codigo)
    {
        $this->prog_codigo = $prog_codigo;
    }
    public function setProgDenominacion($prog_denominacion)
    {
        $this->prog_denominacion = $prog_denominacion;
    }
    public function setTitProgramaTitproId($id)
    {
        $this->tit_programa_titpro_id = $id;
    }
    public function setProgTipo($prog_tipo)
    {
        $this->prog_tipo = $prog_tipo;
    }

    // CRUD helpers
    public function getNextId()
    {
        $query = "SELECT COALESCE(MAX(prog_codigo), 0) + 1 FROM PROGRAMA";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function create()
    {
        $retryLogic = function () {
            // Se asume que prog_codigo es proporcionado manualmente por el usuario (Código Nacional)
            if (!$this->prog_codigo) {
                throw new Exception("El código del programa es obligatorio.");
            }
            $query = "INSERT INTO PROGRAMA (prog_codigo, prog_denominacion, TIT_PROGRAMA_titpro_id, prog_tipo) 
                      VALUES (:prog_codigo, :prog_denominacion, :tit_programa_titpro_id, :prog_tipo)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':prog_codigo', $this->prog_codigo);
            $stmt->bindParam(':prog_denominacion', $this->prog_denominacion);
            $stmt->bindParam(':tit_programa_titpro_id', $this->tit_programa_titpro_id);
            $stmt->bindParam(':prog_tipo', $this->prog_tipo);
            return $stmt->execute();
        };

        try {
            return $retryLogic();
        } catch (PDOException $e) {
            return $this->handleTruncation($e, 'programa', [
                'prog_denominacion' => $this->prog_denominacion,
                'prog_tipo' => $this->prog_tipo
            ], $retryLogic);
        }
    }

    public function read()
    {
        $sql = "SELECT p.prog_codigo, p.prog_denominacion, p.TIT_PROGRAMA_titpro_id as titpro_id, p.prog_tipo, t.titpro_nombre 
                FROM PROGRAMA p
                INNER JOIN TITULO_PROGRAMA t ON p.TIT_PROGRAMA_titpro_id = t.titpro_id
                WHERE p.prog_codigo = :prog_codigo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':prog_codigo' => $this->prog_codigo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $sql = "SELECT p.prog_codigo, p.prog_denominacion, p.TIT_PROGRAMA_titpro_id as titpro_id, p.prog_tipo, t.titpro_nombre 
                FROM PROGRAMA p
                INNER JOIN TITULO_PROGRAMA t ON p.TIT_PROGRAMA_titpro_id = t.titpro_id
                ORDER BY p.prog_codigo DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = "UPDATE PROGRAMA 
                  SET prog_denominacion = :prog_denominacion, 
                      TIT_PROGRAMA_titpro_id = :tit_programa_titpro_id, 
                      prog_tipo = :prog_tipo
                  WHERE prog_codigo = :prog_codigo";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':prog_denominacion', $this->prog_denominacion);
        $stmt->bindParam(':tit_programa_titpro_id', $this->tit_programa_titpro_id);
        $stmt->bindParam(':prog_tipo', $this->prog_tipo);
        $stmt->bindParam(':prog_codigo', $this->prog_codigo);
        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM PROGRAMA WHERE prog_codigo = :prog_codigo";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':prog_codigo', $this->prog_codigo);
        return $stmt->execute();
    }

    public function getCompetenciasByPrograma()
    {
        require_once __DIR__ . '/CompetenciaProgramaModel.php';
        $assocModel = new CompetenciaProgramaModel();
        return $assocModel->getCompetenciasByPrograma($this->prog_codigo);
    }
}
