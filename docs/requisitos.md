# Sección Requisitos (TP2-8)

Página pública con los requisitos para donar sangre y, debajo, el módulo "Preguntas frecuentes": 12 preguntas en desplegables armados con el acordeón de Bootstrap.

El artículo, el menú y el módulo se guardan en la base de datos, así que no aparecen en git. Este documento registra los pasos para recrearlos en otra instalación. En git quedan el diseño `acordeon` de la plantilla, los colores del acordeón en `user.css` y las copias del HTML en `docs/`. Las rutas del panel están en inglés porque el sitio usa el idioma `en-GB`.

## Recrearla automáticamente

[`scripts/requisitos.php`](scripts/requisitos.php) hace los pasos 1 a 4 en la base local. Desde la carpeta del proyecto, con MySQL de XAMPP encendido:

```
C:\xampp\php\php.exe docs\scripts\requisitos.php
```

Se puede ejecutar más de una vez: busca cada elemento por su alias (el módulo, por su título) y lo actualiza en lugar de duplicarlo. El texto del artículo lo toma de [`articulos/requisitos.html`](articulos/requisitos.html) y el del módulo, de [`modulos/preguntas-frecuentes.html`](modulos/preguntas-frecuentes.html). `docs\scripts\todo.php` lo ejecuta junto con las demás secciones.

## Archivos

| Archivo | Contenido |
|---|---|
| `templates/cassiopeia_donavida/html/mod_custom/acordeon.php` | Diseño alternativo del módulo Custom que carga el JS del acordeón |
| `media/templates/site/cassiopeia_donavida/css/user.css` | Colores de la pregunta abierta (bloque "Acordeón") |
| `docs/articulos/requisitos.html` | Copia del texto del artículo |
| `docs/modulos/preguntas-frecuentes.html` | Copia del HTML del módulo |
| `docs/scripts/requisitos.php` | Script que crea la sección |

## 1. Categoría

**Content → Categories → New**

- Title: `Requisitos`

## 2. Artículo

**Content → Articles → New**

- Title: `Requisitos para donar sangre`
- Category: `Requisitos`
- Texto: el HTML de [`articulos/requisitos.html`](articulos/requisitos.html), desde `<p class="lead">` hasta el final, pegado con **Toggle Editor** desactivado

## 3. Ítem de menú

**Menus → Main Menu → New**

- Menu Title: `Requisitos` (alias `requisitos`)
- Menu Item Type: **Articles → Single Article**
- Select Article: `Requisitos para donar sangre`
- Pestaña **Options**: `Hide` en Category, Parent Category, Author, Create Date, Modify Date, Publish Date, Hits, Navigation y Tags

La página queda en `index.php/requisitos`. `plantilla.php` ubica el ítem último en el menú: Inicio · Campañas · Sedes · Requisitos.

## 4. Módulo "Preguntas frecuentes"

**Content → Site Modules → New → Custom**

- Title: `Preguntas frecuentes` (título visible)
- Contenido: el HTML de [`modulos/preguntas-frecuentes.html`](modulos/preguntas-frecuentes.html), desde `<p>` hasta el final, pegado con **Toggle Editor** desactivado
- Position: `main-bottom`
- Menu Assignment: **Only on the pages selected → Requisitos**
- Pestaña **Advanced**: Layout `acordeon` (de cassiopeia_donavida) · Module Tag `section` · Header Tag `h2`

## Preguntas

| N° | Tema | Pregunta |
|---|---|---|
| 1 | Edad | ¿Qué edad tengo que tener para donar? |
| 2 | Peso | ¿Cuánto tengo que pesar? |
| 3 | Tatuajes | Me hice un tatuaje o un piercing, ¿puedo donar? |
| 4 | Medicación | Estoy tomando medicación, ¿puedo donar? |
| 5 | Viajes | Viajé hace poco, ¿puedo donar? |
| 6 | Ayuno | ¿Tengo que ir en ayunas? |
| 7 | Frecuencia | ¿Cada cuánto puedo donar? |
| 8 | Enfermedad | Estoy resfriado o tuve fiebre, ¿puedo donar? |
| 9 | Embarazo | Estoy embarazada o dando de mamar, ¿puedo donar? |
| 10 | Vacunas | Me vacuné hace poco, ¿puedo donar? |
| 11 | Documentación | ¿Qué tengo que llevar? |
| 12 | Duración | ¿Cuánto tiempo lleva? ¿Es seguro? |

Las respuestas son orientativas, basadas en los criterios generales de donación en Argentina. Tanto la página como el módulo lo aclaran: la aptitud la confirma el profesional en la entrevista previa.

## Decisiones

- **Requisitos en un artículo y preguntas en un módulo**: los requisitos son el contenido principal de la página. Las preguntas son un bloque aparte que se podría mostrar en otras páginas con solo cambiar la asignación del módulo.
- **Módulo Custom con el acordeón de Bootstrap**: Joomla no trae un módulo de preguntas frecuentes, y el acordeón ya está en el CSS de Cassiopeia (`accordion-*`). Cada pregunta es un `button` con `data-bs-toggle="collapse"`, y `data-bs-parent` cierra la pregunta anterior al abrir otra, así la lista no se hace larga.
- **Diseño `acordeon` en la plantilla hija**: el acordeón necesita el JS `collapse` de Bootstrap. Hoy ya lo carga el menú (Collapsible Dropdown), pero si el menú cambiara las preguntas dejarían de abrirse sin ningún error. El diseño llama a `HTMLHelper::_('bootstrap.collapse')`, que lo carga con el gestor de assets de Joomla (sin duplicarlo si ya estaba), y después usa el diseño original de `mod_custom`. Es un override con otro nombre: el módulo "Nuestras sedes" sigue usando el original.
- **Colores de la pregunta abierta**: Bootstrap la compila con texto azul oscuro (`#0f244d`) sobre gris azulado (`#e7e9ee`). En `user.css` pasa a `#7d0b11` sobre `#f8e7e8`, que da 9:1 de contraste (AA y AAA), y la flecha también pasa a rojo oscuro.
- **Opciones de vista en el ítem de menú y no en el artículo**: cuando un ítem de menú apunta a un solo artículo, Joomla prioriza las opciones del ítem (y las globales de Contenido) sobre las del artículo. Por eso autor, fechas, categoría y visitas se ocultan desde el ítem.
- **`section` y `h2` en el módulo**: la página ya tiene el `h1` del artículo. El título del módulo va como `h2` y cada pregunta como `h3`, así la jerarquía de títulos es correcta para los lectores de pantalla.
- **Librerías y plugins usados**:
  - **Bootstrap 5**, que viene con Cassiopeia: `accordion` y `collapse` en el módulo; `row`, `card`, `list-unstyled` y `alert` en el artículo.
  - **Font Awesome**, incluido en Joomla: íconos `icon-check` e `icon-info-circle`.
  - **Gestor de assets de Joomla** (`HTMLHelper::_('bootstrap.collapse')`): carga el JS del acordeón.
- **Editar con CodeMirror** (Users → tu usuario → Basic Settings → Editor): TinyMCE puede borrar los atributos `data-bs-*` y los `<span>` vacíos de los íconos.

## Verificación

- `index.php/requisitos` muestra el artículo sin autor, fechas, categoría ni visitas, y abajo "Preguntas frecuentes" con 12 preguntas cerradas.
- Al hacer clic en una pregunta se abre su respuesta con fondo rosa claro, y se cierra la que estaba abierta.
- En el celular (menos de 768 px) las dos tarjetas de requisitos quedan una debajo de la otra.
- Capturas: `docs/despues/11-requisitos.png` (con una pregunta abierta) y `docs/despues/12-requisitos-celular.png`.
