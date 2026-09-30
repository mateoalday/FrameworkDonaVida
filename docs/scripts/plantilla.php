<?php

/**
 * Configura la plantilla DonaVida (TP2-9) en la base de datos local.
 *
 * Los archivos de la plantilla hija llegan con git, pero su registro y su
 * configuración viven en la base. Este script:
 *   - registra cassiopeia_donavida si falta (lo mismo que System → Discover),
 *   - la deja como estilo predeterminado, con el logo y Roboto (local),
 *   - pasa Main Menu al header (posición menu, sin título, Collapsible Dropdown),
 *   - renombra la portada como "Inicio" y ordena el menú principal:
 *     Inicio · Campañas · Sedes · Requisitos (saltea los que todavía no existan).
 * El detalle está en docs/plantilla.md.
 *
 * Uso, desde la carpeta del proyecto y con MySQL de XAMPP encendido:
 *   C:\xampp\php\php.exe docs\scripts\plantilla.php
 */

use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Table\Menu;
use Joomla\CMS\Table\Module;

require __DIR__ . '/comun.php';

// Alias de los ítems del menú principal, en el orden en que se muestran
const ORDEN_MENU = ['home', 'campanas', 'sedes', 'requisitos'];

const LOGO = 'images/donavida/logo-donavida.png#joomlaImage://local-images/donavida/logo-donavida.png?width=321&height=96';

try {
    echo 'Plantilla DonaVida en la base "' . $db->setQuery('SELECT DATABASE()')->loadResult() . '"' . PHP_EOL;

    if (!is_file(JPATH_BASE . '/templates/cassiopeia_donavida/templateDetails.xml')) {
        throw new RuntimeException('faltan los archivos de la plantilla hija: hacé git pull en main');
    }

    // 1. Registrar la plantilla hija (System → Discover → Install)
    $extension = $db->setQuery(
        $db->createQuery()
            ->select($db->quoteName(['extension_id', 'state']))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('template'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('cassiopeia_donavida'))
            ->where($db->quoteName('client_id') . ' = 0')
    )->loadObject();

    if ($extension && (int) $extension->state !== -1) {
        informar('sin cambios', 'plantilla cassiopeia_donavida (ya registrada)');
    } else {
        $installer = new Installer();
        $installer->setDatabase($db);

        // Si nunca se hizo Discover, primero hay que agregarla como "descubierta"
        if (!$extension) {
            foreach ($installer->discover() as $encontrada) {
                if ($encontrada->type === 'template' && $encontrada->element === 'cassiopeia_donavida' && (int) $encontrada->client_id === 0) {
                    $encontrada->check();
                    $encontrada->store();
                    $extension = (object) ['extension_id' => $encontrada->extension_id];
                }
            }
        }

        if (!$extension || !$installer->discover_install((int) $extension->extension_id)) {
            throw new RuntimeException('no se pudo registrar la plantilla cassiopeia_donavida');
        }

        informar('registrada', 'plantilla cassiopeia_donavida');
    }

    // 2. Estilo predeterminado con el logo y la tipografía
    $estiloId = buscarId($db, '#__template_styles', ['template' => 'cassiopeia_donavida', 'client_id' => 0]);

    if (!$estiloId) {
        throw new RuntimeException('la plantilla cassiopeia_donavida no tiene estilo');
    }

    $params = $db->setQuery(
        $db->createQuery()
            ->select($db->quoteName('params'))
            ->from($db->quoteName('#__template_styles'))
            ->where($db->quoteName('id') . ' = ' . $estiloId)
    )->loadResult();

    $db->setQuery(
        $db->createQuery()
            ->update($db->quoteName('#__template_styles'))
            ->set($db->quoteName('home') . ' = ' . $db->quote('0'))
            ->where($db->quoteName('client_id') . ' = 0')
            ->where($db->quoteName('home') . ' = ' . $db->quote('1'))
            ->where($db->quoteName('id') . ' != ' . $estiloId)
    )->execute();

    $db->setQuery(
        $db->createQuery()
            ->update($db->quoteName('#__template_styles'))
            ->set($db->quoteName('home') . ' = ' . $db->quote('1'))
            ->set($db->quoteName('params') . ' = ' . $db->quote(combinarParams($params, [
                'brand'         => '1',
                'logoFile'      => LOGO,
                'useFontScheme' => 'media/templates/site/cassiopeia/css/global/fonts-local_roboto.css',
            ])))
            ->where($db->quoteName('id') . ' = ' . $estiloId)
    )->execute();
    informar('actualizado', 'estilo predeterminado, con logo y Roboto (local)');

    // 3. La portada se llama "Inicio"
    $inicioId = buscarId($db, '#__menu', ['client_id' => 0, 'home' => 1, 'language' => '*']);
    // Menu::bind exige home, language y published para no "quitarle" la portada al ítem
    guardar(new Menu($db), $inicioId, ['title' => 'Inicio', 'home' => 1, 'language' => '*', 'published' => 1], 'ítem de portada "Inicio"');

    // 4. Main Menu en el header
    $moduloId     = 0;
    $paramsModulo = '';
    $menus        = $db->setQuery(
        $db->createQuery()
            ->select($db->quoteName(['id', 'params']))
            ->from($db->quoteName('#__modules'))
            ->where($db->quoteName('module') . ' = ' . $db->quote('mod_menu'))
            ->where($db->quoteName('client_id') . ' = 0')
            ->order($db->quoteName('id'))
    )->loadObjectList();

    foreach ($menus as $candidato) {
        if ((json_decode($candidato->params, true)['menutype'] ?? '') === 'mainmenu') {
            $moduloId     = (int) $candidato->id;
            $paramsModulo = $candidato->params;
            break;
        }
    }

    if (!$moduloId) {
        throw new RuntimeException('no se encontró el módulo que muestra el menú "mainmenu"');
    }

    guardar(new Module($db), $moduloId, [
        'position'  => 'menu',
        'showtitle' => 0,
        'params'    => combinarParams($paramsModulo, ['layout' => 'cassiopeia:collapse-metismenu']),
    ], 'módulo "Main Menu" en el header');

    // 5. Orden del menú principal
    $ids = [];

    foreach (ORDEN_MENU as $alias) {
        $id = $alias === 'home'
            ? $inicioId
            : buscarId($db, '#__menu', ['client_id' => 0, 'menutype' => 'mainmenu', 'parent_id' => 1, 'alias' => $alias]);

        if ($id) {
            $ids[] = $id;
        }
    }

    $items = $db->setQuery(
        $db->createQuery()
            ->select($db->quoteName(['id', 'title']))
            ->from($db->quoteName('#__menu'))
            ->whereIn($db->quoteName('id'), $ids)
            ->order($db->quoteName('lft'))
    )->loadAssocList('id', 'title');

    if (array_keys($items) === $ids) {
        informar('sin cambios', 'orden del menú');
    } else {
        $menu = new Menu($db);

        for ($i = 1; $i < \count($ids); $i++) {
            if (!$menu->moveByReference($ids[$i - 1], 'after', $ids[$i])) {
                throw new RuntimeException('orden del menú: ' . $menu->getError());
            }
        }

        informar('actualizado', 'orden del menú');
    }

    echo 'Listo: menú ' . implode(' · ', array_map(fn ($id) => $items[$id], $ids)) . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
