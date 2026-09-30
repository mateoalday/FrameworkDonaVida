<?php

/**
 * Quita el acceso de usuarios del sitio público en la base de datos local.
 *
 * DonaVida es un sitio informativo: el donante no necesita cuenta. Este script
 * despublica el módulo "Login Form" que Joomla crea al instalar y deja
 * desactivado el registro de usuarios. El panel sigue en /administrator.
 * El detalle está en docs/usuarios.md.
 *
 * Uso, desde la carpeta del proyecto y con MySQL de XAMPP encendido:
 *   C:\xampp\php\php.exe docs\scripts\usuarios.php
 */

use Joomla\CMS\Table\Module;

require __DIR__ . '/comun.php';

try {
    echo 'Usuarios del sitio en la base "' . $db->setQuery('SELECT DATABASE()')->loadResult() . '"' . PHP_EOL;

    // 1. Módulos de login del sitio (el instalador crea "Login Form" en sidebar-right)
    $logins = $db->setQuery(
        $db->createQuery()
            ->select($db->quoteName(['id', 'title', 'published']))
            ->from($db->quoteName('#__modules'))
            ->where($db->quoteName('module') . ' = ' . $db->quote('mod_login'))
            ->where($db->quoteName('client_id') . ' = 0')
    )->loadObjectList();

    foreach ($logins as $login) {
        if ((int) $login->published !== 1) {
            informar('sin cambios', 'módulo "' . $login->title . '" (ya despublicado)');
            continue;
        }

        guardar(new Module($db), (int) $login->id, ['published' => 0], 'módulo "' . $login->title . '" despublicado');
    }

    // 2. Registro de usuarios desactivado (Users → Options → Allow User Registration: No)
    $componenteId = idComponente($db, 'com_users');
    $params       = $db->setQuery(
        $db->createQuery()
            ->select($db->quoteName('params'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('extension_id') . ' = ' . $componenteId)
    )->loadResult();

    $db->setQuery(
        $db->createQuery()
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('params') . ' = ' . $db->quote(combinarParams($params, ['allowUserRegistration' => '0'])))
            ->where($db->quoteName('extension_id') . ' = ' . $componenteId)
    )->execute();
    informar('actualizado', 'registro de usuarios desactivado');

    echo 'Listo: el sitio no muestra login; el panel está en /administrator.' . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
