# Workflow de desarrollo — Tema Bijoux (amagajoyerias.com)

_Propuesta. Combina Gitflow con despliegue a Hostinger vía MCP. Complementa `site_inventory.md` y
`bijoux-theme-KB.md` (léelos primero — ahí está el porqué de las decisiones de abajo)._

## 0. Premisa de arquitectura (por qué el flujo es así)

- **`bijoux` (tema padre) es comercial (VamTam/Envato) y no se versiona en nuestro repo.** Se trata
  como dependencia externa fija a una versión (hoy: v34), documentada, actualizada aparte (updater
  propio del tema) — no vive en git.
- **No existe child theme hoy.** Este workflow lo crea como primer entregable: `bijoux-child`. Es el
  único artefacto de código que vamos a versionar, construir y desplegar.
- **El diseño visual (colores, layout, contenido de página, Theme Builder de Elementor) vive en la
  BD**, no en archivos — por tanto **no es versionable en git** con este mecanismo. Lo que sí se
  versiona es: PHP del child theme (`functions.php`, overrides de WooCommerce, widgets propios si se
  crean), CSS/JS propios, y opcionalmente exports JSON de templates/kit de Elementor (ver §7,
  mejora futura).
- **No hay entorno de staging separado en Hostinger** (un solo sitio = prod). El "staging" de este
  flujo es el entorno local.

## 1. Repositorio

Repo git: `amaga-site-wp` (GitHub, privado, cuenta personal — ya creado). Contiene el child theme
y la documentacion del proyecto; NO contiene el tema padre ni plugins de terceros (eso vive en
`vendor/`, hermano del repo, no versionado - ver seccion 2):

```
amaga-site-wp/                       (repo git)
themes/bijoux-child/
  style.css              # Header obligatorio: Template: bijoux, Version: x.y.z
  functions.php          # Override/extension de functions.php del padre
  woocommerce/            # Overrides puntuales de plantillas WC (solo lo que se toque)
  assets/css/, assets/js/ # CSS/JS propios (plano, sin LESS/Grunt salvo que se justifique)
docs/                     # site_inventory.md, bijoux-theme-KB.md, theme-workflow.md (este archivo)
.wp-env.json              # Config del entorno local Docker
.gitignore
README.md                 # Instrucciones de entorno local + como overridear archivos del padre
```

`main`/`develop` se empujan al remoto `git@github.com:camarinb2096/amaga-site-wp.git`.

## 2. Entorno de desarrollo local

Objetivo: poder ver y probar cambios del child theme sin tocar prod.

1. **Decidido**: stack local con **Docker**. Recomendado `wp-env` (oficial de WordPress, corre sobre
   Docker, config versionable en `.wp-env.json` dentro del repo) sobre alternativas como
   `docker-compose` a mano, salvo que el usuario prefiera control manual del compose file — en ese
   caso se documenta un `docker-compose.yml` equivalente (WordPress + MySQL) en el repo.
2. **Dependencias vendored**: instalar en el WordPress local las mismas versiones que en prod (ver
   `site_inventory.md` §Plugins/Temas): WooCommerce 10.6.2, Elementor 4.2.4, Pro Elements 4.2.2,
   `vamtam-elementor-integration` 1.1.14, `vamtam-importers-e` 1.3.4, tema `bijoux` v34, etc. El tema
   padre y los plugins VamTam no son públicos — se obtienen descargándolos una vez desde el sitio
   real (vía `hosting_getWebsiteFileContentV1`/lectura de archivos, o desde la cuenta Envato del
   cliente) y se guardan fuera del repo (o en un repo/artefacto separado de "vendor", nunca junto al
   código propio versionado).
3. **Montaje del child theme**: `wp-content/themes/bijoux-child` del WordPress local apunta
   (symlink o volumen Docker) directamente a la carpeta del repo — así cualquier cambio se ve al
   refrescar el navegador.
4. **Contenido de prueba**: usar `samples/content.xml` (demo WXR del propio tema, ya identificado en
   el KB) para tener páginas realistas sin tocar datos reales del cliente. Para paridad exacta con
   prod (ej. depurar un bug visual reportado), se puede hacer un export puntual de prod (Duplicator)
   e importarlo local — nunca al revés.
5. **Activación**: activar el tema `bijoux-child` (con `bijoux` como padre) en el WP local.

## 3. Ramas (Gitflow)

| Rama | Propósito | Nace de | Va a |
|---|---|---|---|
| `main` | Refleja exactamente lo desplegado en prod. Cada commit = una versión publicada, taggeada `vX.Y.Z`. | — | — |
| `develop` | Integración continua de features probadas en local. | `main` | `release/*` |
| `feature/<nombre>` | Una implementación puntual (ej. `feature/reactivar-carrito`, `feature/nuevo-widget-testimonios`). | `develop` | `develop` (PR) |
| `release/x.y.z` | Congela alcance para publicar: bump de versión, changelog, QA final en local. | `develop` | `main` + back-merge a `develop` |
| `hotfix/x.y.z` | Arreglo urgente sobre lo que YA está en prod. | `main` | `main` + `develop` |

Convención de versión: semver sobre el child theme (`MAJOR.MINOR.PATCH` en el header de
`style.css` y en `CHANGELOG.md`).

## 4. Ciclo de trabajo (feature)

1. `git checkout -b feature/x develop`.
2. Trabajar contra el WordPress local (§2). Probar visualmente en el navegador local.
3. Commit + push; PR hacia `develop` (o merge directo si se trabaja sin remoto todavía).
4. Merge a `develop` cuando esté validado localmente.

## 5. Release

1. `git checkout -b release/x.y.z develop`.
2. Bump de versión en `style.css` (header `Version:`) + entrada en `CHANGELOG.md`.
3. QA final completo en el entorno local sobre el contenido de prueba (§2.4) — idealmente
   revisando el listado de páginas/flujos críticos del sitio (home, catálogo, ficha de producto,
   contacto — recordando que carrito/checkout están intencionalmente desactivados, KB §1).
4. Merge `release/x.y.z` → `main`, tag `vX.Y.Z`. Merge `release/x.y.z` → `develop` (back-merge).

## 6. Publicación en prod

Con `main` en el estado de la release recién taggeada, y **desde este mismo entorno local** (Claude
Code corriendo aquí, con acceso al Hostinger MCP):

1. Backup previo del sitio: `hosting_clearWebsiteCacheV1` no es backup — usar Duplicator (ya
   instalado en el sitio) o exportar el tema actual de prod antes de sobreescribir, como red de
   seguridad.
2. Desplegar el child theme empaquetado (la carpeta `main` del repo, limpia de `.git`/`node_modules`)
   con `hosting_deployWordpressTheme`:
   - `domain: amagajoyerias.com`
   - `slug: bijoux-child`
   - `themePath: <ruta local a la carpeta del tema en main>`
   - `activate: true` únicamente en el primer despliegue (activar el child theme por primera vez);
     en despliegues posteriores el tema ya está activo, así que `activate` es irrelevante/false.
3. Limpiar caché del sitio: `hosting_clearWebsiteCacheV1`.
4. Smoke test manual de las páginas críticas en producción.
5. Si algo falla: redeploy de la versión anterior (`hosting_deployWordpressTheme` apuntando al tag
   `vX.Y.Z` previo) — por eso `main` siempre debe reflejar exactamente lo publicado, para poder
   hacer rollback determinístico.

## 7. Mejoras futuras (no bloqueantes para arrancar)

- **Versionar templates de Elementor Theme Builder / kit** como export JSON dentro del repo (Elementor
  soporta export/import de templates) para que cambios estructurales de layout también queden en
  git, no solo en la BD de prod. Se añadiría como carpeta `elementor-templates/` en el repo con un
  paso manual de export/import (no hay API de Elementor vía Hostinger MCP hoy).
- **Sitio de staging real** en Hostinger (subdominio o sitio aparte) para probar releases con datos
  reales antes de prod, si el volumen de cambios lo justifica.
- **CI simple** (lint PHP/CSS, validación de header de versión) al abrir PR hacia `develop`, una vez
  el repo esté en GitHub/GitLab.

## 8. Decisiones confirmadas

- **Repo**: GitHub, cuenta personal del usuario, privado.
- **Entorno local**: Docker (`wp-env` como opción por defecto).
- **Staging**: no por ahora — el entorno local es la única validación previa a prod. Se revisita
  (§7) si el volumen/riesgo de cambios lo justifica más adelante.

## 9. Estado actual y próximos pasos

Hecho:
- [x] Repo `amaga-site-wp` creado en GitHub (privado, cuenta personal).
- [x] Scaffolding del child theme: `themes/bijoux-child/style.css` + `functions.php`, `.wp-env.json`,
  `.gitignore`, `README.md`, `docs/` con la documentación del proyecto.
- [x] `vendor/bijoux` (tema padre completo) y `vendor/contact-form-7` descargados vía FTP.

Pendiente:
1. Descargar el resto de `vendor/`: `woocommerce`, `elementor`, `pro-elements`,
   `vamtam-elementor-integration`, `vamtam-importers-e` (referenciados ya en `.wp-env.json`).
2. Limpiar carpetas sueltas en la raíz del proyecto (`contact-form-7/` debería vivir en `vendor/`,
   `vendorclear/` parece accidental).
3. Primer `npx @wordpress/env start` y activar `Bijoux Child` sobre `bijoux` en local.
4. Primer commit del scaffolding en `amaga-site-wp` (revisar `git status` y confirmar antes de
   commitear/pushear).
