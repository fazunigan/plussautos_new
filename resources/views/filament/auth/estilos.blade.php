{{--
    Estilo de la pantalla de acceso. Va por CSS sobre las clases del layout
    simple de Filament en vez de reemplazar la vista: así la pantalla mantiene
    su comportamiento (validación, límite de intentos, accesibilidad) y no se
    rompe cuando Filament se actualiza.
--}}
<style>
    .fi-simple-layout {
        position: relative;
        background:
            radial-gradient(120% 90% at 15% 0%, oklch(0.52 0.16 259.2) 0%, transparent 55%),
            radial-gradient(110% 80% at 100% 100%, oklch(0.30 0.12 259.2) 0%, transparent 60%),
            linear-gradient(160deg, oklch(0.437 0.171 259.2) 0%, oklch(0.245 0.098 259.2) 100%);
        isolation: isolate;
    }

    /* Malla diagonal tenue, la misma del marcador de fotos del sitio. */
    .fi-simple-layout::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: -1;
        opacity: 0.06;
        background-image: linear-gradient(
            135deg,
            #fff 25%, transparent 25%, transparent 50%,
            #fff 50%, #fff 75%, transparent 75%, transparent
        );
        background-size: 18px 18px;
    }

    .fi-simple-layout-header img {
        height: 2.75rem;
        width: auto;
    }

    .fi-simple-main {
        border: 1px solid oklch(1 0 0 / 0.14);
        border-radius: 16px;
        box-shadow: 0 24px 60px oklch(0.15 0.06 259.2 / 0.45);
    }

    /* El encabezado de la tarjeta, en la voz del sitio. */
    .fi-simple-main .fi-header-heading {
        letter-spacing: -0.02em;
    }

    @media (prefers-reduced-motion: no-preference) {
        .fi-simple-main {
            animation: acceso-entra 480ms cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .fi-simple-layout-header {
            animation: acceso-entra 480ms cubic-bezier(0.22, 1, 0.36, 1) both;
        }
    }

    @keyframes acceso-entra {
        from {
            opacity: 0;
            transform: translateY(12px);
        }
    }
</style>
