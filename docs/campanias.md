# Sección Campañas (TP2-6)

Página pública con las colectas programadas y el módulo "Próximas campañas" en la portada, con las próximas tres como tarjetas.

La categoría, los campos, los artículos, el menú y el módulo se guardan en la base de datos, así que no aparecen en git. Este documento registra los pasos para recrearlos en otra instalación. En git quedan las imágenes, los dos overrides de la plantilla y el estilo del blog en `user.css`. Las rutas del panel están en inglés porque el sitio usa el idioma `en-GB`.

## Recrearla automáticamente

[`scripts/campanias.php`](scripts/campanias.php) hace los pasos 1 a 5 en la base local. Desde la carpeta del proyecto, con MySQL de XAMPP encendido:

```
C:\xampp\php\php.exe docs\scripts\campanias.php
```

Se puede ejecutar más de una vez: busca cada elemento por su alias (el módulo, por su título) y lo actualiza en lugar de duplicarlo. Los datos de las campañas están al principio del script. `docs\scripts\todo.php` lo ejecuta junto con las demás secciones.

Las imágenes ya están en `images/campanas/`. Si cambian los datos de una campaña, se regeneran con:

```
powershell -ExecutionPolicy Bypass -File docs\scripts\imagenes-campanas.ps1
```

## Archivos

| Archivo | Contenido |
|---|---|
| `templates/cassiopeia_donavida/html/mod_articles_category/proximas.php` | Diseño alternativo del módulo: tarjetas con imagen, fecha, sede y grupos; solo campañas futuras |
| `templates/cassiopeia_donavida/html/plg_fields_calendar/calendar.php` | Override que muestra los campos de fecha como `17/10/2026` |
| `media/templates/site/cassiopeia_donavida/css/user.css` | Tamaño de los títulos del blog en columnas (bloque "Blog en columnas") |
| `images/campanas/*.png` | Imagen de cada campaña (1200×675) |
| `docs/scripts/campanias.php` | Script que crea la sección |
| `docs/scripts/imagenes-campanas.ps1` | Script que genera las imágenes |

## 1. Categoría

**Content → Categories → New**

- Title: `Campañas` (alias `campanas`)
- Description: `Colectas programadas en nuestras sedes. Elegí la más cercana y vení en el horario indicado: no hace falta sacar turno.`

## 2. Campos personalizados

**Content → Fields → New**, los tres con **Category: Campañas**, **Required: Yes** y, en la pestaña Options, **Automatic Display: Before Display Content**.

| Title | Name | Type | Opciones |
|---|---|---|---|
| Fecha | `fecha` | Calendar | Show Time: No |
| Sede | `sede` | List | Las 5 sedes (texto: `Sede Neuquén Centro`, valor: `sede-neuquen-centro`, etc.) |
| Grupos buscados | `grupos` | Checkboxes | `0-`, `0+`, `A-`, `A+`, `B-`, `B+`, `AB-`, `AB+` (texto y valor iguales) |

## 3. Campañas

**Content → Articles → New**, con **Category: Campañas**. Los campos se cargan en la pestaña **Fields**. En la pestaña **Images and Links**, la misma imagen en Intro Image y Full Article Image, con su texto alternativo. Los datos son ficticios.

| Orden | Title | Fecha | Sede | Grupos | Imagen |
|---|---|---|---|---|---|
| 1 | Colecta de octubre en Neuquén Centro | 17/10/2026 | Sede Neuquén Centro | 0-, 0+, A- | `neuquen-centro.png` |
| 2 | Jornada solidaria en Cipolletti | 31/10/2026 | Sede Cipolletti | B-, AB- | `cipolletti.png` |
| 3 | Colecta de primavera en Plottier | 14/11/2026 | Sede Plottier | A+, 0+ | `plottier.png` |
| 4 | Maratón de grupos negativos en General Roca | 28/11/2026 | Sede General Roca | 0-, A-, B-, AB- | `general-roca.png` |
| 5 | Colecta de fin de año en Centenario | 12/12/2026 | Sede Centenario | 0+, A+, B+ | `centenario.png` |

El texto de introducción y el completo de cada una están en el script. El orden de los artículos es el de las fechas (Content → Articles, ordenar por Ordering y arrastrar).

## 4. Ítem de menú

**Menus → Main Menu → New**

- Menu Title: `Campañas` (alias `campanas`)
- Menu Item Type: **Articles → Category Blog**
- Choose a Category: `Campañas`
- Pestaña **Blog Layout**: # Leading `0` · # Intro `9` · # Links `0` · Article Class `boxed columns-3` · Article Order `Ordering` · Pagination `Hide`
- Pestaña **Options**: `Hide` en Category, Parent Category, Author, Create Date, Modify Date, Publish Date, Hits y Navigation
- Pestaña **Page Display**: Show Page Heading `Yes`
- Pestaña **Category**: Category Title `Hide` · Category Description `Show`

La página queda en `index.php/campanas`. `plantilla.php` ubica el ítem segundo en el menú: Inicio · Campañas · Sedes · Requisitos.

## 5. Módulo "Próximas campañas"

**Content → Site Modules → New → Articles - Category**

- Title: `Próximas campañas` (título visible)
- Position: `main-top`
- Menu Assignment: **Only on the pages selected → Inicio**
- Pestaña **Filtering**: Count `0` (todas: el diseño se queda con las próximas 3) · Category `Campañas`
- Pestaña **Ordering**: Article Field to Order By `Article Order` · Ordering Direction `Ascending`
- Pestaña **Display**: Linked Titles `Yes`; Date, Category, Hits, Author, Introtext y Read More en `Hide`
- Pestaña **Advanced**: Layout `proximas` (de cassiopeia_donavida) · Module Tag `section` · Header Tag `h2`

## Decisiones

- **Campañas como artículos**: cada campaña tiene texto, imagen y su propia página, que es lo que resuelven los artículos de Joomla. La categoría las agrupa, y así el blog y el módulo las toman solas: una campaña nueva aparece en los dos lugares sin tocar nada más.
- **Fecha, sede y grupos como campos personalizados**: quedan como datos con su tipo (fecha, lista, casillas) y no como texto libre. Así el módulo puede leer la fecha para esconder las campañas que ya pasaron, y la sede se elige de una lista que no admite errores de tipeo. Los campos se asignan solo a la categoría Campañas, para que no aparezcan en el artículo de Requisitos.
- **Fecha guardada al mediodía UTC**: Joomla pasa las fechas a la zona horaria de quien mira. Guardada a las 00:00 UTC, en Argentina (UTC-3) se vería el día anterior.
- **Diseño `proximas` para "Articles - Category"**: el diseño original del módulo es una lista de títulos y no puede mostrar imágenes ni campos personalizados. El diseño alternativo arma tarjetas de Bootstrap con la imagen de introducción, la fecha, la sede y los grupos (como etiquetas `badge`), saltea las campañas con fecha pasada y muestra hasta 3. Toda la tarjeta es un link (`stretched-link`). Es un override con otro nombre, así que el diseño original sigue disponible.
- **Override del campo Calendar**: el formato de fecha sale del idioma del sitio (`en-GB`: `2026-10-17`). El override de la plantilla muestra `17/10/2026` sin cambiar el idioma de todo el sitio.
- **Blog en tres columnas con `boxed columns-3`**: son clases que Cassiopeia ya trae para el blog, así que no hizo falta CSS nuevo para la grilla. Solo se achicó el título, que con el tamaño de un `h2` ocupaba tres líneas por tarjeta. En el celular queda una columna.
- **Imágenes generadas con un script**: son placas con la paleta de DonaVida, sin fotos de terceros ni problemas de licencia, y se pueden regenerar si cambian los datos. Se generan con .NET (`System.Drawing`) porque el PHP de XAMPP no trae GD activado. Cada imagen tiene texto alternativo con sus datos, porque la placa es texto dentro de una imagen.
- **Librerías y plugins usados**:
  - **Bootstrap 5**, que viene con Cassiopeia: `card`, `row-cols`, `badge`, `stretched-link` y `btn-outline-primary` en el módulo.
  - **Font Awesome**, incluido en Joomla: íconos `icon-calendar` e `icon-location`.
  - **Plugin Content - Fields**: muestra los campos personalizados en el blog y en cada campaña.
  - **Plugins Fields - Calendar, List y Checkboxes**: los tipos de los tres campos.
  - **`FieldsHelper`** del componente Fields: el diseño `proximas` lo usa para leer los campos de cada artículo.

## Verificación

- `index.php/campanas` muestra las 5 campañas en tres columnas, en orden de fecha, cada una con su imagen, fecha, sede, grupos, introducción y "Read more".
- Cada campaña abre su página con la imagen grande y el texto completo.
- En Inicio, "Próximas campañas" muestra las 3 más cercanas que todavía no pasaron, y el botón "Ver todas las campañas" lleva al blog.
- Capturas: `docs/despues/13-portada-campanas.png`, `14-campanas.png`, `15-campana-detalle.png` y `16-campanas-celular.png`.
