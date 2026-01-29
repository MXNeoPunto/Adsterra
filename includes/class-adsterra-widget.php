<?php
/**
 * Widget Class
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Adsterra_Editor_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'adsterra_editor_widget', // Base ID
			esc_html__( 'Adsterra Editor Banner', 'adsterra-editor' ), // Name
			array( 'description' => esc_html__( 'Displays an Adsterra banner from your saved zones.', 'adsterra-editor' ) ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$slot_id = ! empty( $instance['slot_id'] ) ? intval( $instance['slot_id'] ) : 1;
		$options = get_option( 'adsterra_editor_settings' );
		$key     = 'ad_zone_' . $slot_id;

		if ( empty( $options[ $key ] ) ) {
			return;
		}

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		
		echo '<!-- Adsterra Editor Widget Zone ' . esc_html( $slot_id ) . ' -->';
		echo $options[ $key ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Intended for script output
		
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title   = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$slot_id = ! empty( $instance['slot_id'] ) ? intval( $instance['slot_id'] ) : 1;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'adsterra-editor' ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'slot_id' ) ); ?>"><?php esc_attr_e( 'Select Ad Zone:', 'adsterra-editor' ); ?></label> 
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'slot_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'slot_id' ) ); ?>">
				<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $slot_id, $i ); ?>><?php echo sprintf( esc_html__( 'Zone #%d', 'adsterra-editor' ), $i ); ?></option>
				<?php endfor; ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title']   = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['slot_id'] = ( ! empty( $new_instance['slot_id'] ) ) ? intval( $new_instance['slot_id'] ) : 1;

		return $instance;
	}

}
