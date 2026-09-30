<?php

/**
 * Arranque de Joomla y funciones compartidas por los scripts de contenido.
 *
 * Cada script de sección hace `require __DIR__ . '/comun.php';` y queda con
 * $app, $db (la base local) y $usuario (el primer Super User, que figura como
 * autor de lo que se crea). No se ejecuta solo: ver README.md.
 *
 * Para trabajar sobre una copia de la base (con el mismo prefijo de tablas),
 * antes de ejecutar un script: set DONAVIDA_DB=nombre_de_la_copia
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

const _JEXEC = 1;

define('JPATH_BASE', dirname(__DIR__, 2));
require_once JPATH_BASE . '/includes/defines.php';

if (!is_file(JPATH_CONFIGURATION . '/configuration.php')) {
    fwrite(STDERR, "No se encontró configuration.php: primero hay que instalar Joomla.\n");
    exit(1);
}

require_once JPATH_BASE . '/includes/framework.php';

// Mismo arranque que cli/joomla.php, sin ejecutar la consola
$container = Factory::getContainer();
$container->alias('session', 'session.cli')
    ->alias('JSession', 'session.cli')
    ->alias(\Joomla\CMS\Session\Session::class, 'session.cli')
    ->alias(\Joomla\Session\Session::class, 'session.cli')
    ->alias(\Joomla\Session\SessionInterface::class, 'session.cli');

$app                  = $container->get(\Joomla\Console\Application::class);
Factory::$application = $app;

// La consola lo hace al ejecutarse: registra las clases de los componentes (Contactos, Campos)
$app->createExtensionNamespaceMap();

/** @var DatabaseDriver $db */
$db = $container->get(DatabaseInterface::class);

try {
    if ($copia = getenv('DONAVIDA_DB')) {
        $db->select($copia);
    }

    $adminId = (int) $db->setQuery(
        $db->createQuery()
            ->select('MIN(' . $db->quoteName('user_id') . ')')
            ->from($db->quoteName('#__user_usergroup_map'))
            ->where($db->quoteName('group_id') . ' = 8')
    )->loadResult();
} catch (Throwable $e) {
    fwrite(STDERR, 'No se pudo conectar a la base: ' . $e->getMessage() . PHP_EOL . '¿Está encendido MySQL en XAMPP?' . PHP_EOL);
    exit(1);
}

$usuario = $container->get(UserFactoryInterface::class)->loadUserById($adminId);
$app->loadIdentity($usuario);

/**
 * Devuelve el valor de $columna (por defecto el id) de la primera fila que cumple las condiciones, o 0.
 */
function buscarId(DatabaseDriver $db, string $tabla, array $condiciones, string $columna = 'id'): int
{
    $query = $db->createQuery()
        ->select($db->quoteName($columna))
        ->from($db->quoteName($tabla));

    foreach ($condiciones as $campo => $valor) {
        $query->where($db->quoteName($campo) . ' = ' . $db->quote($valor));
    }

    return (int) $db->setQuery($query, 0, 1)->loadResult();
}

/**
 * Carga la fila existente (si hay) y guarda los datos con las validaciones de Joomla.
 */
function guardar(Table $tabla, int $id, array $datos, string $descripcion): void
{
    if ($id) {
        $tabla->load($id);
    }

    if (!$tabla->bind($datos) || !$tabla->check() || !$tabla->store()) {
        throw new RuntimeException($descripcion . ': ' . $tabla->getError());
    }

    informar($id ? 'actualizado' : 'creado', $descripcion);
}

/**
 * Combina los cambios con un JSON de parámetros existente y devuelve el JSON resultante.
 */
function combinarParams(?string $json, array $cambios): string
{
    return json_encode(array_merge(json_decode($json ?: '{}', true) ?: [], $cambios));
}

function informar(string $accion, string $descripcion): void
{
    echo '  ' . str_pad($accion, 12) . ' ' . $descripcion . PHP_EOL;
}
