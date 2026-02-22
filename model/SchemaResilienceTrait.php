<?php

trait SchemaResilienceTrait
{
    /**
     * Intenta corregir errores de truncamiento (22001) y reintentar la operación.
     * 
     * @param PDOException $e La excepción capturada
     * @param string $tableName El nombre de la tabla
     * @param array $params Arreglo asociativo con los datos que causaron el error (para identificar la columna)
     * @param callable $retryCallback Función anónima con la lógica para reintentar (debe retornar el resultado esperado)
     * @return mixed Resultado del reintento
     * @throws PDOException Si no se puede corregir o el error no es de truncamiento
     */
    protected function handleTruncation($e, $tableName, $params, $retryCallback)
    {
        // En PostgreSQL el código 22001 es "String data, right truncated"
        if ($e->getCode() == '22001' || strpos($e->getMessage(), '22001') !== false) {

            // Intentamos identificar qué columna falló por la longitud de los datos
            // Buscamos la columna cuyo valor enviado sea "sospechosamente" largo
            foreach ($params as $column => $value) {
                if (is_string($value) && strlen($value) > 40) { // Umbral de sospecha
                    try {
                        // Limpiar el nombre de la columna (quitar : si es un bind param)
                        $cleanColumn = ltrim($column, ':');

                        // Determinar un tamaño nuevo (por defecto 255 si es pequeño, o +50% del actual)
                        $newSize = max(255, strlen($value) + 100);

                        // Detectar driver para usar la sintaxis correcta
                        $driver = Conexion::getDriver();

                        if ($driver === 'pgsql') {
                            $sql = "ALTER TABLE $tableName ALTER COLUMN $cleanColumn TYPE VARCHAR($newSize)";
                        } else {
                            $sql = "ALTER TABLE $tableName MODIFY COLUMN $cleanColumn VARCHAR($newSize)";
                        }

                        // Ejecutar el ALTER/MODIFY
                        $this->db->exec($sql);

                        // Loggear la reparación
                        $logMsg = "[SchemaFix] Columna '$cleanColumn' en tabla '$tableName' ampliada a $newSize por truncamiento.\n";
                        file_put_contents(dirname(__DIR__) . '/schema_repairs.log', "[" . date('Y-m-d H:i:s') . "] " . $logMsg, FILE_APPEND);

                        // Reintentar la operación original
                        return $retryCallback();
                    } catch (Exception $ex) {
                        // Si falla el alter, lanzamos el error de base de datos original
                        throw $e;
                    }
                }
            }
        }

        // Si no es un error que sepamos manejar o falla la identificación, relanzar
        throw $e;
    }
}
