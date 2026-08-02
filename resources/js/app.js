import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);

/**
 * Cotizador por pasos.
 *
 * Los campos no llevan el atributo `required` de HTML a propósito: los pasos
 * inactivos están ocultos, y el navegador no puede enfocar un campo invisible
 * para mostrar su mensaje, así que el envío quedaría bloqueado sin explicación.
 * La validación real vive en el servidor; esto solo evita que alguien avance
 * con el paso a medias.
 */
Alpine.data('cotizador', (pasoInicial = 1) => ({
    paso: pasoInicial,
    total: 3,
    error: '',

    faltantes() {
        const actual = this.$refs['paso' + this.paso];

        if (! actual) {
            return [];
        }

        const vistos = new Set();

        return [...actual.querySelectorAll('[data-req]')].filter((campo) => {
            if (campo.type === 'radio') {
                if (vistos.has(campo.name)) {
                    return false;
                }
                vistos.add(campo.name);

                return ! actual.querySelector(`input[name="${campo.name}"]:checked`);
            }

            return campo.value.trim() === '';
        });
    },

    siguiente() {
        const faltan = this.faltantes();

        if (faltan.length > 0) {
            this.error = faltan.length === 1
                ? 'Falta un dato para continuar.'
                : `Faltan ${faltan.length} datos para continuar.`;
            faltan[0].focus();

            return;
        }

        this.error = '';
        this.paso = Math.min(this.paso + 1, this.total);
        this.alTope();
    },

    anterior() {
        this.error = '';
        this.paso = Math.max(this.paso - 1, 1);
        this.alTope();
    },

    alTope() {
        const suave = ! window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        this.$refs.tope?.scrollIntoView({
            behavior: suave ? 'smooth' : 'auto',
            block: 'nearest',
        });
    },
}));

/**
 * Navegador de la hoja de inspección de la portada. Las filas se re-crean al
 * cambiar de categoría (la clave del x-for incluye la categoría), de modo que
 * la animación de entrada se repite y el cambio de filtro se percibe.
 */
Alpine.data('hojaInspeccion', (filas = []) => ({
    filas,
    cat: 'todas',

    get categorias() {
        const vistas = new Map();

        this.filas.forEach((f) => {
            if (! vistas.has(f.cat)) {
                vistas.set(f.cat, { valor: f.cat, etiqueta: f.catLabel, orden: f.catOrder });
            }
        });

        return [
            { valor: 'todas', etiqueta: 'Todas', orden: 0 },
            ...[...vistas.values()].sort((a, b) => a.orden - b.orden),
        ];
    },

    get visibles() {
        return this.cat === 'todas'
            ? this.filas
            : this.filas.filter((f) => f.cat === this.cat);
    },

    elegir(valor) {
        this.cat = valor;
    },
}));

/**
 * Cifra que cuenta hacia arriba al entrar en pantalla.
 *
 * Parte mostrando el valor final, no cero: si el observador nunca dispara
 * (motor sin soporte, render sin scroll, JavaScript caído) la cifra correcta
 * ya está a la vista. La animación solo la lleva a cero un instante antes
 * de empezar.
 */
Alpine.data('contador', (valor = 0, duracion = 1100) => ({
    mostrado: valor,
    corrido: false,

    init() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const observador = new IntersectionObserver(([entrada]) => {
            if (! entrada.isIntersecting || this.corrido) {
                return;
            }

            this.corrido = true;
            observador.disconnect();
            this.animar(valor, duracion);
        }, { threshold: 0.4 });

        observador.observe(this.$el);
    },

    animar(destino, duracion) {
        let inicio = null;
        let terminado = false;

        const finalizar = () => {
            if (terminado) {
                return;
            }

            terminado = true;
            this.mostrado = destino;
        };

        // Red de seguridad: si requestAnimationFrame se detiene a medio camino
        // (pestaña en segundo plano, ahorro de energía, render sin compositor),
        // la cifra llega igual a su valor final en vez de quedarse congelada.
        const red = setTimeout(finalizar, duracion + 500);

        const paso = (ahora) => {
            if (terminado) {
                return;
            }

            // La cifra recién baja a cero cuando la animación arranca de
            // verdad. Si el primer fotograma nunca llega, nunca se mueve del
            // valor correcto.
            if (inicio === null) {
                inicio = ahora;
                this.mostrado = 0;
            }

            const avance = Math.min(Math.max((ahora - inicio) / duracion, 0), 1);
            // ease-out-quint, la misma curva que el resto del sitio
            this.mostrado = Math.round(destino * (1 - Math.pow(1 - avance, 5)));

            if (avance < 1) {
                requestAnimationFrame(paso);

                return;
            }

            clearTimeout(red);
            finalizar();
        };

        requestAnimationFrame(paso);
    },
}));

/** Rueda de categorías de la pauta de inspección. */
Alpine.data('ruedaPauta', (categorias = []) => ({
    categorias,
    activa: categorias.length ? categorias[0].valor : null,

    get seleccionada() {
        return this.categorias.find((c) => c.valor === this.activa) ?? null;
    },

    elegir(valor) {
        this.activa = valor;
    },

    esActiva(valor) {
        return this.activa === valor;
    },
}));

window.Alpine = Alpine;

Alpine.start();
