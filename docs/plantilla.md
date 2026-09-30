# Plantilla DonaVida (TP2-9)

Plantilla hija de Cassiopeia (`cassiopeia_donavida`) con la paleta, el logo, la tipografía y el menú de DonaVida. Cassiopeia queda sin modificar, como punto de comparación con el estado original de `docs/antes/`.

## Archivos

| Archivo | Contenido |
|---|---|
| `templates/cassiopeia_donavida/templateDetails.xml` | Definición de la plantilla hija (padre: `cassiopeia`) |
| `media/templates/site/cassiopeia_donavida/css/user.css` | Paleta #B01018 y tamaño del logo |
| `images/donavida/logo-donavida.png` | Logo que usa el sitio |
| `docs/logo/logo-donavida.svg` | Fuente vectorial del logo |

El resto (estilo, logo elegido, tipografía y menú) se guarda en la base de datos. Los pasos del panel están abajo.

## Cambios respecto de Cassiopeia

| Elemento | Cassiopeia original | DonaVida |
|---|---|---|
| Color principal (header, botones) | `#112855` azul | `#B01018` rojo |
| Hover y final del degradado del header | `#424077` | `#7D0B11` |
| Links | `#224FAA` | `#B01018` |
| Colores compilados de Bootstrap (foco, paginación, listas, desplegables) | `#010156` | `#B01018` |
| Logo | Texto "CASSIOPEIA" | Gota blanca con corazón y "DonaVida" |
| Tipografía | Ninguna (fuente del sistema) | Roboto (local) |
| Menú | Columna derecha, con título, solo "Home" | En el header, sin título: Inicio · Sedes, con botón de hamburguesa en el celular |

## Pasos en el panel

Las rutas están en inglés porque el sitio usa el idioma `en-GB`.

1. **Plantilla hija.** System → Site Templates → Cassiopeia Details and Files → **Create Child Template**, con el nombre `donavida`.
   - En otra instalación **no hay que crearla de nuevo**: los archivos llegan con git y se registra desde **System → Discover**, instalando `cassiopeia_donavida`.
2. **Estilo predeterminado.** System → Site Template Styles → estrella en `cassiopeia_donavida - Default`.
3. **Parámetros del estilo** (pestaña Advanced):
   - Brand: `Yes`
   - Logo: `images/donavida/logo-donavida.png`
   - Font Scheme: `Roboto (local)`
   - Colour: queda en `Standard`, porque los colores se reemplazan en `user.css`
4. **Menú.**
   - Menus → Main Menu → Home → Menu Title: `Inicio`
   - Content → Site Modules → Main Menu → Show Title: `Hide` · Position: `menu` · pestaña Advanced → Layout: `Collapsible Dropdown` (de Cassiopeia)

## Decisiones

- **Plantilla hija en vez de editar Cassiopeia**: la original queda intacta para comparar el antes y el después, y los cambios no se pierden si Joomla actualiza Cassiopeia.
- **Colores en `user.css`**: Cassiopeia solo trae los esquemas Standard y Alternative, y el desplegable "Colour" solo busca archivos en la carpeta de Cassiopeia. Cassiopeia carga el `user.css` de la hija automáticamente después de sus propios estilos, así que alcanza con sobrescribir sus variables (`--cassiopeia-color-*`). Además se reemplazan los colores que Bootstrap dejó compilados en azul.
- **Contraste**: #B01018 sobre blanco da 7,2:1, así que cumple WCAG AA y AAA para texto normal.
- **Logo en PNG**: el gestor de medios de Joomla no acepta SVG por defecto, porque un SVG puede contener scripts. El PNG está exportado al doble de resolución y `user.css` lo muestra a 3rem (48 px) de alto para que se vea nítido. Va en blanco porque se muestra sobre el header rojo.
- **Roboto local y no Google Fonts**: la fuente se sirve desde el propio sitio, así que no depende de internet durante la demo.
- **Collapsible Dropdown**: es el diseño de menú de Cassiopeia que agrega el botón de hamburguesa en pantallas chicas. Usa el componente `collapse` de Bootstrap.

## Pendiente

- Agregar Campañas y Requisitos al menú cuando existan sus secciones (TP2-6 y TP2-8).
- Las migas de pan dicen "Home" porque ese texto sale del idioma `en-GB` del sitio, no del menú.
