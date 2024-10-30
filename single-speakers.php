<?php get_header(); ?>

<?php the_post(); ?>

<div id="container">
	<div id="content" role="main">
		<h1 class="title"><?php the_title(); ?></h1>
	
		<div id="speaker_image_section">
			<!--<img src="<?php echo get_post_meta($post->ID, 'speaker_img', true); ?>">-->
			<div><?php the_post_thumbnail( 'medium' ) ?></div>
			<div><?php speaker_url_link() ?> | <?php speaker_twitter_link() ?></div>
			<div><?php speaker_presentation_link() ?></div>
		</div>
		
		<div id="speaker_info"><?php speaker_bio() ?></div>

		<div id="speaker_session_info">
			<div id="speaker_session_title"><?php speaker_presentation_title() ?></div>
			<?php speaker_presentation_description() ?>
		</div>
	</div>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
