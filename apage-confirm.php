<?php 
/*template name: aConfirm*/

function confirm_register_js() {	
	wp_register_script('confirm',  get_stylesheet_directory_uri() . '/js/confirm.js', 'jquery');
	wp_enqueue_script('confirm');
}
add_action('wp_enqueue_scripts', 'confirm_register_js');

get_header(); ?>


<?php nectar_page_header($post->ID);  ?>

<?php 
$options = get_option('salient'); 
$secret = $_GET['secret'];
global $wpdb;
$found = false;
$update = true;  // prevent adwords conversion if there is no secret
if($secret!=="") {
    $query = $wpdb->prepare ( "SELECT * FROM " . $wpdb->postmeta . " WHERE meta_value='%s' AND meta_key='fs_signature_secret'", $secret );
    $row = $wpdb->get_row( $query );
    if( $row ) {
        $post_id = $row->post_id;
        if( $post_id ) {
            $sig = get_post ( $post_id, 'OBJECT' );
            if($sig->post_type === 'fs_signature' && ( $sig->post_status==="draft" || $sig->post_status==="private" ) ) {
                $custom = get_post_custom( $post_id );
                $update = $sig->post_status === "private";
                if( $sig->post_title !== "" && $custom['fs_signature_country'][0] ) {
                    $found = true;
                    $sig->post_status = "private";
                    if( ! $update ) update_post_meta( $post_id, 'fs_signature_registered', date('Y-m-d') );
                    wp_update_post ( $sig );
                }
            }
        }
    }
}
?>

<div class="container-wrap">

	<div class="container main-content">
		
		<div class="row liftup">
                    
                    <?php
                    
                    if( ! $found ) { 
                        if( $secret ) { ?>
                            <P>Sorry, the secret code doesn't match our records and we can't confirm your email.</P>
                            <P>The link in our original email to you only works once.  If you would like to change your email preferences, enter your email address here, and we will send you a fresh email, with a link that will enable you to update your details.</P>
                        <?php } else { ?>
                            <h2>Welcome back.</h2>
                            To edit your details on Freestyle Cyclists' campaign to reform helmet law, enter your email address below, and we will send you
                            a new email link with a secret code that works just once.
                        <?php } ?>
                        
                        <form name="confirm">
                            <table border="0">
                                <tbody>
                                    <tr><td class="leftcol">Your email:</td><td class="rightcol"><input name="email" /></td></tr>
                                    <tr><td colspan="2"><button type="button" id="confirmButton">Get a new email</button></td></tr>
                                    <tr><td colspan="2"><div id="ajax-loading" class="farleft"><img src="<?php echo get_site_url();?>/wp-includes/js/thickbox/loadingAnimation.gif"></div></td></tr>
                                    <tr><td colspan="2"><div id="returnMessage"></div></td></tr>
                                </tbody>
                            </table>
                            <input name="action" value="reconfirmSignature" type="hidden">
                            <input name="secretkey" value="<?=$secret;?>" type="hidden">
                            <?php wp_nonce_field( "fs_reconfirm_sig", "fs_nonce" );?>
                        </form>
                        <P>Otherwise, send an email to <a href='mailto:info@freestylecyclists.org'>info@freestylecyclists.org</a> so we can help you sort it out.</P>
                        <P><a href="<?=get_site_url();?>/sign-the-petition-to-reform-helmet-law/">Click here to add your name to the petition to reform bicycle helmet laws</a></p>
                        <?php
                    } else {

/*			if(have_posts()) : while(have_posts()) : the_post();
					the_content();
			endwhile; endif;  */ ?>
			                        
                        <h2>Thanks for confirming your email address with Freestyle Cyclists</h2>
                        You can update any details below. If you would like to be kept informed, upgrade your notification option below:
                        <form name="confirm">
                            <table border="0">
                                <tbody>
                                <tr><td class="leftcol">name:</td><td class="rightcol"><?=$sig->post_title;?></td></tr>
                                <tr valign="top"><td class="leftcol"><input name="fs_signature_public" class="inputc" value="y" <?=($custom['fs_signature_public'][0] === "y" ? "checked='checked'" : "");?> id="public" type="checkbox"></td><td>Show my name on this website</td></tr>
                                <tr valign="top"><td class="leftcol">email:</td><td class="rightcol"><?=$custom['fs_signature_email'][0];?></td></tr>
                                <tr valign="top"><td class="leftcol">Email me:</td><td>
                                        <input type="radio" value="" name="fs_signature_newsletter" <?=($custom['fs_signature_newsletter'][0] === "" ? "checked" : "")?>>Never<br/>
                                        <input type="radio" value="y" name="fs_signature_newsletter" <?=($custom['fs_signature_newsletter'][0] === "y" ? "checked" : "")?>>Occasionally: When something important is happening<br/>
                                        <input type="radio" value="m" name="fs_signature_newsletter" <?=($custom['fs_signature_newsletter'][0]==="m" ? "checked" : "")?>>More often: Keep me updated</td></tr>

                                <tr><td class="leftcol">Country:</td><td><select id="country" class="inputc" name="fs_signature_country" style="width: 200px;">
                                <option value="">Please select</option>
                                <?php
                                $fs_country = fs_country();
                                foreach( $fs_country as $ab => $title ) { ?>
                                    <option value="<?=$ab;?>" <?=($ab===$custom['fs_signature_country'][0] ? "selected" : "")?>><?php echo $title;?></option>
                                <?php } ?>
                                </select></td></tr>

                                <tr><td class="state leftcol <?=($custom['fs_signature_country'][0]==="AU" ? "" : "removed")?>">State:</td><td class="rightcol state <?=($custom['fs_signature_country'][0]==="AU" ? "" : "removed")?>">
                                        <select name="fs_signature_state" class="inputc">
                                <?php
                                $fs_states = fs_states();
                                foreach( $fs_states as $ab => $title ) { ?>
                                    <option value="<?=$ab;?>" <?=($custom['fs_signature_state'][0] === $ab ? "selected" : "")?>><?php echo $title;?></option>
                                <?php } ?>
                                </select></td></tr>

                                <tr><td class="leftcol">Comment:</td><td class="rightcol"><textarea id="message" name="excerpt" class="inputc"><?=$sig->post_excerpt?></textarea><br><font size="-2">Comments are subject to moderation</font></td></tr>
                                <tr><td colspan="2"><button type="button" id="confirmButton">Confirm</button></td></tr>
                                <tr><td colspan="2"><div id="ajax-loading" class="farleft"><img src="<?php echo get_site_url();?>/wp-includes/js/thickbox/loadingAnimation.gif"></div></td></tr>
                                <tr><td colspan="2"><div id="returnMessage"></div></td></tr>
                            </tbody></table>
                            <input name="action" value="confirmSignature" type="hidden">
                            <input name="id" value="<?=$sig->ID?>" type="hidden">
                            <input name="secretkey" value="<?=$secret;?>" type="hidden">
                            <?php wp_nonce_field( "fs_confirm_sig_" . $sig->ID, "fs_nonce");?>
                        </form>
                    <?php } ?>
                        <P><a href="<?=get_site_url();?>/signatures">See who has signed</a></p>
                </div><!--/row-->

        </div><!--/container-->
        

    </div>
<?php get_footer(); ?>

    <!-- Google Code for new registration Conversion Page -->
    <script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 1000718227;
    var google_conversion_language = "en";
    var google_conversion_format = "1";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "ga-OCK2vplYQk_-W3QM";
    var google_remarketing_only = false;
    /* ]]> */
    </script>
    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
        <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1000718227/?label=ga-OCK2vplYQk_-W3QM&amp;guid=ON&amp;script=0"/>
        </div>
    </noscript>
