<?php
/**
 * bijoux-child functions and definitions.
 *
 * Este archivo se suma al functions.php de bijoux (no lo reemplaza) — ambos se ejecutan.
 * Todo hook/filtro/CPT propio del sitio va aquí, nunca en wp-content/themes/bijoux/functions.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BIJOUX_CHILD_VERSION', wp_get_theme()->get( 'Version' ) );

/**
 * Encola el style.css del child después de los estilos del framework (VamtamEnqueues
 * encola en prioridad 999 — ver bijoux-theme-KB.md), así cualquier override gana la cascada.
 */
add_action( 'wp_enqueue_scripts', 'bijoux_child_enqueue_styles', 1000 );
function bijoux_child_enqueue_styles() {
	wp_enqueue_style(
		'bijoux-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array(),
		BIJOUX_CHILD_VERSION
	);
}
