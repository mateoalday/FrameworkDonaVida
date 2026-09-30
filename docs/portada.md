# Portada

Tres módulos Custom que le dan a Inicio un bloque de presentación, una sección de motivos para donar y un pie de página con las secciones y el contacto. Todo el diseño sale de clases de Bootstrap 5 en el HTML de cada módulo, más unas reglas en `user.css` de la plantilla hija.

Los módulos se guardan en la base de datos, así que no aparecen en git. Este documento registra cómo recrearlos. Las rutas del panel están en inglés porque el sitio usa el idioma `en-GB`.

## Recrearla automáticamente

[`scripts/portada.php`](scripts/portada.php) crea o actualiza los tres módulos y despublica el Login Form. `todo.php` lo ejecuta junto con los demás:

```
C:\xampp\php\php.exe docs\scripts\todo.php
```

Busca cada módulo por su título, así que se puede ejecutar varias veces sin duplicar nada. El HTML lo toma de `docs/modulos/portada-*.html`.

## Módulos

**Content → Site Modules → New → Custom**, con el HTML pegado con **Toggle Editor** desactivado (desde el `<section>` o `<div>` del principio, sin el comentario).

| Título | HTML | Posición | Show Title | Module Style (pestaña Advanced) | Menu Assignment |
|---|---|---|---|---|---|
| Portada - Hero | [`modulos/portada-hero.html`](modulos/portada-hero.html) | `banner` | Hide | Inherited | Only on the pages selected → Inicio |
| Portada - Por qué donar | [`modulos/portada-por-que-donar.html`](modulos/portada-por-que-donar.html) | `top-a` | Hide | `System-none` | Only on the pages selected → Inicio |
| Pie de página | [`modulos/portada-footer.html`](modulos/portada-footer.html) | `footer` | Hide | Inherited | On all pages |

Además: **Content → Site Modules → Login Form → Unpublish**.

## Cambios respecto de la instalación

| Elemento | Original | DonaVida |
|---|---|---|
| Portada, parte superior | Solo el título "Home" | Hero a todo el ancho con degradado rojo, título, texto y botones "Ver campañas" y "Dónde donar" |
| Portada, debajo del hero | Nada | "¿Por qué donar?" en tres tarjetas con ícono, que se apilan en el celular |
| Pie de página | No había | Degradado rojo con tres columnas (DonaVida, Secciones, Contacto) y una línea legal, en todas las páginas |
| Formulario de login | En la columna derecha de todas las páginas | Despublicado |

## Decisiones

- **Módulos Custom con HTML de Bootstrap** y no una plantilla nueva: el contenido queda editable desde el panel y el diseño lo resuelve el framework que ya trae Cassiopeia.
- **Clases de Bootstrap usadas**: grilla (`container`, `row`, `col-md-4`, `g-4`), tipografía (`display-5`, `lead`, `fw-bold`, `h5`, `small`), botones (`btn-light`, `btn-outline-light`, `btn-lg`), utilidades (`d-flex`, `flex-wrap`, `gap-2`, `h-100`, `rounded-3`, `shadow-sm`, `list-unstyled`, `border-top`). La grilla hace que las tres columnas se apilen solas en pantallas chicas.
- **Íconos de Font Awesome que incluye Joomla** (`icon-droplet`, `icon-hand-holding-heart`, `icon-clock`, `icon-shield`, `icon-envelope`, `icon-location`): no hace falta cargar ninguna librería externa.
- **Module Style `System-none` en "Por qué donar"**: la posición `top-a` de Cassiopeia envuelve cada módulo en una tarjeta. Con ese estilo el módulo sale sin envoltorio y las tarjetas las arma el propio HTML.
- **Pie de página en todas las páginas y hero solo en Inicio**: el hero es la presentación del sitio; el pie es navegación y contacto, que sirven en cualquier página.
- **Fondo del hero con degradado y sin foto**: se ve bien sin depender de una imagen. Para sumar una foto, subirla a `images/donavida/hero.jpg` y agregar la regla de abajo en `user.css`.
- **Textos de "Por qué donar" sin cifras**: describen el proceso (componentes de la sangre, entrevista previa, material descartable) sin números que requieran una fuente estadística. Si se agregan cifras, citar la fuente oficial en el informe.
- **Login Form despublicado**: los visitantes no inician sesión en este sitio; el panel se usa desde `/administrator`.
- **Animación de las tarjetas desactivada con `prefers-reduced-motion`**: respeta a quien configuró su sistema para reducir el movimiento.
- **Links relativos** (`index.php/sedes`): el plugin System - SEF los convierte en URLs completas, así funcionan en cualquier carpeta de instalación. "Campañas" y "Requisitos" apuntan a los alias `campanas` y `requisitos`, que son los que usa `plantilla.php` para ordenar el menú.

Regla para sumar una foto de fondo al hero:

```css
.hero-donavida {
  background-image: linear-gradient(135deg, rgba(176, 16, 24, .88) 0%, rgba(125, 11, 17, .88) 100%),
                    url("../../../../../images/donavida/hero.jpg");
  background-size: cover;
  background-position: center;
}
```

## Pendiente

- Los botones "Ver campañas" y los links a Campañas y Requisitos del pie funcionan cuando existan esos ítems de menú (alias `campanas` y `requisitos`).
- Datos de contacto del pie ficticios, como los de las sedes.

## Verificación

- En Inicio: hero rojo a todo el ancho, debajo las tres tarjetas y abajo el pie de página. Sin formulario de login.
- En `index.php/sedes`: el pie de página también aparece; el hero y las tarjetas no.
- En el celular (375 px): las tarjetas y las columnas del pie se apilan, sin scroll horizontal.
