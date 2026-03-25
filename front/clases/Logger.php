<?php
// front/clases/Logger.php

class Logger {
    // 1. Atributo privado (Demuestra Encapsulación para el RA4)
    private $archivo_log;

    // 2. Método Constructor
    public function __construct($nombre_archivo = 'actividad.jsonl') {
        // Define dónde se guardará el archivo (en la carpeta front)
        $this->archivo_log = __DIR__ . '/../' . $nombre_archivo;
    }

    // 3. Método público para registrar datos
    public function registrar($usuario_id, $accion) {
        // Creamos un array asociativo con los datos (RA6)
        $datos = [
            'timestamp' => date('Y-m-d H:i:s'),
            'usuario_id' => $usuario_id,
            'accion' => $accion
        ];

        // Convertimos el array a formato JSON (RA8)
        // El PHP_EOL añade el salto de línea para que sea JSONL válido
        $linea_jsonl = json_encode($datos) . PHP_EOL;

        // Guardamos la línea en el archivo de texto (RA5 y RA8)
        file_put_contents($this->archivo_log, $linea_jsonl, FILE_APPEND);
    }
}
?>
