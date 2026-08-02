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

window.Alpine = Alpine;

Alpine.start();
