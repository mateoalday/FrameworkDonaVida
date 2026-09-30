<?php

/**
 * Crea o actualiza la sección Sedes (TP2-7) en la base de datos local.
 *
 * Crea: la categoría de contactos "Sedes", el campo "Horarios", las 5 sedes,
 * el ítem de menú "Sedes" y el módulo "Nuestras sedes" (con el HTML de
 * docs/modulos/sedes.html). También ajusta tres opciones de Contactos.
 * El detalle está en docs/sedes.md.
 *
 * Uso, desde la carpeta del proyecto y con MySQL de XAMPP encendido:
 *   C:\xampp\php\php.exe docs\scripts\sedes.php
 */

use Joomla\CMS\Table\Category;
use Joomla\CMS\Table\Menu;
use Joomla\CMS\Table\Module;
use Joomla\Component\Contact\Administrator\Table\ContactTable;
use Joomla\Component\Fields\Administrator\Table\FieldTable;

require __DIR__ . '/comun.php';

$sedes = [
    ['Sede Neuquén Centro', 'sede-neuquen-centro', 'neuquen@donavida.org', 'Av. Argentina 1200', 'Neuquén', 'Neuquén', '0299 400-1001', 'Lun a vie, 8 a 13 h'],
    ['Sede Cipolletti', 'sede-cipolletti', 'cipolletti@donavida.org', 'Irigoyen 450', 'Cipolletti', 'Río Negro', '0299 400-1002', 'Lun a vie, 8 a 12 h · Sáb, 9 a 12 h'],
    ['Sede Plottier', 'sede-plottier', 'plottier@donavida.org', 'Av. San Martín 300', 'Plottier', 'Neuquén', '0299 400-1003', 'Mar y jue, 8 a 12 h'],
    ['Sede General Roca', 'sede-general-roca', 'roca@donavida.org', 'Tucumán 800', 'General Roca', 'Río Negro', '0298 400-1004', 'Lun a vie, 7:30 a 12:30 h'],
    ['Sede Centenario', 'sede-centenario', 'centenario@donavida.org', 'Av. Libertador 150', 'Centenario', 'Neuquén', '0299 400-1005', 'Lun, mié y vie, 8 a 12 h'],
];

try {
    echo 'Sección Sedes en la base "' . $db->setQuery('SELECT DATABASE()')->loadResult() . '"' . PHP_EOL;

    // 1. Categoría de contactos
    $categoria = new Category($db);
    $categoria->setCurrentUser($usuario);
    $catId = buscarId($db, '#__categories', ['extension' => 'com_contact', 'alias' => 'sedes']);

    if (!$catId) {
        $categoria->setLocation($categoria->getRootId(), 'last-child');
    }

    guardar($categoria, $catId, [
        'extension'   => 'com_contact',
        'title'       => 'Sedes',
        'alias'       => 'sedes',
        'description' => '',
        'published'   => 1,
        'access'      => 1,
        'language'    => '*',
        'params'      => '{"category_layout":"","image":"","image_alt":""}',
        'metadesc'    => '',
        'metakey'     => '',
        'metadata'    => '{"author":"","robots":""}',
    ], 'categoría "Sedes"');
    $categoria->rebuildPath($categoria->id);
    $catId = (int) $categoria->id;

    // 2. Campo personalizado para los horarios
    $campo = new FieldTable($db);
    $campo->setCurrentUser($usuario);
    $campoId = buscarId($db, '#__fields', ['context' => 'com_contact.contact', 'name' => 'horarios']);

    guardar($campo, $campoId, [
        'context'             => 'com_contact.contact',
        'group_id'            => 0,
        'title'               => 'Horarios',
        'name'                => 'horarios',
        'label'               => 'Horarios',
        'default_value'       => '',
        'type'                => 'textarea',
        'note'                => '',
        'description'         => '',
        'state'               => 1,
        'required'            => 0,
        'only_use_in_subform' => 0,
        'language'            => '*',
        'access'              => 1,
        'fieldparams'         => '{"rows":"","cols":"","maxlength":"","filter":""}',
        'params'              => '{"showlabel":"1","display":"2","display_readonly":"2","searchindex":"0"}',
    ], 'campo "Horarios"');
    $campoId = (int) $campo->id;

    // 3. Opciones de Contactos: email visible y sin formulario (no hay correo en local)
    $componente = $db->setQuery(
        $db->createQuery()
            ->select([$db->quoteName('extension_id'), $db->quoteName('params')])
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_contact'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
    )->loadObject();

    $db->setQuery(
        $db->createQuery()
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('params') . ' = ' . $db->quote(
                combinarParams($componente->params, ['show_email' => '1', 'show_email_headings' => '1', 'show_email_form' => '0'])
            ))
            ->where($db->quoteName('extension_id') . ' = ' . (int) $componente->extension_id)
    )->execute();
    informar('actualizado', 'opciones de Contactos');

    // 4. Sedes, cada una con su horario en el campo personalizado
    foreach ($sedes as [$nombre, $alias, $email, $direccion, $ciudad, $provincia, $telefono, $horarios]) {
        $contacto = new ContactTable($db);
        $contacto->setCurrentUser($usuario);
        $contactoId = buscarId($db, '#__contact_details', ['catid' => $catId, 'alias' => $alias]);

        guardar($contacto, $contactoId, [
            'name'         => $nombre,
            'alias'        => $alias,
            'catid'        => $catId,
            'email_to'     => $email,
            'address'      => $direccion,
            'suburb'       => $ciudad,
            'state'        => $provincia,
            'country'      => 'Argentina',
            'postcode'     => '',
            'telephone'    => $telefono,
            'mobile'       => '',
            'fax'          => '',
            'webpage'      => '',
            'con_position' => '',
            'misc'         => '',
            'image'        => '',
            'sortname1'    => '',
            'sortname2'    => '',
            'sortname3'    => '',
            'metakey'      => '',
            'metadesc'     => '',
            'published'    => 1,
            'access'       => 1,
            'language'     => '*',
            'params'       => '{}',
            'metadata'     => '{"robots":"","rights":""}',
        ], 'sede "' . $nombre . '"');

        $itemId = (string) $contacto->id;

        $db->setQuery(
            $db->createQuery()
                ->delete($db->quoteName('#__fields_values'))
                ->where($db->quoteName('field_id') . ' = ' . $campoId)
                ->where($db->quoteName('item_id') . ' = ' . $db->quote($itemId))
        )->execute();

        $db->setQuery(
            $db->createQuery()
                ->insert($db->quoteName('#__fields_values'))
                ->columns($db->quoteName(['field_id', 'item_id', 'value']))
                ->values($campoId . ', ' . $db->quote($itemId) . ', ' . $db->quote($horarios))
        )->execute();
    }

    // 5. Ítem de menú: listado de la categoría Sedes
    if (!buscarId($db, '#__menu_types', ['menutype' => 'mainmenu', 'client_id' => 0])) {
        throw new RuntimeException('no existe el menú "mainmenu" (Main Menu)');
    }

    $menu   = new Menu($db);
    $menuId = buscarId($db, '#__menu', ['client_id' => 0, 'menutype' => 'mainmenu', 'alias' => 'sedes']);

    if (!$menuId) {
        $menu->setLocation($menu->getRootId(), 'last-child');
    }

    guardar($menu, $menuId, [
        'menutype'          => 'mainmenu',
        'title'             => 'Sedes',
        'alias'             => 'sedes',
        'note'              => '',
        'link'              => 'index.php?option=com_contact&view=category&id=' . $catId,
        'type'              => 'component',
        'published'         => 1,
        'component_id'      => (int) $componente->extension_id,
        'browserNav'        => 0,
        'access'            => 1,
        'img'               => ' ',
        'template_style_id' => 0,
        'params'            => '{"menu_text":1,"menu_show":1}',
        'home'              => 0,
        'language'          => '*',
        'client_id'         => 0,
    ], 'ítem de menú "Sedes"');
    $menu->rebuildPath($menu->id);

    // 6. Módulo "Nuestras sedes", solo en la portada
    $html = file_get_contents(JPATH_BASE . '/docs/modulos/sedes.html');
    $html = trim(preg_replace('/^\s*<!--.*?-->/s', '', $html));

    $modulo   = new Module($db);
    $moduloId = buscarId($db, '#__modules', ['client_id' => 0, 'module' => 'mod_custom', 'title' => 'Nuestras sedes']);

    guardar($modulo, $moduloId, [
        'title'        => 'Nuestras sedes',
        'note'         => '',
        'content'      => $html,
        'ordering'     => 1,
        'position'     => 'sidebar-right',
        'published'    => 1,
        'publish_up'   => null,
        'publish_down' => null,
        'module'       => 'mod_custom',
        'access'       => 1,
        'showtitle'    => 1,
        'params'       => '{"prepare_content":0,"backgroundimage":"","layout":"_:default","moduleclass_sfx":"","cache":1,'
            . '"cache_time":900,"cachemode":"static","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}',
        'client_id'    => 0,
        'language'     => '*',
    ], 'módulo "Nuestras sedes"');

    $inicioId = buscarId($db, '#__menu', ['client_id' => 0, 'home' => 1, 'language' => '*']);

    $db->setQuery(
        $db->createQuery()
            ->delete($db->quoteName('#__modules_menu'))
            ->where($db->quoteName('moduleid') . ' = ' . (int) $modulo->id)
    )->execute();

    $db->setQuery(
        $db->createQuery()
            ->insert($db->quoteName('#__modules_menu'))
            ->columns($db->quoteName(['moduleid', 'menuid']))
            ->values((int) $modulo->id . ', ' . $inicioId)
    )->execute();

    echo 'Listo: la sección Sedes está en index.php/sedes y el módulo en la portada.' . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
