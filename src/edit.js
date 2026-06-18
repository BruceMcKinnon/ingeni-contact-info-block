import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import {
	useBlockProps,
	InspectorControls,
	BlockControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	PanelRow,
	ToggleControl,
	ToolbarGroup,
	ToolbarButton,
	Placeholder,
	Spinner,
	ExternalLink,
	TextControl,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import './editor.scss';

const PHONE_ICON = (
	<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
		<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
	</svg>
);

const EMAIL_ICON = (
	<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
		<rect x="2" y="4" width="20" height="16" rx="2"/>
		<path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
	</svg>
);

const ADDRESS_ICON = (
	<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
		<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
		<circle cx="12" cy="10" r="3"/>
	</svg>
);


export default function Edit( { attributes, setAttributes } ) {
	const {
		showPhone, showPhone2,
		showEmail, showEmail2,
		showStreetAddress, showPostalAddress,
		showTown, showState, showZipCode, showCountry,
		layout,
		overridePhone, overridePhone2,
		overrideEmail, overrideEmail2,
		overrideStreetAddress, overridePostalAddress,
		overrideTown,
		overrideState,
		overrideZipCode, overrideCountry,
		showIconsContact, showIconsAddress
	} = attributes;

	const [ contactData, setContactData ] = useState( null );
	const [ isLoading, setIsLoading ] = useState( true );

	useEffect( () => {
		apiFetch( { path: '/ingeni-contact/v1/info' } )
			.then( ( data ) => {
				setContactData( data );
				setIsLoading( false );
			} )
			.catch( () => {
				setContactData( {} );
				setIsLoading( false );
			} );
	}, [] );

	const blockProps = useBlockProps( {
		className: `ingeni-contact-info-block--layout-${ layout }`,
	} );

	if ( isLoading ) {
		return (
			<div { ...blockProps }>
				<Placeholder icon="id-alt" label={ __( 'Contact Info', 'ingeni-contact-info-block' ) }>
					<Spinner />
				</Placeholder>
			</div>
		);
	}

	const globalPhone         = contactData?.phone || '';
	const globalPhone2         = contactData?.phone2 || '';
	const globalEmail         = contactData?.email || '';
	const globalEmail2        = contactData?.email2 || '';
	const globalStreetAddress = contactData?.street_address || '';
	const globalTown          = contactData?.town || '';
	const globalState         = contactData?.state || '';
	const globalZipCode       = contactData?.zip_code || '';
	const globalCountry       = contactData?.country || '';
	const globalPostalAddress = contactData?.postal_address || '';

	const phone         = overridePhone || globalPhone;
	const phone2         = overridePhone2 || globalPhone2;
	const email         = overrideEmail || globalEmail;
	const email2         = overrideEmail2 || globalEmail2;
	const streetAddress = overrideStreetAddress || globalStreetAddress;
	const town          = overrideTown || globalTown;
	const state         = overrideState || globalState;
	const zipCode       = overrideZipCode || globalZipCode;
	const country       = overrideCountry || globalCountry;

	const hasPhone   = showPhone && phone;
	const hasPhone2  = showPhone2 && phone2;
	const hasEmail   = showEmail && email;
	const hasEmail2   = showEmail2 && email2;
	const hasAddress = ( (showStreetAddress && streetAddress) || town || state || zipCode || country );
	const hasPostalAddress = (showPostalAddress && globalPostalAddress);
	var hasVisibleContent = false;
	hasVisibleContent = (hasPhone || hasEmail || hasAddress || hasEmail2 || hasPhone2 || hasPostalAddress);
	if (hasVisibleContent) {
		hasVisibleContent = true;
	}

	var hasAnyData = false;
	hasAnyData = ( phone || phone2 || email || email2 || streetAddress || town || state || zipCode || country );
	if (hasAnyData) {
		hasAnyData = true;
	}

	const buildAddressParts = () => {
		const parts = [];
		if ( streetAddress && showStreetAddress ) {
			parts.push( streetAddress );
		}
		var cityLine = '';
		if ( showTown && showState ) {
			cityLine = [ town, state ].filter( Boolean ).join( ', ' );
		} else if ( showState ) {
			cityLine = state;
		} else if ( showTown ) {
			cityLine = town;
		}
		if ( cityLine ) {
			parts.push( cityLine );
		}

		if ( zipCode && showZipCode ) {
			parts.push( zipCode );
		}

		if ( country && showCountry ) {
			parts.push( country );
		}
		return parts;
	};

	const settingsUrl = window?.ingeniContactBlock?.settingsUrl || '/wp-admin/options-general.php?page=ingeni-contact-info';

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon="list-view"
						label={ __( 'Stacked layout', 'ingeni-contact-info-block' ) }
						isPressed={ layout === 'stacked' }
						onClick={ () => setAttributes( { layout: 'stacked' } ) }
					/>
					<ToolbarButton
						icon="ellipsis"
						label={ __( 'Inline layout', 'ingeni-contact-info-block' ) }
						isPressed={ layout === 'inline' }
						onClick={ () => setAttributes( { layout: 'inline' } ) }
					/>
				</ToolbarGroup>
			</BlockControls>
			<InspectorControls>
				<PanelBody
					title={ __( 'Display Contacts', 'ingeni-contact-info-block' ) }
					initialOpen={ true }
					>
					<p className="ingeni-contact-info-block__settings-hint">
						{ __( 'Choose which fields to display from your global contact information.', 'ingeni-contact-info-block' ) }
					</p>
					<ToggleControl
						label={ __( 'Show phone', 'ingeni-contact-info-block' ) }
						help={ phone ? phone : __( 'No phone number set', 'ingeni-contact-info-block' ) }
						checked={ showPhone }
						onChange={ ( val ) => setAttributes( { showPhone: val } ) }
					/>
					<ToggleControl
						label={ __( 'Show phone #2', 'ingeni-contact-info-block' ) }
						help={ phone2 ? phone2 : __( 'No phone number set', 'ingeni-contact-info-block' ) }
						checked={ showPhone2 }
						onChange={ ( val ) => setAttributes( { showPhone2: val } ) }
					/>
					<ToggleControl
						label={ __( 'Show email', 'ingeni-contact-info-block' ) }
						help={ email ? email : __( 'No email set', 'ingeni-contact-info-block' ) }
						checked={ showEmail }
						onChange={ ( val ) => setAttributes( { showEmail: val } ) }
					/>
					<ToggleControl
						label={ __( 'Show email #2', 'ingeni-contact-info-block' ) }
						help={ email2 ? email2 : __( 'No email set', 'ingeni-contact-info-block' ) }
						checked={ showEmail2 }
						onChange={ ( val ) => setAttributes( { showEmail2: val } ) }
					/>
					<ToggleControl
						label={ __( 'Show contact icons', 'ingeni-contact-info-block' ) }
						checked={ showIconsContact }
						onChange={ ( val ) => setAttributes( { showIconsContact: val } ) }
					/>					
				</PanelBody>
				<PanelBody 
					title={ __( 'Display Address', 'ingeni-contact-info-block' ) }
					initialOpen={ true }
					>
					<p className="ingeni-contact-info-block__settings-hint">
					{ __( 'Choose which address fields to display from your global contact information.', 'ingeni-contact-info-block' ) }
					</p>
					<ToggleControl
						label={ __( 'Show street address', 'ingeni-contact-info-block' ) }
						help={ streetAddress ? streetAddress : __( 'No address set', 'ingeni-contact-info-block' ) }
						checked={ showStreetAddress }
						onChange={ ( val ) => setAttributes( { showStreetAddress: val } ) }
					/>
					<ToggleControl
						label={ __( 'Show town', 'ingeni-contact-info-block' ) }
						help={ town ? town : __( 'No town set', 'ingeni-contact-info-block' ) }
						checked={showTown }
						onChange={ ( val ) => setAttributes( { showTown: val } ) }
					/>
					<ToggleControl
						label={ __( 'Show State', 'ingeni-contact-info-block' ) }
						help={ state ? state : __( 'No state set', 'ingeni-contact-info-block' ) }
						checked={ showState }
						onChange={ ( val ) => setAttributes( { showState: val } ) }
					/>
					<ToggleControl
						label={ __( 'Show ZIP / Post code', 'ingeni-contact-info-block' ) }
						help={ zipCode ? zipCode : __( 'No postal code set', 'ingeni-contact-info-block' ) }
						checked={ showZipCode }
						onChange={ ( val ) => setAttributes( { showZipCode: val } ) }
					/>
					<ToggleControl
						label={ __( 'Show Country', 'ingeni-contact-info-block' ) }
						help={ country ? country : __( 'No country set', 'ingeni-contact-info-block' ) }
						checked={ showCountry }
						onChange={ ( val ) => setAttributes( { showCountry: val } ) }
					/>
					<ToggleControl
						label={ __( 'Show Postal Address', 'ingeni-contact-info-block' ) }
						help={ globalPostalAddress ? globalPostalAddress : __( 'No postal address set', 'ingeni-contact-info-block' ) }
						checked={ showPostalAddress }
						onChange={ ( val ) => setAttributes( { showPostalAddress: val } ) }
					/>					
					<ToggleControl
						label={ __( 'Show address icons', 'ingeni-contact-info-block' ) }
						checked={ showIconsAddress }
						onChange={ ( val ) => setAttributes( { showIconsAddress: val } ) }
					/>	
				</PanelBody>
				<PanelBody
					title={ __( 'Override Values', 'ingeni-contact-info-block' ) }
					initialOpen={ false }
				>
					<p className="ingeni-contact-info-block__settings-hint">
						{ __( 'Optionally override global values for this specific block instance. Leave blank to use the global setting.', 'ingeni-contact-info-block' ) }
					</p>
					{ showPhone && (
						<TextControl
							label={ __( 'Phone override', 'ingeni-contact-info-block' ) }
							value={ overridePhone }
							placeholder={ globalPhone || __( 'Global value', 'ingeni-contact-info-block' ) }
							onChange={ ( val ) => setAttributes( { overridePhone: val } ) }
						/>
					) }
					{ showEmail && (
						<TextControl
							label={ __( 'Email override', 'ingeni-contact-info-block' ) }
							value={ overrideEmail }
							placeholder={ globalEmail || __( 'Global value', 'ingeni-contact-info-block' ) }
							onChange={ ( val ) => setAttributes( { overrideEmail: val } ) }
						/>
					) }
					{ showStreetAddress && (
						<>
							<TextControl
								label={ __( 'Street address override', 'ingeni-contact-info-block' ) }
								value={ overrideStreetAddress }
								placeholder={ globalStreetAddress || __( 'Global value', 'ingeni-contact-info-block' ) }
								onChange={ ( val ) => setAttributes( { overrideStreetAddress: val } ) }
							/>
							<TextControl
								label={ __( 'Town / City override', 'ingeni-contact-info-block' ) }
								value={ overrideTown }
								placeholder={ globalTown || __( 'Global value', 'ingeni-contact-info-block' ) }
								onChange={ ( val ) => setAttributes( { overrideTown: val } ) }
							/>
							<TextControl
								label={ __( 'State / Province override', 'ingeni-contact-info-block' ) }
								value={ overrideState }
								placeholder={ globalState || __( 'Global value', 'ingeni-contact-info-block' ) }
								onChange={ ( val ) => setAttributes( { overrideState: val } ) }
							/>
							<TextControl
								label={ __( 'Zip / Postal code override', 'ingeni-contact-info-block' ) }
								value={ overrideZipCode }
								placeholder={ globalZipCode || __( 'Global value', 'ingeni-contact-info-block' ) }
								onChange={ ( val ) => setAttributes( { overrideZipCode: val } ) }
							/>
							<TextControl
								label={ __( 'Country override', 'ingeni-contact-info-block' ) }
								value={ overrideCountry }
								placeholder={ globalCountry || __( 'Global value', 'ingeni-contact-info-block' ) }
								onChange={ ( val ) => setAttributes( { overrideCountry: val } ) }
							/>							
						</>
					) }

				</PanelBody>
				<PanelBody
					title={ __( 'Manage Contact Info', 'ingeni-contact-info-block' ) }
					initialOpen={ false }
				>
					<p>
						{ __( 'Edit your global contact information in the settings page.', 'ingeni-contact-info-block' ) }
					</p>
					<ExternalLink href={ settingsUrl }>
						{ __( 'Open Contact Info Settings', 'ingeni-contact-info-block' ) }
					</ExternalLink>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ ! hasAnyData && (
					<div className="ingeni-contact-info-block__placeholder">
						{ showIconsAddress && (
						<span className="ingeni-contact-info-block__placeholder-icon">
							{ ADDRESS_ICON }
						</span>
						) }
						<p>{ __( 'Contact Info', 'ingeni-contact-info-block' ) }</p>
						<p className="ingeni-contact-info-block__placeholder-hint">
							{ __( 'No contact information found. Add your details in', 'ingeni-contact-info-block' ) }{ ' ' }
							<a href={ settingsUrl } target="_blank" rel="noopener noreferrer">
								{ __( 'Settings > Contact Info', 'ingeni-contact-info-block' ) }
							</a>.
						</p>
					</div>
				) }
				{ hasAnyData && ! hasVisibleContent && (
					<div className="ingeni-contact-info-block__placeholder">
						<p>{ __( 'Contact Info', 'ingeni-contact-info-block' ) }</p>
						<p className="ingeni-contact-info-block__placeholder-hint">
							{ __( 'All fields are hidden. Use the block settings panel to choose which fields to display.', 'ingeni-contact-info-block' ) }
						</p>
					</div>
				) }
				{ hasVisibleContent && (
					<div className="ingeni-contact-info-block__items">
						{ showPhone && phone && (
							<div className="ingeni-contact-info-block__item ingeni-contact-info-block__item--phone">
								{ showIconsContact && (
									<span className="ingeni-contact-info-block__icon">{ PHONE_ICON }</span>
								) }
								<span className="ingeni-contact-info-block__value">
									{ phone }
									{ overridePhone && (
										<span className="ingeni-contact-info-block__override-badge">{ __( 'override', 'ingeni-contact-info-block' ) }</span>
									) }
								</span>
							</div>
						) }
						{ showPhone2 && phone2 && (
							<div className="ingeni-contact-info-block__item ingeni-contact-info-block__item--phone">
								{ showIconsContact && (
									<span className="ingeni-contact-info-block__icon">{ PHONE_ICON }</span>
								) }
								<span className="ingeni-contact-info-block__value">
									{ phone2 }
								</span>
							</div>
						) }						
						{ hasEmail && (
							<div className="ingeni-contact-info-block__item ingeni-contact-info-block__item--email">
								{ showIconsContact && (
									<span className="ingeni-contact-info-block__icon">{ EMAIL_ICON }</span>
								) }
								<span className="ingeni-contact-info-block__value">
									{ email }
									{ overrideEmail && (
										<span className="ingeni-contact-info-block__override-badge">{ __( 'override', 'ingeni-contact-info-block' ) }</span>
									) }
								</span>
							</div>
						) }
						{ showEmail2 && email2 && (
							<div className="ingeni-contact-info-block__item ingeni-contact-info-block__item--email">
								{ showIconsContact && (
									<span className="ingeni-contact-info-block__icon">{ EMAIL_ICON }</span>
								) }
								<span className="ingeni-contact-info-block__value">
									{ email2 }
								</span>
							</div>
						) }						
						{ hasAddress && (
							<div className="ingeni-contact-info-block__item ingeni-contact-info-block__item--address">
								{ showIconsAddress && (
									<span className="ingeni-contact-info-block__icon">{ ADDRESS_ICON }</span>
								) }
								<div className="ingeni-contact-info-block__address-lines">
									{ buildAddressParts().map( ( part, i ) => (
										<span key={ i } className="ingeni-contact-info-block__address-line">{ part }</span>
									) ) }
									{ ( overrideStreetAddress || overrideTown || overrideState || overrideZipCode ) && (
										<span className="ingeni-contact-info-block__override-badge">{ __( 'override', 'ingeni-contact-info-block' ) }</span>
									) }
								</div>
							</div>
						) }
						{ showPostalAddress && (
							<div className="ingeni-contact-info-block__item ingeni-contact-info-block__item--address">
								{ showIconsAddress && (
									<span className="ingeni-contact-info-block__icon">{ EMAIL_ICON }</span>
								) }
								<div className="ingeni-contact-info-block__address-lines">
									<span className="ingeni-contact-info-block__address-line">{ globalPostalAddress }</span>
								</div>
							</div>
						) }
					</div>
				) }
			</div>
		</>
	);
}