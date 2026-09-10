# amaga-site-wp

Repo del child theme **Bijoux Child** para `amagajoyerias.com`. Contiene solo el código propio del
sitio — el tema padre comercial `bijoux` y los plugins de terceros NO viven aquí (ver `vendor/` más
abajo).

## Estructura

```
amaga-site-wp/
├── themes/bijoux-child/   # ÚNICO código versionado: el child theme
├── .wp-env.json           # config del entorno local (Docker vía wp-env)
└── README.md
```

`vendor/` es una carpeta **hermana** de este repo (`../vendor` desde aquí), no versionada:
tema padre `bijoux` + plugins comerciales (WooCommerce, Elementor, Pro Elements, VamTam, Contact
Form 7...). Se sincroniza vía FTP desde el hosting y solo se usa para poder correr WordPress local
— nunca se edita ni se comitea.

## Cómo modificar el tema

El child theme puede sobreescribir **cualquier archivo** del padre (plantillas de página,
plantillas de WooCommerce, etc.), no solo estilos:

1. Localiza el archivo original en `../vendor/bijoux/<ruta>` (ej. `page.php`,
   `woocommerce/cart/cart-empty.php`, `templates/post/content.php`).
2. Copia **solo ese archivo** a `themes/bijoux-child/<misma ruta>`.
3. Edítalo ahí. WordPress usa automáticamente la versión del child en cuanto existe en esa ruta;
   todo lo que no copies sigue viniendo del padre sin cambios.
4. Para CSS/JS propio (no overrides de plantillas), agrégalo en `themes/bijoux-child/style.css` o
   en `themes/bijoux-child/assets/` y encólalo desde `functions.php`.
5. Para hooks/filtros/lógica nueva (no un override de archivo), agrégalo en
   `themes/bijoux-child/functions.php` — se suma al `functions.php` del padre, no lo reemplaza.

**Nunca edites `vendor/bijoux/` directamente** — se pierde en el próximo update del tema y no
queda versionado.

## Entorno local (Docker vía `wp-env`)

Requiere `../vendor/bijoux` y los plugins ya descargados (ver `docs/theme-workflow.md` del
proyecto para el paso a paso de descarga por FTP).

```bash
npx @wordpress/env start
```

Luego entra a `http://localhost:8888/wp-admin` y activa el tema **Bijoux Child** en
Apariencia → Temas.

Para detener/reiniciar:
```bash
npx @wordpress/env stop
npx @wordpress/env destroy   # borra la BD local si necesitas empezar de cero
```

## Flujo de trabajo (Gitflow)

Ver `docs/theme-workflow.md` para el detalle completo de ramas (`main` / `develop` /
`feature/*` / `release/*` / `hotfix/*`) y el proceso de release + despliegue a producción vía
Hostinger.
