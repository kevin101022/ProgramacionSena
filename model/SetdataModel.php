<?php
class SetdataModel
{
    /**
     */
    public function parseCSV(string $filePath): array
    {
        $rows    = [];
        $headers = [];

        // Detectar delimitador
        $delimiter = $this->detectDelimiter($filePath);

        $handle = fopen($filePath, 'r');
        if (!$handle) throw new RuntimeException('No se pudo abrir el archivo.');

        $lineNum = 0;
        while (($cols = fgetcsv($handle, 0, $delimiter)) !== false) {
            // Ignorar líneas completamente vacías
            if (count($cols) === 1 && trim($cols[0]) === '') continue;

            if ($lineNum === 0) {
                // Primera fila = encabezados, limpiar BOM y espacios
                $headers = array_map(fn($h) => trim(preg_replace('/^\xEF\xBB\xBF/', '', $h)), $cols);
            } else {
                $row = [];
                foreach ($headers as $i => $h) {
                    $row[$h] = isset($cols[$i]) ? trim($cols[$i]) : '';
                }
                $rows[] = $row;
            }
            $lineNum++;
        }
        fclose($handle);

        if (empty($headers)) throw new RuntimeException('El CSV está vacío o no tiene encabezados.');

        $stats  = $this->buildStats($headers, $rows);
        $charts = $this->buildCharts($headers, $rows);

        return [
            'headers' => $headers,
            'rows'    => $rows,
            'total'   => count($rows),
            'stats'   => $stats,
            'charts'  => $charts,
        ];
    }

    /** Detecta si el delimitador es coma, punto y coma o tabulador */
    private function detectDelimiter(string $filePath): string
    {
        $handle = fopen($filePath, 'r');
        $line   = fgets($handle);
        fclose($handle);

        $counts = [
            ','  => substr_count($line, ','),
            ';'  => substr_count($line, ';'),
            "\t" => substr_count($line, "\t"),
        ];
        arsort($counts);
        return array_key_first($counts);
    }

    /** Estadísticas por columna: valores únicos y muestra si es texto, suma/promedio si es numérico */
    private function buildStats(array $headers, array $rows): array
    {
        $stats = [];
        foreach ($headers as $h) {
            $values  = array_column($rows, $h);
            $nonEmpty = array_filter($values, fn($v) => $v !== '');
            $unique  = array_unique($nonEmpty);
            $isNum   = count($nonEmpty) > 0 && count(array_filter($nonEmpty, fn($v) => !is_numeric($v))) === 0;

            $stat = [
                'column'  => $h,
                'unique'  => count($unique),
                'is_numeric' => $isNum,
            ];
            if ($isNum) {
                $nums = array_map('floatval', $nonEmpty);
                $stat['sum']  = round(array_sum($nums), 2);
                $stat['avg']  = count($nums) > 0 ? round(array_sum($nums) / count($nums), 2) : 0;
                $stat['max']  = count($nums) > 0 ? max($nums) : 0;
            } else {
                // Top 5 valores más frecuentes
                $freq = array_count_values(array_values($nonEmpty));
                arsort($freq);
                $stat['top5'] = array_slice($freq, 0, 5, true);
            }
            $stats[] = $stat;
        }
        return $stats;
    }

    /**
     * Construye datos para Chart.js:
     * - Columnas de texto con < 30 únicos → gráfico de barras (frecuencia)
     * - Columnas numéricas → tarjeta de suma/promedio
     */
    private function buildCharts(array $headers, array $rows): array
    {
        $charts = [];
        foreach ($headers as $h) {
            $values   = array_column($rows, $h);
            $nonEmpty = array_filter($values, fn($v) => $v !== '');
            if (empty($nonEmpty)) continue;

            $isNum  = count(array_filter($nonEmpty, fn($v) => !is_numeric($v))) === 0;
            $unique = array_unique($nonEmpty);

            // Columna de texto con valores razonables = gráfico de frecuencias
            if (!$isNum && count($unique) <= 30 && count($unique) > 1) {
                $freq = array_count_values(array_values($nonEmpty));
                arsort($freq);
                $charts[] = [
                    'title'  => $h,
                    'type'   => 'bar',
                    'labels' => array_keys($freq),
                    'data'   => array_values($freq),
                ];
            }
        }
        return $charts;
    }
}
