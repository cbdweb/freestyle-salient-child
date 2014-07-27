<?php 
/*template name: Signatures*/

function signature_show_js() {
        wp_register_script( 'angular', "//ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.min.js", 'jquery' );
        wp_enqueue_script('angular');
        wp_register_script( 'angular-animate', "//ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular-animate.min.js", array( 'angular', 'jquery' ) );
        wp_enqueue_script('angular-animate');
       	wp_register_script('signatures',  get_stylesheet_directory_uri() . '/js/signatures.js', array('jquery', 'angular') );
	wp_enqueue_script('signatures');

}
add_action('wp_enqueue_scripts', 'signature_show_js');

get_header(); ?>


<?php nectar_page_header($post->ID);  ?>

<?php 
$options = get_option('salient'); 
?>

<div class="container-wrap" ng-app="signaturesApp">

	<div class="container main-content" ng-controller="signaturesCtrl">
		
		<div class="row liftup">
	
			<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
				
				<?php
					the_content();
				?>
	
			<?php endwhile; endif; 
                        
                        $rows_per_page = 15;
                        $sigs = get_sigs( 0, $rows_per_page ); // first lot of sigs are loaded with the page
                        ?>
                        <script type="text/javascript">
                            _sigs = <?=json_encode($sigs)?>;
                            <?php
                            global $wpdb;
                            $query = $wpdb->prepare('select count(*) from ' . $wpdb->posts . ' where post_type="fs_signature" and post_status="private"', '' );
                            $pages = $wpdb->get_col( $query );
                            $pages = floor ( ($pages[0] + 0.9999) / $rows_per_page ) + 1;
                            if(!$pages) $pages = 1;
                            $data = array('pages'=>$pages);
                            $data['rows_per_page'] = $rows_per_page;
                            ?>
                            _data = <?=json_encode($data)?>;
                        </script>
                        <table id="signatures" border="0" width="90%">
                            <tbody>
                                <tr><th width="120">Name</th><th width="100">Location</th><th>Date</th>
                                <?php if(current_user_can('moderate_comments')) { ?>
                                    <th>Admin</th>
                                <?php } ?>
                                <th>Comment</th></tr>
                                <tr ng-repeat="sig in sigs">
                                    <td>{{sig.name}}</td>
                                    <td>{{sig.location}}</td>
                                    <td>{{sig.date}}</td>
                                    <?php if(current_user_can('moderate_comments')) { ?>
                                    <td><a ng-hide="sig.moderate==='y' || sig.comment===''" ng-click="moderate(sig)" href="#">Approve</a><span ng-hide="sig.moderate==='y' || sig.comment===''"> | </span>
                                        <a ng-hide="sig.comment===''" href="<?=get_site_url();?>/wp-admin/post.php?post={{sig.id}}&action=edit">Edit</a></td>
                                    <?php } ?>
                                    <td>{{sig.comment}}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div id="ajax-loading" ng-class="{'farleft':!showLoading}"><img src="<?php echo get_site_url();?>/wp-includes/js/thickbox/loadingAnimation.gif"></div>
                        
                        <div>
                            <a href="<?=get_site_url();?>/sign-the-petition-to-reform-helmet-law/">Click here to sign this petition</a>
                        </div>
                    <?php
// pagination adapted from http://sgwordpress.com/teaches/how-to-add-wordpress-pagination-without-a-plugin/                    
                    ?>
                        <div ng-hide="data.pages===1" class="pagination"><span>Page {{paged}} of {{data.pages}}</span>
                            <a ng-show="paged>2 && paged > range+1 && showitems<data.pages" ng-click="gotoPage(1)">&laquo; First</a>
                            <a ng-show="paged>1 && showitems<data.pages" ng-click='gotoPage(paged-1)'>&lsaquo; Previous</a>

                            <span ng-show='data.pages!==1' ng-repeat='i in pagearray'>
                                <span ng-show='paged===i' class="current">{{i}}</span>
                                <a ng-hide="paged===i" ng-click="gotoPage(i)" class="inactive">{{i}}</a>
                            </span>

                            <a ng-show='paged<data.pages && showitems<data.pages' onclick='gotoPage(paged+1)'>Next &rsaquo;</a>
                            <a ng-show='paged<data.pages-1 && paged+range-1<data.pages && showitems < data.pages' ng-click='gotoPage(data.pages)'>Last &raquo;</a>
                        </div>

                </div><!--/row-->

        </div><!--/container-->

    </div>
<?php get_footer(); ?>