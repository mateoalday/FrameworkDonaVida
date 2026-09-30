<?php

/**
 * Crea o actualiza la sección Requisitos (TP2-8) en la base de datos local.
 *
 * Crea: la categoría de artículos "Requisitos", el artículo "Requisitos para
 * donar sangre" (con el HTML de docs/articulos/requisitos.html), el ítem de
 * menú "Requisitos" y el módulo "Preguntas frecuentes" (con el HTML de
 * docs/modulos/preguntas-frecuentes.html y el diseño acordeon de la plantilla).
 * El detalle está en docs/requisitos.md.
 *
 * Uso, desde la carpeta del proyecto y con MySQL de XAMPP encendido:
 *   C:\xampp\php\php.exe docs\scripts\requisitos.php
 */

use Joomla\CMS\Table\Menu;
use Joomla\CMS\Table\Module;

require __DIR__ . '/comun.php';

try {
    echo 'Sección Requisitos en la base "' . $db->setQuery('SELECT DATABASE()')->loadResult() . '"' . PHP_EOL;

    // 1. Categoría de artículos
    $catId = guardarCategoria($db, $usuario, 'com_content', 'Requisitos', 'requisitos');

    // 2. Artículo con los requisitos
    $articuloId = guardarArticulo($db, $usuario, $catId, 'requisitos-para-donar-sangre', [
        'title'     => 'Requisitos para donar sangre',
        'introtext' => leerHtml('articulos/requisitos.html'),
    ], 'artículo "Requisitos para donar sangre"');

    // 3. Ítem de menú: el artículo solo, sin autor, fechas, categoría ni visitas.
    // Van en el ítem y no en el artículo: cuando el menú apunta a un artículo,
    // las opciones del menú (y las globales de Contenido) tienen prioridad.
    $menu   = new Menu($db);
    $menuId = buscarId($db, '#__menu', ['client_id' => 0, 'menutype' => 'mainmenu', 'alias' => 'requisitos']);

    if (!$menuId) {
        $menu->setLocation($menu->getRootId(), 'last-child');
    }

    guardar($menu, $menuId, [
        'menutype'          => 'mainmenu',
        'title'             => 'Requisitos',
        'alias'             => 'requisitos',
        'note'              => '',
        'link'              => 'index.php?option=com_content&view=article&id=' . $articuloId,
        'type'              => 'component',
        'published'         => 1,
        'component_id'      => idComponente($db, 'com_content'),
        'browserNav'        => 0,
        'access'            => 1,
        'img'               => ' ',
        'template_style_id' => 0,
        'params'            => '{"show_category":"0","show_parent_category":"0","show_author":"0","show_create_date":"0",'
            . '"show_modify_date":"0","show_publish_date":"0","show_hits":"0","show_item_navigation":"0","show_tags":"0",'
            . '"menu_text":1,"menu_show":1}',
        'home'              => 0,
        'language'          => '*',
        'client_id'         => 0,
    ], 'ítem de menú "Requisitos"');
    $menu->rebuildPath($menu->id);

    // 4. Módulo "Preguntas frecuentes", debajo del artículo y solo en Requisitos
    $modulo   = new Module($db);
    $moduloId = buscarId($db, '#__modules', ['client_id' => 0, 'module' => 'mod_custom', 'title' => 'Preguntas frecuentes']);

    guardar($modulo, $moduloId, [
        'title'        => 'Preguntas frecuentes',
        'note'         => '',
        'content'      => leerHtml('modulos/preguntas-frecuentes.html'),
        'ordering'     => 1,
        'position'     => 'main-bottom',
        'published'    => 1,
        'publish_up'   => null,
        'publish_down' => null,
        'module'       => 'mod_custom',
        'access'       => 1,
        'showtitle'    => 1,
        'params'       => '{"prepare_content":0,"backgroundimage":"","layout":"cassiopeia_donavida:acordeon","moduleclass_sfx":"","cache":1,'
            . '"cache_time":900,"cachemode":"static","module_tag":"section","bootstrap_size":"0","header_tag":"h2","header_class":"","style":"0"}',
        'client_id'    => 0,
        'language'     => '*',
    ], 'módulo "Preguntas frecuentes"');

    asignarModulo($db, (int) $modulo->id, [(int) $menu->id]);

    echo 'Listo: la sección Requisitos está en index.php/requisitos, con las preguntas frecuentes abajo.' . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
