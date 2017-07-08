<?php
class CPG_Widget extends WP_Widget {

    function __construct() {
    	parent::__construct(
	        'cpg_search_widget',
	        __('Color Search', 'cpg' ),
	        array(
	            'description' => __( 'Shows a list with predefined colors, linking to a page where images are filtered by the picked color.', 'cpg' )
	        )

	    );
    }

    function form( $instance ) {
    	$defaults = array(
	        'title' => __('Color Search', 'cpg')
	    );
	    $title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : $defaults['title'];
	?>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'title' ); ?>">
	        	<?php _e('Title', 'cpg'); ?>:
        	</label>
	        <input
	        	class="widefat"
	        	type="url"
	        	id="<?php echo $this->get_field_id( 'title' ); ?>"
	        	name="<?php echo $this->get_field_name( 'title' ); ?>"
	        	value="<?php echo esc_attr( $title ); ?>"
        	/>
	    </p>
	<?php
    }

    function update( $new_instance, $old_instance ) {
    	$instance = $old_instance;
	    $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
	    return $instance;
    }

    function widget( $args, $instance ) {
	    if ( is_active_widget( false, false, 'cpg_search_widget', true ) ) {
	    	$predefined_colors = cpg_return_colors();
	?>
		<div class="widget cpg-widget">
			<h2 class="cpg-widget__title"><?php echo $instance['title']; ?></h2>
			<ul class="cpg-widget__color-list">
			<?php
				foreach ($predefined_colors as $name => $code) {
			?>
				<li class="cpg-widget__color-item">
					<a
						href="<?php echo esc_url( rtrim(get_bloginfo( 'url' ), '/') . '/color/' . $name .'/'); ?>"
						class="cpg-widget__color-link"
						style="background-color: #<?php echo esc_attr( $code ); ?>"
						data-title="<?php echo esc_attr( str_replace('-', ' ', $name) ); ?>"
					>
						<?php echo esc_html($name); ?>
					</a>
				</li>
			<?php } ?>
			</ul>
		</div>
	<?php
	    }
    }

}

function cpg_shortcode_is_present($shortcode = '') {
    $post_to_check = get_post(get_the_ID());
    $found = false;
    if (!$shortcode) {
        return $found;
    }
    if ( stripos($post_to_check->post_content, '[' . $shortcode) !== false ) {
        $found = true;
    }
    return $found;
}

// Register and load the widget
function cpg_load_widget() {
	register_widget( 'CPG_Widget' );
}
add_action( 'widgets_init', 'cpg_load_widget' );
