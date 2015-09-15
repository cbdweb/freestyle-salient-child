<?php
/**
 * Displays a bio and the posts for a given author. The posts can be shown either
 * as excerpts or full contents
 *
 * @package Suffusion
 * @subpackage Templates
 */

global $suffusion, $suf_author_excerpt;

    global $wp_query;
    $wp_query->set_404();
    status_header(404);
	include( get_query_template( '404' ) );