<?php

/**
 * Diseño alternativo "acordeon" del módulo Custom (TP2-8).
 *
 * Muestra el HTML del módulo igual que el diseño original, pero antes carga el
 * componente collapse de Bootstrap, que necesitan los desplegables armados con
 * las clases accordion. Así el módulo no depende de que otro módulo de la
 * página (el menú) ya lo haya cargado.
 *
 * Se elige en el módulo: pestaña Advanced → Layout → acordeon.
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ModuleHelper;

HTMLHelper::_('bootstrap.collapse');

require ModuleHelper::getLayoutPath('mod_custom', 'default');
