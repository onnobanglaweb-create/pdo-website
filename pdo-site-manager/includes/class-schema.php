<?php
/**
 * Declarative settings schema.
 *
 * Every editable option group is described here once. The same description
 * drives the admin UI rendering, the sanitising routine and the default
 * values (which carry the content migrated from the original HTML template).
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Schema registry.
 */
class PDO_SM_Schema {

	/**
	 * Cached schema.
	 *
	 * @var array|null
	 */
	private static $schema = null;

	/**
	 * Standard visibility + ordering fields appended to every repeater row.
	 *
	 * @return array
	 */
	public static function row_meta_fields() {
		return array(
			array(
				'key'     => 'visible',
				'type'    => 'checkbox',
				'label'   => __( 'Visible on the website', 'pdo-site-manager' ),
				'default' => 1,
				'row_tag' => true,
			),
			array(
				'key'     => 'order',
				'type'    => 'order',
				'label'   => __( 'Order', 'pdo-site-manager' ),
				'default' => 0,
			),
		);
	}

	/**
	 * Section display controls shared by page sections.
	 *
	 * @param string $prefix Field prefix.
	 * @param bool   $default_enabled Default visibility.
	 * @return array
	 */
	public static function section_controls( $prefix, $default_enabled = true ) {
		return array(
			array(
				'key'     => $prefix . '_enabled',
				'type'    => 'checkbox',
				'label'   => __( 'Show this section', 'pdo-site-manager' ),
				'default' => $default_enabled ? 1 : 0,
			),
		);
	}

	/**
	 * Background style choices.
	 *
	 * @return array<string,string>
	 */
	public static function background_choices() {
		return array(
			''          => __( 'Default (white)', 'pdo-site-manager' ),
			'light'     => __( 'Light grey', 'pdo-site-manager' ),
			'tint'      => __( 'Primary tint', 'pdo-site-manager' ),
			'dark'      => __( 'Dark green gradient', 'pdo-site-manager' ),
			'navy'      => __( 'Dark navy', 'pdo-site-manager' ),
			'pattern'   => __( 'Hero dotted pattern', 'pdo-site-manager' ),
			'transparent' => __( 'Transparent', 'pdo-site-manager' ),
		);
	}

	/**
	 * Accent colour choices used by cards and stages.
	 *
	 * @return array<string,string>
	 */
	public static function accent_choices() {
		return array(
			'emerald' => __( 'Emerald (primary)', 'pdo-site-manager' ),
			'dark'    => __( 'Dark green', 'pdo-site-manager' ),
			'teal'    => __( 'Teal', 'pdo-site-manager' ),
			'amber'   => __( 'Amber / gold', 'pdo-site-manager' ),
			'blue'    => __( 'Blue', 'pdo-site-manager' ),
			'purple'  => __( 'Purple', 'pdo-site-manager' ),
			'rose'    => __( 'Rose', 'pdo-site-manager' ),
			'slate'   => __( 'Slate', 'pdo-site-manager' ),
		);
	}

	/**
	 * Alignment choices.
	 *
	 * @return array<string,string>
	 */
	public static function align_choices() {
		return array(
			'left'   => __( 'Left', 'pdo-site-manager' ),
			'center' => __( 'Center', 'pdo-site-manager' ),
			'right'  => __( 'Right', 'pdo-site-manager' ),
		);
	}

	/**
	 * Full schema.
	 *
	 * @return array
	 */
	public static function all() {
		if ( null !== self::$schema ) {
			return self::$schema;
		}

		$schema = array(
			'org'          => self::group_org(),
			'home'         => self::group_home(),
			'about'        => self::group_about(),
			'impact'       => self::group_impact(),
			'transparency' => self::group_transparency(),
			'media'        => self::group_media(),
			'involved'     => self::group_involved(),
			'contact'      => self::group_contact(),
			'i18n'         => self::group_i18n(),
		);

		/**
		 * Filter the full settings schema.
		 *
		 * @param array $schema Schema definition.
		 */
		self::$schema = apply_filters( 'pdo_sm_settings_schema', $schema );

		return self::$schema;
	}

	/**
	 * Fetch a single group definition.
	 *
	 * @param string $key Group key.
	 * @return array
	 */
	public static function group( $key ) {
		$all = self::all();
		return isset( $all[ $key ] ) ? $all[ $key ] : array();
	}

	/**
	 * Flatten the fields of a group (ignoring the tab/section grouping).
	 *
	 * @param string $key Group key.
	 * @return array
	 */
	public static function fields( $key ) {
		$group  = self::group( $key );
		$fields = array();
		if ( empty( $group['tabs'] ) ) {
			return $fields;
		}
		foreach ( $group['tabs'] as $tab ) {
			if ( empty( $tab['panels'] ) ) {
				continue;
			}
			foreach ( $tab['panels'] as $panel ) {
				if ( empty( $panel['fields'] ) ) {
					continue;
				}
				foreach ( $panel['fields'] as $field ) {
					$fields[] = $field;
				}
			}
		}
		return $fields;
	}

	/* --------------------------------------------------------------------- */
	/* Organisation                                                          */
	/* --------------------------------------------------------------------- */

	/**
	 * Organisation information group.
	 *
	 * @return array
	 */
	private static function group_org() {
		return array(
			'option' => 'pdo_sm_org',
			'label'  => __( 'Organization', 'pdo-site-manager' ),
			'tabs'   => array(
				'identity' => array(
					'label'  => __( 'Identity', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Core identity', 'pdo-site-manager' ),
							'desc'   => __( 'The organisation name and tagline used across the website, structured data and emails.', 'pdo-site-manager' ),
							'fields' => array(
								array(
									'key'       => 'name',
									'type'      => 'text',
									'label'     => __( 'Organization name', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Peoples Development Organisation',
									'default_bn' => 'পিপলস ডেভেলপমেন্ট অর্গানাইজেশন',
								),
								array(
									'key'       => 'short_name',
									'type'      => 'text',
									'label'     => __( 'Short name / abbreviation', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'PDO',
									'default_bn' => 'পিডিও',
								),
								array(
									'key'       => 'tagline',
									'type'      => 'text',
									'label'     => __( 'Tagline', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Empowering the Grassroots, Engineering the Future.',
								),
								array(
									'key'       => 'description',
									'type'      => 'textarea',
									'label'     => __( 'Organization description', 'pdo-site-manager' ),
									'bilingual' => true,
									'rows'      => 4,
									'default'   => 'Peoples Development Organisation (PDO) is a registered Bangladeshi non-profit operating at the intersection of quality technical education, youth resilience, zero-hunger campuses, and aggressive climate action. We turn vulnerable beneficiaries into self-reliant changemakers.',
								),
								array(
									'key'     => 'type_label',
									'type'    => 'text',
									'label'   => __( 'Organization type badge', 'pdo-site-manager' ),
									'desc'    => __( 'Small badge shown beside the logo, e.g. "NGO".', 'pdo-site-manager' ),
									'default' => 'NGO',
								),
								array(
									'key'     => 'logo_icon',
									'type'    => 'icon',
									'label'   => __( 'Logo symbol (used when no logo image is set)', 'pdo-site-manager' ),
									'default' => 'people-roof',
								),
							),
						),
						array(
							'title'  => __( 'Legal registration', 'pdo-site-manager' ),
							'fields' => array(
								array(
									'key'     => 'established',
									'type'    => 'text',
									'label'   => __( 'Establishment year', 'pdo-site-manager' ),
									'default' => '1997',
								),
								array(
									'key'       => 'reg_authority',
									'type'      => 'text',
									'label'     => __( 'Registration authority', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Department of Social Services, Government of Bangladesh',
								),
								array(
									'key'     => 'reg_number',
									'type'    => 'text',
									'label'   => __( 'Registration number', 'pdo-site-manager' ),
									'default' => 'Nat-295/2000',
								),
								array(
									'key'       => 'compliance_note',
									'type'      => 'text',
									'label'     => __( 'Compliance statement', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Standard: INGO & UN SDG Compliant',
								),
								array(
									'key'       => 'legal_notice',
									'type'      => 'textarea',
									'label'     => __( 'Footer legal notice', 'pdo-site-manager' ),
									'bilingual' => true,
									'rows'      => 2,
									'default'   => 'Registered Non-Profit Under Dept. of Social Services, Bangladesh (Reg: Nat-295/2000).',
								),
								array(
									'key'       => 'copyright',
									'type'      => 'text',
									'label'     => __( 'Copyright line', 'pdo-site-manager' ),
									'bilingual' => true,
									'desc'      => __( 'Use {year} to insert the current year automatically.', 'pdo-site-manager' ),
									'default'   => '© {year} Peoples Development Organisation (PDO). All Rights Reserved.',
								),
							),
						),
					),
				),
				'location' => array(
					'label'  => __( 'Location & contact', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Headquarters', 'pdo-site-manager' ),
							'fields' => array(
								array(
									'key'       => 'office_name',
									'type'      => 'text',
									'label'     => __( 'Office name', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Headquarters & Secretariat',
								),
								array(
									'key'       => 'address',
									'type'      => 'textarea',
									'label'     => __( 'Full address', 'pdo-site-manager' ),
									'bilingual' => true,
									'rows'      => 2,
									'default'   => 'Bahadurpur, Post Office: Chamari, Upazila: Singra, District: Natore, Division: Rajshahi, Bangladesh.',
								),
								array(
									'key'     => 'division',
									'type'    => 'text',
									'label'   => __( 'Division', 'pdo-site-manager' ),
									'default' => 'Rajshahi',
								),
								array(
									'key'     => 'district',
									'type'    => 'text',
									'label'   => __( 'District', 'pdo-site-manager' ),
									'default' => 'Natore',
								),
								array(
									'key'     => 'upazila',
									'type'    => 'text',
									'label'   => __( 'Upazila', 'pdo-site-manager' ),
									'default' => 'Singra',
								),
								array(
									'key'     => 'postal',
									'type'    => 'text',
									'label'   => __( 'Post office / postal information', 'pdo-site-manager' ),
									'default' => 'Post Office: Chamari',
								),
								array(
									'key'     => 'short_location',
									'type'    => 'text',
									'label'   => __( 'Short location label', 'pdo-site-manager' ),
									'default' => 'Singra, Natore, Bangladesh',
								),
							),
						),
						array(
							'title'  => __( 'Contact channels', 'pdo-site-manager' ),
							'fields' => array(
								array(
									'key'     => 'phone',
									'type'    => 'text',
									'label'   => __( 'Official phone number', 'pdo-site-manager' ),
									'default' => '+88 01712520019',
								),
								array(
									'key'     => 'whatsapp',
									'type'    => 'text',
									'label'   => __( 'WhatsApp number (digits only)', 'pdo-site-manager' ),
									'default' => '8801712520019',
								),
								array(
									'key'     => 'email',
									'type'    => 'email',
									'label'   => __( 'Official email', 'pdo-site-manager' ),
									'default' => 'pdoglobal1997@gmail.com',
								),
								array(
									'key'     => 'notification_email',
									'type'    => 'email',
									'label'   => __( 'Form notification recipient', 'pdo-site-manager' ),
									'desc'    => __( 'Leave empty to use the official email address.', 'pdo-site-manager' ),
									'default' => '',
								),
								array(
									'key'       => 'office_hours',
									'type'      => 'text',
									'label'     => __( 'Opening / contact hours', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Saturday – Thursday, 9:00 AM – 5:00 PM (BST)',
								),
								array(
									'key'       => 'response_time',
									'type'      => 'text',
									'label'     => __( 'Response time statement', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Our administrative team responds within 24 to 48 operational hours.',
								),
							),
						),
						array(
							'title'  => __( 'Social profiles', 'pdo-site-manager' ),
							'fields' => array(
								array(
									'key'        => 'socials',
									'type'       => 'repeater',
									'label'      => __( 'Social media links', 'pdo-site-manager' ),
									'item_label' => 'label',
									'add_label'  => __( 'Add social profile', 'pdo-site-manager' ),
									'fields'     => array(
										array(
											'key'     => 'label',
											'type'    => 'text',
											'label'   => __( 'Label', 'pdo-site-manager' ),
											'default' => '',
										),
										array(
											'key'     => 'icon',
											'type'    => 'icon',
											'label'   => __( 'Icon', 'pdo-site-manager' ),
											'default' => 'link',
										),
										array(
											'key'     => 'url',
											'type'    => 'url',
											'label'   => __( 'URL', 'pdo-site-manager' ),
											'default' => '',
										),
									),
									'default'    => array(
										array(
											'label'   => 'Facebook',
											'icon'    => 'facebook',
											'url'     => 'https://www.facebook.com/pdoglobal',
											'visible' => 1,
											'order'   => 0,
										),
										array(
											'label'   => 'WhatsApp',
											'icon'    => 'whatsapp',
											'url'     => 'https://wa.me/8801712520019',
											'visible' => 1,
											'order'   => 1,
										),
										array(
											'label'   => 'Email',
											'icon'    => 'mail',
											'url'     => 'mailto:pdoglobal1997@gmail.com',
											'visible' => 1,
											'order'   => 2,
										),
									),
								),
							),
						),
					),
				),
				'map'      => array(
					'label'  => __( 'Map', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Interactive map', 'pdo-site-manager' ),
							'desc'   => __( 'The map is rendered from these values. Nothing is hardcoded in the theme.', 'pdo-site-manager' ),
							'fields' => array(
								array(
									'key'     => 'map_enabled',
									'type'    => 'checkbox',
									'label'   => __( 'Display the map', 'pdo-site-manager' ),
									'default' => 1,
								),
								array(
									'key'     => 'map_lat',
									'type'    => 'text',
									'label'   => __( 'Latitude', 'pdo-site-manager' ),
									'default' => '24.5000',
								),
								array(
									'key'     => 'map_lng',
									'type'    => 'text',
									'label'   => __( 'Longitude', 'pdo-site-manager' ),
									'default' => '89.1500',
								),
								array(
									'key'     => 'map_zoom_bbox',
									'type'    => 'text',
									'label'   => __( 'OpenStreetMap bounding box', 'pdo-site-manager' ),
									'desc'    => __( 'Format: minLon,minLat,maxLon,maxLat', 'pdo-site-manager' ),
									'default' => '89.0500,24.4000,89.3000,24.6000',
								),
								array(
									'key'     => 'map_embed',
									'type'    => 'textarea',
									'label'   => __( 'Custom map embed URL (optional)', 'pdo-site-manager' ),
									'desc'    => __( 'Paste a Google Maps or OpenStreetMap embed URL to override the generated map.', 'pdo-site-manager' ),
									'rows'    => 2,
									'default' => '',
								),
								array(
									'key'     => 'map_link',
									'type'    => 'url',
									'label'   => __( 'Google Maps link', 'pdo-site-manager' ),
									'default' => 'https://maps.google.com/?q=Singra,Natore,Bangladesh',
								),
								array(
									'key'       => 'map_pin_label',
									'type'      => 'text',
									'label'     => __( 'Map pin label', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Location Pin: Bahadurpur, Singra, Natore',
								),
								array(
									'key'       => 'map_caption',
									'type'      => 'text',
									'label'     => __( 'Map caption', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Central Implementation Hub: Singra, Natore',
								),
							),
						),
					),
				),
				'language' => array(
					'label'  => __( 'Language', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Bilingual settings', 'pdo-site-manager' ),
							'fields' => array(
								array(
									'key'     => 'bilingual_enabled',
									'type'    => 'checkbox',
									'label'   => __( 'Enable English / Bengali switching', 'pdo-site-manager' ),
									'default' => 1,
								),
								array(
									'key'     => 'default_language',
									'type'    => 'select',
									'label'   => __( 'Default language', 'pdo-site-manager' ),
									'options' => array(
										'en' => __( 'English', 'pdo-site-manager' ),
										'bn' => __( 'Bengali (বাংলা)', 'pdo-site-manager' ),
									),
									'default' => 'en',
								),
							),
						),
					),
				),
			),
		);
	}

	/* --------------------------------------------------------------------- */
	/* Homepage                                                              */
	/* --------------------------------------------------------------------- */

	/**
	 * Homepage group.
	 *
	 * @return array
	 */
	private static function group_home() {
		return array(
			'option' => 'pdo_sm_home',
			'label'  => __( 'Homepage', 'pdo-site-manager' ),
			'tabs'   => array(
				'hero'      => array(
					'label'  => __( 'Hero', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Hero section', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'hero' ),
								array(
									array(
										'key'       => 'hero_badge',
										'type'      => 'text',
										'label'     => __( 'Introductory badge', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Grassroots Systemic Transformation Since 1997',
									),
									array(
										'key'     => 'hero_badge_icon',
										'type'    => 'icon',
										'label'   => __( 'Badge icon', 'pdo-site-manager' ),
										'default' => '',
									),
									array(
										'key'       => 'hero_title',
										'type'      => 'text',
										'label'     => __( 'Main headline', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Empowering the Grassroots,',
										'default_bn' => 'তৃণমূলের ক্ষমতায়ন,',
									),
									array(
										'key'       => 'hero_title_highlight',
										'type'      => 'text',
										'label'     => __( 'Highlighted headline text', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Engineering the Future.',
										'default_bn' => 'ভবিষ্যতের নির্মাণ।',
									),
									array(
										'key'       => 'hero_description',
										'type'      => 'textarea',
										'label'     => __( 'Description', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 4,
										'default'   => 'Peoples Development Organisation (PDO) is a registered Bangladeshi non-profit operating at the intersection of quality technical education, youth resilience, zero-hunger campuses, and aggressive climate action. We turn vulnerable beneficiaries into self-reliant changemakers.',
									),
									array(
										'key'     => 'hero_background',
										'type'    => 'select',
										'label'   => __( 'Background style', 'pdo-site-manager' ),
										'options' => self::background_choices(),
										'default' => 'pattern',
									),
									array(
										'key'   => 'hero_background_image',
										'type'  => 'image',
										'label' => __( 'Background image (optional)', 'pdo-site-manager' ),
									),
								)
							),
						),
						array(
							'title'  => __( 'Hero buttons', 'pdo-site-manager' ),
							'fields' => array(
								array(
									'key'       => 'hero_btn1_label',
									'type'      => 'text',
									'label'     => __( 'Primary button label', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Explore Our Impact',
								),
								array(
									'key'     => 'hero_btn1_url',
									'type'    => 'url',
									'label'   => __( 'Primary button URL', 'pdo-site-manager' ),
									'default' => '/impact/',
								),
								array(
									'key'     => 'hero_btn1_icon',
									'type'    => 'icon',
									'label'   => __( 'Primary button icon', 'pdo-site-manager' ),
									'default' => 'arrow-right',
								),
								array(
									'key'       => 'hero_btn2_label',
									'type'      => 'text',
									'label'     => __( 'Secondary button label', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Partner With Us',
								),
								array(
									'key'     => 'hero_btn2_url',
									'type'    => 'url',
									'label'   => __( 'Secondary button URL', 'pdo-site-manager' ),
									'default' => '/get-involved/#partner',
								),
								array(
									'key'     => 'hero_btn2_icon',
									'type'    => 'icon',
									'label'   => __( 'Secondary button icon', 'pdo-site-manager' ),
									'default' => 'handshake',
								),
							),
						),
						array(
							'title'  => __( 'Verification badges', 'pdo-site-manager' ),
							'fields' => array(
								array(
									'key'        => 'hero_badges',
									'type'       => 'repeater',
									'label'      => __( 'Verification badges', 'pdo-site-manager' ),
									'item_label' => 'text',
									'add_label'  => __( 'Add badge', 'pdo-site-manager' ),
									'fields'     => array(
										array(
											'key'       => 'text',
											'type'      => 'text',
											'label'     => __( 'Badge text', 'pdo-site-manager' ),
											'bilingual' => true,
										),
										array(
											'key'     => 'icon',
											'type'    => 'icon',
											'label'   => __( 'Icon', 'pdo-site-manager' ),
											'default' => 'check-circle',
										),
									),
									'default'    => array(
										array(
											'text'    => 'Dept. of Social Services: Nat-295/2000',
											'icon'    => 'check-circle',
											'visible' => 1,
											'order'   => 0,
										),
										array(
											'text'    => 'UN SDG 1, 2, 4, 13, 15, 17 Aligned',
											'icon'    => 'check-circle',
											'visible' => 1,
											'order'   => 1,
										),
										array(
											'text'    => 'Audited Financial Integrity',
											'icon'    => 'check-circle',
											'visible' => 1,
											'order'   => 2,
										),
									),
								),
							),
						),
						array(
							'title'  => __( 'Hero visual panel', 'pdo-site-manager' ),
							'desc'   => __( 'The image mosaic shown beside the headline.', 'pdo-site-manager' ),
							'fields' => array(
								array(
									'key'       => 'hero_panel_title',
									'type'      => 'text',
									'label'     => __( 'Panel title', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Live Field Operations',
								),
								array(
									'key'       => 'hero_panel_tag',
									'type'      => 'text',
									'label'     => __( 'Panel tag', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Active in 2026',
								),
								array(
									'key'        => 'hero_images',
									'type'       => 'repeater',
									'label'      => __( 'Hero image mosaic', 'pdo-site-manager' ),
									'item_label' => 'caption',
									'add_label'  => __( 'Add hero image', 'pdo-site-manager' ),
									'fields'     => array(
										array(
											'key'   => 'image',
											'type'  => 'image',
											'label' => __( 'Image', 'pdo-site-manager' ),
										),
										array(
											'key'   => 'image_url',
											'type'  => 'url',
											'label' => __( 'Or image URL', 'pdo-site-manager' ),
											'desc'  => __( 'Used only when no Media Library image is selected.', 'pdo-site-manager' ),
										),
										array(
											'key'   => 'alt',
											'type'  => 'text',
											'label' => __( 'Image alt text', 'pdo-site-manager' ),
										),
										array(
											'key'       => 'caption',
											'type'      => 'text',
											'label'     => __( 'Caption / statistic label', 'pdo-site-manager' ),
											'bilingual' => true,
										),
										array(
											'key'     => 'accent',
											'type'    => 'select',
											'label'   => __( 'Overlay accent', 'pdo-site-manager' ),
											'options' => self::accent_choices(),
											'default' => 'emerald',
										),
									),
									'default'    => array(
										array(
											'image_url' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=600&q=80',
											'alt'       => 'Mid-Day Meal Campus Nutrition',
											'caption'   => 'Mid-Day Meals (250+)',
											'accent'    => 'emerald',
											'visible'   => 1,
											'order'     => 0,
										),
										array(
											'image_url' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=600&q=80',
											'alt'       => 'Tree Guardian Afforestation',
											'caption'   => '10,000+ Saplings',
											'accent'    => 'teal',
											'visible'   => 1,
											'order'     => 1,
										),
										array(
											'image_url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=600&q=80',
											'alt'       => 'Tiered Scholarship Students',
											'caption'   => '1,200+ Scholarships',
											'accent'    => 'amber',
											'visible'   => 1,
											'order'     => 2,
										),
										array(
											'image_url' => 'https://images.unsplash.com/photo-1581092921461-eab62e97a780?auto=format&fit=crop&w=600&q=80',
											'alt'       => 'TVET Vocational Skills',
											'caption'   => 'TVET Technical Labs',
											'accent'    => 'blue',
											'visible'   => 1,
											'order'     => 3,
										),
									),
								),
								array(
									'key'       => 'hero_hub_label',
									'type'      => 'text',
									'label'     => __( 'Hub label', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Flagship Central Hub',
								),
								array(
									'key'       => 'hero_hub_name',
									'type'      => 'text',
									'label'     => __( 'Hub name', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Bahadurpur Technical School & College',
								),
								array(
									'key'       => 'hero_hub_place',
									'type'      => 'text',
									'label'     => __( 'Hub location', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Singra, Natore',
								),
							),
						),
					),
				),
				'stats'     => array(
					'label'  => __( 'Statistics', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Statistics strip', 'pdo-site-manager' ),
							'desc'   => __( 'Add, edit, duplicate, hide and reorder as many statistics as you need.', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'stats' ),
								array(
									array(
										'key'        => 'stats',
										'type'       => 'repeater',
										'label'      => __( 'Statistics', 'pdo-site-manager' ),
										'item_label' => 'label',
										'add_label'  => __( 'Add statistic', 'pdo-site-manager' ),
										'fields'     => array(
											array(
												'key'   => 'prefix',
												'type'  => 'text',
												'label' => __( 'Prefix', 'pdo-site-manager' ),
											),
											array(
												'key'     => 'value',
												'type'    => 'text',
												'label'   => __( 'Numeric value', 'pdo-site-manager' ),
												'default' => '0',
											),
											array(
												'key'   => 'suffix',
												'type'  => 'text',
												'label' => __( 'Suffix', 'pdo-site-manager' ),
											),
											array(
												'key'       => 'label',
												'type'      => 'text',
												'label'     => __( 'Label', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'description',
												'type'      => 'text',
												'label'     => __( 'Description (optional)', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'   => 'icon',
												'type'  => 'icon',
												'label' => __( 'Icon (optional)', 'pdo-site-manager' ),
											),
											array(
												'key'   => 'color',
												'type'  => 'color',
												'label' => __( 'Number colour', 'pdo-site-manager' ),
											),
											array(
												'key'   => 'bg',
												'type'  => 'color',
												'label' => __( 'Card background', 'pdo-site-manager' ),
											),
											array(
												'key'     => 'animate',
												'type'    => 'checkbox',
												'label'   => __( 'Animate the counter', 'pdo-site-manager' ),
												'default' => 1,
											),
										),
										'default'    => array(
											array(
												'value'   => '1200',
												'suffix'  => '+',
												'label'   => 'Annual Scholarships',
												'icon'    => 'graduation-cap',
												'color'   => '#0d7a53',
												'animate' => 1,
												'visible' => 1,
												'order'   => 0,
											),
											array(
												'value'   => '10000',
												'suffix'  => '+',
												'label'   => 'Tree Guardians',
												'icon'    => 'tree',
												'color'   => '#084c34',
												'animate' => 1,
												'visible' => 1,
												'order'   => 1,
											),
											array(
												'value'   => '250',
												'suffix'  => '+',
												'label'   => 'Daily Mid-Day Meals',
												'icon'    => 'bowl-food',
												'color'   => '#d97706',
												'animate' => 1,
												'visible' => 1,
												'order'   => 2,
											),
											array(
												'value'   => '90',
												'suffix'  => '%+',
												'label'   => 'Attendance Spike',
												'icon'    => 'chart-line',
												'color'   => '#0d7a53',
												'animate' => 1,
												'visible' => 1,
												'order'   => 3,
											),
											array(
												'value'   => '300',
												'suffix'  => '+',
												'label'   => 'Free School Uniforms',
												'icon'    => 'users',
												'color'   => '#2563eb',
												'animate' => 1,
												'visible' => 1,
												'order'   => 4,
											),
											array(
												'value'   => '26',
												'suffix'  => '+ Yrs',
												'label'   => 'Community Trust',
												'icon'    => 'history',
												'color'   => '#7e22ce',
												'animate' => 1,
												'visible' => 1,
												'order'   => 5,
											),
										),
									),
								)
							),
						),
					),
				),
				'pillars'   => array(
					'label'  => __( 'Core pillars', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Core pillars section', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'pillars' ),
								array(
									array(
										'key'       => 'pillars_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Eyebrow label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Foundational Framework',
									),
									array(
										'key'       => 'pillars_title',
										'type'      => 'text',
										'label'     => __( 'Section title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Driving Systemic Change Across Bangladesh',
									),
									array(
										'key'       => 'pillars_description',
										'type'      => 'textarea',
										'label'     => __( 'Section description', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 3,
										'default'   => 'Established in 1997 and registered with the Department of Social Services in 2000 (Reg: Nat-295/2000), PDO bridges the gap between rural grassroots realities and global development capital. We reject short-term handouts in favor of permanent community resilience.',
									),
									array(
										'key'        => 'pillars',
										'type'       => 'repeater',
										'label'      => __( 'Pillar cards', 'pdo-site-manager' ),
										'item_label' => 'title',
										'add_label'  => __( 'Add pillar card', 'pdo-site-manager' ),
										'fields'     => array(
											array(
												'key'   => 'image',
												'type'  => 'image',
												'label' => __( 'Image', 'pdo-site-manager' ),
											),
											array(
												'key'   => 'image_url',
												'type'  => 'url',
												'label' => __( 'Or image URL', 'pdo-site-manager' ),
											),
											array(
												'key'   => 'alt',
												'type'  => 'text',
												'label' => __( 'Image alt text', 'pdo-site-manager' ),
											),
											array(
												'key'   => 'icon',
												'type'  => 'icon',
												'label' => __( 'Icon', 'pdo-site-manager' ),
											),
											array(
												'key'       => 'title',
												'type'      => 'text',
												'label'     => __( 'Title', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'description',
												'type'      => 'textarea',
												'label'     => __( 'Description', 'pdo-site-manager' ),
												'bilingual' => true,
												'rows'      => 4,
											),
											array(
												'key'       => 'badge',
												'type'      => 'text',
												'label'     => __( 'SDG badge / category', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'footer',
												'type'      => 'text',
												'label'     => __( 'Footer statistic', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'link_label',
												'type'      => 'text',
												'label'     => __( 'Link label', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'   => 'link_url',
												'type'  => 'url',
												'label' => __( 'Link URL', 'pdo-site-manager' ),
											),
											array(
												'key'     => 'accent',
												'type'    => 'select',
												'label'   => __( 'Accent colour', 'pdo-site-manager' ),
												'options' => self::accent_choices(),
												'default' => 'emerald',
											),
										),
										'default'    => array(
											array(
												'image_url'   => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=700&q=80',
												'alt'         => "People's Empowerment & Education",
												'icon'        => 'graduation-cap',
												'title'       => "People’s Empowerment & Inclusion",
												'description' => 'Targeted dropout prevention, tiered scholarships (25%, 50%, 100%), second-chance academic reintegration, and social inclusion for marginalized youth, Dalits, ethnic minorities, and persons with disabilities.',
												'badge'       => 'SDG 1 & SDG 4',
												'footer'      => '1,200+ Scholarships Annually',
												'link_url'    => '/impact/',
												'accent'      => 'emerald',
												'visible'     => 1,
												'order'       => 0,
											),
											array(
												'image_url'   => 'https://images.unsplash.com/photo-1473448912268-2022ce9509d8?auto=format&fit=crop&w=700&q=80',
												'alt'         => 'Climate Resilience & Tree Guardians',
												'icon'        => 'tree',
												'title'       => 'Climate Resilience & Stewardship',
												'description' => 'Moving beyond token sapling distribution through our 250,000 "Tree Guardian" roadmap, quarterly plant survival M&E, river protection campaigns (Narad River), and circular zero-waste community models.',
												'badge'       => 'SDG 13 & SDG 15',
												'footer'      => '10,000+ Guardians Formed',
												'link_url'    => '/impact/',
												'accent'      => 'teal',
												'visible'     => 1,
												'order'       => 1,
											),
											array(
												'image_url'   => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=700&q=80',
												'alt'         => 'Innovation & Strategic Partnership',
												'icon'        => 'handshake',
												'title'       => 'Innovation & Partnership',
												'description' => 'Leveraging TVET vocational hubs (Bahadurpur Tech), "Janatar Bazar" anti-middleman agricultural hubs, digital mental health apps ("Monbarta"), and seamless execution with SRI Welfare Foundation.',
												'badge'       => 'SDG 8 & SDG 17',
												'footer'      => 'Tri-Sector Strategic Alliance',
												'link_url'    => '/impact/',
												'accent'      => 'amber',
												'visible'     => 1,
												'order'       => 2,
											),
										),
									),
								)
							),
						),
					),
				),
				'toc'       => array(
					'label'  => __( 'Theory of Change', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Theory of Change section', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'toc' ),
								array(
									array(
										'key'       => 'toc_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Eyebrow label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Transformative Methodology',
									),
									array(
										'key'       => 'toc_title',
										'type'      => 'text',
										'label'     => __( 'Section title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Our Theory of Change (ToC): Charity to Sustainable Empowerment',
									),
									array(
										'key'       => 'toc_description',
										'type'      => 'textarea',
										'label'     => __( 'Section description', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 2,
										'default'   => 'We do not provide passive relief. We identify systemic friction points and engineer durable human capital.',
									),
									array(
										'key'        => 'toc_stages',
										'type'       => 'repeater',
										'label'      => __( 'Stages', 'pdo-site-manager' ),
										'item_label' => 'title',
										'add_label'  => __( 'Add stage', 'pdo-site-manager' ),
										'fields'     => array(
											array(
												'key'   => 'number',
												'type'  => 'text',
												'label' => __( 'Stage number', 'pdo-site-manager' ),
											),
											array(
												'key'       => 'stage_label',
												'type'      => 'text',
												'label'     => __( 'Stage label', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'title',
												'type'      => 'text',
												'label'     => __( 'Heading', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'description',
												'type'      => 'textarea',
												'label'     => __( 'Description', 'pdo-site-manager' ),
												'bilingual' => true,
												'rows'      => 3,
											),
											array(
												'key'   => 'icon',
												'type'  => 'icon',
												'label' => __( 'Icon', 'pdo-site-manager' ),
											),
											array(
												'key'     => 'accent',
												'type'    => 'select',
												'label'   => __( 'Accent colour', 'pdo-site-manager' ),
												'options' => self::accent_choices(),
												'default' => 'emerald',
											),
										),
										'default'    => array(
											array(
												'number'      => '01',
												'stage_label' => 'Step 01: The Action',
												'title'       => 'Targeted Intervention',
												'description' => 'Diagnosing acute dropout bottlenecks, student hunger, and environmental vulnerability. Deploying Tiered Scholarships, Zero-Hunger lunches, and designated Tree Guardianship.',
												'icon'        => 'bank',
												'accent'      => 'emerald',
												'visible'     => 1,
												'order'       => 0,
											),
											array(
												'number'      => '02',
												'stage_label' => 'Step 02: The Output',
												'title'       => 'Immediate Friction Removal',
												'description' => 'Financial hurdles eradicated for 1,200+ students. 250+ students fed daily; children wear uniforms with pride; trees planted with assigned caretakers.',
												'icon'        => 'check-circle',
												'accent'      => 'teal',
												'visible'     => 1,
												'order'       => 1,
											),
											array(
												'number'      => '03',
												'stage_label' => 'Step 03: The Outcome',
												'title'       => 'Behavioral Transformation',
												'description' => 'School attendance leaps to 90%+. Psychological shame dissolves. Children nurture their saplings proactively and resist neighborhood deforestation.',
												'icon'        => 'chart-line',
												'accent'      => 'amber',
												'visible'     => 1,
												'order'       => 2,
											),
											array(
												'number'      => '04',
												'stage_label' => 'Step 04: The Impact',
												'title'       => 'Systemic Self-Reliance',
												'description' => 'Students graduate into skilled TVET employment. Generational poverty cycles are permanently broken and communities own their climate resilience.',
												'icon'        => 'award',
												'accent'      => 'blue',
												'visible'     => 1,
												'order'       => 3,
											),
										),
									),
								)
							),
						),
					),
				),
				'president' => array(
					'label'  => __( "President's teaser", 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( "President's message teaser", 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'president' ),
								array(
									array(
										'key'   => 'president_photo',
										'type'  => 'image',
										'label' => __( "President's photo", 'pdo-site-manager' ),
									),
									array(
										'key'     => 'president_photo_url',
										'type'    => 'url',
										'label'   => __( 'Or photo URL', 'pdo-site-manager' ),
										'default' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80',
									),
									array(
										'key'       => 'president_name',
										'type'      => 'text',
										'label'     => __( 'Name', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Shaikh Md Rakibul Islam',
									),
									array(
										'key'       => 'president_role',
										'type'      => 'text',
										'label'     => __( 'Designation', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'President, Peoples Development Organisation (PDO)',
									),
									array(
										'key'       => 'president_badge',
										'type'      => 'text',
										'label'     => __( 'Photo badge', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'President & Founder',
									),
									array(
										'key'       => 'president_quote',
										'type'      => 'textarea',
										'label'     => __( 'Quotation', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 5,
										'default'   => '"For over three decades, my journey as an educator has been driven by a singular conviction: true national development cannot be achieved from the top down; it must be nurtured at the grassroots... We do not view the marginalized as passive recipients of aid, but as resilient partners in development."',
									),
									array(
										'key'       => 'president_btn_label',
										'type'      => 'text',
										'label'     => __( 'Button label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Read the Full Presidential Message',
									),
									array(
										'key'     => 'president_btn_url',
										'type'    => 'url',
										'label'   => __( 'Button URL', 'pdo-site-manager' ),
										'default' => '/about/#president',
									),
								)
							),
						),
					),
				),
				'gallery'   => array(
					'label'  => __( 'Gallery', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Homepage gallery strip', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'gallery' ),
								array(
									array(
										'key'       => 'gallery_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Eyebrow label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Visual Documentation',
									),
									array(
										'key'       => 'gallery_title',
										'type'      => 'text',
										'label'     => __( 'Section title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Grassroots Operations in Pictures',
									),
									array(
										'key'       => 'gallery_link_label',
										'type'      => 'text',
										'label'     => __( 'Link label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'View All Impact Programs →',
									),
									array(
										'key'     => 'gallery_link_url',
										'type'    => 'url',
										'label'   => __( 'Link URL', 'pdo-site-manager' ),
										'default' => '/impact/',
									),
									array(
										'key'        => 'gallery',
										'type'       => 'repeater',
										'label'      => __( 'Gallery images', 'pdo-site-manager' ),
										'item_label' => 'caption',
										'add_label'  => __( 'Add gallery image', 'pdo-site-manager' ),
										'fields'     => array(
											array(
												'key'   => 'image',
												'type'  => 'image',
												'label' => __( 'Image', 'pdo-site-manager' ),
											),
											array(
												'key'   => 'image_url',
												'type'  => 'url',
												'label' => __( 'Or image URL', 'pdo-site-manager' ),
											),
											array(
												'key'   => 'alt',
												'type'  => 'text',
												'label' => __( 'Alt text', 'pdo-site-manager' ),
											),
											array(
												'key'       => 'caption',
												'type'      => 'text',
												'label'     => __( 'Caption', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'category',
												'type'      => 'text',
												'label'     => __( 'Category', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'   => 'url',
												'type'  => 'url',
												'label' => __( 'Destination URL (optional)', 'pdo-site-manager' ),
											),
										),
										'default'    => array(
											array(
												'image_url' => 'https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=400&q=80',
												'alt'       => 'Midday meal distribution',
												'caption'   => 'Mid-Day Meal',
												'category'  => 'Nutrition',
												'visible'   => 1,
												'order'     => 0,
											),
											array(
												'image_url' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=400&q=80',
												'alt'       => 'Planting saplings',
												'caption'   => 'Tree Guardian',
												'category'  => 'Climate',
												'visible'   => 1,
												'order'     => 1,
											),
											array(
												'image_url' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=400&q=80',
												'alt'       => 'Technical training',
												'caption'   => 'TVET Lab',
												'category'  => 'Education',
												'visible'   => 1,
												'order'     => 2,
											),
											array(
												'image_url' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=400&q=80',
												'alt'       => 'Winter blanket distribution',
												'caption'   => 'Winter Blankets',
												'category'  => 'Relief',
												'visible'   => 1,
												'order'     => 3,
											),
											array(
												'image_url' => 'https://images.unsplash.com/photo-1576765608535-5f04d1e3f289?auto=format&fit=crop&w=400&q=80',
												'alt'       => '1-Taka medical camp',
												'caption'   => '1-Taka Clinic',
												'category'  => 'Health',
												'visible'   => 1,
												'order'     => 4,
											),
											array(
												'image_url' => 'https://images.unsplash.com/photo-1596495578065-6e0763fa1178?auto=format&fit=crop&w=400&q=80',
												'alt'       => 'School uniform recipients',
												'caption'   => 'Free Uniforms',
												'category'  => 'Education',
												'visible'   => 1,
												'order'     => 5,
											),
										),
									),
								)
							),
						),
					),
				),
				'partners'  => array(
					'label'  => __( 'Partners', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Partners showcase', 'pdo-site-manager' ),
							'desc'   => __( 'Partner records themselves are managed under PDO Website Manager → Partners.', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'partners' ),
								array(
									array(
										'key'       => 'partners_title',
										'type'      => 'text',
										'label'     => __( 'Section heading', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Strategic Execution & Institutional Network',
									),
									array(
										'key'     => 'partners_limit',
										'type'    => 'number',
										'label'   => __( 'Maximum partners to display', 'pdo-site-manager' ),
										'default' => 8,
										'min'     => 1,
										'max'     => 48,
									),
								)
							),
						),
					),
				),
				'cta'       => array(
					'label'  => __( 'Call to action', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Closing call-to-action', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'cta' ),
								array(
									array(
										'key'       => 'cta_title',
										'type'      => 'text',
										'label'     => __( 'Title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Partner with PDO and scale grassroots impact',
									),
									array(
										'key'       => 'cta_description',
										'type'      => 'textarea',
										'label'     => __( 'Description', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 3,
										'default'   => 'Join our network of INGOs, CSR foundations, government agencies and individual volunteers building climate-resilient, educated and self-reliant communities in northern Bangladesh.',
									),
									array(
										'key'       => 'cta_btn1_label',
										'type'      => 'text',
										'label'     => __( 'Primary button label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Become a Volunteer',
									),
									array(
										'key'     => 'cta_btn1_url',
										'type'    => 'url',
										'label'   => __( 'Primary button URL', 'pdo-site-manager' ),
										'default' => '/get-involved/#volunteer',
									),
									array(
										'key'       => 'cta_btn2_label',
										'type'      => 'text',
										'label'     => __( 'Secondary button label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Institutional Partnership',
									),
									array(
										'key'     => 'cta_btn2_url',
										'type'    => 'url',
										'label'   => __( 'Secondary button URL', 'pdo-site-manager' ),
										'default' => '/get-involved/#partner',
									),
								)
							),
						),
					),
				),
			),
		);
	}

	/* --------------------------------------------------------------------- */
	/* About                                                                 */
	/* --------------------------------------------------------------------- */

	/**
	 * About page group.
	 *
	 * @return array
	 */
	private static function group_about() {
		return array(
			'option' => 'pdo_sm_about',
			'label'  => __( 'About Us', 'pdo-site-manager' ),
			'tabs'   => array(
				'intro'      => array(
					'label'  => __( 'Introduction', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Page introduction', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'intro' ),
								array(
									array(
										'key'       => 'intro_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Eyebrow label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Our Identity & Roots',
									),
									array(
										'key'       => 'intro_title',
										'type'      => 'text',
										'label'     => __( 'Page title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'About Peoples Development Organisation',
									),
									array(
										'key'       => 'intro_description',
										'type'      => 'textarea',
										'label'     => __( 'Introductory description', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 3,
										'default'   => 'Driving systemic change since 1997. From rural roots in Singra, Natore to a scalable nationwide development institution.',
									),
									array(
										'key'   => 'intro_banner',
										'type'  => 'image',
										'label' => __( 'Banner image (optional)', 'pdo-site-manager' ),
									),
								)
							),
						),
					),
				),
				'history'    => array(
					'label'  => __( 'Who we are', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Who we are & history', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'history' ),
								array(
									array(
										'key'       => 'history_title',
										'type'      => 'text',
										'label'     => __( 'Section title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Who We Are: Driving Systemic Change Since 1997',
									),
									array(
										'key'     => 'history_icon',
										'type'    => 'icon',
										'label'   => __( 'Icon', 'pdo-site-manager' ),
										'default' => 'bank',
									),
									array(
										'key'       => 'history_content',
										'type'      => 'richtext',
										'label'     => __( 'Paragraphs', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => '<p>Established in 1997 and formally registered with the Department of Social Services in 2000 (Registration No: Nat-295/2000), the Peoples Development Organisation (PDO) is a grassroots, non-profit development organisation dedicated to driving systemic change across marginalized communities in Bangladesh.</p><p>Originating from deep rural roots with our headquarters in Singra, Natore, PDO has strategically evolved into a nationwide development force. We act as a critical bridge between vulnerable populations and global opportunities, focusing on scalable, community-driven interventions.</p><p>Fully aligned with the United Nations Sustainable Development Goals (SDGs) and the national development vision of Bangladesh, our multifaceted initiatives encompass quality education, climate resilience, youth empowerment, and digital innovation. By integrating modern technology with on-the-ground realities, PDO empowers communities to break the cycle of poverty.</p>',
									),
									array(
										'key'   => 'history_image',
										'type'  => 'image',
										'label' => __( 'Featured image', 'pdo-site-manager' ),
									),
									array(
										'key'     => 'history_image_position',
										'type'    => 'select',
										'label'   => __( 'Image position', 'pdo-site-manager' ),
										'options' => array(
											'right' => __( 'Right', 'pdo-site-manager' ),
											'left'  => __( 'Left', 'pdo-site-manager' ),
										),
										'default' => 'right',
									),
									array(
										'key'     => 'history_background',
										'type'    => 'select',
										'label'   => __( 'Background', 'pdo-site-manager' ),
										'options' => self::background_choices(),
										'default' => '',
									),
								)
							),
						),
						array(
							'title'  => __( 'Institutional milestones', 'pdo-site-manager' ),
							'fields' => array(
								array(
									'key'       => 'milestones_title',
									'type'      => 'text',
									'label'     => __( 'Milestones heading', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Institutional Milestones',
								),
								array(
									'key'        => 'milestones',
									'type'       => 'repeater',
									'label'      => __( 'Milestones', 'pdo-site-manager' ),
									'item_label' => 'title',
									'add_label'  => __( 'Add milestone', 'pdo-site-manager' ),
									'fields'     => array(
										array(
											'key'   => 'year',
											'type'  => 'text',
											'label' => __( 'Year', 'pdo-site-manager' ),
										),
										array(
											'key'       => 'title',
											'type'      => 'text',
											'label'     => __( 'Title', 'pdo-site-manager' ),
											'bilingual' => true,
										),
										array(
											'key'       => 'description',
											'type'      => 'textarea',
											'label'     => __( 'Description', 'pdo-site-manager' ),
											'bilingual' => true,
											'rows'      => 2,
										),
										array(
											'key'   => 'icon',
											'type'  => 'icon',
											'label' => __( 'Icon', 'pdo-site-manager' ),
										),
										array(
											'key'   => 'image',
											'type'  => 'image',
											'label' => __( 'Image (optional)', 'pdo-site-manager' ),
										),
									),
									'default'    => array(
										array(
											'year'        => '1997',
											'title'       => 'Community Inception',
											'description' => 'Grassroots youth movement launched in Bahadurpur, Natore.',
											'icon'        => 'seedling',
											'visible'     => 1,
											'order'       => 0,
										),
										array(
											'year'        => '2000',
											'title'       => 'Formal Government Registration',
											'description' => 'Registered with Dept. of Social Services (Reg: Nat-295/2000).',
											'icon'        => 'certificate',
											'visible'     => 1,
											'order'       => 1,
										),
										array(
											'year'        => '2010',
											'title'       => 'Bahadurpur Tech Integration',
											'description' => 'Established TVET flagship implementation hub for technical skills.',
											'icon'        => 'school',
											'visible'     => 1,
											'order'       => 2,
										),
										array(
											'year'        => '2026',
											'title'       => 'Vision 2030 & Mega Scaling',
											'description' => '10k+ Tree Guardians, Zero Hunger pilot, and 1,200+ annual scholarships.',
											'icon'        => 'chart-line',
											'visible'     => 1,
											'order'       => 3,
										),
									),
								),
							),
						),
					),
				),
				'vision'     => array(
					'label'  => __( 'Vision & values', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Vision, mission and values', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'vision' ),
								array(
									array(
										'key'       => 'vision_title',
										'type'      => 'text',
										'label'     => __( 'Vision heading', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Our Vision',
									),
									array(
										'key'     => 'vision_icon',
										'type'    => 'icon',
										'label'   => __( 'Vision icon', 'pdo-site-manager' ),
										'default' => 'eye',
									),
									array(
										'key'       => 'vision_description',
										'type'      => 'textarea',
										'label'     => __( 'Vision description', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 4,
										'default'   => 'To build an inclusive, climate-resilient, and digitally empowered society where marginalized communities enjoy universal human rights, economic independence, and equitable access to resources.',
									),
									array(
										'key'   => 'vision_image',
										'type'  => 'image',
										'label' => __( 'Vision image', 'pdo-site-manager' ),
									),
									array(
										'key'     => 'vision_background',
										'type'    => 'select',
										'label'   => __( 'Vision card background', 'pdo-site-manager' ),
										'options' => self::background_choices(),
										'default' => 'tint',
									),
									array(
										'key'       => 'mission_title',
										'type'      => 'text',
										'label'     => __( 'Mission heading', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Our Mission',
									),
									array(
										'key'     => 'mission_icon',
										'type'    => 'icon',
										'label'   => __( 'Mission icon', 'pdo-site-manager' ),
										'default' => 'bullseye',
									),
									array(
										'key'       => 'mission_description',
										'type'      => 'textarea',
										'label'     => __( 'Mission description', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 4,
										'default'   => 'To design and implement sustainable, community-driven projects by integrating modern technology (ICT & AI), fostering social entrepreneurship, and establishing strong cross-sectoral partnerships to eliminate systemic barriers to education, health, and economic mobility.',
									),
									array(
										'key'   => 'mission_image',
										'type'  => 'image',
										'label' => __( 'Mission image', 'pdo-site-manager' ),
									),
									array(
										'key'     => 'mission_background',
										'type'    => 'select',
										'label'   => __( 'Mission card background', 'pdo-site-manager' ),
										'options' => self::background_choices(),
										'default' => 'tint',
									),
									array(
										'key'       => 'values_title',
										'type'      => 'text',
										'label'     => __( 'Core values heading', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Core Values',
									),
									array(
										'key'     => 'values_icon',
										'type'    => 'icon',
										'label'   => __( 'Core values icon', 'pdo-site-manager' ),
										'default' => 'shield-check',
									),
									array(
										'key'        => 'values',
										'type'       => 'repeater',
										'label'      => __( 'Core values', 'pdo-site-manager' ),
										'item_label' => 'title',
										'add_label'  => __( 'Add value', 'pdo-site-manager' ),
										'fields'     => array(
											array(
												'key'       => 'title',
												'type'      => 'text',
												'label'     => __( 'Title', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'description',
												'type'      => 'text',
												'label'     => __( 'Description', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'   => 'icon',
												'type'  => 'icon',
												'label' => __( 'Icon', 'pdo-site-manager' ),
											),
										),
										'default'    => array(
											array(
												'title'       => 'Transparency & Accountability',
												'description' => 'Rigorous audits & integrity.',
												'icon'        => 'scale-balanced',
												'visible'     => 1,
												'order'       => 0,
											),
											array(
												'title'       => 'Needs-Based Action',
												'description' => 'Rooted strictly in actual grassroots realities.',
												'icon'        => 'bullseye',
												'visible'     => 1,
												'order'       => 1,
											),
											array(
												'title'       => 'Inclusivity & Gender Justice',
												'description' => 'Empowering without discrimination.',
												'icon'        => 'users',
												'visible'     => 1,
												'order'       => 2,
											),
											array(
												'title'       => 'Do No Harm',
												'description' => 'Protecting communities ethically and safely.',
												'icon'        => 'shield',
												'visible'     => 1,
												'order'       => 3,
											),
										),
									),
								)
							),
						),
					),
				),
				'thematic'   => array(
					'label'  => __( 'Thematic pillars', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Strategic thematic pillars', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'thematic' ),
								array(
									array(
										'key'       => 'thematic_title',
										'type'      => 'text',
										'label'     => __( 'Section title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Six Strategic Thematic Pillars',
									),
									array(
										'key'       => 'thematic_description',
										'type'      => 'text',
										'label'     => __( 'Section description', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Holistic community interventions executing UN SDGs',
									),
									array(
										'key'        => 'thematic',
										'type'       => 'repeater',
										'label'      => __( 'Thematic pillars', 'pdo-site-manager' ),
										'item_label' => 'title',
										'add_label'  => __( 'Add thematic pillar', 'pdo-site-manager' ),
										'fields'     => array(
											array(
												'key'       => 'number',
												'type'      => 'text',
												'label'     => __( 'Pillar number / label', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'title',
												'type'      => 'text',
												'label'     => __( 'Title', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'   => 'icon',
												'type'  => 'icon',
												'label' => __( 'Icon', 'pdo-site-manager' ),
											),
											array(
												'key'       => 'description',
												'type'      => 'textarea',
												'label'     => __( 'Description', 'pdo-site-manager' ),
												'bilingual' => true,
												'rows'      => 3,
											),
											array(
												'key'   => 'image',
												'type'  => 'image',
												'label' => __( 'Image (optional)', 'pdo-site-manager' ),
											),
											array(
												'key'   => 'sdgs',
												'type'  => 'text',
												'label' => __( 'Related SDGs', 'pdo-site-manager' ),
											),
											array(
												'key'     => 'accent',
												'type'    => 'select',
												'label'   => __( 'Accent colour', 'pdo-site-manager' ),
												'options' => self::accent_choices(),
												'default' => 'emerald',
											),
										),
										'default'    => array(
											array(
												'number'      => 'Pillar 1',
												'title'       => 'Quality Education & TVET',
												'icon'        => 'graduation-cap',
												'description' => '1,200+ Tiered Scholarships, rural TVET labs, Mid-Day meals for retention, psycho-social mentoring for failed board examinees.',
												'sdgs'        => 'SDG 4',
												'accent'      => 'emerald',
												'visible'     => 1,
												'order'       => 0,
											),
											array(
												'number'      => 'Pillar 2',
												'title'       => 'Climate Action & Circular Economy',
												'icon'        => 'recycle',
												'description' => '250,000 Tree Guardians target by 2030, Zero-Waste models, solar mini-grids, disaster response squad during floods.',
												'sdgs'        => 'SDG 13, SDG 15',
												'accent'      => 'teal',
												'visible'     => 1,
												'order'       => 1,
											),
											array(
												'number'      => 'Pillar 3',
												'title'       => 'Universal Health & Tele-counseling',
												'icon'        => 'stethoscope',
												'description' => '"1-Taka" grassroots clinics, "Monbarta" 24/7 youth mental health app, SRHR adolescent care, assistive device distribution.',
												'sdgs'        => 'SDG 3',
												'accent'      => 'rose',
												'visible'     => 1,
												'order'       => 2,
											),
											array(
												'number'      => 'Pillar 4',
												'title'       => 'Social Inclusion & Human Rights',
												'icon'        => 'users',
												'description' => 'Transgender (Hijra) livelihoods, anti-GBV emergency legal aid, youth School Cabinets for civic democratic awareness.',
												'sdgs'        => 'SDG 5, SDG 10',
												'accent'      => 'purple',
												'visible'     => 1,
												'order'       => 3,
											),
											array(
												'number'      => 'Pillar 5',
												'title'       => 'Smart Agri & Janatar Bazar',
												'icon'        => 'basket',
												'description' => 'Direct market hubs eliminating syndicates, Zero-Hunger campus models, climate-resilient farming, mini cold storage.',
												'sdgs'        => 'SDG 2, SDG 8',
												'accent'      => 'amber',
												'visible'     => 1,
												'order'       => 4,
											),
											array(
												'number'      => 'Pillar 6',
												'title'       => 'Digital Transformation & IT',
												'icon'        => 'cpu',
												'description' => 'Rural youth tech incubation (AI, Freelancing, IoT), digital literacy drives, anti-propaganda and cybersecurity workshops.',
												'sdgs'        => 'SDG 9',
												'accent'      => 'blue',
												'visible'     => 1,
												'order'       => 5,
											),
										),
									),
								)
							),
						),
					),
				),
				'president'  => array(
					'label'  => __( "President's message", 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( "Full president's message", 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'president' ),
								array(
									array(
										'key'   => 'president_photo',
										'type'  => 'image',
										'label' => __( 'Photo', 'pdo-site-manager' ),
									),
									array(
										'key'     => 'president_photo_url',
										'type'    => 'url',
										'label'   => __( 'Or photo URL', 'pdo-site-manager' ),
										'default' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
									),
									array(
										'key'       => 'president_label',
										'type'      => 'text',
										'label'     => __( 'Introductory label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Leadership Speech',
									),
									array(
										'key'       => 'president_heading',
										'type'      => 'text',
										'label'     => __( 'Heading', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Message from the President',
									),
									array(
										'key'       => 'president_name',
										'type'      => 'text',
										'label'     => __( 'Name', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Shaikh Md Rakibul Islam',
									),
									array(
										'key'       => 'president_role',
										'type'      => 'text',
										'label'     => __( 'Designation', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'President, Peoples Development Organisation (PDO)',
									),
									array(
										'key'       => 'president_message',
										'type'      => 'richtext',
										'label'     => __( 'Full message', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => '<p><strong>"Empowering the Grassroots, Engineering the Future"</strong></p><p>Welcome to the Peoples Development Organisation (PDO).</p><p>For over three decades, my journey as an educator has been driven by a singular, unwavering conviction: true national development cannot be achieved from the top down; it must be nurtured at the grassroots. Having dedicated my life to the expansion of education in rural Bangladesh, I have witnessed firsthand both the immense potential of our marginalized youth and the systemic barriers that hold them back.</p><p>Education is the bedrock of societal transformation. When we established Bahadurpur Technical School &amp; College, our vision was not merely to build an institution, but to create a hub for human capital development—a place where underprivileged youth could acquire the technical and vocational skills necessary to break the cycle of intergenerational poverty. However, academic institutions alone cannot solve the multifaceted challenges of rural communities. Issues like climate vulnerability, public health crises, and economic exclusion require a broader, more integrated socio-economic intervention.</p><p>This realization is the heartbeat of the Peoples Development Organisation (PDO).</p><p>Since its inception, PDO has served as a dynamic platform to scale our community-driven initiatives. We do not view the marginalized as passive recipients of aid, but as resilient partners in development. From our rigorous \'Tiered Scholarship Program\' and \'Zero Hunger Campus Model\' to our massive climate action campaigns and rural healthcare interventions, every initiative at PDO is designed to foster self-reliance, ensure gender justice, and build climate resilience.</p><p>Our strength lies in unity and strategic collaboration. The profound impacts we have achieved over the years—often in close partnership with the SRI Welfare Foundation and our dedicated community volunteers—demonstrate that when grassroots determination is paired with structured, transparent execution, the results are deeply transformative.</p><p>To our international donors, development partners, and well-wishers, I extend a heartfelt invitation. Join us in this journey of transformative impact. Together, we can bridge the gap between global resources and grassroots realities, ensuring that no community is left behind.</p>',
									),
									array(
										'key'       => 'president_signature',
										'type'      => 'text',
										'label'     => __( 'Signature name', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Shaikh Md Rakibul Islam',
									),
									array(
										'key'   => 'president_signature_image',
										'type'  => 'image',
										'label' => __( 'Signature image (optional)', 'pdo-site-manager' ),
									),
									array(
										'key'       => 'president_tenure',
										'type'      => 'text',
										'label'     => __( 'Date / tenure', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Official Presidential Address',
									),
								)
							),
						),
					),
				),
				'leadership' => array(
					'label'  => __( 'Leadership', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Leadership & management', 'pdo-site-manager' ),
							'desc'   => __( 'Individual team members are managed under PDO Website Manager → Team Members. Groups come from the Team Group taxonomy.', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'leadership' ),
								array(
									array(
										'key'       => 'leadership_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Eyebrow label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Governance Directory',
									),
									array(
										'key'       => 'leadership_title',
										'type'      => 'text',
										'label'     => __( 'Section title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Executive Committee (2026–2028)',
									),
									array(
										'key'       => 'leadership_description',
										'type'      => 'text',
										'label'     => __( 'Section description', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Formally elected 2-year tenure spearheading strategic direction and glocal compliance.',
									),
									array(
										'key'     => 'leadership_layout',
										'type'    => 'select',
										'label'   => __( 'Layout', 'pdo-site-manager' ),
										'options' => array(
											'grid'    => __( 'Photo grid', 'pdo-site-manager' ),
											'cards'   => __( 'Detailed cards', 'pdo-site-manager' ),
											'compact' => __( 'Compact list', 'pdo-site-manager' ),
										),
										'default' => 'grid',
									),
									array(
										'key'     => 'leadership_columns',
										'type'    => 'number',
										'label'   => __( 'Columns on desktop', 'pdo-site-manager' ),
										'default' => 4,
										'min'     => 2,
										'max'     => 6,
									),
								)
							),
						),
					),
				),
				'partners'   => array(
					'label'  => __( 'Strategic partners', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Strategic partners section', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'partners' ),
								array(
									array(
										'key'       => 'partners_title',
										'type'      => 'text',
										'label'     => __( 'Section title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Strategic Partners & Institutional Network',
									),
									array(
										'key'       => 'partners_description',
										'type'      => 'text',
										'label'     => __( 'Section description', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Tri-sector collaboration with government, academia, foundations and the global development community.',
									),
								)
							),
						),
					),
				),
			),
		);
	}

	/* --------------------------------------------------------------------- */
	/* Impact                                                                */
	/* --------------------------------------------------------------------- */

	/**
	 * Impact page group.
	 *
	 * @return array
	 */
	private static function group_impact() {
		return array(
			'option' => 'pdo_sm_impact',
			'label'  => __( 'Our Impact', 'pdo-site-manager' ),
			'tabs'   => array(
				'intro'       => array(
					'label'  => __( 'Introduction', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Page introduction', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'intro' ),
								array(
									array(
										'key'       => 'intro_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Small label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Flagship Strategic Interventions',
									),
									array(
										'key'       => 'intro_title',
										'type'      => 'text',
										'label'     => __( 'Main heading', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Our Impact & Initiatives',
									),
									array(
										'key'       => 'intro_description',
										'type'      => 'textarea',
										'label'     => __( 'Description', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 3,
										'default'   => 'Scalable, community-anchored models tackling education barriers, classroom hunger, climate vulnerability, and youth disenfranchisement.',
									),
									array(
										'key'   => 'intro_banner',
										'type'  => 'image',
										'label' => __( 'Featured banner image', 'pdo-site-manager' ),
									),
								)
							),
						),
					),
				),
				'scholarship' => array(
					'label'  => __( 'Scholarships & chart', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Scholarship programme', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'scholarship' ),
								array(
									array(
										'key'       => 'scholarship_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Eyebrow label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Flagship Academic Retention Initiative',
									),
									array(
										'key'       => 'scholarship_title',
										'type'      => 'text',
										'label'     => __( 'Title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'The PDO Tiered Scholarship Program',
									),
									array(
										'key'       => 'scholarship_content',
										'type'      => 'richtext',
										'label'     => __( 'Description', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => '<p>Since 2000, PDO has actively dismantled educational exclusion for marginalized students across Natore. Recognizing that financial hardship triggers peak dropouts during 9th Grade, SSC, and HSC levels, we operate a <strong>Needs-Based Tiered Allocation System</strong> rather than a generic one-size-fits-all model.</p><p>By conducting rigorous background assessments and partnering directly with Bahadurpur Technical School &amp; College as our Central Implementation Hub, we safeguard 1,200+ vulnerable youth annually with pinpoint Value for Money (VfM).</p>',
									),
									array(
										'key'       => 'chart_title',
										'type'      => 'text',
										'label'     => __( 'Chart title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Annual Scholarship Allocation',
									),
									array(
										'key'       => 'chart_subtitle',
										'type'      => 'text',
										'label'     => __( 'Chart subtitle', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Value for Money (VfM) Optimized Needs Distribution',
									),
									array(
										'key'        => 'tiers',
										'type'       => 'repeater',
										'label'      => __( 'Scholarship tiers (drives the chart)', 'pdo-site-manager' ),
										'item_label' => 'title',
										'add_label'  => __( 'Add tier', 'pdo-site-manager' ),
										'desc'       => __( 'The doughnut chart and the percentages are generated from these records.', 'pdo-site-manager' ),
										'fields'     => array(
											array(
												'key'       => 'title',
												'type'      => 'text',
												'label'     => __( 'Tier title', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'   => 'percentage',
												'type'  => 'text',
												'label' => __( 'Scholarship percentage', 'pdo-site-manager' ),
											),
											array(
												'key'     => 'students',
												'type'    => 'number',
												'label'   => __( 'Student count', 'pdo-site-manager' ),
												'default' => 0,
												'min'     => 0,
												'max'     => 1000000,
											),
											array(
												'key'       => 'students_label',
												'type'      => 'text',
												'label'     => __( 'Student count label', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'description',
												'type'      => 'text',
												'label'     => __( 'Description', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'     => 'color',
												'type'    => 'color',
												'label'   => __( 'Chart colour', 'pdo-site-manager' ),
												'default' => '#0d7a53',
											),
										),
										'default'    => array(
											array(
												'title'          => 'Tier 1: 100% Full',
												'percentage'     => '100%',
												'students'       => 150,
												'students_label' => '150+ Students',
												'description'    => 'Extreme poor, orphans, at immediate risk of abandonment.',
												'color'          => '#0d7a53',
												'visible'        => 1,
												'order'          => 0,
											),
											array(
												'title'          => 'Tier 2: 50% Half',
												'percentage'     => '50%',
												'students'       => 300,
												'students_label' => '300+ Students',
												'description'    => 'Low-income families struggling with exam & tuition fees.',
												'color'          => '#14b8a6',
												'visible'        => 1,
												'order'          => 1,
											),
											array(
												'title'          => 'Tier 3: 25% Partial',
												'percentage'     => '25%',
												'students'       => 750,
												'students_label' => '750+ Students',
												'description'    => 'Supplementary assistance for books, commute, & supplies.',
												'color'          => '#f59e0b',
												'visible'        => 1,
												'order'          => 2,
											),
										),
									),
								)
							),
						),
					),
				),
				'programs'    => array(
					'label'  => __( 'Programs', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Programs & initiatives section', 'pdo-site-manager' ),
							'desc'   => __( 'Individual programs are managed under PDO Website Manager → Programs.', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'programs' ),
								array(
									array(
										'key'       => 'programs_title',
										'type'      => 'text',
										'label'     => __( 'Section title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Programs & Initiatives',
									),
									array(
										'key'       => 'programs_description',
										'type'      => 'text',
										'label'     => __( 'Section description', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Flagship models designed for replication by INGO, CSR and government partners.',
									),
									array(
										'key'     => 'programs_limit',
										'type'    => 'number',
										'label'   => __( 'Maximum programs to display', 'pdo-site-manager' ),
										'default' => 12,
										'min'     => 1,
										'max'     => 60,
									),
								)
							),
						),
					),
				),
				'stories'     => array(
					'label'  => __( 'Impact stories', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Impact stories section', 'pdo-site-manager' ),
							'desc'   => __( 'Individual stories are managed under PDO Website Manager → Impact Stories.', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'stories' ),
								array(
									array(
										'key'       => 'stories_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Eyebrow label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Voices from the Ground',
									),
									array(
										'key'       => 'stories_title',
										'type'      => 'text',
										'label'     => __( 'Section title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Stories of Impact: Beyond Numbers',
									),
									array(
										'key'       => 'stories_description',
										'type'      => 'text',
										'label'     => __( 'Section description', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Behind every data point is a human life restored and empowered.',
									),
									array(
										'key'     => 'stories_limit',
										'type'    => 'number',
										'label'   => __( 'Maximum stories to display', 'pdo-site-manager' ),
										'default' => 9,
										'min'     => 1,
										'max'     => 60,
									),
								)
							),
						),
					),
				),
				'vision2030'  => array(
					'label'  => __( 'Vision 2030', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Vision 2030 strategic targets', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'vision2030' ),
								array(
									array(
										'key'       => 'vision2030_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Eyebrow label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Roadmap to 2030',
									),
									array(
										'key'       => 'vision2030_title',
										'type'      => 'text',
										'label'     => __( 'Title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Strategic Vision 2030: Scaling the Impact',
									),
									array(
										'key'       => 'vision2030_description',
										'type'      => 'textarea',
										'label'     => __( 'Description', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 3,
										'default'   => 'Aligning directly with the final stretch of the UN Sustainable Development Goals, our Vision 2030 shifts community aid from passive handouts to systemic local ownership:',
									),
									array(
										'key'        => 'vision2030_targets',
										'type'       => 'repeater',
										'label'      => __( 'Strategic targets', 'pdo-site-manager' ),
										'item_label' => 'title',
										'add_label'  => __( 'Add target', 'pdo-site-manager' ),
										'fields'     => array(
											array(
												'key'   => 'number',
												'type'  => 'text',
												'label' => __( 'Target number', 'pdo-site-manager' ),
											),
											array(
												'key'   => 'suffix',
												'type'  => 'text',
												'label' => __( 'Suffix', 'pdo-site-manager' ),
											),
											array(
												'key'       => 'title',
												'type'      => 'text',
												'label'     => __( 'Target title', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'description',
												'type'      => 'textarea',
												'label'     => __( 'Description', 'pdo-site-manager' ),
												'bilingual' => true,
												'rows'      => 2,
											),
											array(
												'key'   => 'icon',
												'type'  => 'icon',
												'label' => __( 'Icon', 'pdo-site-manager' ),
											),
											array(
												'key'     => 'accent',
												'type'    => 'select',
												'label'   => __( 'Background accent', 'pdo-site-manager' ),
												'options' => self::accent_choices(),
												'default' => 'emerald',
											),
										),
										'default'    => array(
											array(
												'number'      => '250,000',
												'suffix'      => '+',
												'title'       => 'Tree Guardians Cultivated',
												'description' => 'Creating a permanent, volunteer-monitored carbon sink across northern Bangladesh.',
												'icon'        => 'tree',
												'accent'      => 'teal',
												'visible'     => 1,
												'order'       => 0,
											),
											array(
												'number'      => '5,000',
												'suffix'      => '+',
												'title'       => 'Dropouts Reintegrated',
												'description' => 'Zero dropouts policy across our target educational zones with TVET technical certifications.',
												'icon'        => 'graduation-cap',
												'accent'      => 'emerald',
												'visible'     => 1,
												'order'       => 1,
											),
											array(
												'number'      => '20',
												'suffix'      => '+ Campuses',
												'title'       => 'Zero Hunger Replications',
												'description' => 'Guaranteeing 10,000+ rural students receive free daily midday nutrition.',
												'icon'        => 'bowl-food',
												'accent'      => 'amber',
												'visible'     => 1,
												'order'       => 2,
											),
										),
									),
								)
							),
						),
					),
				),
			),
		);
	}

	/* --------------------------------------------------------------------- */
	/* Transparency                                                          */
	/* --------------------------------------------------------------------- */

	/**
	 * Transparency page group.
	 *
	 * @return array
	 */
	private static function group_transparency() {
		return array(
			'option' => 'pdo_sm_transparency',
			'label'  => __( 'Transparency', 'pdo-site-manager' ),
			'tabs'   => array(
				'intro'      => array(
					'label'  => __( 'Introduction', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Page introduction', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'intro' ),
								array(
									array(
										'key'       => 'intro_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Small label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Institutional Trust & Accountability',
									),
									array(
										'key'       => 'intro_title',
										'type'      => 'text',
										'label'     => __( 'Main heading', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Transparency & Compliance',
									),
									array(
										'key'       => 'intro_description',
										'type'      => 'textarea',
										'label'     => __( 'Description', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 3,
										'default'   => 'International INGO standard governance, rigorous MEAL frameworks, anti-money laundering compliance, and open audit disclosures.',
									),
								)
							),
						),
					),
				),
				'governance' => array(
					'label'  => __( 'Governance', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Governance & compliance cards', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'governance' ),
								array(
									array(
										'key'       => 'governance_title',
										'type'      => 'text',
										'label'     => __( 'Section title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Governance & Compliance',
									),
									array(
										'key'        => 'governance',
										'type'       => 'repeater',
										'label'      => __( 'Compliance cards', 'pdo-site-manager' ),
										'item_label' => 'title',
										'add_label'  => __( 'Add compliance card', 'pdo-site-manager' ),
										'fields'     => array(
											array(
												'key'   => 'icon',
												'type'  => 'icon',
												'label' => __( 'Icon', 'pdo-site-manager' ),
											),
											array(
												'key'       => 'title',
												'type'      => 'text',
												'label'     => __( 'Title', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'description',
												'type'      => 'textarea',
												'label'     => __( 'Description', 'pdo-site-manager' ),
												'bilingual' => true,
												'rows'      => 3,
											),
											array(
												'key'   => 'image',
												'type'  => 'image',
												'label' => __( 'Image (optional)', 'pdo-site-manager' ),
											),
											array(
												'key'   => 'link_url',
												'type'  => 'url',
												'label' => __( 'Policy link (optional)', 'pdo-site-manager' ),
											),
											array(
												'key'       => 'link_label',
												'type'      => 'text',
												'label'     => __( 'Policy link label', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'     => 'accent',
												'type'    => 'select',
												'label'   => __( 'Accent colour', 'pdo-site-manager' ),
												'options' => self::accent_choices(),
												'default' => 'emerald',
											),
										),
										'default'    => array(
											array(
												'icon'        => 'shield-check',
												'title'       => 'Safeguarding & PSEA',
												'description' => 'Strict Zero-Tolerance policy toward Sexual Exploitation and Abuse (PSEA) and comprehensive protection protocols for children and vulnerable adults.',
												'accent'      => 'emerald',
												'visible'     => 1,
												'order'       => 0,
											),
											array(
												'icon'        => 'megaphone',
												'title'       => 'GRM & Whistleblowing',
												'description' => 'Confidential, multi-channel Grievance Redressal Mechanism empowering students, parents, and field staff to report infractions safely.',
												'accent'      => 'teal',
												'visible'     => 1,
												'order'       => 1,
											),
											array(
												'icon'        => 'scale-balanced',
												'title'       => 'AML / CFT Compliance',
												'description' => 'All funding routed exclusively through institutional banking channels following Anti-Money Laundering & Countering Financing of Terrorism standards.',
												'accent'      => 'amber',
												'visible'     => 1,
												'order'       => 2,
											),
											array(
												'icon'        => 'lock',
												'title'       => 'Data Ethics & Privacy',
												'description' => 'Compliant with global personal privacy protocols, safeguarding beneficiary dignity and digital mental health confidential data.',
												'accent'      => 'blue',
												'visible'     => 1,
												'order'       => 3,
											),
										),
									),
								)
							),
						),
					),
				),
				'meal'       => array(
					'label'  => __( 'MEAL framework', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'MEAL framework', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'meal' ),
								array(
									array(
										'key'       => 'meal_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Eyebrow label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Monitoring, Evaluation, Accountability, and Learning',
									),
									array(
										'key'       => 'meal_title',
										'type'      => 'text',
										'label'     => __( 'Title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'The PDO MEAL Framework',
									),
									array(
										'key'       => 'meal_description',
										'type'      => 'textarea',
										'label'     => __( 'Description', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 2,
										'default'   => 'We ensure every dollar is tracked against quantifiable human progress. Our framework utilizes live operational loops:',
									),
									array(
										'key'        => 'meal_items',
										'type'       => 'repeater',
										'label'      => __( 'Framework items', 'pdo-site-manager' ),
										'item_label' => 'title',
										'add_label'  => __( 'Add framework item', 'pdo-site-manager' ),
										'fields'     => array(
											array(
												'key'       => 'title',
												'type'      => 'text',
												'label'     => __( 'Title', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'description',
												'type'      => 'textarea',
												'label'     => __( 'Description', 'pdo-site-manager' ),
												'bilingual' => true,
												'rows'      => 3,
											),
											array(
												'key'   => 'icon',
												'type'  => 'icon',
												'label' => __( 'Icon', 'pdo-site-manager' ),
											),
											array(
												'key'   => 'image',
												'type'  => 'image',
												'label' => __( 'Illustration (optional)', 'pdo-site-manager' ),
											),
										),
										'default'    => array(
											array(
												'title'       => 'Real-Time Continuous Monitoring',
												'description' => 'Quarterly plant audits for Tree Guardians and daily biometric/logbook classroom attendance checks for Mid-Day Meal and Scholarships.',
												'icon'        => 'chart-line',
												'visible'     => 1,
												'order'       => 0,
											),
											array(
												'title'       => 'Community Accountability (GRM)',
												'description' => 'Direct feedback loops connecting beneficiary councils, parents, and school cabinets to executive management.',
												'icon'        => 'users',
												'visible'     => 1,
												'order'       => 1,
											),
											array(
												'title'       => 'Data-Driven Cross-Verification',
												'description' => 'Cross-referencing programmatic progress with Upazila Parishad authorities, local school boards, and independent observers.',
												'icon'        => 'clipboard-check',
												'visible'     => 1,
												'order'       => 2,
											),
										),
									),
								)
							),
						),
					),
				),
				'vfm'        => array(
					'label'  => __( 'Value for Money', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Value for Money', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'vfm' ),
								array(
									array(
										'key'       => 'vfm_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Eyebrow label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Social Return on Investment',
									),
									array(
										'key'       => 'vfm_title',
										'type'      => 'text',
										'label'     => __( 'Section title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Value for Money (VfM): Maximum Impact, Zero Waste',
									),
									array(
										'key'       => 'vfm_intro',
										'type'      => 'textarea',
										'label'     => __( 'Introduction', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 3,
										'default'   => 'Unlike top-heavy bureaucratic entities, PDO avoids costly corporate overhead. We utilize existing grassroots infrastructure and deep community volunteerism.',
									),
									array(
										'key'        => 'vfm_bars',
										'type'       => 'repeater',
										'label'      => __( 'Supporting statistics', 'pdo-site-manager' ),
										'item_label' => 'label',
										'add_label'  => __( 'Add statistic', 'pdo-site-manager' ),
										'fields'     => array(
											array(
												'key'       => 'label',
												'type'      => 'text',
												'label'     => __( 'Label', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'     => 'value',
												'type'    => 'number',
												'label'   => __( 'Percentage', 'pdo-site-manager' ),
												'default' => 0,
												'min'     => 0,
												'max'     => 100,
											),
											array(
												'key'   => 'display',
												'type'  => 'text',
												'label' => __( 'Displayed value', 'pdo-site-manager' ),
											),
											array(
												'key'     => 'color',
												'type'    => 'color',
												'label'   => __( 'Bar colour', 'pdo-site-manager' ),
												'default' => '#0d7a53',
											),
										),
										'default'    => array(
											array(
												'label'   => 'Direct Program Delivery',
												'value'   => 92,
												'display' => '~92%',
												'color'   => '#0d7a53',
												'visible' => 1,
												'order'   => 0,
											),
											array(
												'label'   => 'M&E and Compliance',
												'value'   => 5,
												'display' => '5%',
												'color'   => '#14b8a6',
												'visible' => 1,
												'order'   => 1,
											),
											array(
												'label'   => 'Administrative & Operational Cost',
												'value'   => 3,
												'display' => '3%',
												'color'   => '#f59e0b',
												'visible' => 1,
												'order'   => 2,
											),
										),
									),
									array(
										'key'       => 'vfm_detail',
										'type'      => 'richtext',
										'label'     => __( 'Detailed explanation', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => '<p>Nearly 100% of donor funding goes directly to ground-level saplings, student meals, and tuition relief.</p>',
									),
									array(
										'key'   => 'vfm_image',
										'type'  => 'image',
										'label' => __( 'Infographic / supporting image', 'pdo-site-manager' ),
									),
								)
							),
						),
					),
				),
				'reports'    => array(
					'label'  => __( 'Policies & reports', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Document library section', 'pdo-site-manager' ),
							'desc'   => __( 'Documents themselves are managed under PDO Website Manager → Documents.', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'reports' ),
								array(
									array(
										'key'       => 'reports_title',
										'type'      => 'text',
										'label'     => __( 'Section title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Institutional Governance & Audit Disclosures',
									),
									array(
										'key'       => 'reports_description',
										'type'      => 'text',
										'label'     => __( 'Section description', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Official documentation available for donors, UN auditors, and partner INGOs.',
									),
									array(
										'key'       => 'reports_badge',
										'type'      => 'text',
										'label'     => __( 'Badge', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Updated for FY 2026',
									),
									array(
										'key'     => 'reports_filter',
										'type'    => 'checkbox',
										'label'   => __( 'Show category filter', 'pdo-site-manager' ),
										'default' => 1,
									),
								)
							),
						),
					),
				),
			),
		);
	}

	/* --------------------------------------------------------------------- */
	/* Media                                                                 */
	/* --------------------------------------------------------------------- */

	/**
	 * Media page group.
	 *
	 * @return array
	 */
	private static function group_media() {
		return array(
			'option' => 'pdo_sm_media',
			'label'  => __( 'Media & Insights', 'pdo-site-manager' ),
			'tabs'   => array(
				'intro' => array(
					'label'  => __( 'Page settings', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Media page introduction', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'intro' ),
								array(
									array(
										'key'       => 'intro_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Small label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'News, Field Action & Thought Leadership',
									),
									array(
										'key'       => 'intro_title',
										'type'      => 'text',
										'label'     => __( 'Main heading', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Media & Insights',
									),
									array(
										'key'       => 'intro_description',
										'type'      => 'textarea',
										'label'     => __( 'Description', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 3,
										'default'   => 'Real-time coverage of our emergency relief drives, seasonal humanitarian campaigns, and public policy advocacy.',
									),
									array(
										'key'   => 'intro_banner',
										'type'  => 'image',
										'label' => __( 'Banner image', 'pdo-site-manager' ),
									),
								)
							),
						),
						array(
							'title'  => __( 'Archive behaviour', 'pdo-site-manager' ),
							'fields' => array(
								array(
									'key'     => 'show_filter',
									'type'    => 'checkbox',
									'label'   => __( 'Show the category filter', 'pdo-site-manager' ),
									'default' => 1,
								),
								array(
									'key'       => 'filter_all_label',
									'type'      => 'text',
									'label'     => __( '"All" filter label', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'All News & Updates',
								),
								array(
									'key'     => 'per_page',
									'type'    => 'number',
									'label'   => __( 'Articles per page', 'pdo-site-manager' ),
									'default' => 9,
									'min'     => 1,
									'max'     => 48,
								),
								array(
									'key'     => 'show_share',
									'type'    => 'checkbox',
									'label'   => __( 'Show social sharing links on single articles', 'pdo-site-manager' ),
									'default' => 1,
								),
								array(
									'key'     => 'show_related',
									'type'    => 'checkbox',
									'label'   => __( 'Show related articles', 'pdo-site-manager' ),
									'default' => 1,
								),
								array(
									'key'       => 'read_more_label',
									'type'      => 'text',
									'label'     => __( '"Read more" label', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Read More',
								),
							),
						),
					),
				),
			),
		);
	}

	/* --------------------------------------------------------------------- */
	/* Get involved                                                          */
	/* --------------------------------------------------------------------- */

	/**
	 * Form field repeater definition used by the volunteer / partner forms.
	 *
	 * @param array $default_rows Default rows.
	 * @param string $label       Repeater label.
	 * @return array
	 */
	private static function form_fields_repeater( $key, $label, $default_rows ) {
		return array(
			'key'        => $key,
			'type'       => 'repeater',
			'label'      => $label,
			'item_label' => 'label',
			'add_label'  => __( 'Add form field', 'pdo-site-manager' ),
			'fields'     => array(
				array(
					'key'   => 'name',
					'type'  => 'text',
					'label' => __( 'Field key', 'pdo-site-manager' ),
					'desc'  => __( 'Lowercase identifier stored with the submission. Leave empty to generate it from the label.', 'pdo-site-manager' ),
				),
				array(
					'key'       => 'label',
					'type'      => 'text',
					'label'     => __( 'Label', 'pdo-site-manager' ),
					'bilingual' => true,
				),
				array(
					'key'     => 'type',
					'type'    => 'select',
					'label'   => __( 'Field type', 'pdo-site-manager' ),
					'options' => array(
						'text'     => __( 'Text', 'pdo-site-manager' ),
						'email'    => __( 'Email', 'pdo-site-manager' ),
						'tel'      => __( 'Phone', 'pdo-site-manager' ),
						'url'      => __( 'URL', 'pdo-site-manager' ),
						'number'   => __( 'Number', 'pdo-site-manager' ),
						'date'     => __( 'Date', 'pdo-site-manager' ),
						'select'   => __( 'Dropdown', 'pdo-site-manager' ),
						'radio'    => __( 'Radio buttons', 'pdo-site-manager' ),
						'checkbox' => __( 'Checkbox', 'pdo-site-manager' ),
						'textarea' => __( 'Long text', 'pdo-site-manager' ),
					),
					'default' => 'text',
				),
				array(
					'key'       => 'placeholder',
					'type'      => 'text',
					'label'     => __( 'Placeholder', 'pdo-site-manager' ),
					'bilingual' => true,
				),
				array(
					'key'     => 'required',
					'type'    => 'checkbox',
					'label'   => __( 'Required', 'pdo-site-manager' ),
					'default' => 0,
				),
				array(
					'key'       => 'options',
					'type'      => 'textarea',
					'label'     => __( 'Choices (one per line)', 'pdo-site-manager' ),
					'bilingual' => true,
					'rows'      => 4,
				),
				array(
					'key'     => 'width',
					'type'    => 'select',
					'label'   => __( 'Field width', 'pdo-site-manager' ),
					'options' => array(
						'half' => __( 'Half width', 'pdo-site-manager' ),
						'full' => __( 'Full width', 'pdo-site-manager' ),
					),
					'default' => 'half',
				),
			),
			'default'    => $default_rows,
		);
	}

	/**
	 * Get involved group.
	 *
	 * @return array
	 */
	private static function group_involved() {
		$volunteer_rows = array(
			array(
				'name'        => 'full_name',
				'label'       => 'Full Name',
				'type'        => 'text',
				'placeholder' => 'e.g. Tanvir Ahmed',
				'required'    => 1,
				'width'       => 'half',
				'visible'     => 1,
				'order'       => 0,
			),
			array(
				'name'        => 'email',
				'label'       => 'Email Address',
				'type'        => 'email',
				'placeholder' => 'tanvir@example.com',
				'required'    => 1,
				'width'       => 'half',
				'visible'     => 1,
				'order'       => 1,
			),
			array(
				'name'        => 'phone',
				'label'       => 'Phone / WhatsApp',
				'type'        => 'tel',
				'placeholder' => '+8801XXXXXXXXX',
				'required'    => 1,
				'width'       => 'half',
				'visible'     => 1,
				'order'       => 2,
			),
			array(
				'name'        => 'district',
				'label'       => 'Current District / Upazila',
				'type'        => 'text',
				'placeholder' => 'e.g. Singra, Natore',
				'required'    => 1,
				'width'       => 'half',
				'visible'     => 1,
				'order'       => 3,
			),
			array(
				'name'     => 'area_of_action',
				'label'    => 'Preferred Area of Action',
				'type'     => 'select',
				'required' => 1,
				'width'    => 'half',
				'options'  => "Tree Guardian & Climate Action\nEducation Mentor (Dropout Recovery)\nZero Hunger Campus Food Support\nDisaster Relief & Medical Camps\nDigital Media, Content & Tech",
				'visible'  => 1,
				'order'    => 4,
			),
			array(
				'name'        => 'skill',
				'label'       => 'Primary Skill or Profession',
				'type'        => 'text',
				'placeholder' => 'e.g. Student, Teacher, IT Freelancer',
				'required'    => 0,
				'width'       => 'half',
				'visible'     => 1,
				'order'       => 5,
			),
			array(
				'name'        => 'motivation',
				'label'       => 'Why do you want to join PDO?',
				'type'        => 'textarea',
				'placeholder' => 'Tell us briefly about your motivation...',
				'required'    => 1,
				'width'       => 'full',
				'visible'     => 1,
				'order'       => 6,
			),
		);

		$partner_rows = array(
			array(
				'name'        => 'organization',
				'label'       => 'Organization / Agency Name',
				'type'        => 'text',
				'placeholder' => 'e.g. Global Green Fund / Tech CSR',
				'required'    => 1,
				'width'       => 'half',
				'visible'     => 1,
				'order'       => 0,
			),
			array(
				'name'     => 'org_type',
				'label'    => 'Organization Type',
				'type'     => 'select',
				'required' => 1,
				'width'    => 'half',
				'options'  => "International NGO (INGO)\nUN Agency / Multilateral Fund\nCorporate CSR Foundation\nPhilanthropic Trust / Foundation\nGovernment Ministry / Agency",
				'visible'  => 1,
				'order'    => 1,
			),
			array(
				'name'        => 'contact_person',
				'label'       => 'Contact Person & Designation',
				'type'        => 'text',
				'placeholder' => 'e.g. Sarah Jenkins, Country Director',
				'required'    => 1,
				'width'       => 'half',
				'visible'     => 1,
				'order'       => 2,
			),
			array(
				'name'        => 'email',
				'label'       => 'Official Email Address',
				'type'        => 'email',
				'placeholder' => 'partner@org.org',
				'required'    => 1,
				'width'       => 'half',
				'visible'     => 1,
				'order'       => 3,
			),
			array(
				'name'     => 'focus_area',
				'label'    => 'Strategic Interest / Focus Area',
				'type'     => 'select',
				'required' => 1,
				'width'    => 'full',
				'options'  => "Tree Guardian Afforestation Expansion (SDG 13/15)\nMulti-Campus Zero Hunger Mid-Day Meal Model (SDG 2/4)\nDistrict-Wide Tiered Scholarship Underwriting\nVocational TVET Tech Labs & STEM Incubation\nEmergency Humanitarian & Climate Relief",
				'visible'  => 1,
				'order'    => 4,
			),
			array(
				'name'        => 'details',
				'label'       => 'Project Scope or Inquiry Details',
				'type'        => 'textarea',
				'placeholder' => 'Describe proposed partnership or mandate...',
				'required'    => 1,
				'width'       => 'full',
				'visible'     => 1,
				'order'       => 5,
			),
		);

		return array(
			'option' => 'pdo_sm_involved',
			'label'  => __( 'Get Involved', 'pdo-site-manager' ),
			'tabs'   => array(
				'intro'     => array(
					'label'  => __( 'Introduction', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Page introduction', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'intro' ),
								array(
									array(
										'key'       => 'intro_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Eyebrow label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Join the Movement',
									),
									array(
										'key'       => 'intro_title',
										'type'      => 'text',
										'label'     => __( 'Heading', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Get Involved With PDO',
									),
									array(
										'key'       => 'intro_description',
										'type'      => 'textarea',
										'label'     => __( 'Description', 'pdo-site-manager' ),
										'bilingual' => true,
										'rows'      => 3,
										'default'   => 'Whether you want to become a certified Tree Guardian, volunteer your skills, or partner with us as an INGO or CSR funder, your action shapes the grassroots.',
									),
									array(
										'key'   => 'intro_image',
										'type'  => 'image',
										'label' => __( 'Image', 'pdo-site-manager' ),
									),
									array(
										'key'     => 'intro_background',
										'type'    => 'select',
										'label'   => __( 'Background', 'pdo-site-manager' ),
										'options' => self::background_choices(),
										'default' => '',
									),
								)
							),
						),
					),
				),
				'volunteer' => array(
					'label'  => __( 'Volunteer form', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Volunteer registration form', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'volunteer' ),
								array(
									array(
										'key'       => 'volunteer_tab_label',
										'type'      => 'text',
										'label'     => __( 'Tab label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Volunteer Sign-up',
									),
									array(
										'key'       => 'volunteer_title',
										'type'      => 'text',
										'label'     => __( 'Form title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Apply to Become a PDO Volunteer or Tree Guardian',
									),
									array(
										'key'       => 'volunteer_description',
										'type'      => 'text',
										'label'     => __( 'Form description', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Join our dynamic network of rural and urban changemakers.',
									),
									array(
										'key'       => 'volunteer_submit',
										'type'      => 'text',
										'label'     => __( 'Submit button label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Submit Volunteer Application',
									),
									array(
										'key'       => 'volunteer_success',
										'type'      => 'text',
										'label'     => __( 'Success message', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Thank you! Your volunteer application has been registered with PDO.',
									),
									self::form_fields_repeater( 'volunteer_fields', __( 'Volunteer form fields', 'pdo-site-manager' ), $volunteer_rows ),
								)
							),
						),
					),
				),
				'partner'   => array(
					'label'  => __( 'Partnership form', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Institutional partnership form', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'partner' ),
								array(
									array(
										'key'       => 'partner_tab_label',
										'type'      => 'text',
										'label'     => __( 'Tab label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Partner (INGO / CSR)',
									),
									array(
										'key'       => 'partner_title',
										'type'      => 'text',
										'label'     => __( 'Form title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Institutional Partnership & CSR Inquiries',
									),
									array(
										'key'       => 'partner_description',
										'type'      => 'text',
										'label'     => __( 'Form description', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Collaborate with PDO on high-impact scalable rural projects.',
									),
									array(
										'key'       => 'partner_submit',
										'type'      => 'text',
										'label'     => __( 'Submit button label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Submit Institutional Inquiry',
									),
									array(
										'key'       => 'partner_success',
										'type'      => 'text',
										'label'     => __( 'Success message', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Partnership inquiry received. Our Secretariat will contact you shortly.',
									),
									self::form_fields_repeater( 'partner_fields', __( 'Partnership form fields', 'pdo-site-manager' ), $partner_rows ),
								)
							),
						),
					),
				),
				'adopt'     => array(
					'label'  => __( 'Adopt-a-Project', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Adopt-a-Project model', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'adopt' ),
								array(
									array(
										'key'       => 'adopt_eyebrow',
										'type'      => 'text',
										'label'     => __( 'Eyebrow label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Direct Social Return',
									),
									array(
										'key'       => 'adopt_title',
										'type'      => 'text',
										'label'     => __( 'Section title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'The "Adopt-a-Project" Model',
									),
									array(
										'key'       => 'adopt_description',
										'type'      => 'text',
										'label'     => __( 'Section description', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Direct, traceable donor sponsorship with full photographic and MEAL attendance logs.',
									),
									array(
										'key'        => 'adopt_projects',
										'type'       => 'repeater',
										'label'      => __( 'Project cards', 'pdo-site-manager' ),
										'item_label' => 'title',
										'add_label'  => __( 'Add project card', 'pdo-site-manager' ),
										'fields'     => array(
											array(
												'key'   => 'image',
												'type'  => 'image',
												'label' => __( 'Image', 'pdo-site-manager' ),
											),
											array(
												'key'   => 'image_url',
												'type'  => 'url',
												'label' => __( 'Or image URL', 'pdo-site-manager' ),
											),
											array(
												'key'   => 'alt',
												'type'  => 'text',
												'label' => __( 'Image alt text', 'pdo-site-manager' ),
											),
											array(
												'key'       => 'category',
												'type'      => 'text',
												'label'     => __( 'Category', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'title',
												'type'      => 'text',
												'label'     => __( 'Title', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'description',
												'type'      => 'textarea',
												'label'     => __( 'Description', 'pdo-site-manager' ),
												'bilingual' => true,
												'rows'      => 3,
											),
											array(
												'key'       => 'stat',
												'type'      => 'text',
												'label'     => __( 'Supporting statistic / caption', 'pdo-site-manager' ),
												'bilingual' => true,
											),
											array(
												'key'       => 'btn_label',
												'type'      => 'text',
												'label'     => __( 'Button label', 'pdo-site-manager' ),
												'bilingual' => true,
												'default'   => 'Inquire Now',
											),
											array(
												'key'   => 'btn_url',
												'type'  => 'url',
												'label' => __( 'Custom button URL (optional)', 'pdo-site-manager' ),
												'desc'  => __( 'Leave empty to open the selected inquiry form with the project pre-filled.', 'pdo-site-manager' ),
											),
											array(
												'key'     => 'form',
												'type'    => 'select',
												'label'   => __( 'Inquiry form', 'pdo-site-manager' ),
												'options' => array(
													'partner'   => __( 'Institutional partnership form', 'pdo-site-manager' ),
													'volunteer' => __( 'Volunteer form', 'pdo-site-manager' ),
													'contact'   => __( 'Contact form', 'pdo-site-manager' ),
												),
												'default' => 'partner',
											),
										),
										'default'    => array(
											array(
												'image_url'   => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=500&q=80',
												'alt'         => 'Mid-day meal adoption',
												'category'    => 'Campus Nutrition',
												'title'       => 'Adopt a School Mid-Day Meal',
												'description' => 'Guarantee 250 students a hot, nutritious lunch every day for a full semester. Drastically reduces absenteeism and fuels learning.',
												'stat'        => 'Custom Scope',
												'btn_label'   => 'Inquire Now',
												'form'        => 'partner',
												'visible'     => 1,
												'order'       => 0,
											),
											array(
												'image_url'   => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=500&q=80',
												'alt'         => 'Tree Guardians adoption',
												'category'    => 'Climate Action',
												'title'       => 'Adopt 1,000 Tree Guardians',
												'description' => 'Fund 1,000 fruit and timber saplings distributed to primary school students with 12-month quarterly survival monitoring and awards.',
												'stat'        => 'High Survival M&E',
												'btn_label'   => 'Inquire Now',
												'form'        => 'partner',
												'visible'     => 1,
												'order'       => 1,
											),
											array(
												'image_url'   => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=500&q=80',
												'alt'         => 'School uniform adoption',
												'category'    => 'Retention & Parity',
												'title'       => 'Adopt 200 School Uniforms',
												'description' => 'Provide custom-tailored, dignified school uniforms for 200 rural children, eliminating classroom stigma and keeping them enrolled.',
												'stat'        => '100% Retention Target',
												'btn_label'   => 'Inquire Now',
												'form'        => 'partner',
												'visible'     => 1,
												'order'       => 2,
											),
										),
									),
								)
							),
						),
					),
				),
			),
		);
	}

	/* --------------------------------------------------------------------- */
	/* Contact                                                               */
	/* --------------------------------------------------------------------- */

	/**
	 * Contact page group.
	 *
	 * @return array
	 */
	private static function group_contact() {
		$contact_rows = array(
			array(
				'name'        => 'name',
				'label'       => 'Your Name',
				'type'        => 'text',
				'placeholder' => 'Full Name',
				'required'    => 1,
				'width'       => 'half',
				'visible'     => 1,
				'order'       => 0,
			),
			array(
				'name'        => 'email',
				'label'       => 'Email Address',
				'type'        => 'email',
				'placeholder' => 'you@example.com',
				'required'    => 1,
				'width'       => 'half',
				'visible'     => 1,
				'order'       => 1,
			),
			array(
				'name'        => 'subject',
				'label'       => 'Subject',
				'type'        => 'text',
				'placeholder' => 'Regarding Scholarship / Tree Guardian / Partnership',
				'required'    => 1,
				'width'       => 'full',
				'visible'     => 1,
				'order'       => 2,
			),
			array(
				'name'        => 'message',
				'label'       => 'Your Message',
				'type'        => 'textarea',
				'placeholder' => 'Please write your message here...',
				'required'    => 1,
				'width'       => 'full',
				'visible'     => 1,
				'order'       => 3,
			),
		);

		return array(
			'option' => 'pdo_sm_contact',
			'label'  => __( 'Contact Us', 'pdo-site-manager' ),
			'tabs'   => array(
				'intro' => array(
					'label'  => __( 'Page content', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Contact page introduction', 'pdo-site-manager' ),
							'desc'   => __( 'Address, phone, email, social profiles and map settings live under PDO Website Manager → Organization.', 'pdo-site-manager' ),
							'fields' => array(
								array(
									'key'       => 'intro_eyebrow',
									'type'      => 'text',
									'label'     => __( 'Page label', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Connect With Our Secretariat',
								),
								array(
									'key'       => 'intro_title',
									'type'      => 'text',
									'label'     => __( 'Page title', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Contact Us & Legal Information',
								),
								array(
									'key'       => 'intro_description',
									'type'      => 'textarea',
									'label'     => __( 'Description', 'pdo-site-manager' ),
									'bilingual' => true,
									'rows'      => 3,
									'default'   => 'Reach our central operational headquarters in Natore, Bangladesh or schedule an institutional donor briefing.',
								),
								array(
									'key'       => 'legal_title',
									'type'      => 'text',
									'label'     => __( 'Legal card title', 'pdo-site-manager' ),
									'bilingual' => true,
									'default'   => 'Statutory Legal Credentials',
								),
								array(
									'key'       => 'legal_description',
									'type'      => 'textarea',
									'label'     => __( 'Legal card description', 'pdo-site-manager' ),
									'bilingual' => true,
									'rows'      => 2,
									'default'   => "Formally registered with the Department of Social Services, Government of the People's Republic of Bangladesh.",
								),
							),
						),
					),
				),
				'form'  => array(
					'label'  => __( 'Contact form', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Contact form', 'pdo-site-manager' ),
							'fields' => array_merge(
								self::section_controls( 'form' ),
								array(
									array(
										'key'       => 'form_title',
										'type'      => 'text',
										'label'     => __( 'Form title', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Send an Instant Message to PDO Secretariat',
									),
									array(
										'key'       => 'form_submit',
										'type'      => 'text',
										'label'     => __( 'Submit button label', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Send Message',
									),
									array(
										'key'       => 'form_success',
										'type'      => 'text',
										'label'     => __( 'Success message', 'pdo-site-manager' ),
										'bilingual' => true,
										'default'   => 'Thank you — your message has been received by the PDO Secretariat.',
									),
									self::form_fields_repeater( 'form_fields', __( 'Contact form fields', 'pdo-site-manager' ), $contact_rows ),
								)
							),
						),
					),
				),
			),
		);
	}

	/* --------------------------------------------------------------------- */
	/* Translations                                                          */
	/* --------------------------------------------------------------------- */

	/**
	 * Default interface strings (English + Bengali).
	 *
	 * @return array
	 */
	public static function default_strings() {
		$rows = array(
			array( 'nav_home', 'Home', 'হোম' ),
			array( 'nav_about', 'About Us', 'আমাদের সম্পর্কে' ),
			array( 'nav_impact', 'Our Impact', 'আমাদের প্রভাব' ),
			array( 'nav_transparency', 'Transparency', 'স্বচ্ছতা' ),
			array( 'nav_media', 'Media & News', 'মিডিয়া ও সংবাদ' ),
			array( 'nav_involved', 'Get Involved', 'যুক্ত হোন' ),
			array( 'nav_contact', 'Contact Us', 'যোগাযোগ' ),
			array( 'cta_join', 'Join as Guardian', 'গার্ডিয়ান হিসেবে যোগ দিন' ),
			array( 'read_more', 'Read More', 'আরও পড়ুন' ),
			array( 'read_policy', 'Read Policy', 'নীতিমালা দেখুন' ),
			array( 'download', 'Download', 'ডাউনলোড' ),
			array( 'preview', 'Preview', 'প্রিভিউ' ),
			array( 'close', 'Close', 'বন্ধ করুন' ),
			array( 'all', 'All', 'সব' ),
			array( 'search', 'Search', 'অনুসন্ধান' ),
			array( 'search_placeholder', 'Search programs, reports, news…', 'কর্মসূচি, প্রতিবেদন বা সংবাদ খুঁজুন…' ),
			array( 'quick_links', 'Quick Navigation', 'দ্রুত নেভিগেশন' ),
			array( 'flagship_programs', 'Flagship Programs', 'ফ্ল্যাগশিপ কর্মসূচি' ),
			array( 'connect', 'Connect & Glocal Office', 'যোগাযোগ ও কার্যালয়' ),
			array( 'required_field', 'This field is required.', 'এই তথ্যটি আবশ্যক।' ),
			array( 'invalid_email', 'Please enter a valid email address.', 'সঠিক ইমেইল ঠিকানা দিন।' ),
			array( 'form_error', 'Your submission could not be saved. Please review the highlighted fields.', 'আপনার তথ্য সংরক্ষণ করা যায়নি। অনুগ্রহ করে চিহ্নিত ঘরগুলো দেখুন।' ),
			array( 'spam_error', 'Your submission was blocked by our spam filter. Please try again.', 'স্প্যাম ফিল্টারের কারণে আপনার তথ্য গ্রহণ করা যায়নি। আবার চেষ্টা করুন।' ),
			array( 'rate_error', 'Too many submissions from this device. Please try again in a few minutes.', 'অনেকবার চেষ্টা করা হয়েছে। কিছুক্ষণ পরে আবার চেষ্টা করুন।' ),
			array( 'no_results', 'No content found.', 'কোনো কন্টেন্ট পাওয়া যায়নি।' ),
			array( 'posted_on', 'Published on', 'প্রকাশিত' ),
			array( 'share', 'Share', 'শেয়ার করুন' ),
			array( 'related', 'Related Articles', 'সম্পর্কিত প্রতিবেদন' ),
			array( 'back_to_news', 'Back to Media & Insights', 'মিডিয়া ও সংবাদে ফিরুন' ),
			array( 'view_program', 'View Program', 'কর্মসূচি দেখুন' ),
			array( 'skip_to_content', 'Skip to content', 'মূল কন্টেন্টে যান' ),
			array( 'menu', 'Menu', 'মেনু' ),
			array( 'language', 'Language', 'ভাষা' ),
			array( 'no_document', 'Document file not uploaded yet.', 'ডকুমেন্ট ফাইল এখনো আপলোড করা হয়নি।' ),
			array( 'load_more', 'Load More', 'আরও দেখুন' ),
			array( 'back_to_top', 'Back to top', 'উপরে ফিরুন' ),
		);

		$out = array();
		$i   = 0;
		foreach ( $rows as $row ) {
			$out[] = array(
				'key'     => $row[0],
				'en'      => $row[1],
				'bn'      => $row[2],
				'visible' => 1,
				'order'   => $i++,
			);
		}
		return $out;
	}

	/**
	 * Translations group.
	 *
	 * @return array
	 */
	private static function group_i18n() {
		return array(
			'option' => 'pdo_sm_i18n',
			'label'  => __( 'Translations', 'pdo-site-manager' ),
			'tabs'   => array(
				'strings' => array(
					'label'  => __( 'Interface strings', 'pdo-site-manager' ),
					'panels' => array(
						array(
							'title'  => __( 'Interface strings', 'pdo-site-manager' ),
							'desc'   => __( 'Buttons, labels and notices used by the theme. Leave the Bengali column empty to fall back to English.', 'pdo-site-manager' ),
							'fields' => array(
								array(
									'key'        => 'strings',
									'type'       => 'repeater',
									'label'      => __( 'Strings', 'pdo-site-manager' ),
									'item_label' => 'key',
									'add_label'  => __( 'Add string', 'pdo-site-manager' ),
									'fields'     => array(
										array(
											'key'   => 'key',
											'type'  => 'text',
											'label' => __( 'String key', 'pdo-site-manager' ),
										),
										array(
											'key'   => 'en',
											'type'  => 'text',
											'label' => __( 'English', 'pdo-site-manager' ),
										),
										array(
											'key'   => 'bn',
											'type'  => 'text',
											'label' => __( 'Bengali (বাংলা)', 'pdo-site-manager' ),
										),
									),
									'default'    => self::default_strings(),
								),
							),
						),
					),
				),
			),
		);
	}
}
