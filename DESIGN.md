# Design

## Theme

**Escena física:** un taller ordenado a las ocho de la mañana. Luz fría entrando por la ventana, piso de concreto limpio, herramientas colgadas cada una en su lugar, una carpeta con la hoja de inspección sobre el capó. No es un showroom con pisos brillantes ni un patio de autos con banderines.

**Tema: claro.** La decisión no es por defecto. El contenido dominante son fotos de autos, y un fondo oscuro las tiñe y compite con ellas; sobre blanco puro se leen como lo que son. Además el uso real es a plena luz del día, en la calle, en un celular con el brillo peleando contra el sol.

**Estrategia de color: comprometida.** El azul de marca ocupa las anclas estructurales (héroe, bloques de conversión, pie de página) y el blanco puro sostiene el encabezado, el catálogo y las fichas.

El primario es el **azul exacto del logo, `#004AAD`**, muestreado del archivo y no estimado a ojo. Es un cobalto, no un azul marino: más claro y más saturado que el de auto360, Juan Ignacio Walker y Automotriz TuAuto, lo que ya da algo de distancia. Pero la diferenciación real de este sitio no está en la paleta, y conviene ser honesto al respecto: está en la hoja de inspección publicada, en la galería de defectos y en el tono del texto. Ninguna de las tres referencias muestra lo que su stock tiene malo.

## Color

Todo en OKLCH. Contrastes verificados contra blanco puro.

Contrastes medidos, no estimados. Todos pasan WCAG AA con holgura.

```css
:root {
  /* Superficies */
  --bg:            oklch(1     0     0);       /* #FFFFFF blanco puro */
  --surface:       oklch(0.975 0.007 259.2);   /* #F4F7FC secciones alternas */
  --border:        oklch(0.900 0.012 259.2);   /* #D9DEE6 */

  /* Tinta */
  --ink:           oklch(0.200 0.022 259.2);   /* #101620 · 18.1:1 sobre blanco */
  --ink-muted:     oklch(0.450 0.020 259.2);   /* #4F5661 ·  7.4:1 sobre blanco */

  /* Marca: el azul exacto del logo */
  --primary:       oklch(0.437 0.171 259.2);   /* #004AAD ·  8.1:1 */
  --primary-deep:  oklch(0.285 0.115 259.2);   /* #002561 · 14.6:1 — pie de página */
  --primary-soft:  oklch(0.945 0.028 259.2);   /* #E2EEFF — pastillas y avisos */

  /* Acento ámbar: señalización, nunca relleno grande */
  --accent:        oklch(0.720 0.155 62);      /* #E88B23 ·  7.0:1 con --ink encima */
  --accent-ink:    oklch(0.470 0.140 58);      /* #924000 ·  7.1:1 sobre blanco */

  /* Error de formulario */
  --danger:        oklch(0.505 0.190 27);      /* #B91C1E ·  6.5:1 sobre blanco */
}
```

**El ámbar no es el color de las llamadas a la acción.** Azul cobalto con botones ámbar grandes lee como IKEA. El ámbar queda restringido a tres usos, todos con significado: el estado "Reservado", los marcadores de observación de la hoja de inspección, y el contador de detalles documentados. Sobre el ámbar el texto va en `--ink`, nunca en blanco.

**Las llamadas a la acción usan el azul.** Sobre blanco, botón azul con texto blanco. Sobre el azul del héroe, la lógica se invierte: botón blanco con texto azul. Nunca dos colores saturados peleando en el mismo botón.

**El error de formulario es su propio color.** Un campo mal llenado y una observación de inspección no significan lo mismo, así que no comparten color.

**Estados del vehículo.** Disponible no lleva insignia: es el caso normal y no necesita ruido. Reservado va en ámbar con la palabra escrita. Vendido va en gris, con la foto desaturada y la palabra escrita. El color nunca comunica solo.

Sin modo oscuro en la primera versión. Un tema claro bien resuelto vale más que dos temas a medias.

## Logo

El archivo original (`public/logo.png`, 6250×1875) es demasiado pesado para servirlo en el encabezado. De él se derivan, con fondo blanco convertido a transparencia y sin premultiplicar:

- `public/img/logo.webp` (600×104, 18 KB): versión a color, para el encabezado blanco.
- `public/img/logo-white.webp` (600×104, 11 KB): versión monocromática blanca, para el pie azul oscuro.
- `public/favicon.ico`, `public/img/apple-touch-icon.png`, `public/img/icon-512.png`: recorte cuadrado del símbolo del auto con la llave.

**El encabezado es blanco y no azul.** La palabra "Pluss" del logo es negra y sobre azul desaparecería. Además, un encabezado azul seguido de un héroe azul convierte el primer pliegue en un solo bloque de color sin jerarquía.

## Typography

**Familias.** Dos, autoalojadas vía npm, sin llamadas a Google Fonts.

- **Archivo** (variable, ejes de peso y ancho) para todo el texto. Es una grotesca de Omnibus-Type, fundición argentina, diseñada para señalética e impresión exigente. Trae el soporte completo de diacríticos del español y una raíz industrial que calza con la escena del taller. Se descartaron Inter, DM Sans y Space Grotesk: son los reflejos de entrenamiento y producen el mismo sitio que todos.
- **Geist Mono** exclusivamente para datos numéricos: kilometraje, año, cilindrada, precio, patente. No es disfraz técnico. Es funcional: alinea las cifras en columna en las tablas de especificaciones y en las cards del catálogo, que es donde el comprador compara. Jamás en títulos ni en párrafos.

**Escala.** Modular con razón 1.28, fluida con `clamp()`.

```css
--text-xs:   0.78rem;
--text-sm:   0.875rem;
--text-base: 1rem;
--text-lg:   1.28rem;
--text-xl:   clamp(1.5rem,  1.2rem + 1.4vw, 1.95rem);
--text-2xl:  clamp(1.95rem, 1.5rem + 2.2vw, 2.75rem);
--text-3xl:  clamp(2.5rem,  1.8rem + 3.4vw, 3.75rem);  /* techo 60px */
```

El techo de los títulos es 3.75rem, muy por debajo del límite de 6rem. Un sitio que grita contradice la personalidad de la marca: la confianza no se comunica con tipografía enorme.

**Detalles.** Interletrado de display en `-0.02em`, nunca más apretado. `text-wrap: balance` en h1–h3 y `text-wrap: pretty` en párrafos largos. Ancho de línea máximo 68ch. Los títulos usan Archivo en peso 700 con el eje de ancho en 105 para dar autoridad sin recurrir a un segundo tipo.

## Layout

- Contenedor máximo 1240px; las fichas de vehículo usan 1120px porque son documentos de lectura.
- Espaciado fluido con `clamp()` y ritmo variable: separación generosa entre secciones (`clamp(4rem, 8vw, 7rem)`), agrupación apretada dentro de cada bloque de datos.
- Grilla del catálogo sin puntos de quiebre: `repeat(auto-fit, minmax(300px, 1fr))`.
- Radios: 10px en cards y contenedores, 8px en campos de formulario, pastilla completa solo en etiquetas de estado. Nada por sobre 16px.
- Sombras y bordes no se combinan. Las cards del catálogo llevan borde de 1px sin sombra en reposo, y al pasar el cursor cambian a sombra definida de 8px de desenfoque sin borde.
- Escala semántica de `z-index`: `--z-dropdown: 10`, `--z-sticky: 20`, `--z-overlay: 30`, `--z-modal: 40`, `--z-toast: 50`.
- Barra de acción fija al pie en la ficha en móvil: precio a la izquierda, WhatsApp a la derecha. El comprador nunca tiene que buscar cómo escribir.

## Components

**Card de vehículo.** Foto en proporción 4:3 con carga diferida, modelo y versión en Archivo, y una fila de cuatro datos en Geist Mono: año, kilometraje, transmisión, combustible. Precio destacado. Cuando el auto tiene defectos catalogados, un contador discreto: "5 detalles documentados". Ese contador es el diferenciador de la marca a la vista en el catálogo, no un dato escondido en la ficha.

**Hoja de inspección.** El componente firma del sitio y la razón por la que existe. Lista los puntos revisados por categoría (motor, transmisión, frenos, suspensión, carrocería, interior, documentación) con estado conforme, con observación, o reparado. Se lee como el documento que es: filas, no cards. Sin nesting, sin íconos decorativos.

**Galería de detalles.** Separada de las fotos de presentación y anunciada como tal: "18 fotos del auto · 5 fotos de los detalles". Cada foto de defecto lleva su descripción escrita y su ubicación en el vehículo. Ninguna otra compraventa del mercado chileno hace esto.

**Filtros del catálogo.** En escritorio, columna lateral fija. En móvil, hoja inferior con `<dialog>` nativo, nunca un desplegable absoluto dentro de un contenedor con desbordamiento oculto. Cada filtro aplicado se muestra como pastilla removible sobre los resultados. El estado vive en la URL para que los filtros se puedan compartir y Google los pueda indexar.

**Barra de WhatsApp.** Sin globo flotante genérico en la esquina. En la ficha, la acción vive en la barra fija y el mensaje llega pre-llenado con el auto: marca, modelo, año y enlace.

## Motion

Contenida y funcional. La marca es franca, no juguetona.

- Curva única `cubic-bezier(0.22, 1, 0.36, 1)` (ease-out-quint). Sin rebote, sin elástico.
- Duraciones: 150 ms en estados de control, 200-300 ms en cambios de vista, 420-520 ms en entradas.

**Las entradas se escriben con `animation`, nunca con `transition`, y solo definen el fotograma inicial.** El reposo es el estado natural del elemento, así que si la animación no llega a correr (pestaña oculta, render sin compositor, motor sin soporte) el contenido igual se ve. Ningún elemento parte con `opacity: 0` en su regla base.

**No hay revelados al hacer scroll.** Una misma entrada repetida en cada sección es el tic que delata una plantilla. La entrada se reserva para dos lugares donde el movimiento carga información:

- **El primer pliegue de la portada:** título, bajada, botones y nota entran escalonados a 70 ms. Es la única coreografía de carga del sitio.
- **Las grillas de vehículos:** escalonado de 55 ms por card mediante `--i`. En este catálogo filtrar recarga la página, así que el orden de entrada es la respuesta visible al filtro aplicado.

Lo demás es movimiento de interacción, que responde a algo que hizo la persona:

| Elemento | Qué hace |
|---|---|
| Cards de vehículo | Se elevan 4 px y la foto escala a 1.04 |
| Botones | Se elevan al pasar el cursor, se hunden a 0.98 al presionar (acuse táctil en móvil, donde no hay hover) |
| Navegación | Subrayado que crece desde la izquierda |
| Encabezado | Fijo arriba, con sombra que aparece al desplazarse |
| Hoja de inspección | Las filas entran escalonadas a 35 ms al cambiar de categoría |
| Galería | Cruce de opacidad de 300 ms entre fotos; miniaturas escalan a 1.05 |
| Preguntas frecuentes | La respuesta entra al abrir, disparada por el cambio de `display` |
| Cotizador | Barra de avance con transición de ancho de 300 ms |

**`prefers-reduced-motion: reduce` anula duración y retardo.** El retardo importa tanto como la duración: sin anularlo, un escalonado de 300 ms deja el contenido en su fotograma inicial todo ese tiempo.

## Imagery

La fotografía de los vehículos es el contenido principal del sitio y viene del cliente. Requisitos del sistema:

- Proporción 4:3 uniforme en el catálogo, en tres tamaños: `thumb` 400×300, `card` 600×450 y `full` 1600×1200. Las conversiones se generan en WebP, que cubre prácticamente todo el parque de navegadores y no depende de que el servidor traiga soporte AVIF compilado. Migrar a AVIF más adelante es cambiar una línea por conversión.
- Video de recorrido obligatorio en cada ficha: es lo que reemplaza la visita al local.
- Las fotos de defectos son de cerca y sin retoque. Una foto de defecto retocada destruye la premisa del sitio.
- Texto alternativo descriptivo y específico. "Raya de 8 cm en la puerta trasera derecha" gana a "detalle del vehículo": sirve al lector de pantalla y a Google por igual.
- Nunca un bloque de color donde debería ir una foto. Un vehículo sin fotos no se publica.
