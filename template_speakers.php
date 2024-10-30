<?php
/*
Template Name: Speakers Page
*/
?>

<?php get_header(); ?>

<?php the_post(); ?>
		
	<div id="container">
		<div id="content" role="main">
			<h1 class="title">Speakers</h1>
			<?php $loop = new WP_Query( array( 'post_type' => 'speakers', 'posts_per_page' => 50, 'orderby' => title, 'order' => ASC) ); ?>
			<div id="speakers">
			<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
				<?php echo "<a name=\"".str_replace(' ', '', get_the_title())."\"></a>" ?>
				
				<div id="ind_speaker" style="clear: both; padding-top: 10px;">
					<div id="speaker_image_section">
						<div><?php the_post_thumbnail() ?></div>
						<div><?php speaker_url_link() ?> | <?php speaker_twitter_link() ?></div>
						<div><?php speaker_presentation_link() ?></div>
					</div>
					<div id="speaker_info">
						<div id="speaker_info_title"><strong><a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a></strong></div>
						<?php speaker_bio() ?>
					</div>
				</div>
			<?php endwhile; ?>
			</div>
		</div><!-- #content -->
	</div><!-- #container -->
	
<?php get_sidebar(); ?>

<?php get_footer(); ?>
	