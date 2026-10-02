# DonaVida Comunidad

Sitio informativo de DonaVida, red de donación de sangre: campañas, sedes y requisitos para donar.

Trabajo Práctico N°2 de Frameworks e Interoperabilidad (UNCo) — Grupo Código Rojo: Matteo Alday, Fidel Pizarro, Joaquín Vulcano.

## Qué es nuestro

El repositorio tiene casi 10.000 archivos, pero **casi todos son de Joomla**: vienen tal cual en el paquete que se descarga de joomla.org (`Joomla_5.4.9-Stable-Full_Package.zip`) y no los modificamos. Están versionados para que el sitio se pueda levantar clonando el repo, y para que el primer commit muestre Joomla recién instalado y cada commit posterior, exactamente qué cambiamos.

Lo que hicimos nosotros son unos 50 archivos, en estas carpetas:

| Carpeta o archivo | Qué es | Quién |
|---|---|---|
| [`templates/cassiopeia_donavida/`](templates/cassiopeia_donavida/) | Plantilla hija de Cassiopeia y sus overrides: diseño `proximas` del módulo de campañas, diseño `acordeon` de las preguntas frecuentes y formato de fecha `dd/mm/aaaa` | L tres |
| [`media/templates/site/cassiopeia_donavida/css/user.css`](media/templates/site/cassiopeia_donavida/css/user.css) | Estilos propios: paleta roja #B01018 sobre las variables de Bootstrap y Cassiopeia, acordeón, blog en columnas y portada | Los tres |
| [`images/donavida/`](images/donavida/) y [`images/campanas/`](images/campanas/) | Logo de DonaVida e imágenes de las campañas | Joaquín (logo), Fidel (campañas) |
| [`docs/scripts/`](docs/scripts/) | Scripts que cargan el contenido de cada sección en la base local (Joomla guarda el contenido en la base, no en archivos) | Joaquín (`comun.php`, `todo.php`, `sedes.php`, `plantilla.php`), Fidel (`campanias.php`, `requisitos.php`, `usuarios.php`, imágenes), Matteo (`portada.php`) |
| [`docs/modulos/`](docs/modulos/) y [`docs/articulos/`](docs/articulos/) | HTML de los módulos y artículos, armado con clases de Bootstrap | Los tres |
| `docs/sedes.md`, `campanias.md`, `requisitos.md`, `portada.md`, `plantilla.md`, `usuarios.md` | Paso a paso y decisiones de cada sección | Cada uno la suya |
| [`docs/antes/`](docs/antes/) y [`docs/despues/`](docs/despues/) | Capturas del sitio sin modificar y terminado | Matteo (antes), Fidel y Joaquín (después) |
| [`docs/modificaciones.md`](docs/modificaciones.md) | Tabla de cambios respecto de Joomla y Cassiopeia | Los tres |
| `README.md` y `.gitignore` | Este archivo y la lista de lo que no se sube (por ejemplo `configuration.php`, que tiene la contraseña de la base) | Matteo, Joaquín |

Lo que se ve en el sitio por sección:

- **Sedes** (Joaquín): componente Contactos adaptado con 5 sedes y el campo "Horarios", y el módulo "Nuestras sedes" en la portada.
- **Campañas** (Fidel): 5 campañas con campos de fecha, sede y grupos buscados, el blog en tres columnas y el módulo "Próximas campañas" con tarjetas.
- **Requisitos** (Fidel): página de requisitos y el módulo "Preguntas frecuentes" con el acordeón de Bootstrap.
- **Portada** (Matteo): hero, "¿Por qué donar?" y pie de página.
- **Plantilla** (Joaquín): paleta, logo, tipografía y menú en el header.


El resto de las carpetas (`administrator/`, `components/`, `libraries/`, `modules/`, `plugins/`, etc.) es el núcleo de Joomla sin cambios.

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
