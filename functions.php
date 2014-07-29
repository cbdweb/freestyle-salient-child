<?php
/*
 * Newsletters
 */
add_action( 'init', 'create_fs_newsletter' );
function create_fs_newsletter() {
	$labels = array(
        'name' => _x('Newsletters', 'post type general name'),
        'singular_name' => _x('Newsletter', 'post type singular name'),
        'add_new' => _x('Add New', 'events'),
        'add_new_item' => __('Add New Newsletter'),
        'edit_item' => __('Edit Newsletter'),
        'new_item' => __('New Newsletter'),
        'view_item' => __('View Newsletter'),
        'search_items' => __('Search Newsletter'),
        'not_found' =>  __('No newsletters found'),
        'not_found_in_trash' => __('No newsletters found in Trash'),
        'parent_item_colon' => '',
    );
    register_post_type( 'fs_newsletter',
        array(
            'label'=>__('Newsletters'),
            'labels' => $labels,
            'description' => 'Each post is one newsletter.',
            'public' => true,
            'can_export' => true,
            'exclude_from_search' => false,
            'has_archive' => true,
            'show_ui' => true,
            'capability_type' => 'post',
            'menu_icon' => "dashicons-megaphone",
            'hierarchical' => false,
            'rewrite' => false,
            'supports'=> array('title', 'editor' ) ,
            'show_in_nav_menus' => true,
        )
    );
}
/*
 * specify columns in admin view of signatures custom post listing
 */
add_filter ( "manage_edit-fs_newsletter_columns", "fs_newsletter_edit_columns" );
add_action ( "manage_posts_custom_column", "fs_newsletter_custom_columns" );
function fs_newsletter_edit_columns($columns) {
    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => "Subject",
        "fs_col_post_type" => "Subscriber type",
        "fs_col_newsletter_type" => "Newsletter type",
        "fs_newsletter_country" => "Country",
        "fs_newsletter_state" => "State",
    );
    return $columns;
}
function fs_newsletter_custom_columns($column) {
    global $post;
    $custom = get_post_custom();
    switch ( $column ) {
        case "title":
            echo $post->post_title;
            break;
        case "fs_col_post_type":
            echo $custom["fs_newsletter_post_type"][0];
            break;
        case "fs_col_newsletter_type":
            echo $custom["fs_newsletter_newsletter_type"][0];
            break;
        case "fs_newsletter_country":
            echo $custom["fs_newsletter_country"][0];
            break;
        case "fs_newsletter_state":
            echo ( $custom["fs_signature_country"][0]==="AU" ? $custom["fs_signature_state"][0] : "&nbsp;" );
            break;
    }
}
/*
 * Add fields for admin to edit signature custom post
 */
add_action( 'admin_init', 'fs_newsletter_create' );
function fs_newsletter_create() {
    add_meta_box('fs_newsletter_meta', 'Newsletter', 'fs_newsletter_meta', 'fs_newsletter' );
}
function fs_newsletter_meta() {
    global $post;
    $custom = get_post_custom( $post->ID );
    $meta_country = $custom['fs_newsletter_country'][0];
    $meta_state = get_post_meta( $post->ID, 'fs_newsletter_state' ); // checkboxes stored as arrays
    $meta_post_type = $custom['fs_newsletter_post_type'][0];
    $meta_newsletter = get_post_meta( $post->ID, 'fs_newsletter_newsletter_type' ); // checkbox stored as mutiple value
    
    echo '<input type="hidden" name="fs-newsletter-nonce" id="fs-newsletter-nonce" value="' .
        wp_create_nonce( 'fs-newsletter-nonce' ) . '" />';
    ?>
    <div class="fs-meta">
        <ul>
            <li><label>Send to which group?</label>
                <select name="fs_newsletter_post_type">
                    <option value="">Please select</option>
                    <option value="members"<?=($meta_post_type==="members" ? " selected" : "")?>>Members</option>
                    <option value="signatures"<?=($meta_post_type==="signatures" ? " selected" : "")?>>Signatures</option>
                </select>
                <span class="smallfont">Fields below are ignored if "members" is chosen</span>
            </li>
            <li><label>Newsletter level</label>
                <input name="fs_newsletter_newsletter_type[]" type="checkbox" value="y"
                    <?=( is_array( $meta_newsletter[0] ) && in_array( "y", $meta_newsletter[0] ) ? " checked" : "")?>
                >Occasional 
            </li>
            <li>
                <label>&nbsp;</label>
                <input name="fs_newsletter_newsletter_type[]" type="checkbox" value="m"
                    <?=( is_array( $meta_newsletter[0] ) && in_array( "m", $meta_newsletter[0] ) ? " checked" : "")?>
                >Frequent
            </li>
            <li><label>Country</label>
                <select name="fs_newsletter_country">
                    <option value="all"<?=($meta_country==="all" ? " selected" : "")?>>All</option>
                    <?php 
                    $fs_country = fs_country();
                    foreach($fs_country as $ab => $title ) { ?>
                        <option value="<?=$ab;?>"<?php echo ($meta_country===$ab ? " selected='selected'" : "") ;?>><?php echo $title;?></option>
                    <?php } ?>
                </select>
            </li>
            <li><label>State</label>
                <span class="smallfont">State is ignored unless Australia is chosen above</span>
            </li>
            <?php 
            $fs_states = fs_states();
            foreach($fs_states as $ab => $title ) { ?>
                <li><label>&nbsp;</label>
                    <input name="fs_newsletter_state[]" type="checkbox" value="<?=$ab;?>"
                        <?=( is_array( $meta_state[0] ) && in_array( $ab, $meta_state[0] ) ? " checked" : "") ;?>
                    >
                    <?php echo $title;?>
                </li>
            <?php } ?>
        </ul>
    </div>
    <?php    
}

add_action ('save_post', 'save_fs_newsletter');
 
function save_fs_newsletter(){
 
    global $post;

    // - still require nonce

    if ( !wp_verify_nonce( $_POST['fs-newsletter-nonce'], 'fs-newsletter-nonce' )) {
        return $post->ID;
    }

    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;

    // - convert back to unix & update post

    update_post_meta($post->ID, "fs_newsletter_country", $_POST["fs_newsletter_country"] );
    update_post_meta($post->ID, "fs_newsletter_state", $_POST["fs_newsletter_state"] ); // is an array of states
    update_post_meta($post->ID, "fs_newsletter_newsletter_type", $_POST["fs_newsletter_newsletter_type"] ); // is an array of newsletter preference types
    update_post_meta($post->ID, "fs_newsletter_post_type", $_POST["fs_newsletter_post_type"] );
}

add_filter('post_updated_messages', 'newsletter_updated_messages');
 
function newsletter_updated_messages( $messages ) {
 
  global $post, $post_ID;
 
  $messages['fs_newsletter'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Newsletter updated. <a href="%s">View item</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Newsletter updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Newsletter restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Newsletter published. <a href="%s">View Newsletter</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Newsletter saved.'),
    8 => sprintf( __('Newsletter submitted. <a target="_blank" href="%s">Preview newsletter</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Newsletter scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview newsletter</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Newsletter draft updated. <a target="_blank" href="%s">Preview newsletter</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );
 
  return $messages;
}
/*
 * Give ourselves control over admin styles
 */
add_action( 'wp_print_styles', 'my_deregister_styles', 100 );
function my_deregister_styles() {
	wp_deregister_style( 'wp-admin' );
}
/*
 * label for title field on custom posts
 */

add_filter('enter_title_here', 'fs_newsletter_enter_title');
function fs_newsletter_enter_title( $input ) {
    global $post_type;

    if ( 'fs_newsletter' === $post_type ) {
        return __( 'Newsletter (email) subject' );
    }
    return $input;
}
