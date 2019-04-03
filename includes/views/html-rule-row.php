<tr data-ruleid="<?php echo $rule_id; ?>" class="u-rule-raw">
    <td class="column-name"><?php echo $the_rule->title; ?></td>
    <td class="column-include">
        <?php echo $the_rule->get_post_types_html(); ?>
    </td>
    <td class="column-menu">
        <?php echo $the_rule->menu; ?>
    </td>
    <td class="column-sub-items">
        <?php echo $the_rule->submenu; ?>
    </td>
    <td class="column-sub-loop">
        <?php echo $the_rule->loop_menu; ?>
    </td>
    <td class="column-sub-effect">
        <?php echo $the_rule->apply_styles; ?>
    </td>
    <td class="column-actions">
        <button class="uns-edit-rule">Edit</button>
        <button class="uns-delete-rule">Delete</button>
    </td>
</tr>