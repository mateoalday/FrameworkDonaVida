<?php

/**
 * Override de cómo se muestra un campo personalizado de tipo Calendar (TP2-6).
 *
 * El original usa el formato del idioma (DATE_FORMAT_LC4, que en en-GB es
 * 2026-10-17). Acá se muestra como en Argentina: 17/10/2026, y con la hora
 * si el campo la tiene activada.
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

$value = $field->value;

if ($value == '') {
    return;
}

if (\is_array($value)) {
    $value = implode(', ', $value);
}

$formato = $field->fieldparams->get('showtime', 0) ? 'd/m/Y H:i' : 'd/m/Y';

echo htmlentities(HTMLHelper::_('date', $value, $formato));
