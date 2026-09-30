# Scripts de contenido

Joomla guarda el contenido (categorías, artículos, contactos, menús, módulos, configuración de la plantilla) en la base de datos de cada uno, y git solo lleva archivos. Cada sección tiene acá un script que crea o actualiza su contenido en la base local, así todos ven lo mismo sin pasarse la base ni pisarse.

## Después de cada `git pull`

Desde la carpeta del proyecto, con MySQL de XAMPP encendido:

```
C:\xampp\php\php.exe docs\scripts\todo.php
```

Ejecuta todos los scripts. Se puede correr las veces que haga falta: lo que ya existe se actualiza en lugar de duplicarse, y cada script toca solo lo de su sección.

## Scripts

| Script | Issue | Qué deja en la base |
|---|---|---|
| `plantilla.php` | TP2-9 | Plantilla hija registrada y predeterminada, con logo y Roboto; Main Menu en el header; portada "Inicio"; orden del menú |
| `portada.php` | – | Módulos "Portada - Hero", "Portada - Por qué donar" y "Pie de página"; Login Form despublicado |
| `sedes.php` | TP2-7 | Categoría y campo "Horarios", las 5 sedes, ítem de menú "Sedes" y módulo "Nuestras sedes" |
| `comun.php` | – | Arranque de Joomla y funciones compartidas (no se ejecuta solo) |
| `todo.php` | – | Ejecuta todos los anteriores; `plantilla.php` va al final porque ordena el menú |

## Sumar el script de tu sección

1. Armá la sección en el panel, como siempre.
2. Creá `docs/scripts/<seccion>.php` tomando `sedes.php` como modelo: empieza con `require __DIR__ . '/comun.php';` y, para cada elemento, busca si ya existe (`buscarId`) y lo guarda con la tabla de Joomla que corresponda (`guardar`). `todo.php` lo va a ejecutar solo.
3. Reglas para no pisarse:
   - Identificar cada elemento por su **alias** (los módulos, por su título), para poder ejecutarlo varias veces.
   - Crear o actualizar **solo lo de tu sección**; nunca borrar ni cambiar lo de otros.
   - Ítems del menú principal con alias `campanas` y `requisitos`: con esos alias `plantilla.php` los ubica en el orden del issue (Inicio · Campañas · Sedes · Requisitos).
   - Si después cambiás algo en el panel, actualizá el script en el mismo PR.
4. Probalo sobre una copia de tu base antes de subirlo: creá la copia en phpMyAdmin (Operations → Copy database to) y ejecutá `set DONAVIDA_DB=nombre_de_la_copia` antes del script.

Los artículos necesitan registros extra además de su tabla (flujo de publicación, artículos destacados). Si tu sección usa artículos, conviene sumar una función para crearlos en `comun.php` en vez de resolverlo en cada script.
