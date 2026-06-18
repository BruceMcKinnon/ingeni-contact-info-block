<?php
/**
 * Render callback for the Contact Info block.
 *
 * Reads global contact data from the site option, applies any per-instance
 * overrides, renders only the toggled-on fields, and outputs LD+JSON
 * structured data for phone numbers and addresses.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 */

$show_phone   = isset( $attributes['showPhone'] ) ? $attributes['showPhone'] : false;
$show_phone2   = isset( $attributes['showPhone2'] ) ? $attributes['showPhone2'] : false;
$show_email   = isset( $attributes['showEmail'] ) ? $attributes['showEmail'] : false;
$show_email2   = isset( $attributes['showEmail2'] ) ? $attributes['showEmail2'] : false;

$show_street_address = isset( $attributes['showStreetAddress'] ) ? $attributes['showStreetAddress'] : false;
$show_town = isset( $attributes['showTown'] ) ? $attributes['showTown'] : false;
$show_state = isset( $attributes['showState'] ) ? $attributes['showState'] : false;
$show_zip = isset( $attributes['showZipCode'] ) ? $attributes['showZipCode'] : false;
$show_country = isset( $attributes['showCountry'] ) ? $attributes['showCountry'] : false;
$show_postal_addr = isset( $attributes['showPostalAddress'] ) ? $attributes['showPostalAddress'] : false;

$show_any_address = 0;
$show_any_address = ($show_street_address || $show_town || $show_state || $show_zip || $show_country || $show_postal_addr);

$show_icons_contact = isset( $attributes['showIconsContact'] ) ? $attributes['showIconsContact'] : false;
$show_icons_address = isset( $attributes['showIconsAddress'] ) ? $attributes['showIconsAddress'] : false;

$layout       = isset( $attributes['layout'] ) ? $attributes['layout'] : 'stacked';

$override_phone          = ! empty( $attributes['overridePhone'] ) ? $attributes['overridePhone'] : '';
$override_email          = ! empty( $attributes['overrideEmail'] ) ? $attributes['overrideEmail'] : '';
$override_street_address = ! empty( $attributes['overrideStreetAddress'] ) ? $attributes['overrideStreetAddress'] : '';
$override_town           = ! empty( $attributes['overrideTown'] ) ? $attributes['overrideTown'] : '';
$override_state          = ! empty( $attributes['overrideState'] ) ? $attributes['overrideState'] : '';
$override_zip_code       = ! empty( $attributes['overrideZipCode'] ) ? $attributes['overrideZipCode'] : '';
$override_country       = ! empty( $attributes['overrideCountry'] ) ? $attributes['overrideCountry'] : '';

$data = function_exists( 'ingeni_contact_get_all' ) ? ingeni_contact_get_all() : array();

$phone          = $override_phone ? $override_phone : ( isset( $data['phone'] ) ? $data['phone'] : '' );
$phone2         = $data['phone2'];
$email_addr     = $override_email ? $override_email : ( isset( $data['email'] ) ? $data['email'] : '' );
$email_addr2    = $data['email2'];
$street_address = $override_street_address ? $override_street_address : ( isset( $data['street_address'] ) ? $data['street_address'] : '' );
$town           = $override_town ? $override_town : ( isset( $data['town'] ) ? $data['town'] : '' );
$state          = $override_state ? $override_state : ( isset( $data['state'] ) ? $data['state'] : '' );
$zip_code       = $override_zip_code ? $override_zip_code : ( isset( $data['zip_code'] ) ? $data['zip_code'] : '' );
$country        = $override_country ? $override_country : ( isset( $data['country'] ) ? $data['country'] : '' );
$postal_addr    = $data['postal_address'];

$has_phone   = ($show_phone && $phone) || ($show_phone2 && $phone2);
$has_email   = ($show_email && $email_addr) || ($show_email2 && $email_addr2);
$has_address = ( ! empty( $street_address ) || ! empty( $town ) || ! empty( $state ) || ! empty( $zip_code )  || ! empty( $postal_addr ) );


$business_name = trim($data['business_name']);
if ( !$business_name ) {
	$business_name = get_bloginfo('name');
}
$business_logo_url = trim($data['business_logo_url']);
if ( !$business_logo_url ) {
	$business_logo_url = esc_url( wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ) ); 
}
$business_type = trim($data['business_type']);


if ( ! $has_phone && ! $has_email && ! $phone2 && ! $email_addr2 && ! $has_address ) {
	return;
}

$wrapper_attributes = get_block_wrapper_attributes( array(
	'class' => 'ingeni-contact-info-block--layout-' . esc_attr( $layout ),
) );

$phone_icon   = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
$email_icon   = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>';
$address_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>';



// Build LD+JSON structured data.
$schema = array(
	'@context' => 'https://schema.org',
	'@type' => str_replace(' ','',$business_type),
	'name' => $business_name,
	'image' => $business_logo_url,
	'url' => get_bloginfo('url'),
);

if ( $phone ) {
	$schema['telephone'] = $phone;
}

if ( $email_addr ) {
	$schema['email'] = $email_addr;
}

if ( $data['lat'] && $data['lng'] ) {
	$geo_schema = array( '@type' => 'GeoCoordinates','latitude' => $data['lat'],'longitude' => $data['lng'] );
	$schema['geo'] = $geo_schema;
}

if ( $has_address ) {
	$address_schema = array(
		'@type' => 'PostalAddress',
	);
	if ( ! empty( $street_address ) ) {
		$address_schema['streetAddress'] = $street_address;
	}
	if ( ! empty( $town ) ) {
		$address_schema['addressLocality'] = $town;
	}
	if ( ! empty( $state ) ) {
		$address_schema['addressRegion'] = $state;
	}
	if ( ! empty( $zip_code ) ) {
		$address_schema['postalCode'] = $zip_code;
	}
	if ( ! empty( $country ) ) {
		$address_schema['addressCountry'] = $country;
	}
	$schema['address'] = $address_schema;
}
?>
<div <?php echo $wrapper_attributes; ?>>
	<div class="ingeni-contact-info-block__items">
		<?php if ( $has_phone ) : ?>
			<?php if ( $show_phone ) { ?>
				<div class="ingeni-contact-info-block__item ingeni-contact-info-block__item--phone">
					<?php if ( $show_icons_contact ) { ?>
						<span class="ingeni-contact-info-block__icon"><?php echo $phone_icon; ?></span>
					<?php } ?>
					<span class="ingeni-contact-info-block__value">
						<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
					</span>
				</div>
			<?php } ?>
			<?php if ( $show_phone2 ) { ?>
				<div class="ingeni-contact-info-block__item ingeni-contact-info-block__item--phone">
					<?php if ( $show_icons_contact ) { ?>
						<span class="ingeni-contact-info-block__icon"><?php echo $phone_icon; ?></span>
					<?php } ?>
					<span class="ingeni-contact-info-block__value">
						<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone2 ) ); ?>"><?php echo esc_html( $phone2 ); ?></a>
					</span>
				</div>
			<?php } ?>
			
		<?php endif; ?>

		<?php if ( $has_email ) : ?>
			<?php if ( $show_email ) { ?>
				<div class="ingeni-contact-info-block__item ingeni-contact-info-block__item--email">
					<?php if ( $show_icons_contact ) { ?>
						<span class="ingeni-contact-info-block__icon"><?php echo $email_icon; ?></span>
					<?php } ?>

					<span class="ingeni-contact-info-block__value">
						<a href="mailto:<?php echo esc_attr( $email_addr ); ?>"><?php echo esc_html( $email_addr ); ?></a>
					</span>
				</div>
			<?php } ?>
			<?php if ( $show_email2 ) { ?>
				<div class="ingeni-contact-info-block__item ingeni-contact-info-block__item--email">
					<?php if ( $show_icons_contact ) { ?>
						<span class="ingeni-contact-info-block__icon"><?php echo $email_icon; ?></span>
					<?php } ?>

					<span class="ingeni-contact-info-block__value">
						<a href="mailto:<?php echo esc_attr( $email_addr2 ); ?>"><?php echo esc_html( $email_addr2 ); ?></a>
					</span>
				</div>
			<?php } ?>			
		<?php endif; ?>

		<?php if ( $has_address && $show_any_address ) : ?>
			<div class="ingeni-contact-info-block__item ingeni-contact-info-block__item--address">
				<?php if ( $show_icons_address ) { ?>
					<span class="ingeni-contact-info-block__icon"><?php echo $address_icon; ?></span>
				<?php } ?>
				<div class="ingeni-contact-info-block__address-lines">
					<?php if ( $show_street_address ) { ?>
						<?php if ( ! empty( $street_address ) ) : ?>
							<span class="ingeni-contact-info-block__address-line"><?php echo esc_html( $street_address ); ?></span>
						<?php endif; ?>
						<?php
						if ( !$show_town) {
							$town = '';
						}
						if ( !$show_state ) {
							$state = '';
						}
						$city_parts = array_filter( array( $town, $state ) );
						if ( ! empty( $city_parts ) ) :
						?>
							<span class="ingeni-contact-info-block__address-line"><?php echo esc_html( implode( ', ', $city_parts ) ); ?></span>
						<?php endif; ?>
						<?php if ( (!empty( $zip_code )) && $show_zip ) : ?>
							<span class="ingeni-contact-info-block__address-line"><?php echo esc_html( $zip_code ); ?></span>
						<?php endif; ?>
						<?php if ( (!empty( $country )) && $show_country ) : ?>
							<span class="ingeni-contact-info-block__address-line"><?php echo esc_html( $country ); ?></span>
						<?php endif; ?>
					<?php } ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( (! empty( $postal_addr )) && $show_postal_addr ) { ?>
			<div class="ingeni-contact-info-block__item ingeni-contact-info-block__item--address">
				<?php if ( $show_icons_address ) { ?>
					<span class="ingeni-contact-info-block__icon"><?php echo $email_icon; ?></span>
				<?php } ?>
				<div class="ingeni-contact-info-block__address-lines">
					<span class="ingeni-contact-info-block__address-line"><?php echo esc_html( $postal_addr ); ?></span>
				</div>
			</div>
		<?php } ?>
		
	</div>
	<!-- Ingeni Contact Info Block JSON feed --><script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
</div>