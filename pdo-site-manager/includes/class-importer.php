<?php
/**
 * Initial content migration.
 *
 * Creates the WordPress pages, taxonomy terms, navigation menus and the
 * content records migrated from the original HTML template. Every step is
 * idempotent – running it twice never duplicates anything and never
 * overwrites content an administrator has already edited.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Importer.
 */
class PDO_SM_Importer {

	/**
	 * Meta key used to recognise seeded records.
	 */
	const SEED_META = '_pdo_seed_key';

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_action( 'admin_post_pdo_sm_run_import', array( $this, 'handle_run' ) );
	}

	/**
	 * Page blueprint.
	 *
	 * @return array<string,array>
	 */
	public static function pages() {
		return array(
			'home'         => array(
				'title'    => __( 'Home', 'pdo-site-manager' ),
				'slug'     => 'home',
				'template' => '',
			),
			'about'        => array(
				'title'    => __( 'About Us', 'pdo-site-manager' ),
				'slug'     => 'about',
				'template' => 'templates/page-about.php',
			),
			'impact'       => array(
				'title'    => __( 'Our Impact', 'pdo-site-manager' ),
				'slug'     => 'impact',
				'template' => 'templates/page-impact.php',
			),
			'transparency' => array(
				'title'    => __( 'Transparency', 'pdo-site-manager' ),
				'slug'     => 'transparency',
				'template' => 'templates/page-transparency.php',
			),
			'media'        => array(
				'title'    => __( 'Media & Insights', 'pdo-site-manager' ),
				'slug'     => 'media-insights',
				'template' => 'templates/page-media-insights.php',
			),
			'involved'     => array(
				'title'    => __( 'Get Involved', 'pdo-site-manager' ),
				'slug'     => 'get-involved',
				'template' => 'templates/page-involved.php',
			),
			'contact'      => array(
				'title'    => __( 'Contact', 'pdo-site-manager' ),
				'slug'     => 'contact',
				'template' => 'templates/page-contact.php',
			),
		);
	}

	/**
	 * Run the whole import.
	 *
	 * @param array $steps Steps to run (empty = all).
	 * @return array Report of what happened.
	 */
	public function run( $steps = array() ) {
		$all   = array( 'settings', 'terms', 'pages', 'programs', 'team', 'partners', 'stories', 'documents', 'news', 'menus' );
		$steps = $steps ? array_intersect( $all, $steps ) : $all;

		$report = array();

		foreach ( $steps as $step ) {
			$method = 'seed_' . $step;
			if ( method_exists( $this, $method ) ) {
				$report[ $step ] = $this->$method();
			}
		}

		update_option( 'pdo_sm_setup_complete', 1, false );
		PDO_SM_Settings::flush_cache();
		flush_rewrite_rules();

		return $report;
	}

	/**
	 * Write the default settings for groups that have never been saved.
	 *
	 * @return array
	 */
	protected function seed_settings() {
		$created = array();
		foreach ( array_keys( PDO_SM_Schema::all() ) as $group ) {
			$option = PDO_SM_Settings::option_name( $group );
			if ( null === get_option( $option, null ) ) {
				update_option( $option, PDO_SM_Settings::defaults( $group ) );
				$created[] = $group;
			}
		}
		return array(
			'created' => $created,
			'skipped' => count( PDO_SM_Schema::all() ) - count( $created ),
		);
	}

	/**
	 * Seed taxonomy terms.
	 *
	 * @return array
	 */
	protected function seed_terms() {
		$map = array(
			'pdo_news_cat'    => array(
				'Seasonal Relief',
				'Public Health',
				'Civic Action',
				'Op-Eds & Policy',
				'Field Operations',
				'Press Releases',
			),
			'pdo_program_cat' => array(
				'Education',
				'Nutrition',
				'Climate Action',
				'Health',
				'Livelihood',
			),
			'pdo_team_group'  => array(
				'Executive Committee',
				'Executive Administration & Management',
				'Advisory Board',
			),
			'pdo_partner_cat' => array(
				'Strategic Execution',
				'Academic Hub',
				'Government',
				'Global Network',
			),
			'pdo_story_cat'   => array(
				'Education',
				'Dropout Mentoring',
				'Climate Action',
			),
			'pdo_doc_cat'     => array(
				'Safeguarding Policy',
				'PSEA Policy',
				'AML & CFT Guidelines',
				'Annual Audit Reports',
				'Institutional Documents',
			),
		);

		$created = 0;
		foreach ( $map as $taxonomy => $terms ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}
			foreach ( $terms as $index => $term ) {
				if ( term_exists( $term, $taxonomy ) ) {
					continue;
				}
				$result = wp_insert_term( $term, $taxonomy );
				if ( ! is_wp_error( $result ) ) {
					update_term_meta( $result['term_id'], '_pdo_order', $index );
					++$created;
				}
			}
		}

		return array( 'created' => $created );
	}

	/**
	 * Create the site pages and assign the front page.
	 *
	 * @return array
	 */
	protected function seed_pages() {
		$stored  = get_option( 'pdo_sm_pages', array() );
		$stored  = is_array( $stored ) ? $stored : array();
		$created = array();

		foreach ( self::pages() as $key => $page ) {
			$existing = isset( $stored[ $key ] ) ? get_post( (int) $stored[ $key ] ) : null;

			if ( ! $existing || 'page' !== $existing->post_type || 'trash' === $existing->post_status ) {
				$by_path = get_page_by_path( $page['slug'] );
				if ( $by_path instanceof WP_Post ) {
					$existing = $by_path;
				}
			}

			if ( $existing instanceof WP_Post ) {
				$stored[ $key ] = $existing->ID;
				if ( $page['template'] && get_post_meta( $existing->ID, '_wp_page_template', true ) !== $page['template'] ) {
					update_post_meta( $existing->ID, '_wp_page_template', $page['template'] );
				}
				continue;
			}

			$post_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_title'   => $page['title'],
					'post_name'    => $page['slug'],
					'post_status'  => 'publish',
					'post_content' => '',
					'meta_input'   => array(
						self::SEED_META => 'page_' . $key,
					),
				),
				true
			);

			if ( is_wp_error( $post_id ) ) {
				continue;
			}

			if ( $page['template'] ) {
				update_post_meta( $post_id, '_wp_page_template', $page['template'] );
			}

			$stored[ $key ] = $post_id;
			$created[]      = $page['title'];
		}

		update_option( 'pdo_sm_pages', $stored, false );

		// Point the front page at the Home page without destroying an existing choice.
		if ( 'page' !== get_option( 'show_on_front' ) && ! empty( $stored['home'] ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', (int) $stored['home'] );
		}

		return array( 'created' => $created );
	}

	/**
	 * Create or update a seeded post.
	 *
	 * @param string $seed_key  Unique seed key.
	 * @param array  $post_args wp_insert_post() args.
	 * @param array  $meta      Meta (without the `_pdo_` prefix).
	 * @param array  $terms     Taxonomy => terms.
	 * @return int|false Post ID or false when it already existed.
	 */
	protected function upsert( $seed_key, $post_args, $meta = array(), $terms = array() ) {
		$existing = get_posts(
			array(
				'post_type'      => $post_args['post_type'],
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::SEED_META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $seed_key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( $existing ) {
			return false;
		}

		$post_args['meta_input']                  = isset( $post_args['meta_input'] ) ? $post_args['meta_input'] : array();
		$post_args['meta_input'][ self::SEED_META ] = $seed_key;

		foreach ( $meta as $key => $value ) {
			$post_args['meta_input'][ '_pdo_' . $key ] = $value;
		}

		$post_id = wp_insert_post( $post_args, true );

		if ( is_wp_error( $post_id ) ) {
			return false;
		}

		foreach ( $terms as $taxonomy => $term_names ) {
			if ( taxonomy_exists( $taxonomy ) ) {
				wp_set_object_terms( $post_id, (array) $term_names, $taxonomy );
			}
		}

		return $post_id;
	}

	/**
	 * Seed the flagship programs.
	 *
	 * @return array
	 */
	protected function seed_programs() {
		$programs = array(
			array(
				'key'     => 'program_scholarship',
				'title'   => 'PDO Tiered Scholarship Program',
				'excerpt' => 'A needs-based tiered allocation system that safeguards 1,200+ vulnerable students every year across Natore.',
				'content' => '<p>Since 2000, PDO has actively dismantled educational exclusion for marginalized students across Natore. Recognizing that financial hardship triggers peak dropouts during 9th Grade, SSC, and HSC levels, we operate a <strong>Needs-Based Tiered Allocation System</strong> rather than a generic one-size-fits-all model.</p><p>By conducting rigorous background assessments and partnering directly with Bahadurpur Technical School &amp; College as our Central Implementation Hub, we safeguard 1,200+ vulnerable youth annually with pinpoint Value for Money (VfM).</p>',
				'image'   => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=800&q=80',
				'icon'    => 'graduation-cap',
				'sdgs'    => 'SDG 1 & SDG 4',
				'accent'  => 'emerald',
				'cat'     => 'Education',
				'stats'   => array(
					array( 'value' => '1,200+', 'label' => 'Students supported annually', 'icon' => 'users', 'visible' => 1, 'order' => 0 ),
					array( 'value' => '3', 'label' => 'Needs-based tiers', 'icon' => 'layers', 'visible' => 1, 'order' => 1 ),
				),
				'wins'    => array(
					'Tier 1 (100%): 150+ extreme-poor and orphan students',
					'Tier 2 (50%): 300+ low-income families',
					'Tier 3 (25%): 750+ students supported with books, commute and supplies',
				),
				'cta'     => array( 'Support a Scholarship Tier', '/get-involved/#partner' ),
			),
			array(
				'key'     => 'program_zerohunger',
				'title'   => 'Zero Hunger Campus Model',
				'excerpt' => 'Free, freshly prepared hot mid-day lunches for 200–250+ students and teachers every school day.',
				'content' => '<p>Classroom hunger is one of the starkest causes of rural school absenteeism. Piloted at <strong>Bahadurpur Technical School &amp; College</strong>, this initiative guarantees daily free, freshly prepared, hot and balanced mid-day lunches for <strong>200 to 250+ students and teachers</strong> during tiffin hours.</p>',
				'image'   => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=800&q=80',
				'icon'    => 'bowl-food',
				'sdgs'    => 'SDG 2 (Zero Hunger) & SDG 4',
				'accent'  => 'amber',
				'cat'     => 'Nutrition',
				'stats'   => array(
					array( 'value' => '250+', 'label' => 'Lunches daily', 'icon' => 'bowl-food', 'visible' => 1, 'order' => 0 ),
					array( 'value' => '90%+', 'label' => 'Daily attendance', 'icon' => 'chart-line', 'visible' => 1, 'order' => 1 ),
				),
				'wins'    => array(
					'Over 90%+ daily school attendance achieved within months',
					'Drastically heightened cognitive focus & eliminated midday fatigue',
					'Executed in direct partnership with SRI Welfare Foundation',
				),
				'cta'     => array( 'Sponsor This Model', '/get-involved/#adopt' ),
			),
			array(
				'key'     => 'program_treeguardian',
				'title'   => 'Tree Guardian Mega Project',
				'excerpt' => '10,000+ saplings entrusted to certified school-age "Tree Guardians" with quarterly survival monitoring.',
				'content' => '<p>Unlike one-off sapling photo-ops that quickly wither, PDO and <strong>SRI Welfare Foundation</strong> pioneered the Tree Guardianship philosophy. We distributed <strong>10,000+ saplings</strong> directly into the care of school children, officially entrusting them as certified "Tree Guardians" who water and protect their plant.</p>',
				'image'   => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=800&q=80',
				'icon'    => 'tree',
				'sdgs'    => 'SDG 13 (Climate Action) & SDG 15',
				'accent'  => 'teal',
				'cat'     => 'Climate Action',
				'stats'   => array(
					array( 'value' => '10,000+', 'label' => 'Guardians formed', 'icon' => 'tree', 'visible' => 1, 'order' => 0 ),
					array( 'value' => '250k', 'label' => 'Target by 2030', 'icon' => 'leaf', 'visible' => 1, 'order' => 1 ),
				),
				'wins'    => array(
					'Strict Quarterly M&E Tracking of sapling growth and survival',
					'Annual "Best Tree Guardian Award" honoring dedicated children',
					'In solidarity with Govt. of Bangladesh 250-million tree canopy target',
				),
				'cta'     => array( 'Sign up as Guardian', '/get-involved/#volunteer' ),
			),
			array(
				'key'     => 'program_dropout',
				'title'   => 'Dropout Recovery & Mentoring',
				'excerpt' => 'A specialised recovery wing rehabilitating 40–50 unsuccessful board examinees every year.',
				'content' => '<p>In rural Bangladesh, failing board examinations (SSC/HSC) often brings devastating stigma, acute depression, child labor, or early forced marriage. PDO runs a specialized recovery wing rehabilitating <strong>40 to 50 unsuccessful students annually</strong>.</p>',
				'image'   => 'https://images.unsplash.com/photo-1581092921461-eab62e97a780?auto=format&fit=crop&w=800&q=80',
				'icon'    => 'rotate-left',
				'sdgs'    => 'Second-Chance Education (SDG 4)',
				'accent'  => 'blue',
				'cat'     => 'Education',
				'stats'   => array(
					array( 'value' => '40–50', 'label' => 'Students rehabilitated yearly', 'icon' => 'users', 'visible' => 1, 'order' => 0 ),
				),
				'wins'    => array(
					'Dedicated psycho-social counseling overcoming academic trauma',
					'Parental counseling sessions ensuring a supportive domestic environment',
					'Technical and vocational training pathways at Bahadurpur Tech Hub',
				),
				'cta'     => array( 'Mentor a Student', '/get-involved/#volunteer' ),
			),
			array(
				'key'     => 'program_uniform',
				'title'   => 'Free School Uniform Initiative',
				'excerpt' => '300+ brand-new high quality school uniforms restoring dignity and classroom parity.',
				'content' => '<p>Torn clothes cause children severe embarrassment, leading them to hide at the back of classrooms or drop out altogether. Executed jointly with SRI Welfare Foundation, PDO provided <strong>300+ brand-new high-quality school uniforms</strong> to underprivileged children this year.</p>',
				'image'   => 'https://images.unsplash.com/photo-1596495578065-6e0763fa1178?auto=format&fit=crop&w=800&q=80',
				'icon'    => 'users',
				'sdgs'    => 'Dignity & Retention (SDG 1 & 10)',
				'accent'  => 'purple',
				'cat'     => 'Education',
				'stats'   => array(
					array( 'value' => '300+', 'label' => 'Uniforms provided', 'icon' => 'gift', 'visible' => 1, 'order' => 0 ),
					array( 'value' => '100%', 'label' => 'Retention in recipient cohort', 'icon' => 'check-circle', 'visible' => 1, 'order' => 1 ),
				),
				'wins'    => array(
					'Restoring dignity and parity among classroom peers',
					'Alleviating hidden education expenses for extreme-poor parents',
					'Proven 100% academic retention among uniform recipient cohort',
				),
				'cta'     => array( 'Adopt 200 Uniforms', '/get-involved/#adopt' ),
			),
		);

		$created = 0;
		foreach ( $programs as $index => $program ) {
			$achievements = array();
			foreach ( $program['wins'] as $i => $text ) {
				$achievements[] = array(
					'text'    => $text,
					'icon'    => 'check-circle',
					'visible' => 1,
					'order'   => $i,
				);
			}

			$id = $this->upsert(
				$program['key'],
				array(
					'post_type'    => 'pdo_program',
					'post_title'   => $program['title'],
					'post_excerpt' => $program['excerpt'],
					'post_content' => $program['content'],
					'post_status'  => 'publish',
					'menu_order'   => $index,
				),
				array(
					'image_url'    => $program['image'],
					'image_alt'    => $program['title'],
					'icon'         => $program['icon'],
					'sdgs'         => $program['sdgs'],
					'accent'       => $program['accent'],
					'stats'        => $program['stats'],
					'achievements' => $achievements,
					'cta_label'    => $program['cta'][0],
					'cta_url'      => $program['cta'][1],
					'featured'     => 1,
				),
				array( 'pdo_program_cat' => array( $program['cat'] ) )
			);

			if ( $id ) {
				++$created;
			}
		}

		return array( 'created' => $created );
	}

	/**
	 * Seed the leadership directory.
	 *
	 * @return array
	 */
	protected function seed_team() {
		$members = array(
			array( 'team_rakibul', 'Shaikh Md Rakibul Islam', 'President', 'Visionary educator & founding head guiding long-term institutional expansion.', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80', 'Executive Committee' ),
			array( 'team_hafizur', 'Md. Hafizur Rahman', 'General Secretary', 'Managing constitutional compliance, secretarial duties & board governance.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80', 'Executive Committee' ),
			array( 'team_bobita', 'Bobita Khatun', 'Treasurer', 'Directing institutional accounts, audit alignment & banking fiduciary controls.', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=300&q=80', 'Executive Committee' ),
			array( 'team_farhan', 'Farhan Ishrak Riham', 'Organizing Secretary', 'Mobilizing grassroots volunteer squads, youth delegates and logistical drives.', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80', 'Executive Committee' ),
			array( 'team_taslima', 'Taslima Khatun', 'Executive Member', '', 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=200&q=80', 'Executive Committee' ),
			array( 'team_alamgir', 'Md. Alamgir Hossain', 'Executive Member', '', 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=200&q=80', 'Executive Committee' ),
			array( 'team_nurjahan', 'Mst. Nurjahan Begum', 'Executive Member', '', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80', 'Executive Committee' ),
			array( 'team_rifad', 'Shaikh Rifad Mahmud', 'Executive Director', 'Chief administrative head formulating projects, managing donor relations, INGO MoUs and institutional governance.', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=250&q=80', 'Executive Administration & Management' ),
			array( 'team_abdurrob', 'Abdur Rob', 'Director (Program)', 'Oversees field planning, MEAL alignment, and community execution across all thematic initiatives.', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=250&q=80', 'Executive Administration & Management' ),
			array( 'team_rahiq', 'Rahiq Khan Choudhury', 'Deputy Director (Youth Affairs)', 'Leads youth mobilization, student cabinets, BNCC/Scout partnerships, and climate strike activations.', 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=250&q=80', 'Executive Administration & Management' ),
			array( 'team_arshad', 'Arshad Al Galib', 'Deputy Director (Digital)', 'Directs ICT labs, "Monbarta" tele-health platforms, cyber safety awareness, and digital donor reporting.', 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=250&q=80', 'Executive Administration & Management' ),
		);

		$created = 0;
		foreach ( $members as $index => $member ) {
			list( $key, $name, $role, $bio, $photo, $group ) = $member;
			$id = $this->upsert(
				$key,
				array(
					'post_type'    => 'pdo_team',
					'post_title'   => $name,
					'post_content' => $bio ? '<p>' . $bio . '</p>' : '',
					'post_status'  => 'publish',
					'menu_order'   => $index,
				),
				array(
					'designation' => $role,
					'photo_url'   => $photo,
				),
				array( 'pdo_team_group' => array( $group ) )
			);
			if ( $id ) {
				++$created;
			}
		}

		return array( 'created' => $created );
	}

	/**
	 * Seed the partner directory.
	 *
	 * @return array
	 */
	protected function seed_partners() {
		$partners = array(
			array( 'partner_sri', 'SRI Welfare Foundation', 'Joint Execution Strategic Partner', 'hands-holding', 'emerald', 'Strategic Execution' ),
			array( 'partner_bahadurpur', 'Bahadurpur Tech School & College', 'Flagship Academic Hub & TVET Center', 'school', 'dark', 'Academic Hub' ),
			array( 'partner_dss', 'Dept. of Social Services', 'Govt. of Bangladesh (Nat-295/2000)', 'bank', 'amber', 'Government' ),
			array( 'partner_global', 'Global INGO & UN Network', 'SDG Accelerators & South-South Synergy', 'earth', 'blue', 'Global Network' ),
		);

		$created = 0;
		foreach ( $partners as $index => $partner ) {
			list( $key, $name, $description, $icon, $accent, $cat ) = $partner;
			$id = $this->upsert(
				$key,
				array(
					'post_type'   => 'pdo_partner',
					'post_title'  => $name,
					'post_status' => 'publish',
					'menu_order'  => $index,
				),
				array(
					'description' => $description,
					'icon'        => $icon,
					'accent'      => $accent,
				),
				array( 'pdo_partner_cat' => array( $cat ) )
			);
			if ( $id ) {
				++$created;
			}
		}

		return array( 'created' => $created );
	}

	/**
	 * Seed the impact stories.
	 *
	 * @return array
	 */
	protected function seed_stories() {
		$stories = array(
			array(
				'key'     => 'story_uniform',
				'title'   => 'The Dignity of a Uniform',
				'quote'   => '"I used to hide in the back of the classroom, or sometimes I wouldn\'t go to school at all because my clothes were torn. I felt like I didn\'t belong."',
				'content' => '<p>After receiving a new uniform under the PDO-SRI joint drive, this student shifted to the front desk, participating actively and attending every school day with heads held high.</p>',
				'photo'   => 'https://images.unsplash.com/photo-1596495578065-6e0763fa1178?auto=format&fit=crop&w=600&q=80',
				'tag'     => 'Student Testimonial',
				'cat'     => 'Education',
				'who'     => 'Scholarship recipient (name withheld)',
			),
			array(
				'key'     => 'story_secondchance',
				'title'   => 'The Second Chance (Reclaiming Potential)',
				'quote'   => '"When I didn\'t pass my exams, everyone told me I was useless. I was about to leave for informal manual labor until PDO mentors knocked on our door."',
				'content' => '<p>Through 6 months of empathetic counseling, retake preparation, and TVET vocational training, the student passed successfully and is now employed in technical services.</p>',
				'photo'   => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=600&q=80',
				'tag'     => 'Reclaimed Future',
				'cat'     => 'Dropout Mentoring',
				'who'     => 'TVET graduate (name withheld)',
			),
			array(
				'key'     => 'story_climate',
				'title'   => 'Raising a Climate Warrior',
				'quote'   => '"I was given a title: Tree Guardian. I built a bamboo boundary around my sapling. I water it every morning before school."',
				'content' => '<p>A 10-year-old guardian now actively scolds local adults who throw non-biodegradable plastics near riverbanks, transforming into a vocal grassroots climate leader.</p>',
				'photo'   => 'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=600&q=80',
				'tag'     => 'Climate Warrior',
				'cat'     => 'Climate Action',
				'who'     => 'Certified Tree Guardian, age 10',
			),
		);

		$created = 0;
		foreach ( $stories as $index => $story ) {
			$id = $this->upsert(
				$story['key'],
				array(
					'post_type'    => 'pdo_story',
					'post_title'   => $story['title'],
					'post_content' => $story['content'],
					'post_status'  => 'publish',
					'menu_order'   => $index,
				),
				array(
					'quote'       => $story['quote'],
					'photo_url'   => $story['photo'],
					'image_alt'   => $story['title'],
					'tag'         => $story['tag'],
					'beneficiary' => $story['who'],
					'location'    => 'Singra, Natore',
				),
				array( 'pdo_story_cat' => array( $story['cat'] ) )
			);
			if ( $id ) {
				++$created;
			}
		}

		return array( 'created' => $created );
	}

	/**
	 * Seed the policy / report library.
	 *
	 * The original template referenced PDFs that were never supplied, so the
	 * records are created with their real summaries but without pretending a
	 * file exists. Upload the file to activate the download button.
	 *
	 * @return array
	 */
	protected function seed_documents() {
		$documents = array(
			array(
				'key'     => 'doc_safeguarding',
				'title'   => 'Child & Adult Safeguarding Policy',
				'desc'    => 'Zero tolerance standard for vulnerable groups.',
				'content' => '<p>PDO Child &amp; Vulnerable Adult Safeguarding Policy 2026-2028: Enforces strict behavioral guidelines, digital photo consent, and mandatory reporting for all staff and volunteers.</p>',
				'cat'     => 'Safeguarding Policy',
				'icon'    => 'shield-check',
				'year'    => '2026',
			),
			array(
				'key'     => 'doc_psea',
				'title'   => 'PSEA Institutional Policy',
				'desc'    => 'Prevention of Sexual Exploitation and Abuse.',
				'content' => '<p>PDO Policy on Prevention of Sexual Exploitation and Abuse (PSEA): Zero-tolerance framework adhering strictly to UN standards. Independent confidential investigation committee.</p>',
				'cat'     => 'PSEA Policy',
				'icon'    => 'shield',
				'year'    => '2026',
			),
			array(
				'key'     => 'doc_aml',
				'title'   => 'AML & CFT Guidelines',
				'desc'    => 'Anti-Money Laundering & Banking Audit.',
				'content' => '<p>PDO Anti-Money Laundering &amp; Combating Financing of Terrorism Standards: Institutional bank-only operations, chartered accountant independent annual audits.</p>',
				'cat'     => 'AML & CFT Guidelines',
				'icon'    => 'scale-balanced',
				'year'    => '2026',
			),
			array(
				'key'     => 'doc_audit',
				'title'   => 'Annual Audit Report 2026',
				'desc'    => 'Independent CA firm financial statements.',
				'content' => '<p>PDO Annual Financial &amp; Project Audit: Prepared in full compliance with Department of Social Services, Bangladesh NGO affairs, showing clean opinions across all funds.</p>',
				'cat'     => 'Annual Audit Reports',
				'icon'    => 'file-lines',
				'year'    => '2026',
			),
		);

		$created = 0;
		foreach ( $documents as $index => $document ) {
			$id = $this->upsert(
				$document['key'],
				array(
					'post_type'    => 'pdo_document',
					'post_title'   => $document['title'],
					'post_content' => $document['content'],
					'post_status'  => 'publish',
					'menu_order'   => $index,
				),
				array(
					'description'     => $document['desc'],
					'icon'            => $document['icon'],
					'year'            => $document['year'],
					'preview_enabled' => 1,
					'download_label'  => __( 'Download PDF', 'pdo-site-manager' ),
				),
				array( 'pdo_doc_cat' => array( $document['cat'] ) )
			);
			if ( $id ) {
				++$created;
			}
		}

		return array( 'created' => $created );
	}

	/**
	 * Seed the media articles.
	 *
	 * @return array
	 */
	protected function seed_news() {
		$articles = array(
			array(
				'key'     => 'news_eid',
				'title'   => 'Eid Food Hampers & New Clothes Distribution',
				'excerpt' => 'Distribution of festive food hampers (semai, sugar, edible oil) and brand-new clothes for hundreds of underprivileged children and orphan families during Eid across Singra and Gurudaspur.',
				'image'   => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=600&q=80',
				'cat'     => 'Seasonal Relief',
				'kicker'  => 'Natore & Singra | Annual Campaign',
			),
			array(
				'key'     => 'news_blanket',
				'title'   => 'Midnight Blanket Squads for Homeless & Disabled',
				'excerpt' => 'Volunteer midnight mobile squads delivering warm thermal blankets directly to street dwellers and vulnerable disabled persons during severe cold spells.',
				'image'   => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=600&q=80',
				'cat'     => 'Seasonal Relief',
				'kicker'  => 'Cold Wave Response',
			),
			array(
				'key'     => 'news_dengue',
				'title'   => 'Dengue Prevention & Larvicide Crash Programs',
				'excerpt' => 'Month-long grassroots campaigns, school workshops, and mosquito eradication spraying conducted across dense settlements and educational hubs during peak monsoon.',
				'image'   => 'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?auto=format&fit=crop&w=600&q=80',
				'cat'     => 'Public Health',
				'kicker'  => 'Epidemic Intervention',
			),
			array(
				'key'     => 'news_civic',
				'title'   => 'Youth-Led Traffic Management & Urban Cleanup',
				'excerpt' => 'Voluntary management at busy junctions (e.g. Nichabazar traffic junction) and massive city waste cleanup drives restoring public safety and environmental hygiene.',
				'image'   => 'https://images.unsplash.com/photo-1559027615-cd4628902d4a?auto=format&fit=crop&w=600&q=80',
				'cat'     => 'Civic Action',
				'kicker'  => 'Civic Leadership',
			),
			array(
				'key'     => 'news_tvet',
				'title'   => 'Reforming Rural TVET for Global Digital Markets',
				'excerpt' => 'Analytical assessment advocating for AI, renewable installation skills, and hands-on vocational certifications in rural high schools to eliminate youth unemployment.',
				'image'   => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=600&q=80',
				'cat'     => 'Op-Eds & Policy',
				'kicker'  => 'Shaikh Rifad Mahmud | Policy Brief',
			),
			array(
				'key'     => 'news_heatwave',
				'title'   => 'Extreme Heatwave Oral Saline & Safe Water Drives',
				'excerpt' => 'Distributing thousands of purified water bottles and oral rehydration saline packs to rickshaw pullers, day-laborers, and pedestrians to avert heat strokes.',
				'image'   => 'https://images.unsplash.com/photo-1541888946425-d0fbb1861593?auto=format&fit=crop&w=600&q=80',
				'cat'     => 'Public Health',
				'kicker'  => 'Emergency Relief',
			),
		);

		$created = 0;
		$offset  = 0;
		foreach ( $articles as $article ) {
			$id = $this->upsert(
				$article['key'],
				array(
					'post_type'    => 'pdo_news',
					'post_title'   => $article['title'],
					'post_excerpt' => $article['excerpt'],
					'post_content' => '<p>' . $article['excerpt'] . '</p>',
					'post_status'  => 'publish',
					'post_date'    => gmdate( 'Y-m-d H:i:s', strtotime( '-' . ( $offset * 9 + 3 ) . ' days' ) ),
				),
				array(
					'image_url' => $article['image'],
					'kicker'    => $article['kicker'],
				),
				array( 'pdo_news_cat' => array( $article['cat'] ) )
			);
			if ( $id ) {
				++$created;
			}
			++$offset;
		}

		return array( 'created' => $created );
	}

	/**
	 * Build the navigation menus.
	 *
	 * @return array
	 */
	protected function seed_menus() {
		$pages = get_option( 'pdo_sm_pages', array() );
		$pages = is_array( $pages ) ? $pages : array();

		$created = array();

		$primary = $this->ensure_menu( __( 'PDO Primary Menu', 'pdo-site-manager' ) );
		if ( $primary && ! $this->menu_has_items( $primary ) ) {
			$structure = array(
				array(
					'page'     => 'home',
					'label'    => 'Home',
					'label_bn' => 'হোম',
					'icon'     => 'home',
				),
				array(
					'page'     => 'about',
					'label'    => 'About Us',
					'label_bn' => 'আমাদের সম্পর্কে',
					'icon'     => 'info',
					'children' => array(
						array( 'Who We Are & History', '#history', 'bank' ),
						array( 'Vision, Mission & Values', '#vision', 'eye' ),
						array( 'Leadership & Management', '#leadership', 'users-gear' ),
						array( "President's Address", '#president', 'quote-left' ),
						array( 'Strategic Partners', '#partners', 'handshake' ),
					),
				),
				array(
					'page'     => 'impact',
					'label'    => 'Our Impact',
					'label_bn' => 'আমাদের প্রভাব',
					'icon'     => 'bullseye',
					'children' => array(
						array( 'Tiered Scholarship Program', '#scholarships', 'graduation-cap' ),
						array( 'Programs & Initiatives', '#programs', 'layers' ),
						array( 'Human Stories of Impact', '#stories', 'heart' ),
						array( 'Vision 2030 Targets', '#vision2030', 'chart-line' ),
					),
				),
				array(
					'page'     => 'transparency',
					'label'    => 'Transparency',
					'label_bn' => 'স্বচ্ছতা',
					'icon'     => 'scale-balanced',
					'children' => array(
						array( 'Governance & Compliance', '#governance', 'scale-balanced' ),
						array( 'MEAL Framework & GRM', '#meal', 'chart-line' ),
						array( 'Value for Money (VfM)', '#vfm', 'coins' ),
						array( 'Audit Reports & Policies', '#reports', 'file-pdf' ),
					),
				),
				array(
					'page'     => 'media',
					'label'    => 'Media & News',
					'label_bn' => 'মিডিয়া ও সংবাদ',
					'icon'     => 'newspaper',
				),
				array(
					'page'     => 'involved',
					'label'    => 'Get Involved',
					'label_bn' => 'যুক্ত হোন',
					'icon'     => 'hand-heart',
					'children' => array(
						array( 'Volunteer Sign-up', '#volunteer', 'hand-heart' ),
						array( 'Partner With Us (INGO/CSR)', '#partner', 'handshake' ),
						array( 'Adopt-a-Project Model', '#adopt', 'gift' ),
					),
				),
				array(
					'page'     => 'contact',
					'label'    => 'Contact Us',
					'label_bn' => 'যোগাযোগ',
					'icon'     => 'mail',
				),
			);

			foreach ( $structure as $order => $item ) {
				if ( empty( $pages[ $item['page'] ] ) ) {
					continue;
				}
				$page_id   = (int) $pages[ $item['page'] ];
				$parent_id = wp_update_nav_menu_item(
					$primary,
					0,
					array(
						'menu-item-object-id' => $page_id,
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
						'menu-item-title'     => $item['label'],
						'menu-item-status'    => 'publish',
						'menu-item-position'  => $order + 1,
					)
				);
				if ( is_wp_error( $parent_id ) ) {
					continue;
				}
				update_post_meta( $parent_id, '_pdo_label_bn', $item['label_bn'] );
				update_post_meta( $parent_id, '_pdo_menu_icon', $item['icon'] );

				if ( empty( $item['children'] ) ) {
					continue;
				}
				foreach ( $item['children'] as $child_order => $child ) {
					$child_id = wp_update_nav_menu_item(
						$primary,
						0,
						array(
							'menu-item-title'     => $child[0],
							'menu-item-url'       => get_permalink( $page_id ) . $child[1],
							'menu-item-type'      => 'custom',
							'menu-item-status'    => 'publish',
							'menu-item-parent-id' => $parent_id,
							'menu-item-position'  => $child_order + 1,
						)
					);
					if ( ! is_wp_error( $child_id ) ) {
						update_post_meta( $child_id, '_pdo_menu_icon', $child[2] );
					}
				}
			}
			$created[] = 'primary';
		}

		$footer = $this->ensure_menu( __( 'PDO Footer Menu', 'pdo-site-manager' ) );
		if ( $footer && ! $this->menu_has_items( $footer ) ) {
			foreach ( array( 'home', 'about', 'impact', 'transparency', 'media', 'contact' ) as $order => $key ) {
				if ( empty( $pages[ $key ] ) ) {
					continue;
				}
				wp_update_nav_menu_item(
					$footer,
					0,
					array(
						'menu-item-object-id' => (int) $pages[ $key ],
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
						'menu-item-position'  => $order + 1,
					)
				);
			}
			$created[] = 'footer';
		}

		$programs_menu = $this->ensure_menu( __( 'PDO Footer Programs', 'pdo-site-manager' ) );
		if ( $programs_menu && ! $this->menu_has_items( $programs_menu ) ) {
			$programs = get_posts(
				array(
					'post_type'      => 'pdo_program',
					'posts_per_page' => 6,
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
				)
			);
			foreach ( $programs as $order => $program ) {
				wp_update_nav_menu_item(
					$programs_menu,
					0,
					array(
						'menu-item-object-id' => $program->ID,
						'menu-item-object'    => 'pdo_program',
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
						'menu-item-position'  => $order + 1,
					)
				);
			}
			$created[] = 'footer_programs';
		}

		$locations = get_theme_mod( 'nav_menu_locations', array() );
		$locations = is_array( $locations ) ? $locations : array();
		if ( $primary && empty( $locations['primary'] ) ) {
			$locations['primary'] = $primary;
		}
		if ( $footer && empty( $locations['footer'] ) ) {
			$locations['footer'] = $footer;
		}
		if ( $programs_menu && empty( $locations['footer_programs'] ) ) {
			$locations['footer_programs'] = $programs_menu;
		}
		set_theme_mod( 'nav_menu_locations', $locations );

		return array( 'created' => $created );
	}

	/**
	 * Create a menu if it does not exist yet.
	 *
	 * @param string $name Menu name.
	 * @return int Menu term ID or 0.
	 */
	private function ensure_menu( $name ) {
		$menu = wp_get_nav_menu_object( $name );
		if ( $menu ) {
			return (int) $menu->term_id;
		}
		$menu_id = wp_create_nav_menu( $name );
		return is_wp_error( $menu_id ) ? 0 : (int) $menu_id;
	}

	/**
	 * Does the menu already have items?
	 *
	 * @param int $menu_id Menu ID.
	 * @return bool
	 */
	private function menu_has_items( $menu_id ) {
		$items = wp_get_nav_menu_items( $menu_id );
		return ! empty( $items );
	}

	/**
	 * Handle the admin-post request from the setup wizard.
	 *
	 * @return void
	 */
	public function handle_run() {
		PDO_SM_Capabilities::assert_manage();
		check_admin_referer( 'pdo_sm_run_import' );

		$steps = isset( $_POST['steps'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['steps'] ) ) : array();

		$report = $this->run( $steps );

		set_transient( 'pdo_sm_import_report_' . get_current_user_id(), $report, 5 * MINUTE_IN_SECONDS );

		wp_safe_redirect( add_query_arg( 'pdo_imported', 1, admin_url( 'admin.php?page=pdo-setup' ) ) );
		exit;
	}
}
