# Pluss Autos

Compraventa de autos usados sin punto físico de ventas. El sitio no es un folleto de la empresa: reemplaza al local. Todo lo que un comprador obtendría caminando por un patio de autos existe en la ficha del vehículo, incluidos los defectos.

- Estrategia y marca: [PRODUCT.md](PRODUCT.md)
- Sistema visual: [DESIGN.md](DESIGN.md)
- Diseño funcional: [docs/superpowers/specs/2026-08-01-plussautos-design.md](docs/superpowers/specs/2026-08-01-plussautos-design.md)

## Stack

Laravel 12, PHP 8.3, PostgreSQL 18, Redis, Blade con Tailwind 4 y Alpine en el público, Filament 4 en el panel. Todo corre en Docker con Laravel Sail: no se instala nada en la máquina.

### La versión de PHP está fijada en tres lugares y deben coincidir

```json
// composer.json
"config": { "platform": { "php": "8.3.30" } }
```

```yaml
# compose.yaml
context: './vendor/laravel/sail/runtimes/8.3'
image: 'sail-8.3/app'
```

Y el servidor de Forge, que hoy corre 8.3.30.

`platform.php` es el que importa para el despliegue: obliga a Composer a resolver el `composer.lock` contra esa versión **sin importar qué PHP corra el contenedor donde se ejecute el comando**. Sin él, instalar un paquete desde un contenedor con PHP 8.4 deja en el lock dependencias que el servidor con 8.3 no puede instalar, y el despliegue falla con "Your lock file does not contain a compatible set of packages".

**Para subir la versión de PHP:** primero cámbiala en Forge, después ajusta los tres lugares de arriba, corre `composer update` y reconstruye el contenedor. En ese orden.

## Levantar el entorno

```bash
export WWWUSER=$(id -u) WWWGROUP=$(id -g)   # zsh y bash
docker compose up -d
```

Los comandos se ejecutan dentro del contenedor:

```bash
docker compose exec laravel.test php artisan migrate --seed
docker compose exec laravel.test npm run build
docker compose exec laravel.test php artisan test
```

Para desarrollo con recarga en caliente:

```bash
docker compose exec laravel.test npm run dev
```

### Puertos

Elegidos para no chocar con otros proyectos que ya corren en esta máquina. Están en el `.env` y se pueden cambiar.

| Servicio | URL |
|---|---|
| Sitio | http://localhost:8091 |
| Panel | http://localhost:8091/admin |
| Mailpit | http://localhost:8025 |
| PostgreSQL | localhost:5433 |
| Redis | localhost:6380 |

### Acceso al panel

El seeder crea el usuario `admin@plussautos.cl` con contraseña `password`. **Cámbiala antes de desplegar.**

## Estructura

| Ruta | Qué es |
|---|---|
| `app/Enums` | Transmisión, combustible, carrocería, estados. Con etiquetas en español e integración con Filament |
| `app/Models/Vehicle.php` | El modelo central. `$hidden` protege los campos internos de la serialización |
| `app/Support/VehicleFilter.php` | Traduce los parámetros de la URL del catálogo a una consulta |
| `app/Support/InspectionChecklist.php` | La pauta estándar de revisión, 33 puntos en 8 categorías |
| `app/Support/VehicleSchema.php` | Datos estructurados schema.org de la ficha |
| `app/Filament` | Panel: vehículos, hoja de inspección, leads, marcas, modelos, configuración |
| `resources/views/components/quote-form.blade.php` | Cotizador en tres pasos, usado en la portada y en `/vende-tu-auto` |
| `resources/views/components/photo-placeholder.blade.php` | Marcador con la marca para autos sin fotos |
| `resources/views/components/form/` y `btn.blade.php` | Campos y botones reutilizables |

## Decisiones que conviene no revertir sin pensarlo

**La galería de defectos se deriva de la hoja de inspección.** Los ítems con estado distinto de "conforme" y con foto *son* la galería. Una sola fuente de verdad y un solo formulario que llenar en el panel.

**Marcas y modelos van normalizados**, no como texto libre. Con texto libre los filtros se rompen a la tercera publicación: "Mazda", "MAZDA" y "mazda " pasan a ser tres marcas.

**Los vehículos vendidos mantienen su URL viva** con la insignia "Vendido" y sin acción de contacto. Esas páginas ya tienen posicionamiento en Google y sirven de prueba social.

**El estado de los filtros vive en la URL**, y la paginación es clásica en vez de scroll infinito. Ambas cosas son para que Google indexe el catálogo, que es la principal fuente de clientes de un negocio sin local a la calle.

**Los campos internos** (precio de compra, consignante, comisión, ubicación, patente, notas) están en `$hidden` y hay tests que verifican que no aparezcan en ninguna respuesta pública.

**El sitio no menciona que no haya un punto de venta al público.** Ofrecer la prueba a domicilio es un servicio y así se presenta; anunciar la carencia la instala como tema. `SiteContentTest` falla si alguna página vuelve a mencionarlo. La dirección y el horario de la página de contacto salen de `site_settings` y solo aparecen cuando están cargados.

**El cotizador no calcula un precio.** Captura los datos y tú respondes con la oferta. Mostrar un rango automático exigiría mantener precios base por modelo y año, y un número alto deja al cliente anclado a una cifra que después no se le va a pagar.

**Los campos del cotizador no llevan `required` de HTML.** Los pasos inactivos están ocultos y el navegador no puede enfocar un campo invisible para mostrar su error, así que el envío quedaría bloqueado sin explicación. La validación vive en el servidor y Alpine solo evita avanzar con el paso a medias.

## Pendientes antes de producción

- Reemplazar los datos de contacto de ejemplo desde el panel, en Configuración.
- Cargar fotos reales: los vehículos sembrados no traen imágenes.
- Traducir los mensajes de validación al español (`lang/es`); Laravel 12 no los trae de fábrica.
- Revisar los textos legales de `/terminos` y `/privacidad` con alguien del área.
- Cambiar la contraseña del usuario administrador.
