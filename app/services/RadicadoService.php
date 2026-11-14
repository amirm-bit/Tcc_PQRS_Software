<?php
// app/services/RadicadoService.php - Generación de radicados únicos

class RadicadoService {
    public static function generar(string $tabla, string $prefijo): string {
        $db = Database::getConnection();
        $anio = date('Y');
        $patron = $prefijo . '-' . $anio . '-%';

        for ($intento = 0; $intento < 5; $intento++) {
            $stmt = $db->prepare("
                SELECT radicado FROM {$tabla}
                WHERE radicado LIKE :patron
                ORDER BY radicado DESC
                LIMIT 1
            ");
            $stmt->execute(['patron' => $patron]);
            $ultimo = $stmt->fetchColumn();

            $consecutivo = 1;
            if ($ultimo) {
                $partes = explode('-', $ultimo);
                $consecutivo = (int)end($partes) + 1;
            }

            $candidato = sprintf('%s-%s-%06d', $prefijo, $anio, $consecutivo);

            $check = $db->prepare("SELECT COUNT(*) FROM {$tabla} WHERE radicado = :r");
            $check->execute(['r' => $candidato]);
            if ((int)$check->fetchColumn() === 0) {
                return $candidato;
            }
        }

        throw new Exception('No fue posible generar un número de radicado único.');
    }
}
