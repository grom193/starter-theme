<?php
$context = Timber::context();
$context['form']['placeholder'] = esc_attr_x( 'Search &hellip;', 'placeholder' );
$context['form']['input_value'] = get_search_query();
$context['form']['action'] = esc_url( home_url( '/' ) );
$context['form']['submit_button_value'] = esc_attr_x( 'Search', 'submit button' );

Timber::render( 'partial/searchform.twig', $context );
