<?php
/**
 * Dashboard Widget Class
 *
 * Handles the display of Adsterra statistics in the WordPress dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Adsterra_Editor_Dashboard {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
	}

	/**
	 * Register the dashboard widget.
	 */
	public function add_dashboard_widgets() {
		wp_add_dashboard_widget(
			'adsterra_editor_dashboard_widget',
			__( 'Adsterra Statistics (NeoPunto)', 'adsterra-editor' ),
			array( $this, 'render_dashboard_widget' )
		);
	}

	/**
	 * Render the widget content.
	 */
	public function render_dashboard_widget() {
		$options = get_option( 'adsterra_editor_settings' );
		$api_token = isset( $options['adsterra_api_token'] ) ? trim( $options['adsterra_api_token'] ) : '';

		if ( empty( $api_token ) ) {
			echo '<p>' . sprintf( 
				__( 'Please enter your API Token in the <a href="%s">settings page</a> to view statistics.', 'adsterra-editor' ),
				esc_url( admin_url( 'options-general.php?page=adsterra-editor' ) )
			) . '</p>';
			return;
		}

		// Check for cached data
		$stats = get_transient( 'adsterra_editor_stats' );

		if ( false === $stats ) {
			$stats = $this->fetch_data( $api_token );
			if ( ! is_wp_error( $stats ) ) {
				set_transient( 'adsterra_editor_stats', $stats, 15 * MINUTE_IN_SECONDS );
			}
		}

		if ( is_wp_error( $stats ) ) {
			echo '<p class="adsterra-error">' . esc_html( $stats->get_error_message() ) . '</p>';
			return;
		}

		$this->display_stats( $stats );
	}

	/**
	 * Fetch data from Adsterra API.
	 *
	 * @param string $api_token The API Token.
	 * @return array|WP_Error Stats array or WP_Error.
	 */
	private function fetch_data( $api_token ) {
		// Fetch Stats (Today)
		$today = date( 'Y-m-d' );
		$stats_url = add_query_arg(
			array(
				'start_date'  => $today,
				'finish_date' => $today,
			),
			'https://api3.adsterratools.com/publisher/stats.json'
		);

		$args = array(
			'headers' => array(
				'X-API-Key' => $api_token,
				'Accept'    => 'application/json',
			),
		);

		$response = wp_remote_get( $stats_url, $args );
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$body          = wp_remote_retrieve_body( $response );
		$data          = json_decode( $body, true );

		if ( 200 !== $response_code ) {
			// Adsterra error messages might be in 'message' or 'errors'
			$msg = __( 'API Error', 'adsterra-editor' );
			if ( isset( $data['message'] ) ) {
				$msg .= ': ' . $data['message'];
			} elseif ( isset( $data['detail'] ) ) {
				$msg .= ': ' . $data['detail'];
			} else {
				$msg .= ' (' . $response_code . ')';
			}
			return new WP_Error( 'api_error', $msg );
		}

		if ( empty( $data ) || ! is_array( $data ) ) {
			return new WP_Error( 'api_error', __( 'Invalid response from Adsterra API.', 'adsterra-editor' ) );
		}

		// Adsterra returns a list of items inside 'items' usually, or just a list?
		// Based on common patterns and lack of explicit doc on structure details in the blog,
		// let's assume the root is an array of objects (one per placement/date/group) 
		// OR it has a key like 'items'.
		// The blog PHP example showed: $response->getBody();
		
		// Let's assume it returns a list of objects if successful.
		// If 'items' key exists, use it.
		$items = $data;
		if ( isset( $data['items'] ) ) {
			$items = $data['items'];
		} elseif ( isset( $data['result'] ) ) {
             // Hilltopads used result, maybe they are similar? Unlikely to be exact same.
			$items = $data['result'];
		}

        // Ensure $items is iterable
        if ( ! is_array( $items ) ) {
             // Maybe it's a single object?
             $items = array( $items );
        }

		$impressions = 0;
		$clicks      = 0;
		$revenue     = 0.0;
        $currency    = 'USD'; // Adsterra is mainly USD.

		foreach ( $items as $item ) {
            if ( ! is_array( $item ) && ! is_object( $item ) ) {
                continue;
            }
            $item = (array) $item;

			if ( isset( $item['impressions'] ) ) {
				$impressions += (int) $item['impressions'];
			}
			if ( isset( $item['clicks'] ) ) {
				$clicks += (int) $item['clicks'];
			}
			if ( isset( $item['revenue'] ) ) {
				$revenue += (float) $item['revenue'];
			}
            // Try to find currency if available, usually not in stats response, but revenue is in USD.
		}

		return array(
			'impressions' => $impressions,
			'clicks'      => $clicks,
			'revenue'     => $revenue,
            'currency'    => $currency,
		);
	}

	/**
	 * Display the stats HTML.
	 *
	 * @param array $stats Stats data.
	 */
	private function display_stats( $stats ) {
		?>
		<div class="adsterra-editor-stats">
			<style>
				.adsterra-editor-stats table { width: 100%; text-align: left; }
				.adsterra-editor-stats th { color: #646970; font-weight: 400; padding: 5px 0; }
				.adsterra-editor-stats td { font-weight: 600; font-size: 1.2em; padding: 5px 0; }
				.adsterra-balance { color: #2271b1; font-size: 1.5em !important; }
			</style>
			<table>
				<tbody>
					<!-- Balance Removed as it is not available in Stats API -->
					<tr>
						<th><?php esc_html_e( 'Today\'s Impressions', 'adsterra-editor' ); ?></th>
						<td><?php echo esc_html( number_format_i18n( $stats['impressions'] ) ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Today\'s Clicks', 'adsterra-editor' ); ?></th>
						<td><?php echo esc_html( number_format_i18n( $stats['clicks'] ) ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Today\'s Revenue', 'adsterra-editor' ); ?></th>
						<td><?php echo esc_html( '$' . number_format( $stats['revenue'], 4 ) ); ?></td>
					</tr>
				</tbody>
			</table>
			<p class="description" style="text-align:right; margin-top: 10px;">
				<small><?php esc_html_e( 'Data cached for 15 mins.', 'adsterra-editor' ); ?></small>
			</p>
		</div>
		<?php
	}
}
