<?php
/**
 * The job's custom fields as a definition list: Job Title, Salary, Location.
 *
 * @package Jobs_Board
 */

defined( 'ABSPATH' ) || exit;
?>
<dl class="job-fields">
	<?php foreach ( jobs_board_get_fields() as $jobs_board_key => $jobs_board_field ) : ?>
		<div class="job-fields__row job-fields__row--<?php echo esc_attr( ltrim( $jobs_board_key, '_' ) ); ?>">
			<dt><?php echo esc_html( $jobs_board_field['label'] ); ?></dt>
			<dd><?php echo '' !== $jobs_board_field['value'] ? esc_html( $jobs_board_field['value'] ) : esc_html__( 'Not specified', 'jobs-board' ); ?></dd>
		</div>
	<?php endforeach; ?>
</dl>
