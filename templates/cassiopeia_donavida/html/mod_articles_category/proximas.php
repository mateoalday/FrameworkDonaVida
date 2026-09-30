<?php

/**
 * Diseño alternativo "proximas" del módulo Articles - Category (TP2-6).
 *
 * El diseño original lista solo títulos. Este muestra las próximas campañas
 * como tarjetas de Bootstrap con la imagen de introducción y los campos
 * personalizados Fecha, Sede y Grupos buscados, y saltea las campañas cuya
 * fecha ya pasó. Muestra como máximo $maxCampanas, en el orden del módulo.
 *
 * Se elige en el módulo: pestaña Advanced → Layout → proximas.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

$maxCampanas = 3;
$hoy         = Factory::getDate()->format('Y-m-d');
$proximas    = [];

foreach ($grouped ? array_merge(...array_values($list)) : $list as $item) {
    $campos = [];

    foreach (FieldsHelper::getFields('com_content.article', $item, true) as $campo) {
        $campos[$campo->name] = $campo;
    }

    $fecha = (string) ($campos['fecha']->rawvalue ?? '');

    if ($fecha === '' || substr($fecha, 0, 10) < $hoy) {
        continue;
    }

    $proximas[] = [$item, $campos];

    if (\count($proximas) === $maxCampanas) {
        break;
    }
}

?>
<?php if (!$proximas) : ?>
    <p class="mb-0">No hay campañas programadas por ahora. Podés donar en cualquiera de nuestras sedes en su horario habitual.</p>
    <?php return; ?>
<?php endif; ?>

<div class="row row-cols-1 row-cols-md-3 g-3 mb-3">
    <?php foreach ($proximas as [$item, $campos]) : ?>
        <?php $imagenes = json_decode($item->images ?? '{}'); ?>
        <div class="col">
            <article class="card h-100">
                <?php if (!empty($imagenes->image_intro)) : ?>
                    <?php echo LayoutHelper::render('joomla.html.image', [
                        'src'   => $imagenes->image_intro,
                        'alt'   => $imagenes->image_intro_alt ?? '',
                        'class' => 'card-img-top',
                    ]); ?>
                <?php endif; ?>
                <div class="card-body">
                    <h3 class="h5 card-title">
                        <a class="stretched-link" href="<?php echo $item->link; ?>"><?php echo htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8'); ?></a>
                    </h3>
                    <p class="card-text mb-2">
                        <span class="icon-calendar" aria-hidden="true"></span>
                        <?php echo HTMLHelper::_('date', $campos['fecha']->rawvalue, 'd/m/Y'); ?>
                        <?php if (isset($campos['sede'])) : ?>
                            <br><span class="icon-location" aria-hidden="true"></span>
                            <?php echo $campos['sede']->value; ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($campos['grupos']->rawvalue)) : ?>
                        <p class="card-text mb-0">
                            <span class="visually-hidden">Grupos buscados:</span>
                            <?php foreach ((array) $campos['grupos']->rawvalue as $grupo) : ?>
                                <span class="badge rounded-pill text-bg-primary"><?php echo htmlspecialchars($grupo, ENT_COMPAT, 'UTF-8'); ?></span>
                            <?php endforeach; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
</div>
<a class="btn btn-outline-primary" href="<?php echo Route::_(RouteHelper::getCategoryRoute($proximas[0][0]->catid, $proximas[0][0]->category_language)); ?>">Ver todas las campañas</a>
