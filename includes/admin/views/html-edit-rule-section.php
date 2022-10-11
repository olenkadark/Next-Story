<?php
/**
 * @var U_Next_Story_Rule $the_rule
 * @var array $sections
 */

foreach ( $sections as $sid => $section ) {
	?>

    <h3>
		<?php esc_html_e($section['title']); ?>
		<?php
		$attributes = '';
		$class      = '';
		switch ( $sid ) {
			case 'styles':
				$f = array(
					'id'          => 'apply_styles',
					'description' => __( 'Tick this box to apply custom styles for this rule.',
						'u-next-story' ),
					'type'        => 'checkbox',
					'value'       => $the_rule->apply_styles
				);
				U_Next_Story_Admin_Api::display_field( $f );
				$attributes = $the_rule->apply_styles === 'off' ? 'style="display:none;"' : '';
				$attributes .= ' class="u--grid-2-col"';
				break;
			case 'in_same_term':
				?>
                <small>(<?php echo $section['description'] ?>)</small>
				<?php
				break;
		}
		?></h3>
    <div id="<?php echo $sid; ?>-options" <?php echo $attributes; ?> >
		<?php foreach ( $section['fields'] as $field ) {
			$field['value'] = $the_rule->{$field['id']};
			?>
            <div class="form-field">
                <label for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label>
				<?php U_Next_Story_Admin_Api::display_field( $field );; ?>
            </div>
		<?php } ?>
    </div>
	<?php
}