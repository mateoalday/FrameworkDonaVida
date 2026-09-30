# Usuarios y acceso al sitio

DonaVida es un sitio informativo: campañas, sedes y requisitos se ven sin cuenta. Por eso el sitio público no muestra formulario de login ni permite registrarse. Los integrantes del grupo administran el sitio desde el panel.

## Panel de administración

```
http://localhost/frameworkDonaVida/administrator
```

Cada integrante entra con el Super User que creó al instalar Joomla. Los usuarios se guardan en la base de cada uno y no se comparten por git: los scripts de `docs/scripts/` no los necesitan, porque toman como autor al primer Super User de cada base.

## Recrearlo automáticamente

[`scripts/usuarios.php`](scripts/usuarios.php) hace los dos pasos en la base local. `todo.php` lo ejecuta junto con los demás.

```
C:\xampp\php\php.exe docs\scripts\usuarios.php
```

## Pasos en el panel

1. **Login Form.** Content → Site Modules → Login Form → Status: `Unpublished`. Se despublica y no se borra, así se puede volver a activar.
2. **Registro.** Users → Options → User Options → Allow User Registration: `No`. En Joomla 5 ya viene así; el script lo deja asegurado.

## Decisiones

- **Sin login en el sitio público**: nadie necesita cuenta para informarse sobre cómo donar, y un formulario de login en todas las páginas confunde al donante y ofrece una puerta de entrada que no se usa.
- **Contenido a ancho completo**: el Login Form era lo único en la columna derecha de Campañas, Sedes y Requisitos. Sin él, Cassiopeia deja el contenido a ancho completo. En Inicio la columna sigue, con "Nuestras sedes".
- **Usuarios fuera de git**: poner usuarios en los scripts obligaría a dejar contraseñas en el repositorio.
