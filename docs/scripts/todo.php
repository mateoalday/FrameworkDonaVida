<?php

/**
 * Ejecuta todos los scripts de contenido de esta carpeta, cada uno en su proceso.
 *
 * Los scripts de sección van en orden alfabético y plantilla.php al final,
 * porque ordena el menú con los ítems que crean los demás.
 *
 * Uso, después de cada git pull (desde la carpeta del proyecto, con MySQL encendido):
 *   C:\xampp\php\php.exe docs\scripts\todo.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

$scripts = array_diff(array_map('basename', glob(__DIR__ . '/*.php')), ['comun.php', 'todo.php', 'plantilla.php']);
sort($scripts);
$scripts[] = 'plantilla.php';

foreach ($scripts as $script) {
    passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . DIRECTORY_SEPARATOR . $script), $codigo);

    if ($codigo !== 0) {
        fwrite(STDERR, 'Se detuvo en ' . $script . '.' . PHP_EOL);
        exit($codigo);
    }

    echo PHP_EOL;
}
