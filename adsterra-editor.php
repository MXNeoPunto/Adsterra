<?php
/**
 * Plugin Name: Adsterra Editor
 * Plugin URI:  https://neopunto.com/plugins/adsterra-editor
 * Description: Plugin oficial para gestionar anuncios de Adsterra: banners, popunders, direct links y notificaciones.
 * Version:     1.4.0
 * Author:      NeoPunto
 * Author URI:  https://neopunto.com
 * License:     GPLv2 or later
 * Text Domain: adsterra-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ADSTERRA_EDITOR_VERSION', '1.4.0' );
define( 'ADSTERRA_EDITOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'ADSTERRA_EDITOR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load Text Domain
 */
function adsterra_editor_load_textdomain() {
	load_plugin_textdomain( 'adsterra-editor', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'adsterra_editor_load_textdomain' );

/**
 * Register Admin Settings
 */
function adsterra_editor_register_settings() {
	register_setting( 'adsterra_editor_group', 'adsterra_editor_settings', 'adsterra_editor_sanitize_settings' );

	// API Settings Section
	add_settings_section(
		'adsterra_editor_api_section',
		__( 'API Integration', 'adsterra-editor' ),
		'adsterra_editor_api_section_cb',
		'adsterra-editor'
	);

	add_settings_field(
		'adsterra_api_token',
		__( 'Adsterra API Token', 'adsterra-editor' ),
		'adsterra_editor_api_token_cb',
		'adsterra-editor',
		'adsterra_editor_api_section'
	);

	// Global Scripts Section
	add_settings_section(
		'adsterra_editor_global_section',
		__( 'Global Settings', 'adsterra-editor' ),
		'adsterra_editor_global_section_cb',
		'adsterra-editor'
	);

	add_settings_field(
		'global_scripts_behavior',
		__( 'Load Scripts On', 'adsterra-editor' ),
		'adsterra_editor_global_behavior_cb',
		'adsterra-editor',
		'adsterra_editor_global_section'
	);

	// Smart Link Section
	add_settings_section(
		'adsterra_editor_smart_link_section',
		__( 'Direct Smart Link Settings', 'adsterra-editor' ),
		'adsterra_editor_smart_link_section_cb',
		'adsterra-editor'
	);

	add_settings_field(
		'smart_link_url',
		__( 'Smart Link URL', 'adsterra-editor' ),
		'adsterra_editor_smart_link_url_cb',
		'adsterra-editor',
		'adsterra_editor_smart_link_section'
	);

	add_settings_field(
		'smart_link_frequency',
		__( 'Frequency (Clicks per Day)', 'adsterra-editor' ),
		'adsterra_editor_smart_link_freq_cb',
		'adsterra-editor',
		'adsterra_editor_smart_link_section'
	);

	add_settings_field(
		'smart_link_behavior',
		__( 'Enable On', 'adsterra-editor' ),
		'adsterra_editor_smart_link_behavior_cb',
		'adsterra-editor',
		'adsterra_editor_smart_link_section'
	);

	add_settings_field(
		'global_scripts_exclude_ids',
		__( 'Exclude Page IDs', 'adsterra-editor' ),
		'adsterra_editor_global_exclude_ids_cb',
		'adsterra-editor',
		'adsterra_editor_global_section'
	);

	add_settings_field(
		'global_scripts',
		__( 'Global Scripts (Popunder, Push, etc.)', 'adsterra-editor' ),
		'adsterra_editor_global_scripts_cb',
		'adsterra-editor',
		'adsterra_editor_global_section'
	);

	// Automatic Insertion Section
	add_settings_section(
		'adsterra_editor_auto_section',
		__( 'Automatic Insertion', 'adsterra-editor' ),
		'adsterra_editor_auto_section_cb',
		'adsterra-editor'
	);

	$auto_fields = array(
		'auto_insert_single_top'    => __( 'Start of Post (Single)', 'adsterra-editor' ),
		'auto_insert_single_middle' => __( 'Middle of Post (Single)', 'adsterra-editor' ),
		'auto_insert_single_bottom' => __( 'End of Post (Single)', 'adsterra-editor' ),
		'auto_insert_home_top'      => __( 'Blog Home - Top (Before Posts)', 'adsterra-editor' ),
		'auto_insert_home_bottom'   => __( 'Blog Home - Bottom (After Posts)', 'adsterra-editor' ),
	);

	foreach ( $auto_fields as $key => $label ) {
		add_settings_field(
			$key,
			$label,
			'adsterra_editor_auto_field_cb',
			'adsterra-editor',
			'adsterra_editor_auto_section',
			array( 'key' => $key )
		);
	}

	// Ad Zones Section
	add_settings_section(
		'adsterra_editor_zones_section',
		__( 'Ad Zones (Banners)', 'adsterra-editor' ),
		'adsterra_editor_zones_section_cb',
		'adsterra-editor'
	);

	for ( $i = 1; $i <= 5; $i++ ) {
		add_settings_field(
			'ad_zone_' . $i,
			sprintf( __( 'Ad Zone #%d', 'adsterra-editor' ), $i ),
			'adsterra_editor_zone_cb',
			'adsterra-editor',
			'adsterra_editor_zones_section',
			array( 'id' => $i )
		);
	}
}
add_action( 'admin_init', 'adsterra_editor_register_settings' );

/**
 * Sanitize Settings
 */
function adsterra_editor_sanitize_settings( $input ) {
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		foreach ( $input as $key => $val ) {
			$input[ $key ] = wp_kses_post( $val );
		}
	}
	return $input;
}

/**
 * Add Admin Menu
 */
function adsterra_editor_menu() {
	add_options_page(
		__( 'Adsterra Editor', 'adsterra-editor' ),
		__( 'Adsterra Editor', 'adsterra-editor' ),
		'manage_options',
		'adsterra-editor',
		'adsterra_editor_options_page'
	);
}
add_action( 'admin_menu', 'adsterra_editor_menu' );

/**
 * Section Callbacks
 */
function adsterra_editor_api_section_cb() {
	echo '<p>' . __( 'Enter your Adsterra API Token to view statistics in the Dashboard.', 'adsterra-editor' ) . '</p>';
}

function adsterra_editor_global_section_cb() {
	echo '<p>' . __( 'Enter scripts that should be loaded globally (e.g., in the footer), such as Popunders, Push Notifications, or Direct Link scripts.', 'adsterra-editor' ) . '</p>';
}

function adsterra_editor_smart_link_section_cb() {
	echo '<p>' . __( 'Configure a Direct Smart Link (Popunder style) that opens on click. Ideal for monetizing clicks.', 'adsterra-editor' ) . '</p>';
}

function adsterra_editor_auto_section_cb() {
	echo '<p>' . __( 'Automatically insert ads into your posts and pages without using widgets or shortcodes.', 'adsterra-editor' ) . '</p>';
}

function adsterra_editor_zones_section_cb() {
	echo '<p>' . __( 'Enter your banner ad codes here. You can display them using the shortcode [adsterra_ad id="X"], the Widget, or the Automatic Insertion settings above.', 'adsterra-editor' ) . '</p>';
}

/**
 * Field Callbacks
 */
function adsterra_editor_api_token_cb() {
	$options = get_option( 'adsterra_editor_settings' );
	$value   = isset( $options['adsterra_api_token'] ) ? $options['adsterra_api_token'] : '';
	echo '<input type="text" name="adsterra_editor_settings[adsterra_api_token]" value="' . esc_attr( $value ) . '" class="regular-text" />';
	echo '<p class="description">' . __( 'You can find your API Token in your Adsterra account settings.', 'adsterra-editor' ) . '</p>';
}

function adsterra_editor_global_behavior_cb() {
	$options = get_option( 'adsterra_editor_settings' );
	$value   = isset( $options['global_scripts_behavior'] ) ? $options['global_scripts_behavior'] : 'all';
	?>
	<select name="adsterra_editor_settings[global_scripts_behavior]">
		<option value="all" <?php selected( $value, 'all' ); ?>><?php esc_html_e( 'Everywhere (All Pages)', 'adsterra-editor' ); ?></option>
		<option value="blog_only" <?php selected( $value, 'blog_only' ); ?>><?php esc_html_e( 'Blog Only (Posts, Home, Archives)', 'adsterra-editor' ); ?></option>
	</select>
	<p class="description"><?php esc_html_e( 'Select where global scripts should run. "Blog Only" excludes WooCommerce and static pages.', 'adsterra-editor' ); ?></p>
	<?php
}

function adsterra_editor_global_exclude_ids_cb() {
	$options = get_option( 'adsterra_editor_settings' );
	$value   = isset( $options['global_scripts_exclude_ids'] ) ? $options['global_scripts_exclude_ids'] : '';
	?>
	<input type="text" name="adsterra_editor_settings[global_scripts_exclude_ids]" value="<?php echo esc_attr( $value ); ?>" class="regular-text" />
	<p class="description"><?php esc_html_e( 'Comma-separated list of Page/Post IDs to exclude (e.g., 10, 25, 100).', 'adsterra-editor' ); ?></p>
	<?php
}

function adsterra_editor_global_scripts_cb() {
	$options = get_option( 'adsterra_editor_settings' );
	$value   = isset( $options['global_scripts'] ) ? $options['global_scripts'] : '';
	echo '<textarea name="adsterra_editor_settings[global_scripts]" rows="10" cols="50" class="large-text code">' . esc_textarea( $value ) . '</textarea>';
}

function adsterra_editor_smart_link_url_cb() {
	$options = get_option( 'adsterra_editor_settings' );
	$value   = isset( $options['smart_link_url'] ) ? $options['smart_link_url'] : '';
	echo '<input type="url" name="adsterra_editor_settings[smart_link_url]" value="' . esc_attr( $value ) . '" class="large-text" placeholder="https://..." />';
}

function adsterra_editor_smart_link_freq_cb() {
	$options = get_option( 'adsterra_editor_settings' );
	$value   = isset( $options['smart_link_frequency'] ) ? intval( $options['smart_link_frequency'] ) : 1;
	?>
	<select name="adsterra_editor_settings[smart_link_frequency]">
		<option value="1" <?php selected( $value, 1 ); ?>>1 <?php esc_html_e( 'time per day', 'adsterra-editor' ); ?></option>
		<option value="2" <?php selected( $value, 2 ); ?>>2 <?php esc_html_e( 'times per day', 'adsterra-editor' ); ?></option>
		<option value="3" <?php selected( $value, 3 ); ?>>3 <?php esc_html_e( 'times per day', 'adsterra-editor' ); ?></option>
	</select>
	<?php
}

function adsterra_editor_smart_link_behavior_cb() {
	$options = get_option( 'adsterra_editor_settings' );
	$value   = isset( $options['smart_link_behavior'] ) ? $options['smart_link_behavior'] : 'disabled';
	?>
	<select name="adsterra_editor_settings[smart_link_behavior]">
		<option value="disabled" <?php selected( $value, 'disabled' ); ?>><?php esc_html_e( 'Disabled', 'adsterra-editor' ); ?></option>
		<option value="blog_only" <?php selected( $value, 'blog_only' ); ?>><?php esc_html_e( 'Blog Only (Posts & Home)', 'adsterra-editor' ); ?></option>
		<option value="all" <?php selected( $value, 'all' ); ?>><?php esc_html_e( 'Everywhere (All Pages)', 'adsterra-editor' ); ?></option>
	</select>
	<p class="description"><?php esc_html_e( 'Select "Blog Only" to avoid showing this on WooCommerce or static pages.', 'adsterra-editor' ); ?></p>
	<?php
}

function adsterra_editor_auto_field_cb( $args ) {
	$options = get_option( 'adsterra_editor_settings' );
	$key     = $args['key'];
	$current = isset( $options[ $key ] ) ? $options[ $key ] : '';

	echo '<select name="adsterra_editor_settings[' . esc_attr( $key ) . ']">';
	echo '<option value="">' . __( 'Disabled', 'adsterra-editor' ) . '</option>';
	for ( $i = 1; $i <= 5; $i++ ) {
		$selected = selected( $current, $i, false );
		echo '<option value="' . esc_attr( $i ) . '" ' . $selected . '>' . sprintf( __( 'Zone #%d', 'adsterra-editor' ), $i ) . '</option>';
	}
	echo '</select>';
}

function adsterra_editor_zone_cb( $args ) {
	$id      = $args['id'];
	$options = get_option( 'adsterra_editor_settings' );
	$key     = 'ad_zone_' . $id;
	$value   = isset( $options[ $key ] ) ? $options[ $key ] : '';
	echo '<textarea name="adsterra_editor_settings[' . esc_attr( $key ) . ']" rows="5" cols="50" class="large-text code">' . esc_textarea( $value ) . '</textarea>';
	echo '<p class="description">' . sprintf( __( 'Use shortcode: <code>[adsterra_ad id="%d"]</code>', 'adsterra-editor' ), $id ) . '</p>';
}

/**
 * Options Page HTML
 */
function adsterra_editor_options_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'adsterra_editor_group' );
			do_settings_sections( 'adsterra-editor' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Frontend: Output Global Scripts in Footer
 */
function adsterra_editor_output_global_scripts() {
	$options = get_option( 'adsterra_editor_settings' );
	
	if ( empty( $options['global_scripts'] ) ) {
		return;
	}

	$behavior = isset( $options['global_scripts_behavior'] ) ? $options['global_scripts_behavior'] : 'all';
	$excludes = isset( $options['global_scripts_exclude_ids'] ) ? $options['global_scripts_exclude_ids'] : '';

	// 1. Check Exclusions
	if ( ! empty( $excludes ) ) {
		$exclude_ids = array_map( 'intval', explode( ',', $excludes ) );
		$current_id  = get_queried_object_id();
		if ( in_array( $current_id, $exclude_ids, true ) ) {
			return; // Excluded
		}
	}

	// 2. Check Behavior
	if ( 'blog_only' === $behavior ) {
		// Allowed: Home (Blog Index), Single Post, Archives, Search.
		// Disallowed: Pages, WooCommerce (Shop, Product, Cart, Checkout).
		
		$is_blog_content = is_home() || is_front_page() || is_archive() || is_search() || is_singular( 'post' );
		
		// Strong checks for WooCommerce if active
		$is_woocommerce = false;
		if ( class_exists( 'WooCommerce' ) ) {
			$is_woocommerce = is_woocommerce() || is_cart() || is_checkout() || is_account_page();
		}

		if ( ! $is_blog_content || is_page() || $is_woocommerce ) {
			return; // Skip loading
		}
	}

	echo "<!-- Adsterra Editor Global -->\n";
	echo $options['global_scripts']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Intended for script output
	echo "\n<!-- End Adsterra Editor Global -->\n";
}
add_action( 'wp_footer', 'adsterra_editor_output_global_scripts', 100 );

/**
 * Frontend: Output Smart Link Script
 */
function adsterra_editor_output_smart_link() {
	$options = get_option( 'adsterra_editor_settings' );
	
	$url      = isset( $options['smart_link_url'] ) ? $options['smart_link_url'] : '';
	$behavior = isset( $options['smart_link_behavior'] ) ? $options['smart_link_behavior'] : 'disabled';
	$freq     = isset( $options['smart_link_frequency'] ) ? intval( $options['smart_link_frequency'] ) : 1;

	if ( empty( $url ) || 'disabled' === $behavior ) {
		return;
	}

	// Logic Check
	if ( 'blog_only' === $behavior ) {
		$is_blog_content = is_home() || is_front_page() || is_archive() || is_search() || is_singular( 'post' );
		$is_woocommerce = false;
		if ( class_exists( 'WooCommerce' ) ) {
			$is_woocommerce = is_woocommerce() || is_cart() || is_checkout() || is_account_page();
		}
		if ( ! $is_blog_content || is_page() || $is_woocommerce ) {
			return;
		}
	}
	
	$excludes = isset( $options['global_scripts_exclude_ids'] ) ? $options['global_scripts_exclude_ids'] : '';
	if ( ! empty( $excludes ) ) {
		$exclude_ids = array_map( 'intval', explode( ',', $excludes ) );
		if ( in_array( get_queried_object_id(), $exclude_ids, true ) ) {
			return;
		}
	}

	// Output JS
	?>
	<script>
	(function() {
		var url = "<?php echo esc_url_raw( $url ); ?>";
		var limit = <?php echo intval( $freq ); ?>;
		var today = new Date().toISOString().slice(0, 10); // YYYY-MM-DD
		var key = "adsterra_editor_smart_link_" + today;
		
		var count = parseInt(localStorage.getItem(key) || 0);

		if (count < limit) {
			function onClick(e) {
				count = parseInt(localStorage.getItem(key) || 0);
				if (count >= limit) {
					document.removeEventListener('click', onClick);
					return;
				}
				
				localStorage.setItem(key, count + 1);
				window.open(url, '_blank');
			}
			document.addEventListener('click', onClick);
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'adsterra_editor_output_smart_link', 101 );

/**
 * Frontend: Shortcode [adsterra_ad id="1"]
 */
function adsterra_editor_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'id' => '1',
	), $atts, 'adsterra_ad' );

	$id      = intval( $atts['id'] );
	$options = get_option( 'adsterra_editor_settings' );
	$key     = 'ad_zone_' . $id;

	if ( ! empty( $options[ $key ] ) ) {
		return $options[ $key ]; // Returns raw HTML/JS
	}

	return '';
}
add_shortcode( 'adsterra_ad', 'adsterra_editor_shortcode' );

/**
 * Frontend: Auto Insert - Single Post (Top/Bottom)
 */
function adsterra_editor_inject_content( $content ) {
	if ( is_singular( 'post' ) && in_the_loop() && is_main_query() ) {
		$options = get_option( 'adsterra_editor_settings' );
		
		// Top Injection
		$top_zone = isset( $options['auto_insert_single_top'] ) ? $options['auto_insert_single_top'] : '';
		if ( ! empty( $top_zone ) ) {
			$key = 'ad_zone_' . intval( $top_zone );
			if ( ! empty( $options[ $key ] ) ) {
				$ad_html = "\n<!-- Adsterra Editor Start of Post -->\n<div class='adsterra-ad-top'>\n" . $options[ $key ] . "\n</div>\n<!-- End Adsterra Editor Start of Post -->\n";
				$content = $ad_html . $content;
			}
		}

		// Middle Injection
		$middle_zone = isset( $options['auto_insert_single_middle'] ) ? $options['auto_insert_single_middle'] : '';
		if ( ! empty( $middle_zone ) ) {
			$key = 'ad_zone_' . intval( $middle_zone );
			if ( ! empty( $options[ $key ] ) ) {
				$ad_html = "\n<!-- Adsterra Editor Middle of Post -->\n<div class='adsterra-ad-middle'>\n" . $options[ $key ] . "\n</div>\n<!-- End Adsterra Editor Middle of Post -->\n";
				
				$paragraphs = explode( '</p>', $content );
				$count      = count( $paragraphs );
				
				if ( $count > 2 ) {
					// Insert after the middle paragraph
					$midpoint = floor( $count / 2 );
					
					// Reconstruct content with ad inserted
					$new_content = '';
					foreach ( $paragraphs as $index => $paragraph ) {
						$new_content .= $paragraph;
						// explode removes the delimiter, so add it back if it's not the last empty element
						if ( $index < $count - 1 ) {
							$new_content .= '</p>';
						}
						
						if ( $index === (int) $midpoint - 1 ) { // -1 because index starts at 0
							$new_content .= $ad_html;
						}
					}
					$content = $new_content;
				}
			}
		}

		// Bottom Injection
		$bottom_zone = isset( $options['auto_insert_single_bottom'] ) ? $options['auto_insert_single_bottom'] : '';
		if ( ! empty( $bottom_zone ) ) {
			$key = 'ad_zone_' . intval( $bottom_zone );
			if ( ! empty( $options[ $key ] ) ) {
				$ad_html = "\n<!-- Adsterra Editor End of Post -->\n<div class='adsterra-ad-bottom'>\n" . $options[ $key ] . "\n</div>\n<!-- End Adsterra Editor End of Post -->\n";
				$content = $content . $ad_html;
			}
		}
	}
	return $content;
}
add_filter( 'the_content', 'adsterra_editor_inject_content' );

/**
 * Frontend: Auto Insert - Blog Home (Loop Start)
 */
function adsterra_editor_loop_start( $query ) {
	if ( $query->is_home() && $query->is_main_query() ) {
		$options = get_option( 'adsterra_editor_settings' );
		$zone_id = isset( $options['auto_insert_home_top'] ) ? $options['auto_insert_home_top'] : '';
		
		if ( ! empty( $zone_id ) ) {
			$key = 'ad_zone_' . intval( $zone_id );
			if ( ! empty( $options[ $key ] ) ) {
				echo "\n<!-- Adsterra Editor Blog Top -->\n<div class='adsterra-ad-home-top'>\n";
				echo $options[ $key ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo "\n</div>\n";
			}
		}
	}
}
add_action( 'loop_start', 'adsterra_editor_loop_start' );

/**
 * Frontend: Auto Insert - Blog Home (Loop End)
 */
function adsterra_editor_loop_end( $query ) {
	if ( $query->is_home() && $query->is_main_query() ) {
		$options = get_option( 'adsterra_editor_settings' );
		$zone_id = isset( $options['auto_insert_home_bottom'] ) ? $options['auto_insert_home_bottom'] : '';
		
		if ( ! empty( $zone_id ) ) {
			$key = 'ad_zone_' . intval( $zone_id );
			if ( ! empty( $options[ $key ] ) ) {
				echo "\n<!-- Adsterra Editor Blog Bottom -->\n<div class='adsterra-ad-home-bottom'>\n";
				echo $options[ $key ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo "\n</div>\n";
			}
		}
	}
}
add_action( 'loop_end', 'adsterra_editor_loop_end' );

/**
 * Register Widget
 */
function adsterra_editor_register_widget() {
	require_once ADSTERRA_EDITOR_PATH . 'includes/class-adsterra-widget.php';
	register_widget( 'Adsterra_Editor_Widget' );
}
add_action( 'widgets_init', 'adsterra_editor_register_widget' );

/**
 * Init Dashboard Widget
 */
function adsterra_editor_init_dashboard() {
	if ( is_admin() ) {
		require_once ADSTERRA_EDITOR_PATH . 'includes/class-adsterra-dashboard.php';
		new Adsterra_Editor_Dashboard();
	}
}
add_action( 'admin_init', 'adsterra_editor_init_dashboard' );
