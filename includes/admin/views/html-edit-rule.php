<?php
/**
 * @var U_Next_Story_Rule $the_rule
 * @var int $rule_id
 */
$_sections = U_Next_Story()->settings->settings['general']['sections'];
?>
<tr class="edit-rule" data-ruleid="<?php esc_attr_e($rule_id); ?>">
    <td colspan="6">
        <form action="#" id="edit-rule-form">
            <input type="hidden" name="rule_id" value="<?php esc_attr_e($rule_id); ?>">
            <input type="hidden" name="priority" value="<?php esc_attr_e($the_rule->priority); ?>">
            <div class="u--grid-2-col">
            <div class="column-row">
                <?php
                array_unshift($_sections['general']['fields'] , array(
	                'id'          => 'title',
	                'label'       => __( 'Title', 'u-next-story' ),
	                'type'        => 'text',
                ) );
                $sections = [
                        'general' => $_sections['general'],
                        'styles' => $_sections['styles']
                ];

                include "html-edit-rule-section.php"; ?>
            </div>
            <div class="column-row">
		        <?php
		        $sections = [
			        'in_same_term' => $_sections['in_same_term'],
			        'exclude' => $_sections['exclude']
		        ];
                include "html-edit-rule-section.php"; ?>
            </div>
            </div>
            <div class="submit widefat ">
                <button type="submit" class="button-primary"><?php _e( 'Save rule', 'u-next-story' ); ?></button>
                <button type="button" class="button u-cancel-edit"><?php _e( 'Cancel', 'u-next-story' ); ?></button>
            </div>
        </form>
    </td>
</tr>
