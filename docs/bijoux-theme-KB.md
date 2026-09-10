# Knowledge Base técnico — Tema Bijoux (amagajoyerias.com)

_Generado: 2026-09-09. Fuente: exploración de solo lectura vía Hostinger MCP tools sobre
`wp-content/themes/bijoux` (versión 34) y plugins asociados. Complementa a `site_inventory.md`._

## 1. Resumen ejecutivo

**Bijoux** es un tema WordPress comercial de **VamTam** ("The Ultimate Niche WordPress Theme for
Jewelers", licencia Envato/ThemeForest, ID Envato `7322125`). No es un tema "vainilla": envuelve un
framework propio, el **VamTam Framework** (`wp-content/themes/bijoux/vamtam/`), que se auto-inicializa
desde `functions.php` y provee:

- Un sistema de opciones de tema heredado (`vamtam_theme` option en `wp_options`, con defaults en
  `samples/default-options.php`), que en instalaciones modernas se **migra automáticamente a los
  Global Settings de Elementor** (`VamtamElementorBridge::migrate_theme_options_to_elementor_global_settings`).
- Un **bridge con Elementor / Elementor Pro** (`vamtam/classes/elementor-bridge.php`, ~1480 líneas) que
  registra ubicaciones de Theme Builder, ajusta el `content_width` según el kit activo de Elementor, e
  inyecta compatibilidad de widgets.
- Una **librería de widgets custom de Elementor** distribuida como plugin aparte:
  `wp-content/plugins/vamtam-elementor-integration/` (obligatorio para que el diseño del tema
  funcione dentro del editor de Elementor).
- Overrides de plantillas de **WooCommerce** tanto en el tema (`bijoux/woocommerce/`) como en el
  plugin de integración (`vamtam-elementor-integration/includes/woocommerce/`).

En la práctica, **el diseño visual real de las páginas vive en Elementor** (contenido, layout,
colores, tipografías por sección se editan ahí), mientras que Bijoux aporta: el "chasis" PHP
(`header.php`, `footer.php`, `page.php`, `single.php`, etc.), el CSS base/reset, los widgets custom
que aparecen en el panel de Elementor, y algunos ajustes globales de framework. **No hay Customizer
de WordPress tradicional relevante** para estilos de marca — eso lo gestiona Elementor (Site
Settings / Global Colors / Global Fonts / kit activo).

Particularidad detectada: en `functions.php` hay código custom (no del framework VamTam) que
**deshabilita por completo la compra online** (catálogo puro):
```php
add_filter('woocommerce_is_purchasable', '__return_false');
add_filter('woocommerce_variation_is_purchasable', '__return_false');
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
function disable_cart() { wp_redirect( home_url() ); exit; }
add_action( 'woocommerce_before_cart', 'disable_cart');
add_action( 'woocommerce_before_checkout_form', 'disable_cart');
```
Es decir: **carrito y checkout están bloqueados a nivel de código del tema**, redirigiendo al home.
Esto es consistente con el plugin `yith-woocommerce-catalog-mode` también instalado (posible
redundancia/doble capa de "modo catálogo"). Cualquier agente que vaya a tocar flujo de compra debe
saber que esto es intencional y está hardcodeado aquí, no solo en configuración de un plugin.

## 2. Estructura de carpetas y archivos (resumen)

```
wp-content/themes/bijoux/
├── style.css                # Cabecera del tema (nombre/versión/licencia); sin CSS de marca relevante
├── functions.php            # Bootstrap: carga VamtamFramework + hacks custom (catálogo sin compra)
├── header.php / footer.php  # Wrappers globales de layout
├── page.php / single.php / index.php / archive.php / search.php / 404.php / attachment.php / author.php
│                             # Plantillas estándar de WP; delegan a Elementor Theme Builder cuando hay
│                             # ubicación registrada (elementor_theme_do_location('single') etc.)
├── sidebar.php / searchform.php / comments.php
├── loop.php
├── templates/                # Template parts propios del tema (get_template_part)
│   ├── header.php, header/ (top.php, top/main-menu.php, top/logo.php, top/logo-wrapper.php, sub-header.php, page-title.php)
│   ├── post.php, post/ (content.php, meta.php, meta/*, main/*, header.php, header-large.php)
│   ├── comment.php, comment-small.php
│   ├── cart-dropdown.php, side-buttons.php
├── woocommerce/               # OVERRIDES de plantillas WooCommerce (ver sección 5)
│   ├── cart/, checkout/, myaccount/, global/, loop/
├── vamtam/                    # === VamTam Framework (el "motor" del tema) ===
│   ├── classes/                # Núcleo PHP: framework.php, enqueues.php, elementor-bridge.php,
│   │                            # overrides.php, sidebars.php, templates.php, menu-walker.php,
│   │                            # less-bridge.php, color.php, video-player.php, class-tgm-plugin-activation.php
│   ├── helpers/                 # woocommerce-integration.php (32KB), frontend-wrappers.php, base.php,
│   │                            # icons.php, fonts.php (177KB — catálogo de Google Fonts), template.php,
│   │                            # the-events-calendar-integration.php, css.php, init.php
│   ├── admin/                   # Panel de administración del framework (metaboxes, helpers, classes:
│   │                            # admin.php, plugin-manager.php, diagnostics.php, purchase-helper.php,
│   │                            # migrations.php, config-generator.php, update-notice.php, help-page.php)
│   ├── options/                 # Muy reducido hoy (solo help/docs.php) — opciones legacy migradas a Elementor
│   ├── plugins/                  # ZIPs de los plugins bundleados (vamtam-elementor-integration.zip,
│   │                            # vamtam-importers-e.zip, pojo-accessibility.zip) — versiones "de fábrica"
│   │                            # que se instalan vía TGM Plugin Activation
│   └── assets/                  # CSS/JS/imágenes propios del framework (ver sección 9)
├── samples/                   # Contenido de importación de muestra (demo del tema): content.xml (4MB,
│                             # export WXR completo del demo), theme-mods.json, elementor-settings.json,
│                             # elementor-global-defaults.php, elementor-styles-fallback*.css,
│                             # woocommerce-settings.json, default-options.php (defaults de vamtam_theme),
│                             # dependencies.php (declara plugins requeridos/recomendados vía TGMPA)
├── tribe-events/               # Override mínimo para The Events Calendar (default-template.php)
├── languages/                  # bijoux.pot (i18n)
├── screenshot.jpg, readme.txt, wpml-config.xml
├── composer.json/.lock, package.json/-lock.json, Gruntfile.js, phpcs.xml, utils/grunt/
│                             # Tooling de build del tema (Grunt + LESS, ver sección 9)
```

Nota: `default.php` y `llms.txt` en la raíz de `public_html` (mencionados en `site_inventory.md`) no
son parte del árbol del tema — quedaron fuera de alcance de esta investigación.

## 3. Sistema de theme options / Customizer

- **No existe** un panel clásico de WordPress Customizer con secciones propias de Bijoux con
  impacto visual relevante hoy. El tema declara `add_theme_support('customize-selective-refresh-widgets')`
  pero eso es soporte genérico de widgets, no un panel de theme options.
- Existió (y persiste como capa de compatibilidad) un sistema de opciones **legacy VamTam**:
  - Opción única en BD: `vamtam_theme` (array serializado), con valores por defecto definidos en
    `samples/default-options.php` y cargados en `VamtamFramework::setup_options()`
    (`vamtam/classes/framework.php`).
  - `vamtam/classes/elementor-bridge.php` registra
    `VamtamElementorBridge::migrate_theme_options_to_elementor_global_settings` en el hook
    `elementor/init` — es decir, **al activar Elementor, las opciones legacy se migran a los Global
    Settings/kit de Elementor** (colores, fuente primaria, etc. vía
    `double_primary_font_global_option_check`, `add_global_styles_to_elementor_global_settings`).
  - También hay lógica de compatibilidad para el ancho de contenido: si Elementor Pro está activo,
    `content_width` se toma de `elementor_active_kit` → `_elementor_page_settings['container_width']`.
- **Conclusión práctica**: para cambiar colores globales, tipografías, layout de contenedor, etc.,
  el punto de edición correcto **hoy** es **Elementor → Site Settings (Global Colors / Global Fonts /
  Layout)** del kit activo, no un menú "Bijoux Theme Options" en wp-admin. El framework VamTam sigue
  vivo para: registro de CPTs/soporte de tema, widgets, helpers de WooCommerce, sidebars clásicos, y
  el propio bridge de migración — pero ya no es la fuente de verdad del diseño visual.
- `vamtam/admin/` contiene el panel de administración del framework (diagnósticos, gestor de
  plugins, licencia/compra Envato) — es infraestructura de soporte del tema, no un editor de diseño.

## 4. `functions.php` y hooks/funciones clave

`functions.php` (77 líneas) es deliberadamente delgado:
1. Define `VAMTAM_ENVATO_THEME_ID` y arranca `new VamtamFramework(['name'=>'bijoux','slug'=>'bijoux'])`
   desde `vamtam/classes/framework.php` — esto es lo que realmente registra todo.
2. Filtro `nav_menu_link_attributes` (`vamtam_onepage_menu_hrefs`) para soportar demos "one-page".
3. Filtro `vamtam_escaped_shortcodes` para arreglar HTML inválido de shortcodes de terceros
   (`gallery`, `fl_builder_insert_layout`, `wpforms`).
4. Compat de licencia Envato Hosted (`option_{purchase_code_key}` filtro).
5. Integración con `vamtam-importers-e` (fija ubicaciones de menú tras import de contenido demo).
6. **Bloque custom (no-VamTam) que deshabilita la compra en WooCommerce** — ver sección 1.

**En `vamtam/classes/framework.php`** (`VamtamFramework::theme_supports()`, colgado de
`after_setup_theme`):
- `add_theme_support('woocommerce', [...])` con tamaños de imagen calculados desde
  `content_width` / `woocommerce_catalog_columns`.
- Tamaños de imagen HiDPI: `vamtam_woocommerce_thumbnail_2x`, `vamtam_woocommerce_single_2x`.
- `post-thumbnails`, `automatic-feed-links`, `html5` (comment-list, comment-form, search-form,
  gallery, caption), `title-tag`, `custom-logo`, `align-wide`, `editor-styles`,
  `responsive-embeds`, `customize-selective-refresh-widgets`.
- Soportes custom del tema (namespace `vamtam-*`): `vamtam-ajax-siblings`, `vamtam-page-title-style`,
  `vamtam-tribe-events`, `vamtam-scroll-pinning`, `vamtam-expand-scroll`.
- `vamtam-elementor-widgets`: feature flags muy específicos que activan/desactivan variantes de
  comportamiento de los widgets custom de Elementor (lista larga en el código: p.ej.
  `slides--bijoux-slider-arrows`, `nav-menu--bijoux-menu-toggle`, `button--bijoux-button-type`,
  `products-base--bijoux-products-layout`, `woocommerce-menu-cart--bijoux-button-type`, etc.) — útil
  si un agente necesita saber por qué cierto widget se comporta de una forma "estilo Bijoux"
  específica en vez del default genérico del framework.
- `wc-product-gallery-slider`, `wc-product-gallery-zoom`, y condicionalmente
  `wc-product-gallery-lightbox`.
- `register_nav_menus(['primary-menu' => 'Menu Header'])` — **único menú registrado por el tema**.
- No se detectaron **Custom Post Types ni taxonomías propias** registrados directamente por Bijoux
  (fuera de lo que WooCommerce/The Events Calendar ya aportan). El framework sí tiene
  `VamtamFramework::$complex_layout = ['page', 'post', 'product']` (post types con doble sidebar).

**`vamtam/classes/enqueues.php`** (`VamtamEnqueues`) centraliza todos los `wp_enqueue_style` /
`wp_enqueue_script` del tema vía acciones `wp_enqueue_scripts` (scripts prioridad normal, estilos en
prioridad 999 para cargar después de plugins), más `admin_enqueue_scripts` para el propio panel del
framework. Registra también estilos por-widget (`WP_Nav_Menu_Widget`, `WP_Widget_Tag_Cloud`, etc. →
`self::$widget_styles`).

**`vamtam/classes/sidebars.php`** (`VamtamSidebars`) registra widget areas dinámicamente:
- `page-left` / `page-right` ("Main Widget Area").
- Si WooCommerce está activo: `vamtam-woocommerce-left` / `vamtam-woocommerce-right`
  ("WooCommerce Widget Area").
- Selecciona automáticamente el sidebar correcto según `is_woocommerce()`.

**`vamtam/classes/elementor-bridge.php`** (`VamtamElementorBridge`, clase grande ~1480 líneas):
registra Theme Builder locations (`elementor/theme/register_locations`), helpers de versión de
Elementor/Elementor Pro, migración de opciones legacy → Elementor global settings, fixes de
compatibilidad del editor, y enqueue de scripts/estilos del front cuando Elementor está activo.

## 5. Integración con WooCommerce

**Overrides de plantillas** en `wp-content/themes/bijoux/woocommerce/` (namespace estándar de
WooCommerce template overriding):

| Carpeta | Archivos sobreescritos |
|---|---|
| `cart/` | `cart.php`, `cart-totals.php`, `cart-empty.php`, `proceed-to-checkout-button.php` |
| `checkout/` | `form-checkout.php`, `form-shipping.php`, `form-coupon.php`, `form-login.php` |
| `myaccount/` | `orders.php`, `downloads.php`, `form-login.php`, `form-edit-account.php`, `form-edit-address.php`, `form-lost-password.php`, `form-reset-password.php` |
| `global/` | `wrapper-start.php`, `wrapper-end.php`, `form-login.php` |
| `loop/` | `orderby.php`, `loop-start.php` |

No hay `single-product.php`, `archive-product.php` ni `content-product.php` propios del tema en esa
carpeta — el listado de producto/tienda/producto individual se apoya en **Elementor Theme Builder
locations** (`elementor_theme_do_location('single')` visible en `page.php`) más los widgets custom
de WooCommerce del plugin de integración (sección 6), no en plantillas PHP clásicas del tema.

Nota irónica dado el "modo catálogo" forzado: pese a existir overrides de `cart/` y `checkout/`,
`functions.php` redirige `woocommerce_before_cart` y `woocommerce_before_checkout_form` al home, por
lo que esas plantillas de carrito/checkout **están presentes pero son inalcanzables** en el flujo
normal del sitio actual.

**`vamtam/helpers/woocommerce-integration.php`** (32KB, el archivo de integración WC más grande del
tema): helpers como `vamtam_has_woocommerce()`, `vamtam_wc_get_page_id()`,
`vamtam_wc_get_page_permalink()`, `vamtam_wc_get_cart_url()`, y (a juzgar por el tamaño) probable
lógica adicional de layout/AJAX de carrito, breadcrumbs, hooks de WooCommerce específicos del tema
(no se leyó completo — ver limitaciones).

**Plugin `vamtam-elementor-integration`** también aporta su propia capa WooCommerce:
`includes/woocommerce/wc-templates/cart/mini-cart.php` (override del mini-carrito usado por el
widget `woocommerce-menu-cart`).

## 6. Widgets custom de Elementor (`vamtam-elementor-integration`)

Ubicados en `wp-content/plugins/vamtam-elementor-integration/includes/widgets/`. Nombres de archivo
(≈ nombre de la clase/widget) con descripción inferida:

| Archivo | Widget (inferido) |
|---|---|
| `section.php` | Contenedor/Sección (probable override de Section de Elementor) |
| `column.php` | Columna con espaciados "lógicos" custom (`column--logical-spacings`) |
| `heading.php` | Encabezado enriquecido |
| `animated-headline.php` (18.7KB) | Titular animado (texto rotativo/typing, letras difuminadas) |
| `text-editor.php` | Editor de texto enriquecido |
| `image.php` | Imagen con animaciones de zoom/scale |
| `image-box.php` (24.8KB) | Caja de imagen + texto (posible "box is link") |
| `image-carousel.php` | Carrusel de imágenes |
| `blockquote.php` | Cita destacada |
| `icon-box.php` (12.7KB) | Caja de icono + texto |
| `button.php` (21.3KB) | Botón con variantes de tipo/estilo Bijoux |
| `call-to-action.php` (83.9KB, el widget más grande) | Bloque CTA muy configurable (hero/banner) |
| `divider.php` | Separador |
| `accordion.php` | Acordeón |
| `tabs.php` (21KB) | Pestañas (incluye variante "mega menu") |
| `slides.php` (25.8KB) | Slider/carrusel principal (flechas, stretch, etc. — muy parametrizado) |
| `testimonial-carousel.php` | Carrusel de testimonios |
| `posts.php` / `posts-base.php` / `archive-posts.php` | Listados de entradas de blog (grid/carousel) |
| `post-navigation.php` / `post-info.php` | Navegación entre posts / metadatos de post |
| `nav-menu.php` (41.9KB, muy grande) | Menú de navegación custom (mega menú, toggle mobile, etc.) |
| `search-form.php` | Formulario de búsqueda |
| `social-icons.php` | Iconos sociales |
| `google-maps.php` | Mapa embebido |
| `form.php` (27.9KB) | Formulario (contacto/genérico) |
| `theme-post-title.php` | Título dinámico del post/página actual |
| `products-base.php` (19.2KB) | Base compartida de listados de producto WooCommerce |
| `woocommerce-products.php` (49.1KB, el segundo más grande) | Grid/carousel de productos WooCommerce |
| `wc-archive-products.php` | Listado tipo archivo de productos |
| `wc-categories.php` | Grid de categorías de producto |
| `woocommerce-product-images.php` (8.3KB) | Galería de imágenes de producto |
| `woocommerce-product-meta.php` (11.5KB) | Metadatos de producto (SKU, categorías...) |
| `woocommerce-product-data-tabs.php` | Pestañas de datos de producto (descripción/reviews) |
| `woocommerce-product-related.php` | Productos relacionados |
| `woocommerce-product-upsell.php` | Productos upsell |
| `product-add-to-cart.php` (16.6KB) | Botón/formulario "añadir al carrito" (relevante: está **desactivado** vía hooks en `functions.php`, ver sección 1) |
| `woocommerce-menu-cart.php` (24.8KB) | Icono/dropdown de carrito en el menú |

Helpers relacionados: `includes/helpers/vamtam-elementor-widgets-handler.php` (registro de widgets),
`includes/helpers/vamtam-elementor-utils.php`; hooks generales en `includes/vamtam-elementor-hooks.php`;
Site Settings custom de Elementor en `includes/site-settings/theme-site-settings.php`; dynamic tag
custom `includes/dynamic-tags/vamtam-popup.php`; overrides puente con el tema en
`includes/theme-overrides/functions.php`.

## 7. Page templates (PHP)

No se encontraron comentarios `Template Name:` en los archivos PHP raíz revisados (`page.php`,
`index.php`, `single.php`, `archive.php`, `404.php`, `search.php`, `attachment.php`, `author.php`).
Bijoux **no expone page templates PHP seleccionables desde el editor clásico de WordPress** — el
patrón que usa es distinto: cada plantilla base (`page.php`, `single.php`, etc.) comprueba en
runtime si existe una **ubicación de Elementor Theme Builder** para ese contexto
(`VamtamElementorBridge::is_location_template_exits('single')` /
`elementor_theme_do_location('single')`) y si existe, delega el layout completo a Elementor; si no,
cae al markup PHP clásico del tema (con `page-wrapper`, `sidebar`, etc.).

Consecuencia práctica: la "plantilla" real de una página específica se define y edita **dentro de
Elementor → Theme Builder** (Single Post, Single Product, Archive, 404, Header, Footer...), no
seleccionando un "Page Template" en el metabox de WordPress. Un agente que necesite cambiar el
layout de una plantilla debe buscar primero si hay una plantilla de Theme Builder de Elementor
asignada a esa condición, antes de tocar el PHP del tema.

## 8. Child theme

**No existe ningún child theme** en `wp-content/themes/` — el listado de esa carpeta solo contiene
`bijoux`, `twentytwentyfour`, `twentytwentyfive`, `twentytwentythree` (los temas por defecto de WP).

**Implicación importante para trabajo futuro**: cualquier edición directa a archivos PHP/CSS dentro
de `wp-content/themes/bijoux/` (fuera de Elementor) **se perderá en la próxima actualización del
tema** (VamTam publica actualizaciones vía su propio updater, visible en
`vamtam/vamtam-updates/class-vamtam-updates.php` del plugin de integración y
`vamtam/admin/classes/update-notice.php` del tema). Se recomienda:
- Preferir cambios vía Elementor (Site Settings, Theme Builder, CSS personalizado por widget/página)
  siempre que sea posible — esto sí sobrevive a updates del tema.
- Si se requiere tocar PHP/CSS del tema de forma persistente, **crear un child theme** de Bijoux
  antes de editar, o usar hooks/filtros desde un plugin propio (`functions.php` de un mu-plugin) en
  vez de editar los archivos del tema in-place.

## 9. Convenciones de estilos/CSS observadas

- **Preprocesador**: el tema fuente está escrito en **LESS** (`vamtam/assets/css/*.less`:
  `base.less`, `main.less`, `buttons.less`, `menus.less`, `form-inputs.less`,
  `general-typography.less`, `page-title.less`, `pagination.less`, `comments.less`, `footer.less`,
  `grid.less`, `animations.less`, `side-buttons.less`, `vamtam-custom-classes.less`, `print.less`,
  `closing.less`, más subcarpetas `shortcodes/`, `woocommerce/`, `src/`). Compilado vía
  `vamtam/classes/less-bridge.php` (LESS-en-PHP, `lessphp-extensions.php`) y/o el toolchain Grunt
  (`Gruntfile.js`, `utils/grunt/`, `package.json`).
- **Salida compilada**: `vamtam/assets/css/dist/` contiene los bundles finales servidos en
  producción: `all.css` (305KB, bundle principal + sourcemap `all.css.map`), `blog.css`,
  `header.css`, `editor.css` (estilos del editor de bloques/Elementor), `not-found.css`, `ie11.css`,
  más subcarpetas `woocommerce/`, `responsive/`, `widgets/` (no exploradas en detalle — son
  fragmentos generados, no se recomienda editarlos a mano).
- **CSS del plugin de widgets Elementor**: sus propios assets viven en
  `wp-content/plugins/vamtam-elementor-integration/assets/` (JS principalmente: `vamtam-elementor.js`,
  `vamtam-elementor-frontend.js` + minificados; SVGs de formas en `assets/shapes/`).
- **Naming**: prefijo consistente `vamtam-*` para clases CSS/JS/constantes/opciones propias del
  framework (`vamtam-pagination-wrapper`, `VAMTAM_*` constantes PHP, `vamtam_*` funciones/opciones).
  Prefijo `bijoux` reservado principalmente al text-domain de i18n (`esc_html__('...', 'bijoux')`) y
  al slug del tema, no tanto a nombres de clases CSS.
- **Dónde vive el CSS de "marca"/diseño real**: dado el bridge Elementor descrito en la sección 3,
  el diseño visual específico del sitio (colores, tipografías, espaciados por sección) vive
  mayormente en:
  1. Elementor **Global Settings/Kit** (colores y fuentes globales).
  2. CSS/estilos por widget o por página dentro de cada documento de Elementor.
  3. Posible **"Additional CSS"** del Customizer de WordPress (no verificado — vive en `wp_options`,
     no en archivos; ver limitaciones).
  El CSS del tema (`vamtam/assets/css/dist/*.css`) cubre sobre todo el "esqueleto"/reset, tipografía
  base, grid, paginación, formularios, footer — no el contenido visual específico de cada página del
  sitio de joyería.
- **Fuentes**: `vamtam/helpers/fonts.php` (177KB) es un catálogo masivo de fuentes Google Fonts
  disponibles para el framework/legacy options; las fuentes realmente aplicadas al sitio se
  seleccionan hoy vía Elementor Global Fonts (con posible fuente custom vía
  `vamtam_custom_font_families` option, soportado en `framework.php`).

## 10. Notas y limitaciones

- **Solo lectura de archivos**: no se consultó la base de datos (`wp_options`, `wp_posts`), por lo
  que no se pudo confirmar:
  - Qué tema está realmente **activo** (aunque Bijoux es el candidato lógico y único tema comercial
    instalado — ver también `site_inventory.md`).
  - El contenido real del **kit activo de Elementor** (colores/fuentes globales configurados), ni si
    existe **"Additional CSS"** del Customizer con overrides.
  - Qué **widgets custom** y qué **plantillas de Theme Builder** están efectivamente usados/asignados
    en las páginas publicadas del sitio (eso vive en meta de Elementor por página/kit en BD, no en
    archivos).
  - El valor actual de la opción legacy `vamtam_theme` ni si la migración a Elementor ya se ejecutó
    en este sitio.
- No se leyó el contenido completo de archivos muy grandes (`fonts.php` 177KB,
  `class-tgm-plugin-activation.php` 128KB, `call-to-action.php` 84KB, `woocommerce-products.php`
  49KB, `nav-menu.php` widget 42KB, `elementor-bridge.php` 46KB, `woocommerce-integration.php` 32KB)
  — se inspeccionaron cabeceras/fragmentos representativos. Si un agente futuro necesita modificar
  el comportamiento exacto de alguno de estos, debe leer el archivo completo primero.
- No se exploró en profundidad `samples/content.xml` (4MB, export WXR del contenido demo del tema)
  ni `samples/dependencies.php` (declaración TGMPA de plugins recomendados/requeridos) más allá de
  confirmar su existencia — puede ser útil como referencia de "cómo se ve una página armada
  correctamente con estos widgets" si se necesita reconstruir contenido.
- No se leyó `wp-config.php` ni ningún archivo de credenciales (por instrucción explícita de
  seguridad).
- Este documento es una foto de los **archivos del tema tal como están instalados hoy** (versión
  Bijoux 34); no reflejará cambios de contenido hechos desde wp-admin/Elementor después de esta
  fecha.
