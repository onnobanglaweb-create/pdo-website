<?php
/**
 * Bundled SVG icon library.
 *
 * All icons are locally bundled, stroke based and inherit `currentColor`,
 * so no external icon CDN is ever required.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icon registry + renderer.
 */
class PDO_SM_Icons {

	/**
	 * Cached icon map.
	 *
	 * @var array<string,array{label:string,group:string,path:string}>|null
	 */
	private static $icons = null;

	/**
	 * Full icon library.
	 *
	 * @return array<string,array{label:string,group:string,path:string}>
	 */
	public static function all() {
		if ( null !== self::$icons ) {
			return self::$icons;
		}

		$i = array();

		$add = static function ( $name, $label, $group, $path ) use ( &$i ) {
			$i[ $name ] = array(
				'label' => $label,
				'group' => $group,
				'path'  => $path,
			);
		};

		// --- General / UI ---------------------------------------------------.
		$add( 'home', 'Home', 'general', '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.8V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.8"/>' );
		$add( 'info', 'Information', 'general', '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 7.6h.01"/>' );
		$add( 'check', 'Check', 'general', '<path d="m4.5 12.5 5 5 10-11"/>' );
		$add( 'check-circle', 'Check circle', 'general', '<circle cx="12" cy="12" r="9"/><path d="m8 12.3 2.6 2.6L16 9.5"/>' );
		$add( 'plus', 'Plus', 'general', '<path d="M12 5v14M5 12h14"/>' );
		$add( 'minus', 'Minus', 'general', '<path d="M5 12h14"/>' );
		$add( 'close', 'Close', 'general', '<path d="M6 6l12 12M18 6 6 18"/>' );
		$add( 'menu', 'Menu bars', 'general', '<path d="M4 7h16M4 12h16M4 17h16"/>' );
		$add( 'arrow-right', 'Arrow right', 'general', '<path d="M4 12h15"/><path d="m13 6 6 6-6 6"/>' );
		$add( 'arrow-left', 'Arrow left', 'general', '<path d="M20 12H5"/><path d="m11 6-6 6 6 6"/>' );
		$add( 'arrow-up-right', 'Arrow up right', 'general', '<path d="M7 17 17 7"/><path d="M8 7h9v9"/>' );
		$add( 'chevron-down', 'Chevron down', 'general', '<path d="m6 9 6 6 6-6"/>' );
		$add( 'chevron-right', 'Chevron right', 'general', '<path d="m9 6 6 6-6 6"/>' );
		$add( 'external-link', 'External link', 'general', '<path d="M14 4h6v6"/><path d="M20 4 10 14"/><path d="M19 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5"/>' );
		$add( 'search', 'Search', 'general', '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/>' );
		$add( 'filter', 'Filter', 'general', '<path d="M4 5h16l-6.2 7.3V19l-3.6 2v-8.7z"/>' );
		$add( 'star', 'Star', 'general', '<path d="m12 4 2.5 5.2 5.5.8-4 3.9 1 5.6-5-2.7-5 2.7 1-5.6-4-3.9 5.5-.8z"/>' );
		$add( 'quote', 'Quote', 'general', '<path d="M9.5 7C7 7.8 5.5 10 5.5 13v4h5v-5h-3c0-1.9.8-3.2 2.6-3.8z"/><path d="M18 7c-2.5.8-4 3-4 6v4h5v-5h-3c0-1.9.8-3.2 2.6-3.8z"/>' );
		$add( 'globe', 'Globe', 'general', '<circle cx="12" cy="12" r="9"/><path d="M3.2 9.5h17.6M3.2 14.5h17.6"/><path d="M12 3c2.5 2.6 3.8 5.7 3.8 9S14.5 18.4 12 21c-2.5-2.6-3.8-5.7-3.8-9S9.5 5.6 12 3Z"/>' );
		$add( 'clock', 'Clock', 'general', '<circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3 2"/>' );
		$add( 'history', 'History', 'general', '<path d="M3.5 12a8.5 8.5 0 1 0 2.6-6.1"/><path d="M3.5 4.5V10h5.5"/><path d="M12 8v4.3l3 1.7"/>' );
		$add( 'calendar', 'Calendar', 'general', '<rect x="3.5" y="5" width="17" height="15.5" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/>' );
		$add( 'eye', 'Eye / preview', 'general', '<path d="M2.5 12S6 6.2 12 6.2 21.5 12 21.5 12 18 17.8 12 17.8 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="2.8"/>' );
		$add( 'download', 'Download', 'general', '<path d="M12 4v10"/><path d="m7.5 10.5 4.5 4.5 4.5-4.5"/><path d="M4.5 19.5h15"/>' );
		$add( 'upload', 'Upload', 'general', '<path d="M12 20V9.5"/><path d="m7.5 13 4.5-4.5 4.5 4.5"/><path d="M4.5 4.5h15"/>' );
		$add( 'link', 'Link', 'general', '<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 1 0-5.7-5.7L11.5 6.8"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3A4 4 0 0 0 11 18.7l1.4-1.4"/>' );
		$add( 'send', 'Send', 'general', '<path d="M21 3 10.5 13.5"/><path d="M21 3 14.5 21l-4-7.5L3 9.5z"/>' );
		$add( 'grid', 'Grid', 'general', '<rect x="4" y="4" width="7" height="7" rx="1.4"/><rect x="13" y="4" width="7" height="7" rx="1.4"/><rect x="4" y="13" width="7" height="7" rx="1.4"/><rect x="13" y="13" width="7" height="7" rx="1.4"/>' );
		$add( 'layers', 'Layers', 'general', '<path d="m12 3 9 5-9 5-9-5z"/><path d="m3.5 12.5 8.5 4.7 8.5-4.7"/><path d="m3.5 16.8 8.5 4.7 8.5-4.7"/>' );
		$add( 'settings', 'Settings', 'general', '<circle cx="12" cy="12" r="3"/><path d="M19.4 14.5a1.6 1.6 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.6 1.6 0 0 0-2.7 1.1v.3a2 2 0 1 1-4 0v-.2a1.6 1.6 0 0 0-2.7-1.1l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.6 1.6 0 0 0-1.1-2.7h-.3a2 2 0 1 1 0-4h.2A1.6 1.6 0 0 0 4.6 7l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.6 1.6 0 0 0 2.7-1.1V3a2 2 0 1 1 4 0v.2a1.6 1.6 0 0 0 2.7 1.1l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.6 1.6 0 0 0 1.1 2.7h.3a2 2 0 1 1 0 4h-.2a1.6 1.6 0 0 0-1.4 1Z"/>' );

		// --- Organisation / people -----------------------------------------.
		$add( 'people-roof', 'People & roof (PDO mark)', 'people', '<path d="M3 10.2 12 3.4l9 6.8"/><circle cx="8.6" cy="14.2" r="1.8"/><circle cx="15.4" cy="14.2" r="1.8"/><path d="M5.2 20.6c0-1.9 1.5-3.2 3.4-3.2s3.4 1.3 3.4 3.2"/><path d="M12 20.6c0-1.9 1.5-3.2 3.4-3.2s3.4 1.3 3.4 3.2"/>' );
		$add( 'users', 'Team', 'people', '<circle cx="9" cy="8.5" r="3.2"/><path d="M3 20c0-3.1 2.7-5.2 6-5.2s6 2.1 6 5.2"/><path d="M16.2 6a3.2 3.2 0 0 1 0 6.2"/><path d="M17.6 14.6c2 .7 3.4 2.4 3.4 4.6"/>' );
		$add( 'user', 'Person', 'people', '<circle cx="12" cy="8.2" r="3.6"/><path d="M4.8 20.4c0-3.6 3.2-6 7.2-6s7.2 2.4 7.2 6"/>' );
		$add( 'users-gear', 'Management', 'people', '<circle cx="8.6" cy="8" r="3.1"/><path d="M2.8 19.6c0-3.1 2.6-5.2 5.8-5.2 1 0 2 .2 2.8.6"/><circle cx="17" cy="16.6" r="2.4"/><path d="M17 12.6v1.1M17 19.5v1.1M20.4 14.6l-.9.6M14.5 18.6l-.9.6M20.4 18.6l-.9-.6M14.5 14.6l-.9-.6"/>' );
		$add( 'handshake', 'Partnership', 'people', '<path d="m3 11.5 3-3.2h4l2 1.7 2-1.7h4l3 3.2"/><path d="m7 12.6 3.1 3a1.5 1.5 0 0 0 2.1 0"/><path d="m12.2 15.6 1.8 1.8M14.5 13.6l2.2 2.1M17 11.5l2 2"/><path d="M6 8.3v6.4M18 8.3v6.4"/>' );
		$add( 'hand-heart', 'Volunteer', 'people', '<path d="M12.4 9.6 11 8.3a2 2 0 0 1 2.8-2.8l.6.6.6-.6A2 2 0 0 1 17.8 8l-1.4 1.4"/><path d="M4 13.5h3l2.8 2.2h3.3a1.4 1.4 0 0 1 0 2.8h-2.3"/><path d="m8.4 18.5 4.6 2.2 7-3.6a1.6 1.6 0 0 0-1.5-2.8L15 15.8"/>' );
		$add( 'hands-holding', 'Community support', 'people', '<path d="M4 10.5c1.6 0 2.8 1.2 2.8 2.8v3.2"/><path d="M20 10.5c-1.6 0-2.8 1.2-2.8 2.8v3.2"/><path d="M6.8 16.5h10.4"/><path d="M8.6 12.8 12 9.4l3.4 3.4"/><path d="M12 9.4V4.6"/>' );
		$add( 'quote-left', 'Leadership message', 'people', '<path d="M10 6.5C7 7.6 5.2 10.2 5.2 13.8V18h5.4v-5.6H7.3c0-2.2 1-3.7 3-4.4z"/><path d="M19.4 6.5c-3 1.1-4.8 3.7-4.8 7.3V18H20v-5.6h-3.3c0-2.2 1-3.7 3-4.4z"/>' );
		$add( 'award', 'Award', 'people', '<circle cx="12" cy="9.5" r="5"/><path d="m9 13.8-1.4 6.6 4.4-2.4 4.4 2.4-1.4-6.6"/>' );

		// --- Education -------------------------------------------------------.
		$add( 'graduation-cap', 'Education', 'education', '<path d="m12 4 9.5 4.4L12 12.9 2.5 8.4z"/><path d="M6.5 10.6v4.6c0 1.6 2.5 3 5.5 3s5.5-1.4 5.5-3v-4.6"/><path d="M21.5 8.4v5.2"/>' );
		$add( 'book', 'Book', 'education', '<path d="M4 5.2A1.6 1.6 0 0 1 5.6 3.6H19V19H5.6A1.6 1.6 0 0 0 4 20.6z"/><path d="M4 17.4A1.6 1.6 0 0 1 5.6 15.8H19"/>' );
		$add( 'school', 'School', 'education', '<path d="M12 3.2 20.5 8v12.4h-17V8z"/><path d="M9.5 20.4v-5.2h5v5.2"/><path d="M12 6.4v2.8M10.6 7.8h2.8"/>' );
		$add( 'pen', 'Op-ed / writing', 'education', '<path d="M16.6 3.9a2.1 2.1 0 0 1 3 3L8.3 18.2l-4 1 1-4z"/><path d="m14.5 6 3 3"/>' );
		$add( 'lightbulb', 'Idea / innovation', 'education', '<path d="M9.3 17.5a6 6 0 1 1 5.4 0v1.6a1.4 1.4 0 0 1-1.4 1.4h-2.6a1.4 1.4 0 0 1-1.4-1.4z"/><path d="M9.6 17.5h4.8"/>' );
		$add( 'certificate', 'Registration / certificate', 'education', '<circle cx="12" cy="9.6" r="5.2"/><path d="m9.3 14-1.5 6.4 4.2-2.3 4.2 2.3L14.7 14"/><path d="m10 9.6 1.5 1.5 2.7-2.9"/>' );
		$add( 'rotate-left', 'Second chance', 'education', '<path d="M3.6 12a8.4 8.4 0 1 0 2.4-5.9"/><path d="M3.6 4.8v5.4H9"/>' );
		$add( 'clipboard-check', 'Checklist', 'education', '<rect x="5" y="4.8" width="14" height="16" rx="2"/><path d="M9 4.8V3.6h6v1.2"/><path d="m9.3 13 2 2 3.6-4"/>' );

		// --- Climate / environment ------------------------------------------.
		$add( 'tree', 'Tree', 'climate', '<path d="M12 3.6 6.8 11h3L6 16.4h12L14.2 11h3z"/><path d="M12 16.4v4"/>' );
		$add( 'leaf', 'Leaf', 'climate', '<path d="M20 4.4C9.7 4.4 4.6 8.7 4.6 15.1c0 1.7.5 3.3 1.3 4.5C9.2 14.8 13 12 20 11"/><path d="M5.9 19.6C4.6 17.4 4 14.8 4.6 12"/>' );
		$add( 'seedling', 'Seedling', 'climate', '<path d="M12 20.4v-6.2"/><path d="M12 14.2C12 10.9 14.7 8.2 18 8.2c0 3.3-2.7 6-6 6z"/><path d="M12 15.6C9.5 15.6 7.4 13.5 7.4 11c2.5 0 4.6 2.1 4.6 4.6z"/>' );
		$add( 'sun', 'Sun / heat', 'climate', '<circle cx="12" cy="12" r="4"/><path d="M12 2.8v2.4M12 18.8v2.4M21.2 12h-2.4M5.2 12H2.8M18.5 5.5 16.8 7.2M7.2 16.8l-1.7 1.7M18.5 18.5l-1.7-1.7M7.2 7.2 5.5 5.5"/>' );
		$add( 'droplet', 'Water', 'climate', '<path d="M12 3.4 7.2 9.6a6 6 0 1 0 9.6 0z"/>' );
		$add( 'recycle', 'Circular economy', 'climate', '<path d="m7.2 10.2-2.6 4.4a1.6 1.6 0 0 0 1.4 2.4H9"/><path d="M12 4.4 9.4 8.8"/><path d="m12 4.4 2.8 4.4"/><path d="m18.6 10.6 2 3.4a1.6 1.6 0 0 1-1.4 2.4h-3.4"/><path d="m14.4 19.6-1.8-3 1.8-3"/><path d="M9 17h2.6"/>' );
		$add( 'earth', 'Global', 'climate', '<circle cx="12" cy="12" r="9"/><path d="M3.4 10.2h4l1.8 2.6-1.2 3 2 3.4"/><path d="M20.4 10c-1.3.6-3 .4-3.8-.6-.9-1 .2-2.3-.6-3.1-.6-.6-1.9-.5-2.9.1"/>' );

		// --- Health ----------------------------------------------------------.
		$add( 'heart', 'Heart', 'health', '<path d="M12 20s-7.5-4.4-7.5-9.5A4.3 4.3 0 0 1 12 7.6a4.3 4.3 0 0 1 7.5 2.9C19.5 15.6 12 20 12 20Z"/>' );
		$add( 'heart-pulse', 'Health', 'health', '<path d="M12 20s-7.5-4.4-7.5-9.5A4.3 4.3 0 0 1 12 7.6a4.3 4.3 0 0 1 7.5 2.9C19.5 15.6 12 20 12 20Z"/><path d="M4.8 12.4h3l1.4-2.2 1.8 4 1.6-2.8h3.8"/>' );
		$add( 'stethoscope', 'Clinic', 'health', '<path d="M6 3.6v4.2a4 4 0 0 0 8 0V3.6"/><path d="M6 3.6H4.4M14 3.6h1.6"/><path d="M10 11.8v2.4a4.6 4.6 0 0 0 9.2 0v-1.4"/><circle cx="19.2" cy="11" r="1.8"/>' );
		$add( 'bowl-food', 'Meal / nutrition', 'health', '<path d="M3.4 11.4h17.2c0 4-3.3 7-7.6 7h-2c-4.3 0-7.6-3-7.6-7z"/><path d="M8 8.6c0-1.3 1.8-1.3 1.8-2.6S8 3.4 8 3.4"/><path d="M13 8.6c0-1.3 1.8-1.3 1.8-2.6S13 3.4 13 3.4"/>' );
		$add( 'shield', 'Safeguarding', 'health', '<path d="M12 3.4 5 6v5.6c0 4.2 2.9 7.6 7 9 4.1-1.4 7-4.8 7-9V6z"/>' );
		$add( 'shield-check', 'Protection verified', 'health', '<path d="M12 3.4 5 6v5.6c0 4.2 2.9 7.6 7 9 4.1-1.4 7-4.8 7-9V6z"/><path d="m9 11.8 2.2 2.2 4-4.4"/>' );

		// --- Governance / finance -------------------------------------------.
		$add( 'scale-balanced', 'Governance', 'governance', '<path d="M12 4.2v15.4"/><path d="M7.6 19.6h8.8"/><path d="M4 7.6h16"/><path d="m4 7.6 2.8 5.4a3 3 0 0 1-5.6 0z" /><path d="m20 7.6 2.8 5.4a3 3 0 0 1-5.6 0z" transform="translate(-3.2 0)"/>' );
		$add( 'coins', 'Value for money', 'governance', '<ellipse cx="9" cy="7" rx="5" ry="2.6"/><path d="M4 7v4c0 1.4 2.2 2.6 5 2.6s5-1.2 5-2.6V7"/><path d="M14 10.4c2.5.3 4.4 1.3 4.4 2.6v4c0 1.4-2.2 2.6-5 2.6-2.2 0-4-.7-4.7-1.7"/>' );
		$add( 'chart-line', 'MEAL / monitoring', 'governance', '<path d="M4 4v16h16"/><path d="m7 15 3.4-4 3 2.4L19 7.6"/>' );
		$add( 'chart-pie', 'Chart', 'governance', '<path d="M12 3.4a8.6 8.6 0 1 0 8.6 8.6H12z"/><path d="M14.6 3.9A8.6 8.6 0 0 1 20.1 9.4h-5.5z"/>' );
		$add( 'bank', 'Government / bank', 'governance', '<path d="m12 3.6 9 4.4H3z"/><path d="M5.5 10.6v7M9.8 10.6v7M14.2 10.6v7M18.5 10.6v7"/><path d="M3.4 20.4h17.2"/>' );
		$add( 'lock', 'Data privacy', 'governance', '<rect x="4.6" y="10.4" width="14.8" height="10" rx="2"/><path d="M8.2 10.4V7.8a3.8 3.8 0 1 1 7.6 0v2.6"/>' );
		$add( 'megaphone', 'Whistleblowing / press', 'governance', '<path d="M4 10v4a1.6 1.6 0 0 0 1.6 1.6h2L14 20V4L7.6 8.4h-2A1.6 1.6 0 0 0 4 10Z"/><path d="M17.4 8.6a4.6 4.6 0 0 1 0 6.8"/>' );
		$add( 'file-pdf', 'PDF document', 'governance', '<path d="M6 3.6h7.4L19 9.2v11.2H6z"/><path d="M13.2 3.6v5.8H19"/><path d="M8.8 17.4v-4h1.3a1.3 1.3 0 0 1 0 2.6H8.8"/><path d="M13.6 17.4v-4h1a2 2 0 0 1 0 4z"/>' );
		$add( 'file-lines', 'Report', 'governance', '<path d="M6 3.6h7.4L19 9.2v11.2H6z"/><path d="M13.2 3.6v5.8H19"/><path d="M8.8 13h6.4M8.8 16.2h6.4"/>' );
		$add( 'folder', 'Library', 'governance', '<path d="M3.6 6.6A1.6 1.6 0 0 1 5.2 5h4l2 2.4h7.6A1.6 1.6 0 0 1 20.4 9v9A1.6 1.6 0 0 1 18.8 19.6H5.2A1.6 1.6 0 0 1 3.6 18Z"/>' );

		// --- Media / tech -----------------------------------------------------.
		$add( 'newspaper', 'News', 'media', '<path d="M3.6 6.4h13.2v14H5.2a1.6 1.6 0 0 1-1.6-1.6z"/><path d="M16.8 9h2.4a1.6 1.6 0 0 1 1.6 1.6v8a1.8 1.8 0 0 1-3.6 0"/><path d="M6.4 9.4h7.6M6.4 12.6h7.6M6.4 15.8h4.6"/>' );
		$add( 'image', 'Image', 'media', '<rect x="3.6" y="5" width="16.8" height="14" rx="2"/><circle cx="8.6" cy="9.8" r="1.6"/><path d="m4.4 17 4.6-4.6 3.4 3.4 2.6-2.4 4.6 4.2"/>' );
		$add( 'camera', 'Photo documentation', 'media', '<path d="M3.6 8.6h3.2l1.6-2.4h7.2l1.6 2.4h3.2v10H3.6z"/><circle cx="12" cy="13.4" r="3.4"/>' );
		$add( 'play', 'Play', 'media', '<circle cx="12" cy="12" r="9"/><path d="m10.2 8.6 5.4 3.4-5.4 3.4z"/>' );
		$add( 'laptop', 'Digital / IT', 'media', '<rect x="4.6" y="5.6" width="14.8" height="10" rx="1.6"/><path d="M2.6 18.4h18.8"/>' );
		$add( 'wifi', 'Connectivity', 'media', '<path d="M2.8 9.4a14 14 0 0 1 18.4 0"/><path d="M6.2 13a9 9 0 0 1 11.6 0"/><path d="M9.6 16.6a4.2 4.2 0 0 1 4.8 0"/><path d="M12 20h.01"/>' );
		$add( 'cpu', 'AI / technology', 'media', '<rect x="7" y="7" width="10" height="10" rx="1.6"/><path d="M10 4v3M14 4v3M10 17v3M14 17v3M4 10h3M4 14h3M17 10h3M17 14h3"/>' );

		// --- Agriculture / market --------------------------------------------.
		$add( 'basket', 'Janatar Bazar / market', 'agri', '<path d="M3.4 9.6h17.2l-1.8 9.4a1.6 1.6 0 0 1-1.6 1.4H6.8a1.6 1.6 0 0 1-1.6-1.4z"/><path d="m8.4 9.6 2.2-6M15.6 9.6l-2.2-6"/><path d="M9.6 13.4v3.4M14.4 13.4v3.4"/>' );
		$add( 'wheat', 'Agriculture', 'agri', '<path d="M12 20.4V9"/><path d="M12 9c0-2 1.4-3.6 3.4-3.6C15.4 7.4 14 9 12 9Z"/><path d="M12 9C10 9 8.6 7.4 8.6 5.4 10.6 5.4 12 7 12 9Z"/><path d="M12 13.6c0-2 1.4-3.6 3.4-3.6 0 2-1.4 3.6-3.4 3.6Z"/><path d="M12 13.6c-2 0-3.4-1.6-3.4-3.6 2 0 3.4 1.6 3.4 3.6Z"/>' );
		$add( 'truck', 'Logistics / relief', 'agri', '<path d="M3.4 6.6h10.2v9.8H3.4z"/><path d="M13.6 10h3.6l2.4 2.8v3.6h-6z"/><circle cx="7.4" cy="18" r="1.8"/><circle cx="16.6" cy="18" r="1.8"/>' );
		$add( 'briefcase', 'Livelihood / jobs', 'agri', '<rect x="3.4" y="7.6" width="17.2" height="12" rx="2"/><path d="M9 7.6V5.8a1.4 1.4 0 0 1 1.4-1.4h3.2A1.4 1.4 0 0 1 15 5.8v1.8"/><path d="M3.4 12.6h17.2"/>' );
		$add( 'wrench', 'TVET / skills', 'agri', '<path d="M15.4 3.8a5 5 0 0 0-5.6 6.5L3.6 16.4a2 2 0 0 0 2.8 2.8l6.1-6.2a5 5 0 0 0 6.5-5.6l-2.9 2.9-2.6-.7-.7-2.6z"/>' );
		$add( 'gift', 'Adopt-a-project', 'agri', '<rect x="3.6" y="9.4" width="16.8" height="4" rx="1"/><path d="M5.2 13.4v7h13.6v-7"/><path d="M12 9.4v11"/><path d="M12 9.4S10.6 4 8.2 4a2.2 2.2 0 0 0 0 5.4z"/><path d="M12 9.4S13.4 4 15.8 4a2.2 2.2 0 0 1 0 5.4z"/>' );

		// --- Contact ----------------------------------------------------------.
		$add( 'phone', 'Phone', 'contact', '<path d="M6.4 3.8h3l1.5 3.8-2 1.4a11.5 11.5 0 0 0 5.1 5.1l1.4-2 3.8 1.5v3a1.8 1.8 0 0 1-2 1.8C10.6 18 6 13.4 4.6 5.8a1.8 1.8 0 0 1 1.8-2Z"/>' );
		$add( 'whatsapp', 'WhatsApp', 'contact', '<path d="M3.8 20.2 5.2 16a8.2 8.2 0 1 1 3 2.9z"/><path d="M9 9.4c0 3 2.6 5.6 5.6 5.6.6 0 1.2-.6 1.2-1.2l-1.8-.8-1 1a6.6 6.6 0 0 1-2.9-2.9l1-1-.8-1.8c-.6 0-1.3.5-1.3 1.1z"/>' );
		$add( 'mail', 'Email', 'contact', '<rect x="3.2" y="5.4" width="17.6" height="13.2" rx="2"/><path d="m3.6 7 8.4 6 8.4-6"/>' );
		$add( 'map-pin', 'Location', 'contact', '<path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"/><circle cx="12" cy="10" r="2.6"/>' );
		$add( 'map', 'Map', 'contact', '<path d="m3.6 6.4 5.6-2.2 5.6 2.2 5.6-2.2v13.4l-5.6 2.2-5.6-2.2-5.6 2.2z"/><path d="M9.2 4.2v13.4M14.8 6.4v13.4"/>' );
		$add( 'facebook', 'Facebook', 'contact', '<path d="M14.6 21v-7.6h2.6l.4-3h-3V8.5c0-.9.3-1.5 1.5-1.5h1.6V4.3A21 21 0 0 0 15.4 4c-2.4 0-4 1.4-4 4.1v2.3H8.8v3h2.6V21z"/>' );
		$add( 'twitter', 'X / Twitter', 'contact', '<path d="M4 4.4h4l4.4 6 5-6h2.2l-6.1 7.2 6.7 9h-4l-4.8-6.5-5.4 6.5H3.8l6.6-7.9z"/>' );
		$add( 'linkedin', 'LinkedIn', 'contact', '<rect x="3.6" y="3.6" width="16.8" height="16.8" rx="2.2"/><path d="M8 10.4v6M8 7.4v.01M12 16.4v-3.2a2 2 0 0 1 4 0v3.2M12 10.4v6"/>' );
		$add( 'youtube', 'YouTube', 'contact', '<rect x="3" y="6" width="18" height="12" rx="3"/><path d="m10.4 9.6 4.8 2.4-4.8 2.4z"/>' );
		$add( 'instagram', 'Instagram', 'contact', '<rect x="3.6" y="3.6" width="16.8" height="16.8" rx="4.4"/><circle cx="12" cy="12" r="3.6"/><path d="M17 7h.01"/>' );

		// Previously referenced by templates but missing from the registry, which
		// made those buttons render as empty boxes. See docs/DEVELOPMENT.md.
		$add( 'arrow-up', 'Arrow up', 'general', '<path d="M12 19V5"/><path d="m6 11 6-6 6 6"/>' );
		$add( 'arrow-down', 'Arrow down', 'general', '<path d="M12 5v14"/><path d="m18 13-6 6-6-6"/>' );
		$add( 'chevron-up', 'Chevron up', 'general', '<path d="m6 15 6-6 6 6"/>' );
		$add( 'chevron-left', 'Chevron left', 'general', '<path d="m15 6-6 6 6 6"/>' );
		$add( 'grip', 'Drag handle', 'general', '<circle cx="9" cy="6" r="1.1"/><circle cx="15" cy="6" r="1.1"/><circle cx="9" cy="12" r="1.1"/><circle cx="15" cy="12" r="1.1"/><circle cx="9" cy="18" r="1.1"/><circle cx="15" cy="18" r="1.1"/>' );
		$add( 'bullseye', 'Target', 'impact', '<circle cx="12" cy="12" r="8.4"/><circle cx="12" cy="12" r="4.6"/><circle cx="12" cy="12" r="1.1"/>' );
		$add( 'flag', 'Flag', 'general', '<path d="M5 21V4.4"/><path d="M5 5.2h10.6l-1.4 3.2 1.4 3.2H5z"/>' );
		$add( 'building', 'Building', 'general', '<rect x="4.6" y="3.4" width="14.8" height="17.2" rx="1.6"/><path d="M8.4 7.6h2M13.6 7.6h2M8.4 11.4h2M13.6 11.4h2M10.4 20.6v-4.2h3.2v4.2"/>' );
		$add( 'images', 'Gallery', 'general', '<rect x="3.2" y="6.4" width="13.4" height="11.2" rx="1.8"/><path d="M7.6 17.6 11 13.8l2.4 2.6 1.8-1.8 1.4 1.5"/><circle cx="7.4" cy="10.2" r="1.1"/><path d="M19.2 8.8a1.8 1.8 0 0 1 1.6 1.8v6.6a2.4 2.4 0 0 1-2.4 2.4H8.2"/>' );
		$add( 'sparkles', 'Sparkles', 'general', '<path d="m12 3.6 1.7 4.3 4.3 1.7-4.3 1.7L12 15.6l-1.7-4.3L6 9.6l4.3-1.7z"/><path d="M18.4 15.2l.8 2 2 .8-2 .8-.8 2-.8-2-2-.8 2-.8z"/>' );
		$add( 'target-arrow', 'Goal', 'impact', '<circle cx="11" cy="13" r="7.4"/><circle cx="11" cy="13" r="3.2"/><path d="m13.6 10.4 5-5"/><path d="M16.4 4.2h3.4v3.4"/>' );

		/**
		 * Filter the bundled icon library.
		 *
		 * @param array $i Icon definitions.
		 */
		self::$icons = apply_filters( 'pdo_sm_icons', $i );

		return self::$icons;
	}

	/**
	 * Icon names grouped for the picker UI.
	 *
	 * @return array<string,array<string,string>>
	 */
	public static function grouped() {
		$groups = array();
		foreach ( self::all() as $name => $icon ) {
			$group = isset( $icon['group'] ) ? $icon['group'] : 'general';
			if ( ! isset( $groups[ $group ] ) ) {
				$groups[ $group ] = array();
			}
			$groups[ $group ][ $name ] = $icon['label'];
		}
		return $groups;
	}

	/**
	 * Check whether an icon exists.
	 *
	 * @param string $name Icon name.
	 * @return bool
	 */
	public static function exists( $name ) {
		$icons = self::all();
		return isset( $icons[ $name ] );
	}

	/**
	 * Build the SVG markup for an icon.
	 *
	 * @param string $name Icon name.
	 * @param array  $args Optional attributes: class, size, title, stroke_width.
	 * @return string
	 */
	public static function svg( $name, $args = array() ) {
		$icons = self::all();
		$name  = sanitize_key( str_replace( array( 'fa-solid fa-', 'fa-regular fa-', 'fa-' ), '', (string) $name ) );
		if ( ! isset( $icons[ $name ] ) ) {
			return '';
		}
		$args = wp_parse_args(
			$args,
			array(
				'class'        => 'pdo-icon',
				'size'         => 24,
				'title'        => '',
				'stroke_width' => 1.8,
			)
		);

		$title_markup = '';
		$aria         = ' aria-hidden="true" focusable="false"';
		if ( '' !== $args['title'] ) {
			$id           = 'pdo-icon-' . wp_unique_id();
			$title_markup = '<title id="' . esc_attr( $id ) . '">' . esc_html( $args['title'] ) . '</title>';
			$aria         = ' role="img" aria-labelledby="' . esc_attr( $id ) . '"';
		}

		return sprintf(
			'<svg class="%1$s pdo-icon--%2$s" width="%3$d" height="%3$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%4$s" stroke-linecap="round" stroke-linejoin="round"%5$s>%6$s%7$s</svg>',
			esc_attr( $args['class'] ),
			esc_attr( $name ),
			(int) $args['size'],
			esc_attr( (string) $args['stroke_width'] ),
			$aria,
			$title_markup,
			$icons[ $name ]['path'] // Static, developer defined markup.
		);
	}
}

if ( ! function_exists( 'pdo_icon' ) ) {
	/**
	 * Render (echo) a bundled icon.
	 *
	 * @param string $name Icon name.
	 * @param array  $args Attributes.
	 * @return void
	 */
	function pdo_icon( $name, $args = array() ) {
		echo PDO_SM_Icons::svg( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from a developer defined whitelist.
	}
}

if ( ! function_exists( 'pdo_get_icon' ) ) {
	/**
	 * Return a bundled icon as a string.
	 *
	 * @param string $name Icon name.
	 * @param array  $args Attributes.
	 * @return string
	 */
	function pdo_get_icon( $name, $args = array() ) {
		return PDO_SM_Icons::svg( $name, $args );
	}
}
