=== Adsterra Editor ===
Contributors: NeoPunto
Tags: ads, advertisement, adsterra, banners, popunder, monetization
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin oficial para gestionar anuncios de Adsterra: banners, popunders, direct links y notificaciones.

== Description ==

Adsterra Editor es la solución fácil para integrar anuncios de Adsterra en tu sitio WordPress. Creado por NeoPunto.

Permite agregar scripts de anuncios de manera sencilla sin tocar el código del tema.

**Funcionalidades:**
*   **Direct Smart Link (NUEVO):** Configura un enlace directo que abre una nueva pestaña al hacer clic en cualquier parte del sitio. Configurable (1 o 2 veces al día) y restringible solo al Blog (sin WooCommerce).
*   **Carga Condicional (NUEVO):** Elige dónde cargar los scripts globales (Popunder): "Todo el sitio" o "Solo Blog". También puedes excluir páginas por ID. Ideal para evitar anuncios en WooCommerce o páginas de venta.
*   **Estadísticas API (NUEVO):** Conecta tu Token API de Adsterra y ve tus ganancias e impresiones directamente en el Escritorio de WordPress.
*   **Inserción Automática (NUEVO):** Coloca anuncios automáticamente al inicio, final o en medio (pausa) de tus entradas. También en la página de inicio del blog.
*   **Scripts Globales:** Agrega scripts de Popunders, Push Notifications o Direct Links que se ejecutan en todo el sitio (inyectados en el footer).
*   **Zonas de Banners:** Gestiona hasta 5 zonas de anuncios diferentes.
*   **Shortcodes:** Inserta anuncios en posts o páginas usando `[adsterra_ad id="1"]`.
*   **Widget:** Widget personalizado para colocar tus banners en las barras laterales (sidebars) o footers.

Este plugin es ideal para monetizar tu blog con Adsterra de manera legal y organizada.

== Installation ==

1. Sube la carpeta `adsterra-editor` al directorio `/wp-content/plugins/`.
2. Activa el plugin desde el menú 'Plugins' en WordPress.
3. Ve a 'Ajustes' -> 'Adsterra Editor'.
4. Ingresa tu Token API de Adsterra para habilitar el widget de estadísticas en el escritorio.
5. Pega tus códigos de anuncios de Adsterra en los campos correspondientes.
6. Configura la opción "Load Scripts On" en Global Settings si quieres restringir los Popunders solo al Blog (evitando tienda/checkout).
7. Configura el "Direct Smart Link" si deseas monetizar clics en el cuerpo de la página.
8. Configura la "Inserción Automática" si deseas que los anuncios aparezcan sin usar widgets.

== Frequently Asked Questions ==

= ¿Dónde consigo los códigos de anuncios? =
Debes registrarte en Adsterra.com y generar los códigos para tu sitio web.

= ¿Cómo uso el shortcode? =
Simplemente escribe `[adsterra_ad id="1"]` donde "1" es el número de la zona de anuncio que configuraste en los ajustes.

= ¿Cómo funciona la Inserción Automática? =
En los ajustes, selecciona qué zona de anuncio quieres mostrar al inicio o final de tus entradas. El plugin lo insertará automáticamente en el contenido.

= ¿Es seguro este plugin? =
Sí, el plugin utiliza las funciones estándar de WordPress para guardar y mostrar los anuncios. Recuerda que solo los administradores deben tener acceso a la configuración de anuncios.

== Changelog ==

= 1.4.0 =
* Migración a Adsterra Editor.
* Reemplazo de API de HilltopAds por API de Adsterra.
* Actualización de shortcodes y widgets.

= 1.3.0 =
* Nueva función: Carga Condicional para scripts globales (Solo Blog vs Todo el Sitio).
* Nueva función: Exclusión de IDs específicos para scripts globales.
* Traducción: Añadido soporte i18n y archivos de idioma español (.po/.pot).

= 1.2.0 =
* Integración API: Widget de escritorio con estadísticas.

= 1.1.0 =
* Agregada función de Inserción Automática en entradas (Inicio, Final, Medio) y home.

= 1.0.0 =
* Lanzamiento inicial.
