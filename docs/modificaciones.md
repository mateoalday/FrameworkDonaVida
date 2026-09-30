# Modificaciones realizadas

Cambios aplicados sobre Joomla 5.4.9 y la plantilla Cassiopeia, comparados con las capturas de `docs/antes/`.

| N° | Elemento | Original | Modificación | Fundamento |
|----|----------|----------|--------------|------------|
| 1 | Menú principal | Un solo ítem, "Home" | Nuevo ítem "Requisitos" (Single Article), último del menú | Es una de las tres secciones del sitio (TP2-8). Ver `docs/requisitos.md` |
| 2 | Página Requisitos | No existía | Artículo "Requisitos para donar sangre": dos tarjetas (`card`) con requisitos básicos y consejos para el día de la donación, y un aviso (`alert`) de que la información es orientativa | Las tarjetas separan lo que se necesita para donar de cómo prepararse. En el celular quedan una debajo de la otra (`row-cols-1 row-cols-md-2`) |
| 3 | Datos del artículo | Cassiopeia muestra autor, categoría, fecha de publicación y visitas | Ocultos desde el ítem de menú "Requisitos" | Es una página informativa, no una noticia: esos datos no le sirven al donante |
| 4 | Módulo "Preguntas frecuentes" | Joomla no trae un módulo de preguntas frecuentes | Módulo Custom con 12 preguntas (edad, peso, tatuajes, medicación, viajes, etc.) en un acordeón de Bootstrap, en `main-bottom` y solo en Requisitos | Responde las dudas más comunes sin alargar la página: cada respuesta se abre al hacer clic y cierra la anterior |
| 5 | Diseños del módulo Custom | Solo el diseño `default` | Nuevo diseño `acordeon` en la plantilla hija (`html/mod_custom/acordeon.php`) que carga el JS `collapse` de Bootstrap | Sin ese JS las preguntas no se abren. Hoy lo carga el menú, pero así el módulo no depende de él |
| 6 | Acordeón, pregunta abierta | Texto `#0f244d` sobre `#e7e9ee` y flecha azul (colores compilados de Bootstrap) | Texto `#7d0b11` sobre `#f8e7e8` y flecha en rojo oscuro (`user.css`) | Mantiene la paleta de DonaVida. Contraste 9:1 (WCAG AA y AAA) |
