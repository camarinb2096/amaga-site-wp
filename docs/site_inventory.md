# Inventario de Sitio — amagajoyerias.com (Hostinger)

_Generado: 2026-09-09_

## Resumen del sitio

| Campo | Valor |
|---|---|
| Dominio principal | `amagajoyerias.com` |
| Tipo de sitio | WordPress |
| Estado | Habilitado (`is_enabled: true`) |
| Usuario Hostinger | `u579454336` |
| Client ID | `1016415928` |
| Order ID | `1007930304` |
| Fecha de creación | 2025-09-26 |
| Directorio raíz (document root) | `/home/u579454336/domains/amagajoyerias.com/public_html` |
| Dominio padre | Ninguno (dominio principal, `vhost_type: main`) |

## Dominios, subdominios y redirects

- **Subdominios**: ninguno configurado.
- **Dominios aparcados (parked)**: ninguno.
- **Redirects**: ninguno configurado.

## Estructura del sitio (raíz de `public_html`)

Instalación estándar de WordPress core (`wp-admin`, `wp-includes`, `wp-content`, `wp-login.php`, `wp-settings.php`, etc.), más:
- `wp-config.php` presente — **credenciales de base de datos no incluidas por seguridad**.
- `.htaccess` y `.htaccess.bk` presentes.
- `.private` (directorio, contenido no inspeccionado).
- `llms.txt` y `default.php` presentes (archivos no estándar en la raíz, posiblemente del tema/tema builder VamTam/Bijoux).
- `readme.html`, `license.txt` — archivos estándar de WordPress core.

## Temas instalados

Ubicación: `wp-content/themes/`

| Tema (carpeta) | Nombre | Versión | Notas |
|---|---|---|---|
| `bijoux` | Bijoux | 34 | Tema comercial de VamTam ("The Ultimate Niche WordPress Theme for Jewelers"), licencia Envato. **Es el tema activo/relevante para un sitio de joyería.** |
| `twentytwentyfive` | Twenty Twenty-Five | 1.3 | Tema por defecto de WordPress (fallback) |
| `twentytwentyfour` | Twenty Twenty-Four | 1.3 | Tema por defecto de WordPress (fallback) |
| `twentytwentythree` | Twenty Twenty-Three | 1.6 | Tema por defecto de WordPress (fallback) |

Nota: no se confirmó mediante `wp-config.php`/opciones de BD cuál tema está activo actualmente (esa información vive en la tabla `wp_options`, no accesible vía listado de archivos); por el nombre y descripción del tema Bijoux, es el candidato lógico para un sitio de joyería.

## Plugins instalados

Ubicación: `wp-content/plugins/`

| Plugin (carpeta) | Nombre | Versión | Notas |
|---|---|---|---|
| `woocommerce` | WooCommerce | 10.6.2 | Plataforma de tienda online |
| `elementor` | Elementor Website Builder | 4.2.4 | Page builder |
| `pro-elements` | PRO Elements | 4.2.2 | Extensión pro para Elementor (requiere plugin `elementor`) |
| `vamtam-elementor-integration` | VamTam Elementor Integration | 1.1.14 | Integración de widgets del tema Bijoux/VamTam con Elementor |
| `vamtam-importers-e` | VamTam Importers (E) | 1.3.4 | Importador de contenido de muestra del tema VamTam |
| `yith-woocommerce-wishlist` | YITH WooCommerce Wishlist | 4.17.0 | Lista de deseos para WooCommerce |
| `yith-woocommerce-catalog-mode` | YITH WooCommerce Catalog Mode | 2.57.0 | Modo catálogo (oculta "añadir al carrito"/precio) |
| `woo-checkout-field-editor-pro` | Checkout Field Editor (Checkout Manager) for WooCommerce | 2.2.0 | Editor de campos de checkout |
| `wompi-portal-de-pagos` | Wompi Portal de Pagos | 3.2.0 | Pasarela de pagos (Bancolombia/Wompi) |
| `wp-whatsapp-chat` | Social Chat – Click To Chat App Button | 8.6.4 | Botón de chat de WhatsApp |
| `contact-form-7` | Contact Form 7 | 6.1.7 | Formularios de contacto |
| `duplicator` | Duplicator – Backups & Migration Plugin | 5.0.0 | Backups/migración |
| `loco-translate` | Loco Translate | 2.8.8 | Traducción de textos del sitio |
| `litespeed-cache` | LiteSpeed Cache | 7.9.1 | Caché/optimización |
| `akismet` | Akismet Anti-spam: Spam Protection | 5.7.2 | Antispam para comentarios/formularios |
| `hostinger` | Hostinger Tools | 3.0.77 | Plugin de gestión propio de Hostinger |
| `send-app` | Send – Email and SMS marketing (by Elementor.com) | 1.6.2 | Marketing por email/SMS |
| `angie` | Angie (Agentic AI for WordPress, por Elementor.com) | 1.1.11 | Asistente AI agentic de Elementor (early access) |

## PHP

| Campo | Valor |
|---|---|
| Versión actual | PHP 8.2 (8.2.33) |
| Versiones soportadas disponibles | 7.3, 7.4, 8.0, 8.1, 8.2, 8.3, 8.4, 8.5 |
| memory_limit | 2048M |
| post_max_size | 2048M |
| upload_max_filesize | 2048M |
| max_execution_time | 360s |
| max_input_time | 360s |
| opcache | Habilitado (256M, max_accelerated_files 16229) |
| display_errors | Off |
| log_errors | Off |
| date.timezone | UTC |
| disable_functions | system, exec, shell_exec, passthru, mysql_list_dbs, ini_alter, dl, symlink, link, chgrp, leak, popen, apache_child_terminate, virtual, mb_send_mail |

### Extensiones PHP relevantes (habilitadas)

`bcmath`, `dom`, `fileinfo`, `gd`, `gmp`, `igbinary`, `imagick`, `imap`, `intl`, `mbstring`, `mysqlnd`, `nd_mysqli`, `nd_pdo_mysql`, `opcache`, `pdo`, `pdo_sqlite`, `phar`, `posix`, `soap`, `sockets`, `sysvmsg`, `sysvsem`, `sysvshm`, `tidy`, `timezonedb`, `xmlreader`, `xmlrpc`, `xmlwriter`, `xsl`, `zip` — además de las extensiones "build-in" estándar (core, curl, date, json, openssl, pcre, session, simplexml, etc.).

Extensiones notables **deshabilitadas**: `sodium`, `ffi`, `apcu`, `redis`/`memcache`/`memcached` (no listadas como habilitadas), `ldap`, `mongodb`.

## Bases de datos

| Nombre BD | Usuario | Host | Puerto | Uso de disco | Tamaño máx. | Creada | Última actualización |
|---|---|---|---|---|---|---|---|
| `u579454336_HKn59` | `u579454336_0ATiQ` | `srv1926.hstgr.io` | 3306 | 58 MB | 3072 MB | 2025-09-26 | 2026-01-26 |

No se listó un `domain` explícito asociado a esta base de datos en la respuesta de la API (campo `domain: null`), pero al ser la única BD de la cuenta, es razonable asumir que es la usada por `amagajoyerias.com`. **No se incluyen credenciales/contraseñas** (fuera del alcance de este inventario por seguridad).

## Cron jobs

No hay cron jobs configurados a nivel de cuenta (`hosting_listAccountCronJobsV1` devolvió una lista vacía). WordPress usa su cron "virtual" estándar vía `wp-cron.php` (presente en la raíz), que se dispara por visitas al sitio salvo que se haya desactivado (`DISABLE_WP_CRON`) — eso no se pudo verificar sin leer `wp-config.php` en detalle (se evitó por posibles secretos).

## Node.js / despliegues

No aplica — el sitio es una instalación WordPress (CloudLinux/PHP), no se detectó ningún despliegue Node.js asociado.

## Notas y limitaciones

- No se leyeron los contenidos de `wp-config.php` (para no exponer credenciales); solo se confirmó su existencia.
- No fue posible determinar con certeza absoluta cuál tema está "activo" (esto se define en la tabla `wp_options` de la base de datos, no accesible vía listado de archivos de solo lectura). Por el contexto del sitio (joyería) y el propósito del tema Bijoux, es el más probable.
- El archivo `readme.txt` de `pro-elements` no pudo previsualizarse ("File type is not supported for content preview"); se usó en su lugar el encabezado del archivo principal `pro-elements.php`.
- No se hallaron redirects, subdominios ni dominios aparcados — el sitio opera solo bajo el dominio principal `amagajoyerias.com`.
- No se realizó ninguna acción de escritura, despliegue o modificación; todo el trabajo fue de solo lectura.
