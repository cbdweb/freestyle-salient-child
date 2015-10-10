<?php
add_filter( 'the_content', 'sign_up' );

function sign_up ( $content ) {
    return $content .
        "<div class='sign_up'>" . 
        "<h3>Sign up to support helmet law reform</h3>" .
        do_shortcode ( '[signature]' ) .
        "</div>";
}
