<?php

/**
 * Crea o actualiza los módulos de la portada en la base de datos local.
 *
 * Crea tres módulos Custom con el HTML de docs/modulos/:
 *   - "Portada - Hero" (banner, solo Inicio)
 *   - "Portada - Por qué donar" (top-a, solo Inicio, sin tarjeta de Cassiopeia)
 *   - "Pie de página" (footer, todas las páginas)
 * Además despublica el módulo "Login Form" de la instalación: los visitantes
 * no inician sesión en el sitio. El detalle está en docs/portada.md.
 *
 * Uso, desde la carpeta del proyecto y con MySQL de XAMPP encendido:
 *   C:\xampp\php\php.exe docs\scripts\portada.php
 */

use Joomla\CMS\Table\Module;

require __DIR__ . '/comun.php';

// Título del módulo => archivo, posición, estilo de módulo y si va solo en Inicio
$modulos = [
    'Portada - Hero'          => ['portada-hero.html', 'banner', '0', true],
    'Portada - Por qué donar' => ['portada-por-que-donar.html', 'top-a', 'System-none', true],
    'Pie de página'           => ['portada-footer.html', 'footer', '0', false],
];

try {
    echo 'Portada en la base "' . $db->setQuery('SELECT DATABASE()')->loadResult() . '"' . PHP_EOL;

    $inicioId = buscarId($db, '#__menu', ['client_id' => 0, 'home' => 1, 'language' => '*']);

    if (!$inicioId) {
        throw new RuntimeException('no se encontró el ítem de menú de la portada');
    }

    foreach ($modulos as $titulo => [$archivo, $posicion, $estilo, $soloInicio]) {
        // El comentario del principio documenta el módulo y no se guarda
        $html = file_get_contents(__DIR__ . '/../modulos/' . $archivo);
        $html = trim(preg_replace('/^\s*<!--.*?-->/s', '', $html));

        $modulo   = new Module($db);
        $moduloId = buscarId($db, '#__modules', ['client_id' => 0, 'module' => 'mod_custom', 'title' => $titulo]);

        guardar($modulo, $moduloId, [
            'title'        => $titulo,
            'note'         => '',
            'content'      => $html,
            'ordering'     => 1,
            'position'     => $posicion,
            'published'    => 1,
            'publish_up'   => null,
            'publish_down' => null,
            'module'       => 'mod_custom',
            'access'       => 1,
            'showtitle'    => 0,
            'params'       => '{"prepare_content":0,"backgroundimage":"","layout":"_:default","moduleclass_sfx":"","cache":1,'
                . '"cache_time":900,"cachemode":"static","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"",'
                . '"style":"' . $estilo . '"}',
            'client_id'    => 0,
            'language'     => '*',
        ], 'módulo "' . $titulo . '"');

        // Asignación de menú: 0 = todas las páginas
        $db->setQuery(
            $db->createQuery()
                ->delete($db->quoteName('#__modules_menu'))
                ->where($db->quoteName('moduleid') . ' = ' . (int) $modulo->id)
        )->execute();

        $db->setQuery(
            $db->createQuery()
                ->insert($db->quoteName('#__modules_menu'))
                ->columns($db->quoteName(['moduleid', 'menuid']))
                ->values((int) $modulo->id . ', ' . ($soloInicio ? $inicioId : 0))
        )->execute();
    }

    // El formulario de login que trae la instalación no tiene sentido para un visitante
    $loginId = buscarId($db, '#__modules', ['client_id' => 0, 'module' => 'mod_login', 'published' => 1]);

    if ($loginId) {
        guardar(new Module($db), $loginId, ['published' => 0], 'módulo "Login Form" despublicado');
    } else {
        informar('sin cambios', 'módulo "Login Form" (ya despublicado)');
    }

    echo 'Listo: hero, "Por qué donar" y pie de página en la portada.' . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
