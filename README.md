# DonaVida Comunidad

Sitio informativo de DonaVida, red de donación de sangre: campañas, sedes y requisitos para donar.

Trabajo Práctico N°2 de Frameworks e Interoperabilidad (UNCo) — Grupo Código Rojo: Matteo Alday, Fidel Pizarro, Joaquín Vulcano.

## Stack

- **CMS:** Joomla 5.4.9
- **Framework:** Bootstrap 5 (incluido en la plantilla Cassiopeia)
- **Servidor local:** XAMPP (PHP 8.2, MariaDB/MySQL)

## Cómo levantarlo

1. Copiar el repositorio en `C:\xampp\htdocs\frameworkDonaVida` e iniciar Apache y MySQL desde XAMPP.
2. En phpMyAdmin crear la base `donavida` e importar `db/donavida.sql`.
3. Crear `configuration.php` (no se versiona porque tiene credenciales) y abrir `http://localhost/frameworkDonaVida`.

## Contenido del sitio (scripts)

Las sedes, campañas, menús, módulos y la configuración de la plantilla se guardan en la base de datos de cada uno, que no viaja con git. Por eso cada sección tiene un script en `docs/scripts/` que la carga en la base local sin pisar lo de los demás.

- **Después de cada `git pull`**, con MySQL encendido, ejecutar desde la carpeta del proyecto:
  ```
  C:\xampp\php\php.exe docs\scripts\todo.php
  ```
  Carga todas las secciones. Se puede repetir las veces que haga falta: actualiza lo que ya existe en lugar de duplicarlo.
- **Al terminar tu sección**, sumá su script (`docs/scripts/<seccion>.php`) en el mismo PR. Si no, los demás no van a ver lo que armaste en el panel. Cómo hacerlo y las reglas para no pisarse: [docs/scripts/README.md](docs/scripts/README.md).

## Estructura

```
docs/antes/            Capturas de Joomla y Cassiopeia sin modificar
docs/despues/          Capturas del sitio terminado
docs/modificaciones.md Tabla de cambios realizados
docs/scripts/          Scripts que cargan el contenido de cada sección en la base local
db/                    Export de la base de datos
templates/             Plantillas (incluye la plantilla hija de DonaVida)
```

El resto de las carpetas son el núcleo de Joomla, que no se modifica.
