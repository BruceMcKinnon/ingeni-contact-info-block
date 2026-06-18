<?php
/**
 * Plugin Name:       Ingeni Contact Information Block
 * Description:       Store contact information globally and display selected fields on any page via a Gutenberg block. Access all data from templates via PHP helpers.
 * Version:           2026.01
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Author:            Ingeni & WordPress Telex
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ingeni-contact-info-block
 *
 * @package IngeniContactInfoBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'INGENI_CONTACT_OPTION_KEY', 'ingeni_contact_info' );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 */
if ( ! function_exists( 'ingeni_contact_block_init' ) ) {
	function ingeni_contact_block_init() {
		register_block_type( __DIR__ . '/build/' );
	}
}
add_action( 'init', 'ingeni_contact_block_init' );

/**
 * Register the settings page under Settings menu.
 */
function ingeni_contact_add_settings_page() {
	add_options_page(
		__( 'Contact Information', 'ingeni-contact-info-block' ),
		__( 'Contact Info', 'ingeni-contact-info-block' ),
		'manage_options',
		'ingeni-contact-info',
		'ingeni_contact_render_settings_page'
	);
}
add_action( 'admin_menu', 'ingeni_contact_add_settings_page' );

/**
 * Register settings and fields.
 */
function ingeni_contact_register_settings() {
	register_setting( 'ingeni_contact_settings_group', INGENI_CONTACT_OPTION_KEY, array(
		'type'              => 'array',
		'sanitize_callback' => 'ingeni_contact_sanitize_settings',
		'default'           => ingeni_contact_defaults(),
	) );

	add_settings_section(
		'ingeni_contact_main_section',
		__( 'Contact Details', 'ingeni-contact-info-block' ),
		'__return_null',
		'ingeni-contact-info'
	);

	$fields = ingeni_contact_field_definitions();
	foreach ( $fields as $key => $label ) {
		add_settings_field(
			'ingeni_contact_' . $key,
			$label,
			'ingeni_contact_render_field',
			'ingeni-contact-info',
			'ingeni_contact_main_section',
			array( 'key' => $key, 'label' => $label )
		);
	}
}
add_action( 'admin_init', 'ingeni_contact_register_settings' );

/**
 * Field definitions.
 */
function ingeni_contact_field_definitions() {
	return array(
		'phone' => __( 'Phone', 'ingeni-contact-info-block' ),
		'email' => __( 'Email', 'ingeni-contact-info-block' ),
		'street_address' => __( 'Street Address', 'ingeni-contact-info-block' ),
		'town' => __( 'Town / City', 'ingeni-contact-info-block' ),
		'state' => __( 'State / Province', 'ingeni-contact-info-block' ),
		'zip_code' => __( 'Zip / Post Code', 'ingeni-contact-info-block' ),
		'country' => __( 'Country', 'ingeni-contact-info-block' ),
		'google_analytics' => __( 'Google Analytics Code', 'ingeni-contact-info-block' ),
		'custom_js' => __( 'Custom JavaScript', 'ingeni-contact-info-block' ),
		'phone2' => __( 'Phone2', 'ingeni-contact-info-block' ),
		'email2' => __( 'Email2', 'ingeni-contact-info-block' ),
		'postal_address' => __( 'Postal Address', 'ingeni-contact-info-block' ),
		'lat'  => __( 'Latitude', 'ingeni-contact-info-block' ),
		'lng'  => __( 'Longitude', 'ingeni-contact-info-block' ),
		'map_zoom'  => __( 'Map Zoom', 'ingeni-contact-info-block' ),
		'map_pin_color_hex'  => __( 'Map Pin Colour (Hex)', 'ingeni-contact-info-block' ),
		'abn' => __( 'ABN / ACN', 'ingeni-contact-info-block' ),
		'facebook' => __( 'Facebook URL', 'ingeni-contact-info-block' ),
		'twitter' => __( 'Twitter / X URL', 'ingeni-contact-info-block' ),
		'instagram' => __( 'Instagram URL', 'ingeni-contact-info-block' ),
		'linkedin' => __( 'LinkedIn URL', 'ingeni-contact-info-block' ),
		'youtube' => __( 'YouTube URL', 'ingeni-contact-info-block' ),
		'tiktok' => __( 'TikTok URL', 'ingeni-contact-info-block' ),
		'misc_1' => __( 'Misc #1', 'ingeni-contact-info-block' ),
		'misc_2' => __( 'Misc #2', 'ingeni-contact-info-block' ),
		'misc_3' => __( 'Misc #3', 'ingeni-contact-info-block' ),
		'misc_4' => __( 'Misc #4', 'ingeni-contact-info-block' ),
		'misc_5' => __( 'Misc #5', 'ingeni-contact-info-block' ),
		'misc_6' => __( 'Misc #6', 'ingeni-contact-info-block' ),
		'misc_7' => __( 'Misc #7', 'ingeni-contact-info-block' ),
		'misc_8' => __( 'Misc #8', 'ingeni-contact-info-block' ),
		'misc_9' => __( 'Misc #9', 'ingeni-contact-info-block' ),
		'misc_10' => __( 'Misc #10', 'ingeni-contact-info-block' ),
		'google_maps_key' => __( 'Google Maps JS API key', 'ingeni-contact-info-block' ),
		'business_type' => __( 'Business Type', 'ingeni-contact-info-block' ),
		'business_logo_url' => __( 'Business Logo URL', 'ingeni-contact-info-block' ),
		'business_name' => __( 'Business Name', 'ingeni-contact-info-block' ),
	);
}


/**
 * Default values.
 */
function ingeni_contact_defaults() {
	return array(
		'phone'            => '',
		'email'            => '',
		'street_address'   => '',
		'town'             => '',
		'state'            => '',
		'zip_code'         => '',
		'country'          => 'AU',
		'google_analytics' => '',
		'custom_js'        => '',
		'phone2'           => '',
		'email2'           => '',
		'postal_address'   => '',
		'lat' => '',
		'lng' => '',
		'map_zoom' => '15',
		'map_pin_color_hex' => '',
		'abn' => '',
		'facebook' => '',
		'twitter' => '',
		'instagram' => '',
		'linkedin' => '',
		'youtube' => '',
		'tiktok' => '',
		'misc_1' => '',
		'misc_2' => '',
		'misc_3' => '',
		'misc_4' => '',
		'misc_5' => '',
		'misc_6' => '',
		'misc_7' => '',
		'misc_8' => '',
		'misc_9' => '',
		'misc_10' => '',
		'google_maps_key' => '',
		'business_type' => 'LocalBusiness',
		'business_logo_url' => '',
		'business_name' => '',
	);
}


/**
 * Sanitize settings on save.
 */
function ingeni_contact_sanitize_settings( $input ) {
	$defaults  = ingeni_contact_defaults();
	$sanitized = array();

	foreach ( array_keys( $defaults ) as $key ) {
		if ( ( 'email' === $key ) || ( 'email2' === $key ) ) {
			$sanitized[ $key ] = isset( $input[ $key ] ) ? sanitize_email( $input[ $key ] ) : '';
		} elseif ( in_array( $key, array( 'google_analytics', 'custom_js' ), true ) ) {
			// Allow script tags for GA and custom JS — store as-is but strip null bytes.
			$sanitized[ $key ] = isset( $input[ $key ] ) ? str_replace( "\0", '', $input[ $key ] ) : '';
		} else {
			$sanitized[ $key ] = isset( $input[ $key ] ) ? sanitize_text_field( $input[ $key ] ) : '';
		}
	}

	return $sanitized;
}


/**
 * Render a single settings field.
 */
function ingeni_contact_render_field( $args ) {
	$key     = $args['key'];
	$options = get_option( INGENI_CONTACT_OPTION_KEY, ingeni_contact_defaults() );
	$value   = isset( $options[ $key ] ) ? $options[ $key ] : '';
	$name    = INGENI_CONTACT_OPTION_KEY . '[' . esc_attr( $key ) . ']';

	if ( in_array( $key, array( 'google_analytics', 'custom_js' ), true ) ) {
		printf(
			'<textarea name="%s" rows="5" class="large-text code">%s</textarea>',
			esc_attr( $name ),
			esc_textarea( $value )
		);
		if ( 'google_analytics' === $key ) {
			echo '<p class="description">' . esc_html__( 'Paste your full GA script tag. It will be output in the page head automatically.', 'ingeni-contact-info-block' ) . '</p>';
		} else {
			echo '<p class="description">' . esc_html__( 'Custom JS code output before the closing body tag automatically.', 'ingeni-contact-info-block' ) . '</p>';
		}
	} else {
		printf(
			'<input type="%s" name="%s" value="%s" class="regular-text" />',
			'email' === $key ? 'email' : 'text',
			esc_attr( $name ),
			esc_attr( $value )
		);
	}
}


/**
 * Render the settings page.
 */
function ingeni_contact_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p><?php esc_html_e( 'Enter your contact information below. Use the "Contact Info" block in the editor to display selected fields on any page.', 'ingeni-contact-info-block' ); ?></p>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'ingeni_contact_settings_group' );
			do_settings_sections( 'ingeni-contact-info' );
			submit_button();
			?>
		</form>
		<hr />
		<h2><?php esc_html_e( 'Template Usage', 'ingeni-contact-info-block' ); ?></h2>
		<p><?php esc_html_e( 'Theme developers can access any field from templates:', 'ingeni-contact-info-block' ); ?></p>
		<pre style="background:#f0f0f0;padding:1em;border-radius:4px;"><code>&lt;?php echo esc_html( ingeni_contact_get( 'phone' ) ); ?&gt;
&lt;?php echo esc_html( ingeni_contact_get( 'email' ) ); ?&gt;
&lt;?php echo esc_html( ingeni_contact_get( 'street_address' ) ); ?&gt;</code></pre>
	</div>
	<?php
}


/**
 * Retrieve a specific contact field value.
 *
 * Usage: ingeni_contact_get( 'phone' )
 *
 * Available keys: phone, email, street_address, town, state, zip_code,
 *                 google_analytics, custom_js
 *
 * @param string $key The field key to retrieve.
 * @return string The field value or empty string if not set.
 */
function ingeni_contact_get( $key ) {
	$data = get_option( INGENI_CONTACT_OPTION_KEY, ingeni_contact_defaults() );

	if ( isset( $data[ $key ] ) ) {
		return $data[ $key ];
	}

	return '';
}


/**
 * Retrieve all contact data as an associative array.
 *
 * @return array All stored contact fields.
 */
function ingeni_contact_get_all() {
	return get_option( INGENI_CONTACT_OPTION_KEY, ingeni_contact_defaults() );
}


/**
 * Output Google Analytics code in wp_head if available.
 */
function ingeni_contact_output_ga() {
	$ga_code = ingeni_contact_get( 'google_analytics' );
	if ( ! empty( $ga_code ) ) {
		echo $ga_code;
	}
}
add_action( 'wp_head', 'ingeni_contact_output_ga' );

/**
 * Output custom JS code in wp_footer if available.
 */
function ingeni_contact_output_custom_js() {
	$custom_js = ingeni_contact_get( 'custom_js' );
	if ( ! empty( $custom_js ) ) {

		echo $custom_js;
	}
}
add_action( 'wp_footer', 'ingeni_contact_output_custom_js' );

/**
 * Register REST API endpoint so the block editor can read global contact data.
 */
function ingeni_contact_register_rest_route() {
	register_rest_route( 'ingeni-contact/v1', '/info', array(
		'methods'             => 'GET',
		'callback'            => 'ingeni_contact_rest_get_info',
		'permission_callback' => function () {
			return current_user_can( 'edit_posts' );
		},
	) );
}
add_action( 'rest_api_init', 'ingeni_contact_register_rest_route' );

/**
 * REST callback to return global contact data.
 */
function ingeni_contact_rest_get_info() {
	return rest_ensure_response( ingeni_contact_get_all() );
}

