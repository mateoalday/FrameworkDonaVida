<?php

/**
 * Crea o actualiza la sección Campañas (TP2-6) en la base de datos local.
 *
 * Crea: la categoría de artículos "Campañas", los campos "Fecha", "Sede" y
 * "Grupos buscados" (solo para esa categoría), las 5 campañas con su imagen
 * (images/campanas/, generadas con imagenes-campanas.ps1), el ítem de menú
 * "Campañas" y el módulo "Próximas campañas" en la portada.
 * El detalle está en docs/campanias.md.
 *
 * Uso, desde la carpeta del proyecto y con MySQL de XAMPP encendido:
 *   C:\xampp\php\php.exe docs\scripts\campanias.php
 */

use Joomla\CMS\Table\Menu;
use Joomla\CMS\Table\Module;
use Joomla\Component\Fields\Administrator\Table\FieldTable;

require __DIR__ . '/comun.php';

const GRUPOS = ['0-', '0+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'];

// Mismos nombres y alias que los contactos de sedes.php
const SEDES = [
    'sede-neuquen-centro' => 'Sede Neuquén Centro',
    'sede-cipolletti'     => 'Sede Cipolletti',
    'sede-plottier'       => 'Sede Plottier',
    'sede-general-roca'   => 'Sede General Roca',
    'sede-centenario'     => 'Sede Centenario',
];

// Título, alias, fecha, sede, grupos buscados, imagen, texto de introducción y texto completo. Datos ficticios.
$campanas = [
    [
        'Colecta de octubre en Neuquén Centro', 'colecta-neuquen-centro', '2026-10-17', 'sede-neuquen-centro', ['0-', '0+', 'A-'], 'neuquen-centro',
        'Arrancamos la temporada de colectas en la sede central. Las reservas de grupo 0 están bajas y cada donación cuenta.',
        'La colecta se hace en el salón de la sede, de 9 a 13 h, sin turno previo. Si es tu primera vez, contá unos 40 minutos entre la entrevista, la extracción y el refrigerio.',
    ],
    [
        'Jornada solidaria en Cipolletti', 'jornada-cipolletti', '2026-10-31', 'sede-cipolletti', ['B-', 'AB-'], 'cipolletti',
        'Buscamos donantes de grupos B y AB negativos, los menos frecuentes: solo 2 de cada 100 personas los tienen.',
        'Si no sabés tu grupo, vení igual: se analiza con la donación y te lo informamos. Horario de 9 a 13 h en la sede de Irigoyen 450.',
    ],
    [
        'Colecta de primavera en Plottier', 'colecta-plottier', '2026-11-14', 'sede-plottier', ['A+', '0+'], 'plottier',
        'Una mañana para reponer los grupos más usados en cirugías programadas: A+ y 0+.',
        'Vení con DNI y un desayuno liviano. Al terminar, te damos un certificado de donación para presentar en el trabajo o el estudio.',
    ],
    [
        'Maratón de grupos negativos en General Roca', 'maraton-general-roca', '2026-11-28', 'sede-general-roca', ['0-', 'A-', 'B-', 'AB-'], 'general-roca',
        'Los grupos negativos son los que más cuesta conseguir. Esta jornada está dedicada a ellos.',
        'El grupo 0- se puede transfundir a cualquier paciente en una emergencia, por eso siempre hace falta. Atendemos de 9 a 13 h en Tucumán 800.',
    ],
    [
        'Colecta de fin de año en Centenario', 'colecta-centenario', '2026-12-12', 'sede-centenario', ['0+', 'A+', 'B+'], 'centenario',
        'Antes de las fiestas aumentan los accidentes y bajan las donaciones. Ayudanos a llegar con reservas a fin de año.',
        'La colecta es de 9 a 13 h en Av. Libertador 150. Traé a alguien que también quiera donar: cada donación puede ayudar hasta a tres personas.',
    ],
];

/**
 * Opciones de un campo de lista o casillas, en el formato de fieldparams de Joomla.
 */
function opciones(array $valoresNombres): array
{
    $opciones = [];

    foreach ($valoresNombres as $valor => $nombre) {
        $opciones['options' . \count($opciones)] = ['name' => $nombre, 'value' => (string) $valor];
    }

    return ['options' => $opciones];
}

try {
    echo 'Sección Campañas en la base "' . $db->setQuery('SELECT DATABASE()')->loadResult() . '"' . PHP_EOL;

    // 1. Categoría de artículos
    $catId = guardarCategoria(
        $db,
        $usuario,
        'com_content',
        'Campañas',
        'campanas',
        '<p>Colectas programadas en nuestras sedes. Elegí la más cercana y vení en el horario indicado: no hace falta sacar turno.</p>'
    );

    // 2. Campos personalizados de los artículos, solo en la categoría Campañas
    $campos = [
        'fecha'  => ['Fecha', 'calendar', ['showtime' => '0']],
        'sede'   => ['Sede', 'list', opciones(SEDES) + ['multiple' => '0']],
        'grupos' => ['Grupos buscados', 'checkboxes', opciones(array_combine(GRUPOS, GRUPOS))],
    ];
    $campoIds = [];
    $orden    = 0;

    foreach ($campos as $nombre => [$titulo, $tipo, $fieldparams]) {
        $campo = new FieldTable($db);
        $campo->setCurrentUser($usuario);
        $campoId = buscarId($db, '#__fields', ['context' => 'com_content.article', 'name' => $nombre]);

        guardar($campo, $campoId, [
            'context'             => 'com_content.article',
            'group_id'            => 0,
            'title'               => $titulo,
            'name'                => $nombre,
            'label'               => $titulo,
            'default_value'       => '',
            'type'                => $tipo,
            'note'                => '',
            'description'         => '',
            'state'               => 1,
            'required'            => 1,
            'only_use_in_subform' => 0,
            'language'            => '*',
            'access'              => 1,
            'ordering'            => ++$orden,
            'fieldparams'         => json_encode($fieldparams),
            'params'              => '{"showlabel":"1","display":"2","display_readonly":"2","searchindex":"0"}',
        ], 'campo "' . $titulo . '"');
        $campoIds[$nombre] = (int) $campo->id;

        $db->setQuery(
            $db->createQuery()
                ->delete($db->quoteName('#__fields_categories'))
                ->where($db->quoteName('field_id') . ' = ' . $campoIds[$nombre])
        )->execute();

        $db->setQuery(
            $db->createQuery()
                ->insert($db->quoteName('#__fields_categories'))
                ->columns($db->quoteName(['field_id', 'category_id']))
                ->values($campoIds[$nombre] . ', ' . $catId)
        )->execute();
    }

    // 3. Campañas, en orden de fecha, con imagen y valores de los campos
    foreach ($campanas as $i => [$titulo, $alias, $fecha, $sede, $grupos, $imagen, $intro, $texto]) {
        $ruta  = 'images/campanas/' . $imagen . '.png';
        $media = $ruta . '#joomlaImage://local-images/campanas/' . $imagen . '.png?width=1200&height=675';
        $alt   = 'Placa de la campaña: ' . SEDES[$sede] . ', ' . date('d/m/Y', strtotime($fecha)) . ', grupos ' . implode(', ', $grupos);

        $articuloId = guardarArticulo($db, $usuario, $catId, $alias, [
            'title'     => $titulo,
            'introtext' => '<p>' . $intro . '</p>',
            'fulltext'  => '<p>' . $texto . '</p>',
            'ordering'  => $i + 1,
            'images'    => json_encode([
                'image_intro'            => $media,
                'image_intro_alt'        => $alt,
                'float_intro'            => '',
                'image_intro_caption'    => '',
                'image_fulltext'         => $media,
                'image_fulltext_alt'     => $alt,
                'float_fulltext'         => '',
                'image_fulltext_caption' => '',
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ], 'campaña "' . $titulo . '"');

        if (!is_file(JPATH_BASE . '/' . $ruta)) {
            informar('falta', $ruta . ' (ejecutar imagenes-campanas.ps1)');
        }

        // Fecha al mediodía UTC: así se ve el mismo día en cualquier zona horaria de Argentina
        $valores = ['fecha' => [$fecha . ' 12:00:00'], 'sede' => [$sede], 'grupos' => $grupos];

        foreach ($valores as $nombre => $lista) {
            $db->setQuery(
                $db->createQuery()
                    ->delete($db->quoteName('#__fields_values'))
                    ->where($db->quoteName('field_id') . ' = ' . $campoIds[$nombre])
                    ->where($db->quoteName('item_id') . ' = ' . $db->quote((string) $articuloId))
            )->execute();

            foreach ($lista as $valor) {
                $db->setQuery(
                    $db->createQuery()
                        ->insert($db->quoteName('#__fields_values'))
                        ->columns($db->quoteName(['field_id', 'item_id', 'value']))
                        ->values($campoIds[$nombre] . ', ' . $db->quote((string) $articuloId) . ', ' . $db->quote($valor))
                )->execute();
            }
        }
    }

    // 4. Ítem de menú: blog de la categoría, en tres columnas y sin autor ni fechas de publicación
    $menu   = new Menu($db);
    $menuId = buscarId($db, '#__menu', ['client_id' => 0, 'menutype' => 'mainmenu', 'alias' => 'campanas']);

    if (!$menuId) {
        $menu->setLocation($menu->getRootId(), 'last-child');
    }

    guardar($menu, $menuId, [
        'menutype'          => 'mainmenu',
        'title'             => 'Campañas',
        'alias'             => 'campanas',
        'note'              => '',
        'link'              => 'index.php?option=com_content&view=category&layout=blog&id=' . $catId,
        'type'              => 'component',
        'published'         => 1,
        'component_id'      => idComponente($db, 'com_content'),
        'browserNav'        => 0,
        'access'            => 1,
        'img'               => ' ',
        'template_style_id' => 0,
        'params'            => json_encode([
            'show_page_heading'    => '1',
            'show_category_title'  => '0',
            'show_description'     => '1',
            'num_leading_articles' => '0',
            'num_intro_articles'   => '9',
            'num_links'            => '0',
            'blog_class'           => 'boxed columns-3',
            'orderby_pri'          => 'none',
            'orderby_sec'          => 'order',
            'show_pagination'      => '0',
            'show_intro'           => '1',
            'show_category'        => '0',
            'show_parent_category' => '0',
            'show_author'          => '0',
            'show_create_date'     => '0',
            'show_modify_date'     => '0',
            'show_publish_date'    => '0',
            'show_hits'            => '0',
            'show_item_navigation' => '0',
            'show_readmore'        => '1',
            'show_readmore_title'  => '0',
            'menu_text'            => 1,
            'menu_show'            => 1,
        ]),
        'home'              => 0,
        'language'          => '*',
        'client_id'         => 0,
    ], 'ítem de menú "Campañas"');
    $menu->rebuildPath($menu->id);

    // 5. Módulo "Próximas campañas" (Articles - Category), solo en la portada
    $modulo   = new Module($db);
    $moduloId = buscarId($db, '#__modules', ['client_id' => 0, 'module' => 'mod_articles_category', 'title' => 'Próximas campañas']);

    guardar($modulo, $moduloId, [
        'title'        => 'Próximas campañas',
        'note'         => '',
        'content'      => '',
        'ordering'     => 1,
        'position'     => 'main-top',
        'published'    => 1,
        'publish_up'   => null,
        'publish_down' => null,
        'module'       => 'mod_articles_category',
        'access'       => 1,
        'showtitle'    => 1,
        'params'       => json_encode([
            'mode'                         => 'normal',
            'show_on_article_page'         => '1',
            'count'                        => '0',
            'show_front'                   => 'show',
            'category_filtering_type'      => '1',
            'catid'                        => [(string) $catId],
            'show_child_category_articles' => '0',
            'levels'                       => '1',
            'article_ordering'             => 'a.ordering',
            'article_ordering_direction'   => 'ASC',
            'article_grouping'             => 'none',
            'link_titles'                  => '1',
            'show_date'                    => '0',
            'show_category'                => '0',
            'show_hits'                    => '0',
            'show_author'                  => '0',
            'show_introtext'               => '0',
            'show_readmore'                => '0',
            'layout'                       => 'cassiopeia_donavida:proximas',
            'moduleclass_sfx'              => '',
            'owncache'                     => '1',
            'cache_time'                   => '900',
            'module_tag'                   => 'section',
            'bootstrap_size'               => '0',
            'header_tag'                   => 'h2',
            'header_class'                 => '',
            'style'                        => '0',
        ]),
        'client_id'    => 0,
        'language'     => '*',
    ], 'módulo "Próximas campañas"');

    asignarModulo($db, (int) $modulo->id, [buscarId($db, '#__menu', ['client_id' => 0, 'home' => 1, 'language' => '*'])]);

    echo 'Listo: la sección Campañas está en index.php/campanas y el módulo en la portada.' . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
