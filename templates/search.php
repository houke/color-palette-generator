<?php get_header(); ?>

<div class="c-search__row">
	<div class="c-search__content">
		<div class="c-search__results">
			<?php
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						$cpg_img = wp_get_attachment_image_src( get_the_ID(), 'thumbnail' );
						$cpg_link = get_permalink( get_the_ID() );
			?>
				<div class="c-search__result">
					<a href="<?php echo $cpg_link; ?>">
						<img src="<?php echo $cpg_img[0]; ?>" alt="<?php echo get_the_title(); ?>" />
						<span><?php echo get_the_title(); ?></span>
					</a>
				</div>
			<?php
					}
					echo get_the_posts_pagination();
				} else {
					echo '<h2>No artworks found for this color...</h2>';
				}
			?>
		</div>
	</div>

	<div class="c-search__sidebar">
		<?php
			the_widget( 'CPG_Widget',
				array(
			        'colorpage' => get_bloginfo( 'url' ),
			        'title' => __('Color Search', 'cpg')
			    )
			);
		?>
	</div>
</div>

<?php get_footer(); ?>
