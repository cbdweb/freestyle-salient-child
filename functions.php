<?php
add_filter( 'the_content', 'sign_up' );

function sign_up ( $content ) { 
    if ( is_front_page() || get_the_title()==="Take Action" || get_the_title()==="Membership Levels" || get_the_title()==="Membership Checkout"
        ) return $content;
    return $content . 
        "<div class='sign_up'>" . 
        "<h3>Sign up to support helmet law reform</h3>" .
        fs_theme_sign() .
        "</div>" .
        "<div>" .
        "<a href='/confirm/'>Update your email preferences</a>" .
        "</div>";
}

function my_pmpro_default_country($default)
	{	
		return "AU";
	}
add_filter("pmpro_default_country", "my_pmpro_default_country");

function xyz_filter_wp_mail_from($email){
return "info@freestylecyclists.org";
}
add_filter("wp_mail_from", "xyz_filter_wp_mail_from");

function fs_theme_sign () {
    global $add_signature_register_script;
    $add_signature_register_script = true;

    $narrow = true;

    ob_start() ?>

    <form name="register">
        <table border="0">
            <tbody>
            <tr><td class="leftcol">name:</td><td class="rightcol"><input<?=($narrow || $popup) ? " class='smallinput'" : "";?> type="text" name="title" id="name<?=($popup ? "_popup" : "");?>"></td></tr>
            <tr valign="top"><td class="leftcol"><input name="fs_signature_public" class="inputc" value="y" checked="checked" id="public<?=($popup ? "_popup" : "");?>" type="checkbox"></td><td>Show my name on this website</td></tr>
            <tr valign="top"><td class="leftcol">email:</td><td class="rightcol"><input type="email"<?=($narrow || $popup) ? " class='smallinput'" : "";?> name="fs_signature_email" id="email<?=($popup ? "_popup" : "");?>" title="email address"><br>
                    <div class="smallfont">An email will be sent to you to confirm this address, to ensure the integrity of your registration.</div></td></tr>
            <tr valign="top"><td class="leftcol"><input name="fs_signature_newsletter" class="inputc" value="y" checked="checked" id="newsletter" type="checkbox"></td><td class="medfont">Send me an occasional email if something really important is happening.</td></tr>

            <tr><td class="leftcol">Country:</td>
                <td class="rightcol"><select id="country<?=($popup ? "_popup" : "");?>"<?=($narrow || $popup) ? " class='smallinput'" : "";?> name="fs_signature_country">
                        <option value="" selected="selected">Please select</option>
                        <?php
                        $fs_country = fs_country();
                        foreach( $fs_country as $ab => $title ) { ?>
                            <option value="<?=$ab;?>"><?php echo $title;?></option>
                        <?php } ?>
                    </select></td></tr>

            <tr><td class="state<?=($popup ? "_popup" : "");?> leftcol removed">State:</td>
                <td class="rightcol state<?=($popup ? "_popup" : "");?> removed">
                    <select name="fs_signature_state"<?=($narrow || $popup) ? " class='smallinput'" : "";?>>
                        <?php
                        $fs_states = fs_states();
                        foreach( $fs_states as $ab => $title ) { ?>
                            <option value="<?=$ab;?>"><?php echo $title;?></option>
                        <?php } ?>
                    </select></td></tr>
            <tr><td colspan="2"><?php do_action( 'anr_captcha_form_field' ); ?></td></tr>
            <tr><td class="leftcol"><input id="simpleTuring<?=($popup ? "_popup" : "");?>" name="areYouThere" type="checkbox" value="y" class="inputc"></td><td class="medfont">Tick this box to show you are not a robot</td></tr>
            <tr><td class="leftcol">Comment:</td><td class="rightcol"><textarea name="excerpt" class="inputc"></textarea><br><div class="smallfont">Comments are subject to moderation</div></td></tr>
            <tr><td colspan="2"><button type="button" id="saveButton<?=($popup ? "_popup" : "");?>">Save</button></td></tr>
            <tr><td colspan="2"><div id="ajax-loading<?=($popup ? "_popup" : "");?>" class="farleft"><img src="<?php echo get_site_url();?>/wp-includes/js/thickbox/loadingAnimation.gif"></div></td></tr>
            <tr><td colspan="2"><div id="returnMessage<?=($popup ? "_popup" : "");?>"></div></td></tr>
            </tbody></table>
        <input name="action" value="newSignature" type="hidden">
        <?php wp_nonce_field( "fs_new_sig", "fs_nonce");?>
    </form>
    <?php return ob_get_clean();
}