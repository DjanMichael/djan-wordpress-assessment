<?php
/**
 * "Jobs" custom post type with Job Title, Salary and Location fields — no plugins.
 *
 * - Post type `job` (Jobs in the admin menu): add, edit, trash/delete like posts.
 * - Fields are post meta, edited in a "Job details" meta box and exposed to the REST API.
 * - Admin list shows the three fields as columns (Location is sortable).
 * - Front end: archive at /jobs/ (archive-job.php) and single at /jobs/{slug}/ (single-job.php).
 *
 * @package Jobs_Board
 */

defined( 'ABSPATH' ) || exit;

/**
 * Custom field definitions, keyed by meta key.
 *
 * Keys start with "_" so they stay out of the generic Custom Fields panel;
 * the meta box below is the one place to edit them.
 *
 * @return array<string, array{label: string, placeholder: string, input: string}>
 */
function jobs_board_fields() {
	return array(
		'_job_title'    => array(
			'label'       => __( 'Job Title', 'jobs-board' ),
			'placeholder' => __( 'e.g. Senior WordPress Developer', 'jobs-board' ),
			'input'       => 'job_title',
		),
		'_job_salary'   => array(
			'label'       => __( 'Salary', 'jobs-board' ),
			'placeholder' => __( 'e.g. £45,000 – £55,000 per year', 'jobs-board' ),
			'input'       => 'job_salary',
		),
		'_job_location' => array(
			'label'       => __( 'Location', 'jobs-board' ),
			'placeholder' => __( 'e.g. Chelmsford, Essex (Hybrid)', 'jobs-board' ),
			'input'       => 'job_location',
		),
	);
}

/**
 * Registers the post type and its meta.
 */
function jobs_board_register_post_type() {
	register_post_type(
		'job',
		array(
			'labels'        => array(
				'name'                  => __( 'Jobs', 'jobs-board' ),
				'singular_name'         => __( 'Job', 'jobs-board' ),
				'menu_name'             => __( 'Jobs', 'jobs-board' ),
				'add_new'               => __( 'Add New Job', 'jobs-board' ),
				'add_new_item'          => __( 'Add New Job', 'jobs-board' ),
				'edit_item'             => __( 'Edit Job', 'jobs-board' ),
				'new_item'              => __( 'New Job', 'jobs-board' ),
				'view_item'             => __( 'View Job', 'jobs-board' ),
				'view_items'            => __( 'View Jobs', 'jobs-board' ),
				'search_items'          => __( 'Search Jobs', 'jobs-board' ),
				'not_found'             => __( 'No jobs found.', 'jobs-board' ),
				'not_found_in_trash'    => __( 'No jobs found in Trash.', 'jobs-board' ),
				'all_items'             => __( 'All Jobs', 'jobs-board' ),
				'archives'              => __( 'Job Archives', 'jobs-board' ),
				'item_published'        => __( 'Job published.', 'jobs-board' ),
				'item_updated'          => __( 'Job updated.', 'jobs-board' ),
				'filter_items_list'     => __( 'Filter jobs list', 'jobs-board' ),
				'items_list_navigation' => __( 'Jobs list navigation', 'jobs-board' ),
				'items_list'            => __( 'Jobs list', 'jobs-board' ),
			),
			'public'        => true,
			'has_archive'   => 'jobs',
			'rewrite'       => array(
				'slug'       => 'jobs',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-portfolio',
			'menu_position' => 20,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			'show_in_rest'  => true,
		)
	);

	foreach ( array_keys( jobs_board_fields() ) as $meta_key ) {
		register_post_meta(
			'job',
			$meta_key,
			array(
				'type'              => 'string',
				'single'            => true,
				'default'           => '',
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => static function ( $allowed, $meta_key, $post_id ) {
					return current_user_can( 'edit_post', $post_id );
				},
			)
		);
	}
}
add_action( 'init', 'jobs_board_register_post_type' );

/**
 * Rebuilds rewrite rules once the theme is activated so /jobs/ resolves.
 */
function jobs_board_flush_rewrites() {
	jobs_board_register_post_type();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'jobs_board_flush_rewrites' );

/**
 * Drops the stored rules when switching away; WordPress rebuilds them without /jobs/.
 */
function jobs_board_reset_rewrites() {
	delete_option( 'rewrite_rules' );
}
add_action( 'switch_theme', 'jobs_board_reset_rewrites' );

/**
 * The job's fields with their saved values, in display order.
 *
 * @param int|WP_Post|null $post Job post; defaults to the current post.
 * @return array<string, array{label: string, value: string}> Keyed by meta key.
 */
function jobs_board_get_fields( $post = null ) {
	$post   = get_post( $post );
	$values = array();

	foreach ( jobs_board_fields() as $meta_key => $field ) {
		$values[ $meta_key ] = array(
			'label' => $field['label'],
			'value' => $post ? (string) get_post_meta( $post->ID, $meta_key, true ) : '',
		);
	}

	return $values;
}

/* -------------------------------------------------------------------------
 * Admin: meta box
 * ---------------------------------------------------------------------- */

/**
 * Jobs use the classic edit screen: the block editor tucks meta boxes into a
 * collapsed pane under the canvas, and these three fields should be in plain sight.
 *
 * @param bool   $use_block_editor Whether the post type uses the block editor.
 * @param string $post_type        Post type.
 * @return bool
 */
function jobs_board_use_classic_editor( $use_block_editor, $post_type ) {
	return 'job' === $post_type ? false : $use_block_editor;
}
add_filter( 'use_block_editor_for_post_type', 'jobs_board_use_classic_editor', 10, 2 );

/**
 * Adds the "Job details" box, shown straight under the job's name.
 */
function jobs_board_add_meta_box() {
	add_meta_box( 'jobs-board-details', __( 'Job details', 'jobs-board' ), 'jobs_board_render_meta_box', 'job', 'jobs_board_after_title', 'high' );
}
add_action( 'add_meta_boxes_job', 'jobs_board_add_meta_box' );

/**
 * Prints the custom "after title" meta box area on the job screen.
 *
 * @param WP_Post $post Job being edited.
 */
function jobs_board_meta_boxes_after_title( $post ) {
	if ( 'job' === $post->post_type ) {
		do_meta_boxes( get_current_screen(), 'jobs_board_after_title', $post );
	}
}
add_action( 'edit_form_after_title', 'jobs_board_meta_boxes_after_title' );

/**
 * Prints the Job Title, Salary and Location inputs.
 *
 * @param WP_Post $post Job being edited.
 */
function jobs_board_render_meta_box( $post ) {
	wp_nonce_field( 'jobs_board_save_details', 'jobs_board_nonce' );
	?>
	<table class="form-table" role="presentation">
		<tbody>
			<?php foreach ( jobs_board_fields() as $meta_key => $field ) : ?>
				<tr>
					<th scope="row"><label for="<?php echo esc_attr( $field['input'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
					<td>
						<input
							type="text"
							class="regular-text"
							id="<?php echo esc_attr( $field['input'] ); ?>"
							name="jobs_board[<?php echo esc_attr( $field['input'] ); ?>]"
							value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"
							placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
						>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}

/**
 * Saves the meta box fields.
 *
 * @param int $post_id Job ID.
 */
function jobs_board_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['jobs_board_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['jobs_board_nonce'] ) ), 'jobs_board_save_details' ) ) {
		return;
	}

	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$input = isset( $_POST['jobs_board'] ) && is_array( $_POST['jobs_board'] ) ? wp_unslash( $_POST['jobs_board'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- each value is sanitized below.

	foreach ( jobs_board_fields() as $meta_key => $field ) {
		if ( ! array_key_exists( $field['input'], $input ) ) {
			continue;
		}

		$value = sanitize_text_field( $input[ $field['input'] ] );

		if ( '' === $value ) {
			delete_post_meta( $post_id, $meta_key );
		} else {
			update_post_meta( $post_id, $meta_key, $value );
		}
	}
}
add_action( 'save_post_job', 'jobs_board_save_meta_box' );

/**
 * "Job published / updated" notices on the edit screen instead of the generic "Post …" ones.
 *
 * @param array<string, array<int, string>> $messages Messages keyed by post type.
 * @return array<string, array<int, string>>
 */
function jobs_board_updated_messages( $messages ) {
	$post = get_post();
	$link = $post ? sprintf( ' <a href="%s">%s</a>', esc_url( get_permalink( $post ) ), esc_html__( 'View job', 'jobs-board' ) ) : '';

	$messages['job'] = array(
		0  => '',
		1  => __( 'Job updated.', 'jobs-board' ) . $link,
		2  => __( 'Custom field updated.', 'jobs-board' ),
		3  => __( 'Custom field deleted.', 'jobs-board' ),
		4  => __( 'Job updated.', 'jobs-board' ),
		5  => __( 'Job restored from a revision.', 'jobs-board' ),
		6  => __( 'Job published.', 'jobs-board' ) . $link,
		7  => __( 'Job saved.', 'jobs-board' ),
		8  => __( 'Job submitted.', 'jobs-board' ),
		9  => __( 'Job scheduled.', 'jobs-board' ),
		10 => __( 'Job draft updated.', 'jobs-board' ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'jobs_board_updated_messages' );

/* -------------------------------------------------------------------------
 * Admin: list table columns
 * ---------------------------------------------------------------------- */

/**
 * Adds Job Title, Salary and Location columns after the post title.
 *
 * @param array<string, string> $columns Existing columns.
 * @return array<string, string>
 */
function jobs_board_admin_columns( $columns ) {
	$result = array();

	foreach ( $columns as $key => $label ) {
		$result[ $key ] = $label;

		if ( 'title' === $key ) {
			foreach ( jobs_board_fields() as $meta_key => $field ) {
				$result[ $meta_key ] = $field['label'];
			}
		}
	}

	return $result;
}
add_filter( 'manage_job_posts_columns', 'jobs_board_admin_columns' );

/**
 * Prints a field value in its column.
 *
 * @param string $column  Column key.
 * @param int    $post_id Job ID.
 */
function jobs_board_admin_column_value( $column, $post_id ) {
	if ( array_key_exists( $column, jobs_board_fields() ) ) {
		$value = get_post_meta( $post_id, $column, true );
		echo '' === $value ? '<span aria-hidden="true">—</span>' : esc_html( $value );
	}
}
add_action( 'manage_job_posts_custom_column', 'jobs_board_admin_column_value', 10, 2 );

/**
 * Makes the Location column sortable.
 *
 * @param array<string, string> $columns Sortable columns.
 * @return array<string, string>
 */
function jobs_board_sortable_columns( $columns ) {
	$columns['_job_location'] = '_job_location';

	return $columns;
}
add_filter( 'manage_edit-job_sortable_columns', 'jobs_board_sortable_columns' );

/* -------------------------------------------------------------------------
 * Queries
 * ---------------------------------------------------------------------- */

/**
 * Orders the admin list by Location when asked, and pages the public archive.
 *
 * @param WP_Query $query Current query.
 */
function jobs_board_pre_get_posts( $query ) {
	if ( ! $query->is_main_query() ) {
		return;
	}

	if ( is_admin() && '_job_location' === $query->get( 'orderby' ) ) {
		$query->set( 'meta_key', '_job_location' );
		$query->set( 'orderby', 'meta_value' );
		return;
	}

	if ( ! is_admin() && $query->is_post_type_archive( 'job' ) ) {
		$query->set( 'posts_per_page', 12 );
	}
}
add_action( 'pre_get_posts', 'jobs_board_pre_get_posts' );

/* -------------------------------------------------------------------------
 * SEO: JobPosting structured data on single jobs
 * ---------------------------------------------------------------------- */

/**
 * Prints schema.org JobPosting JSON-LD for the current job.
 */
function jobs_board_job_posting_schema() {
	if ( ! is_singular( 'job' ) ) {
		return;
	}

	$post   = get_queried_object();
	$fields = jobs_board_get_fields( $post );
	$title  = '' !== $fields['_job_title']['value'] ? $fields['_job_title']['value'] : get_the_title( $post );

	$data = array(
		'@context'           => 'https://schema.org',
		'@type'              => 'JobPosting',
		'title'              => $title,
		'description'        => wpautop( wp_kses_post( $post->post_content ) ),
		'datePosted'         => get_the_date( 'c', $post ),
		'url'                => get_permalink( $post ),
		'hiringOrganization' => array(
			'@type'  => 'Organization',
			'name'   => get_bloginfo( 'name' ),
			'sameAs' => home_url( '/' ),
		),
	);

	if ( '' !== $fields['_job_location']['value'] ) {
		$data['jobLocation'] = array(
			'@type'   => 'Place',
			'address' => array(
				'@type'           => 'PostalAddress',
				'addressLocality' => $fields['_job_location']['value'],
				'addressCountry'  => 'GB',
			),
		);
	}

	printf( '<script type="application/ld+json">%s</script>' . "\n", wp_json_encode( $data, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
}
add_action( 'wp_head', 'jobs_board_job_posting_schema' );
