# Pluss Autos — Diseño del sitio

Fecha: 2026-08-01
Estado: aprobado por el usuario

## 1. Contexto

Pluss Autos es una compraventa de autos usados en Chile **sin punto físico de ventas**. Opera en modelo mixto: parte del stock es propio y parte en consignación.

El sitio no es un folleto institucional. Reemplaza al local: todo lo que un comprador obtendría caminando por un patio de autos tiene que existir en la ficha del vehículo.

### Referencias analizadas

| Sitio | Eje | Qué tomamos | Qué evitamos |
|---|---|---|---|
| auto360.cl | Compra directa con pago inmediato, taller propio | Estructura de doble acción comprar/vender, FAQ, proceso en pasos | Peso corporativo, exceso de secciones |
| juanignaciowalker.cl | Tasación online como héroe, marca personal | Formulario de venta arriba del pliegue, prueba social con cifras | Catálogo sin filtros reales |
| automotriztuauto.cl | Showroom físico, servicios | Datos consistentes en cada card, WhatsApp pre-llenado por auto | Estética de plantilla Elementor |

Las tres apoyan la confianza en algo físico (sucursal, taller, showroom). Pluss Autos no lo tiene, y ese es el problema central que el diseño resuelve.

## 2. Decisiones tomadas

| Decisión | Elección |
|---|---|
| Modelo de negocio | Mixto: stock propio + consignación. El público ve un catálogo uniforme; la administración distingue origen, consignante, comisión y ubicación |
| Alcance v1 | Catálogo, ficha, vende tu auto / tasación, reserva |
| Mecánica de reserva | Botón de WhatsApp con el auto pre-cargado; el estado se marca a mano en el panel. Sin modelo de reservas ni pago en línea |
| Hosting | Laravel Cloud |
| Arquitectura | Blade + Tailwind + Alpine en el público, Filament v4 en `/admin` |
| Entorno local | Laravel Sail sobre Docker. Nada se instala en la máquina del usuario |
| Personalidad de marca | Transparencia radical |
| Anti-referencias | Concesionario corporativo genérico, plantilla WordPress/Elementor, clasificado tipo Yapo/Chileautos, agresivo tipo liquidación |

Marca y sistema visual completos en [PRODUCT.md](../../../PRODUCT.md) y [DESIGN.md](../../../DESIGN.md).

## 3. Arquitectura

Laravel 12 monolítico, renderizado en el servidor.

- **Público:** controladores + vistas Blade, Tailwind v4, Alpine para interacción. Server-rendered porque el tráfico principal llega desde búsquedas de Google del tipo "mazda cx-5 2019 usado", y una compraventa sin local depende de ese tráfico más que una con vitrina a la calle.
- **Admin:** Filament v4 en `/admin`, protegido por autenticación.
- **Base de datos:** PostgreSQL 16, igual en local (Sail) y en producción (Laravel Cloud).
- **Medios:** `spatie/laravel-medialibrary`. Disco local en desarrollo, S3 compatible en producción.
- **Colas:** Redis. Las conversiones de imagen se procesan en cola porque un auto son 20 a 40 fotos y no puede bloquear la publicación.

Se descartó Livewire con panel propio (semanas extra de desarrollo y mantención para un beneficio solo estético en una pantalla interna) e Inertia + React (exige SSR para no perder SEO, complejidad sin retorno en un catálogo).

## 4. Modelo de datos

### `brands`
`id`, `name`, `slug` (único), timestamps.

### `vehicle_models`
`id`, `brand_id` (FK), `name`, `slug`, timestamps. Único por `(brand_id, slug)`.

Marcas y modelos van normalizados, no como texto libre. Con texto libre los filtros se rompen a la tercera publicación: "Mazda", "MAZDA" y "mazda " pasan a ser tres marcas distintas.

### `vehicles`

Campos públicos:

`id`, `slug` (único), `brand_id`, `vehicle_model_id`, `version`, `year`, `price` (entero, CLP), `mileage_km`, `transmission` (manual | automatica | cvt), `fuel` (bencina | diesel | hibrido | electrico | gas), `body_type` (sedan | suv | hatchback | camioneta | station_wagon | coupe | van), `engine_cc`, `doors`, `traction` (4x2 | 4x4 | awd), `color`, `owners_count`, `description`, `video_url`, `status` (draft | available | reserved | sold), `published_at`, `sold_at`, `featured`.

Campos internos, nunca expuestos en el sitio público:

`origin` (own | consignment), `plate`, `consignor_name`, `consignor_phone`, `purchase_price`, `commission_amount`, `location`, `internal_notes`.

Timestamps y borrado lógico. Índices sobre `(status, published_at)`, `brand_id`, `vehicle_model_id`, `year`, `price`, `mileage_km`.

**`days_in_stock`** es un atributo calculado desde `published_at` hasta `sold_at` o hasta hoy. No se persiste. Es la métrica por la que se maneja una compraventa y va en el panel, no en el sitio público.

### `inspection_items`
`id`, `vehicle_id` (FK, borrado en cascada), `category` (motor | transmision | frenos | suspension | carroceria | interior | neumaticos | documentacion), `label`, `status` (ok | observacion | reparado), `note`, `sort_order`, timestamps.

Cada ítem puede llevar **una foto** en una colección de medios de archivo único llamada `evidence`.

Decisión clave: **la galería de detalles se deriva de la hoja de inspección**, no es una carga aparte. Los ítems con estado distinto de `ok` y con foto son la galería de defectos. Una sola fuente de verdad y un solo formulario que llenar. El contador "N detalles documentados" del catálogo cuenta los ítems con estado distinto de `ok`.

### Medios del vehículo
Colección `gallery`, múltiple y ordenable. Conversiones: `thumb` 400×300, `card` 600×450, `full` 1600×1200, generadas en WebP. Proporción 4:3 uniforme.

### `leads`
`id`, `type` (consulta | tasacion), `vehicle_id` (nulable), `name`, `phone`, `email`, `message`, campos de tasación (`t_brand`, `t_model`, `t_year`, `t_mileage_km`, `t_plate`), `status` (nuevo | contactado | en_negociacion | cerrado | descartado), `source`, `internal_notes`, timestamps.

Una sola tabla para consultas y tasaciones: comparten ciclo de vida y se gestionan en la misma bandeja.

### `site_settings`
Fila única editable desde el panel: número de WhatsApp, teléfono, correo, Instagram, Facebook, textos de la sección "nosotros". Van en base de datos y no en `.env` para que se puedan cambiar sin desplegar.

### `users`
Tabla estándar de Laravel más `is_admin` (booleano). El acceso a `/admin` lo controla el contrato `FilamentUser` verificando esa bandera. Sin sistema de roles en v1: el negocio lo opera una persona.

## 5. Páginas públicas

### `/` Inicio
1. Héroe con doble acción: "Ver autos disponibles" y "Vender mi auto". Fondo olivo, foto de vehículo.
2. Últimos ingresos: 6 vehículos.
3. **Cómo revisamos cada auto:** el proceso de inspección en pasos. Es el bloque de confianza y reemplaza a la sección "nuestras sucursales" que tienen las referencias.
4. **Te lo llevamos:** cómo funciona comprar sin local.
5. Banda de "Vende tu auto".
6. Preguntas frecuentes.

### `/autos` Catálogo
Filtros: marca, modelo, año (rango), precio (rango), kilometraje (rango), transmisión, combustible, carrocería. Orden: recientes, precio ascendente, precio descendente, menor kilometraje.

El estado de los filtros vive en la URL como parámetros de consulta, de modo que los resultados se puedan compartir por WhatsApp y Google los pueda indexar. Paginación clásica, no scroll infinito, por la misma razón.

En escritorio los filtros van en columna lateral fija. En móvil, en hoja inferior usando `<dialog>` nativo, nunca un desplegable absoluto dentro de un contenedor con desbordamiento oculto. Los filtros aplicados aparecen como pastillas removibles sobre los resultados.

Solo se listan vehículos con estado `available` o `reserved` y `published_at` no nulo.

### `/autos/{slug}` Ficha
El componente central del sitio. Orden:

1. Galería de fotos y video de recorrido.
2. Marca, modelo, versión, año. Precio. Insignia de estado si corresponde.
3. Especificaciones en tabla, cifras en tipografía monoespaciada.
4. **Hoja de inspección** por categoría, con estado por punto revisado.
5. **Detalles documentados:** cada defecto con su foto de cerca, su descripción y su ubicación en el vehículo.
6. Documentación: número de dueños, papeles al día.
7. Autos similares.
8. Barra fija al pie en móvil: precio a la izquierda, WhatsApp a la derecha.

El mensaje de WhatsApp llega pre-llenado con marca, modelo, año y enlace a la ficha.

Un vehículo en estado `draft` o `sold` no es accesible públicamente y responde 404. Un vehículo `sold` mantiene su URL viva con la insignia "Vendido" y sin acción de contacto: son páginas que ya tienen posicionamiento en Google y sirven de prueba social.

### `/vende-tu-auto`
Formulario: nombre, teléfono, marca, modelo, año, kilometraje, patente opcional. Genera un lead de tipo `tasacion`.

### `/nosotros`
El proceso de inspección en detalle, quiénes están detrás, y por qué operar sin local es una ventaja para el comprador.

### `/contacto`
Formulario, WhatsApp, correo, redes.

### Legales
`/terminos` y `/privacidad`.

## 6. Panel de administración

Filament v4 en `/admin`.

**Vehículos.** Formulario por secciones: identificación, especificaciones, precio y estado, fotos, hoja de inspección (repetidor con foto por ítem), datos internos. La tabla muestra foto, modelo, año, precio, estado, origen y **días en stock**, con filtros por estado y origen. La carga de fotos debe funcionar desde el celular: el administrador publica parado al lado del auto.

**Leads.** Bandeja única de consultas y tasaciones, con estado, notas internas y enlace al vehículo consultado.

**Marcas y modelos.** Mantenedor simple.

**Configuración del sitio.** Página de la fila única de `site_settings`.

**Inicio del panel.** Autos publicados, días en stock promedio, leads del mes, autos vendidos en el mes.

## 7. SEO

Es la principal fuente de clientes y va desde el primer día, no como fase posterior.

- URLs del tipo `/autos/mazda-cx-5-gt-awd-2019-142`.
- JSON-LD de tipo `Vehicle` con `Offer` en cada ficha.
- Título y descripción propios por vehículo.
- Open Graph con la primera foto de la galería, para que el enlace se vea bien al compartirlo por WhatsApp.
- `sitemap.xml` generado automáticamente y `robots.txt`.
- Texto alternativo descriptivo en cada foto, incluidas las de defectos.

## 8. Entorno y despliegue

**Local:** Laravel Sail. `docker-compose.yml` con PHP 8.4, PostgreSQL 16, Redis y Mailpit. No se instala PHP, Composer ni Postgres en la máquina del usuario. Los comandos de Composer y Artisan corren dentro del contenedor.

**Producción:** Laravel Cloud. PostgreSQL gestionado, almacenamiento de objetos para los medios, despliegue desde git.

**Paquetes:** `filament/filament` ^4, `spatie/laravel-medialibrary` ^11, `spatie/laravel-sitemap`. En el frontend, Tailwind v4, Alpine y las tipografías Archivo y Geist Mono autoalojadas vía npm.

## 9. Pruebas

Pruebas de funcionalidad sobre lo que puede romper el negocio:

- El catálogo filtra y ordena correctamente, y cada filtro se refleja en la URL.
- Un vehículo en `draft` responde 404; uno en `sold` responde 200 sin acción de contacto.
- El formulario de tasación crea el lead con los campos correctos y valida el teléfono.
- El formulario de consulta desde una ficha asocia el lead a ese vehículo.
- `/admin` exige autenticación y rechaza a un usuario sin `is_admin`.
- Los campos internos del vehículo nunca aparecen en la respuesta pública.

Esta última es la que más importa: una filtración del precio de compra o de los datos del consignante es un problema comercial serio.

## 10. Fuera de alcance en v1

Simulador de financiamiento, blog, reservas con pago en línea (Webpay), modo oscuro, sistema de roles, y sitio en más de un idioma.

La reserva con pago y el simulador se pueden agregar después sin rehacer el modelo de datos.

## 11. Criterios de éxito

1. Un comprador que llega desde Google a una ficha entiende el estado real del auto sin hablar con nadie.
2. El administrador publica un vehículo completo, con fotos y hoja de inspección, desde el celular.
3. Las fichas cargan rápido en conexión móvil chilena con 20 o más fotos.
4. Ningún dato interno aparece en el sitio público.
5. El sitio no se parece a ninguna de las cuatro anti-referencias.
