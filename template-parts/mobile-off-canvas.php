<?php
/**
 * Template part for off canvas menu
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */

  $search_position = get_theme_mod('search-position');
  $hide_social = get_theme_mod('hide_header_social');
  $alt_nav = get_theme_mod('show_alt_nav');

?>
<nav class="off-canvas position-right" id="mobile-menu" data-off-canvas data-position="right" role="navigation">
  <?php if ( function_exists( 'the_custom_logo' ) ) { the_custom_logo(); } ?>
  <?php foundationpress_mobile_nav(); ?>
  <?php if( $alt_nav != '' ): foundationpress_top_bar_alt(); endif; ?>
  <?php if( $hide_social == '' ): get_template_part('template-parts/social-media'); endif; ?>
  <?php if( $hide_social != '' && $search_position == 'search-above-menu' ): get_search_form(); endif; ?>
</nav>

<div class="off-canvas-content" data-off-canvas-content>
