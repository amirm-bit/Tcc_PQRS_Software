<?php
// app/services/UploadService.php - Gestión segura de archivos adjuntos

class UploadService {
    public static function validate(array $file): ?string {
        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // Sin archivo, no es error
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Error al cargar el archivo (código ' . $file['error'] . ').';
        }

        $cfg = config('uploads');
        if ($file['size'] > $cfg['max_size']) {
            return 'El archivo supera el tamaño máximo permitido (10 MB).';
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $cfg['allowed_types'], true)) {
            return 'Tipo de archivo no permitido. Formatos válidos: PDF, JPG, PNG, DOC, DOCX.';
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $cfg['allowed_mimes'], true)) {
            return 'El contenido del archivo no corresponde a un formato permitido.';
        }

        return null;
    }

    public static function store(array $file, string $carpeta): ?array {
        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $error = self::validate($file);
        if ($error !== null) {
            throw new Exception($error);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $nombreGuardado = bin2hex(random_bytes(16)) . '.' . $ext;
        $subdir = $carpeta . '/' . date('Y/m');
        $destinoDir = __DIR__ . '/../../storage/uploads/' . $subdir;

        if (!is_dir($destinoDir) && !mkdir($destinoDir, 0775, true) && !is_dir($destinoDir)) {
            throw new Exception('No fue posible crear el directorio de almacenamiento.');
        }

        $destino = $destinoDir . '/' . $nombreGuardado;
        if (!move_uploaded_file($file['tmp_name'], $destino)) {
            throw new Exception('No fue posible guardar el archivo adjunto.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($destino);

        return [
            'nombre_original' => mb_substr(basename($file['name']), 0, 250),
            'nombre_guardado' => $nombreGuardado,
            'ruta'            => $subdir . '/' . $nombreGuardado,
            'tamano_bytes'    => (int)$file['size'],
            'mime_type'       => $mime ?: 'application/octet-stream',
        ];
    }

    public static function register(int $objetoId, string $tipoObjeto, array $meta): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO archivos_adjuntos
                (tipo_objeto, objeto_id, nombre_original, nombre_guardado, ruta, tamano_bytes, mime_type, subido_por_usuario_id)
            VALUES
                (:tipo_objeto, :objeto_id, :nombre_original, :nombre_guardado, :ruta, :tamano, :mime, :usuario)
        ");
        $stmt->execute([
            'tipo_objeto'     => $tipoObjeto,
            'objeto_id'       => $objetoId,
            'nombre_original' => $meta['nombre_original'],
            'nombre_guardado' => $meta['nombre_guardado'],
            'ruta'            => $meta['ruta'],
            'tamano'          => $meta['tamano_bytes'],
            'mime'            => $meta['mime_type'],
            'usuario'         => Auth::id(),
        ]);
    }

    public static function processMultiple(string $inputName, string $carpeta, int $objetoId, string $tipoObjeto): array {
        if (empty($_FILES[$inputName]) || !is_array($_FILES[$inputName]['name'])) {
            return [];
        }

        $errors = [];
        $files = $_FILES[$inputName];
        $count = count($files['name']);

        for ($i = 0; $i < $count; $i++) {
            if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $single = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];
            try {
                $meta = self::store($single, $carpeta);
                if ($meta) {
                    self::register($objetoId, $tipoObjeto, $meta);
                }
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return $errors;
    }

    public static function findByObjeto(string $tipoObjeto, int $objetoId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM archivos_adjuntos WHERE tipo_objeto = :t AND objeto_id = :id ORDER BY id");
        $stmt->execute(['t' => $tipoObjeto, 'id' => $objetoId]);
        return $stmt->fetchAll();
    }

    public static function formatSize(int $bytes): string {
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 0) . ' KB';
        return $bytes . ' B';
    }
}
