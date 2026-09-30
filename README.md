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

## Estructura

```
docs/antes/            Capturas de Joomla y Cassiopeia sin modificar
docs/despues/          Capturas del sitio terminado
docs/modificaciones.md Tabla de cambios realizados
db/                    Export de la base de datos
templates/             Plantillas (incluye la plantilla hija de DonaVida)
```

El resto de las carpetas son el núcleo de Joomla, que no se modifica.
