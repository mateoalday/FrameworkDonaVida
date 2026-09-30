# DonaVida Comunidad

Sitio informativo de DonaVida, red de donación de sangre: campañas, sedes y requisitos para donar.

Trabajo Práctico N°2 de Frameworks e Interoperabilidad (UNCo) — Grupo Código Rojo: Matteo Alday, Fidel Pizarro, Joaquín Vulcano.

## Stack

- **CMS:** Joomla 5.4.9
- **Framework:** Bootstrap 5 (incluido en la plantilla Cassiopeia)
- **Servidor local:** XAMPP (PHP 8.2, MariaDB/MySQL)

## Cómo levantarlo

1. Clonar el repositorio en `C:\xampp\htdocs\frameworkDonaVida` e iniciar Apache y MySQL desde XAMPP:
   ```
   git clone https://github.com/mateoalday/FrameworkDonaVida.git C:\xampp\htdocs\frameworkDonaVida
   ```
2. Descargar el paquete completo de Joomla 5.4.9 (`Joomla_5.4.9-Stable-Full_Package.zip`, de downloads.joomla.org) y copiar **solo** su carpeta `installation/` dentro del proyecto. El resto del núcleo ya está en el repositorio.
3. Abrir `http://localhost/frameworkDonaVida` y completar el instalador. En la base de datos: host `localhost`, usuario `root`, contraseña vacía y base `donavida`. El instalador crea la base y `configuration.php`, que no se versiona porque tiene credenciales.
4. En la última pantalla, desactivar las actualizaciones automatizadas (así el núcleo sigue igual al del repositorio) y entrar con "Abrir el sitio" o "Abrir la administración", que borra la carpeta `installation/`.
5. Cargar el contenido del sitio con `C:\xampp\php\php.exe docs\scripts\todo.php` (ver abajo).

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
docs/articulos/        Copias del HTML de los artículos (las cargan los scripts)
docs/modulos/          Copias del HTML de los módulos Custom (las cargan los scripts)
docs/scripts/          Scripts que cargan el contenido de cada sección en la base local
templates/             Plantillas (incluye la plantilla hija de DonaVida)
```

El resto de las carpetas son el núcleo de Joomla, que no se modifica.
