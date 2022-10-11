<?php
/**
 * @var int $rule_id
 * @var U_Next_Story_Rule $the_rule
 */
?>
<tr data-ruleid="<?php echo $rule_id; ?>" class="u-rule-raw">
    <td class="column-priority"><?php echo $the_rule->priority; ?></td>
    <td class="column-name"><?php echo $the_rule->title; ?></td>
    <td class="column-include">
        <?php echo $the_rule->get_post_types_html(); ?>
    </td>
    <td class="column-same-terms">
        <?php echo $the_rule->get_same_term_html(); ?>
    </td>
    <td class="column-effect">
        <?php
        $effects = u_ns_get_effects_navigation();
        echo $the_rule->apply_styles === 'on' && isset($effects[$the_rule->effects_navigation])
            ? $effects[$the_rule->effects_navigation]
            : $the_rule->apply_styles;
        ?>
    </td>
    <td class="column-actions">
        <button class="uns-edit-rule"><?php _e('Edit', 'u-next-story'); ?></button>
        <button class="uns-delete-rule"><?php _e('Delete', 'u-next-story'); ?></button>
    </td>
</tr>
