# Product

## Register

brand

## Users

**Compradores (80% del tráfico, mayoría en celular).** Chilenos entre 28 y 55 años que buscan un auto usado de entre $6 y $25 millones. Llegan desde Google buscando un modelo y año específicos ("mazda cx-5 2019 usado"), o desde Instagram. Están asustados: el mercado de usados chileno tiene fama de estafa, gato por liebre y kilometraje adulterado. Comparan en Chileautos y Yapo, donde ven avisos sin curatoría, y en concesionarias, donde el precio es más alto. Su trabajo real no es "encontrar un auto": es **encontrar un auto en el que puedan confiar sin ser mecánicos**.

**Vendedores (20%, el motor del inventario).** Dueños que necesitan vender rápido y sin exponerse a desconocidos en su casa. Su trabajo es **salir del auto sin perder plata ni tiempo**.

**El administrador (una persona: el dueño del negocio).** Publica autos desde el celular, muchas veces parado al lado del auto recién comprado. Necesita cargar 30 fotos, marcar defectos y publicar en minutos, no en una sesión de escritorio.

## Product Purpose

Pluss Autos es una compraventa de autos usados que opera sin punto de venta al público. El sitio no es un folleto de la empresa: hace el trabajo que en otras compraventas hace el local. Todo lo que un comprador obtendría recorriendo un patio de autos (ver el estado real, patear las ruedas, preguntarle al vendedor, revisar los papeles) tiene que existir en la ficha del vehículo.

Ese contexto es interno y explica las decisiones de producto. **En el sitio no se menciona**: ver el principio 5.

Opera en modelo mixto: parte del stock es propio y parte en consignación. El público ve un catálogo uniforme; la administración distingue origen, consignante, comisión y ubicación física del auto.

Éxito significa: un comprador que llega desde Google a una ficha, entiende el estado real del auto sin hablar con nadie, y escribe por WhatsApp ya decidido a verlo.

## Brand Personality

**Franco, meticuloso, mecánico.**

La personalidad es la del vendedor que te muestra la raya del parachoques antes de que la encuentres tú. No es simpatía comercial ni entusiasmo de folleto: es la seguridad tranquila de quien revisó el auto en serio y no tiene nada que esconder.

Voz: directa, específica, sin adjetivos de venta. "Tiene una raya de 8 cm en la puerta trasera derecha, foto 14" en vez de "excelente estado". Los números mandan sobre las promesas. Nunca signos de exclamación, nunca urgencia fabricada.

La emoción objetivo no es entusiasmo: es **alivio**. El comprador debe sentir que por fin alguien le está hablando derecho.

## Anti-references

- **Concesionario corporativo genérico.** Fotos de archivo de gente dándose la mano, lenguaje de folleto, secciones que podrían ser de cualquier rubro. Se ve serio y es invisible.

  La marca ya está comprometida con el azul `#004AAD` del logo, que es el mismo territorio cromático de las tres referencias del rubro. La identidad existente manda, así que la diferenciación tiene que venir de otra parte: del contenido de la ficha, de la hoja de inspección publicada y del tono del texto. Un azul de marca no salva a un sitio genérico, y tampoco lo condena.
- **Plantilla WordPress / Elementor.** Secciones prefabricadas, íconos redondeados sobre cada título, sombras genéricas, grillas de cards idénticas. Es donde cae automotriztuauto.cl.
- **Clasificado tipo Yapo / Chileautos.** Grillas densas de avisos, fotos malas, cero curatoría. Es exactamente el problema que el sitio existe para resolver.
- **Agresivo tipo liquidación.** Rojo y amarillo, "¡OFERTA!", contadores de urgencia, precios tachados. La urgencia fabricada destruye lo único que estamos construyendo.

## Design Principles

1. **La ficha reemplaza al local.** Sin showroom, la ficha del vehículo es el producto. Todo lo demás del sitio existe para llevar tráfico ahí. Recibe el mejor diseño, el mejor rendimiento y la mayor densidad de información.

2. **Mostrar los defectos es la función, no un riesgo.** Cada auto publica sus fallas con foto y ubicación. La competencia esconde; nosotros catalogamos. Es el elemento más difícil de copiar porque exige trabajo real, no decisiones de diseño.

3. **Los datos ganan a los adjetivos.** "112.400 km" y "segundo dueño" comunican; "impecable" y "full equipo" no. Cuando haya que elegir entre un dato y una frase, va el dato.

4. **Diseñar para el celular parado en la calle.** El comprador mira la ficha en el paradero; el administrador publica el auto parado al lado del vehículo. Pantalla chica, conexión mala, una mano. Ese es el caso base, no la excepción.

5. **Llevamos el auto donde está el comprador.** El sitio ofrece la prueba a domicilio como un servicio y punto. **No menciona que no exista un punto de venta al público**, ni para justificarlo ni para presentarlo como ventaja: anunciar una carencia la instala como tema en la cabeza de quien compra. La página de contacto muestra dirección y horario solo cuando existen y están cargados en el panel; mientras no los haya, esas filas no aparecen. Hay una prueba automatizada que falla si alguna página vuelve a mencionarlo.

## Accessibility & Inclusion

- **WCAG 2.1 AA.** Texto de cuerpo ≥4.5:1, incluyendo los placeholders de los filtros y el texto de las especificaciones.
- **El estado nunca se comunica solo con color.** "Reservado" y "Vendido" llevan etiqueta de texto además del color: parte del público objetivo tiene daltonismo y es la banda etaria donde más prevalece.
- **`prefers-reduced-motion`** respetado en toda transición y en la galería.
- **Cada foto de vehículo con alt descriptivo**, y las fotos de defectos con alt que nombra el defecto y su ubicación. No es solo accesibilidad: es SEO en un sitio cuyo tráfico depende de Google.
- **Objetivos táctiles ≥44px** y filtros usables con una sola mano.
- Sitio en español de Chile. Precios en pesos chilenos con separador de miles de punto.
