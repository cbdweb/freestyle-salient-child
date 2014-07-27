<?php 
/*template name: Sign*/

acf_form_head();

get_header(); ?>


<?php nectar_page_header($post->ID);  ?>

<?php 
$options = get_option('salient'); 
?>

<div class="container-wrap">

	<div class="container main-content">
		
		<div class="row">
	
			<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
				
				<?php
					the_content();
				?>
	
			<?php endwhile; endif; 
			
				$options = array(
					'id'=>'sign',
					'post_id'=>'new_post',
					'new_post'=>array(
						'post_status'=>'draft',
						'post_type'=>'signature',
					),
					'post_title'=>true,
					'submit_value'=>'Sign up',
				);
				acf_form($options); 
			?>
				
	
		</div><!--/row-->
		
	</div><!--/container-->

</div>
<?php get_footer(); ?>