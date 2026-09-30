# Sección Sedes (TP2-7)

Página pública con las sedes de donación y un módulo en Inicio que las lista con un link a esa página.

Casi todo lo de esta sección se guarda en la base de datos (contactos, campo, menú y módulo), así que no aparece en git. Este documento registra los pasos para poder recrearla en otra instalación. Las rutas del panel están en inglés porque el sitio usa el idioma `en-GB`.

## Recrearla automáticamente

[`scripts/sedes.php`](scripts/sedes.php) hace los pasos 1 a 6 en la base local. Desde la carpeta del proyecto, con MySQL de XAMPP encendido:

```
C:\xampp\php\php.exe docs\scripts\sedes.php
```

Se puede ejecutar más de una vez: lo que ya existe se actualiza en lugar de duplicarse (busca cada elemento por su alias), y no toca el contenido de otras secciones. Usa las clases de Joomla, así que respeta el prefijo de tablas y los IDs de cada base. El contenido del módulo lo toma de `modulos/sedes.html`.

Los pasos manuales de abajo hacen lo mismo desde el panel.

## 1. Categoría

**Components → Contacts → Categories → New**

- Title: `Sedes`

## 2. Campo personalizado "Horarios"

**Components → Contacts → Fields** (contexto **Contact**) **→ New**

- Title: `Horarios`
- Type: `Textarea`
- Category: todas (sin restricción)

## 3. Opciones de Contactos

**Components → Contacts → Options**. Se cambiaron solo estas tres respecto de la instalación:

| Pestaña | Opción | Antes | Ahora | Motivo |
|---|---|---|---|---|
| Contact | Email | Hide | Show | Mostrar el email de cada sede |
| List Layouts | Email | Hide | Show | Mostrar el email en el listado de sedes |
| Form | Contact Form | Show | Hide | En XAMPP no hay servidor de correo y el formulario fallaría al enviar |

## 4. Sedes

**Components → Contacts → New**, con **Category: Sedes**. Los horarios se cargan en la pestaña **Fields**. Los datos son ficticios.

| Name | Address | City or Suburb | State or County | Telephone | Email | Horarios |
|---|---|---|---|---|---|---|
| Sede Neuquén Centro | Av. Argentina 1200 | Neuquén | Neuquén | 0299 400-1001 | neuquen@donavida.org | Lun a vie, 8 a 13 h |
| Sede Cipolletti | Irigoyen 450 | Cipolletti | Río Negro | 0299 400-1002 | cipolletti@donavida.org | Lun a vie, 8 a 12 h · Sáb, 9 a 12 h |
| Sede Plottier | Av. San Martín 300 | Plottier | Neuquén | 0299 400-1003 | plottier@donavida.org | Mar y jue, 8 a 12 h |
| Sede General Roca | Tucumán 800 | General Roca | Río Negro | 0298 400-1004 | roca@donavida.org | Lun a vie, 7:30 a 12:30 h |
| Sede Centenario | Av. Libertador 150 | Centenario | Neuquén | 0299 400-1005 | centenario@donavida.org | Lun, mié y vie, 8 a 12 h |

Country: `Argentina` en todas.

## 5. Ítem de menú

**Menus → Main Menu → New**

- Menu Title: `Sedes` (alias `sedes`)
- Menu Item Type: **Contacts → List Contacts in a Category**
- Choose a Category: `Sedes`

La página queda en `index.php/sedes`.

## 6. Módulo "Nuestras sedes"

**Content → Site Modules → New → Custom**

- Title: `Nuestras sedes` (título visible)
- Contenido: el HTML de [`modulos/sedes.html`](modulos/sedes.html), desde `<p>` hasta el final, pegado con **Toggle Editor** desactivado
- Position: `sidebar-right`
- Menu Assignment: **Only on the pages selected → Home**

## Decisiones

- **Horarios como campo personalizado** y no en "Miscellaneous Information": queda como un dato propio de cada sede, con su etiqueta, en vez de texto libre mezclado.
- **Formulario de contacto oculto**: el sitio corre en local sin servidor de correo. El email se muestra para que el donante escriba desde su propio cliente.
- **Módulo Custom con HTML fijo**: es el tipo de módulo que pide el issue, y Joomla no trae un módulo que liste contactos. La contra es que los datos quedan duplicados: si cambia una sede, hay que actualizar el contacto y el módulo.
- **Librerías y plugins usados en el módulo**:
  - **Bootstrap 5**, que viene con Cassiopeia: `list-group` para la lista y `btn btn-primary` para el botón.
  - **Font Awesome**, incluido en Joomla: íconos `icon-location` e `icon-clock`.
  - **Plugin System - SEF**: convierte el link relativo `index.php/sedes` en la URL completa, así funciona en cualquier carpeta de instalación.
- **Plugin Content - Email Cloaking** (activo por defecto): en la página de Sedes oculta los emails a los bots y los muestra con JavaScript.
- **Editar el módulo con CodeMirror** (Users → tu usuario → Basic Settings → Editor): el editor visual TinyMCE puede borrar los `<span>` vacíos de los íconos al guardar.

## Verificación

- `index.php/sedes` lista las 5 sedes con horarios, teléfono, email, ciudad, provincia y país. Al entrar a cada sede se ve además la dirección.
- En Inicio, la columna derecha muestra "Nuestras sedes" y el botón "Ver direcciones y teléfonos" lleva a la página de Sedes.
