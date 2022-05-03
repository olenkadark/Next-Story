<?php
/**
 * @var array $rules
 */
?>
<table class="wp-list-table widefat fixed striped" id="u_next_story_rules_table">
    <thead>
        <tr>
            <th id="priority" class="manage-column column-priority">
                <?php _e('Priority', 'u-next-story'); ?>
            </th>
            <th id="name" class="manage-column column-name column-primary">
                <?php _e('Title', 'u-next-story'); ?>
            </th>
            <th id="post-types" class="manage-column column-post-types">
                <?php _e('Post Types', 'u-next-story'); ?>
            </th>
            <th id="menu" class="manage-column column-menu">
                <?php _e('Menu', 'u-next-story'); ?>
            </th>
            <th id="sub-items" class="manage-column column-sub-items">
                <?php _e('Sub-items', 'u-next-story'); ?>
            </th>
            <th id="exclude" class="manage-column column-loop">
                <?php _e('Loop', 'u-next-story'); ?>
            </th>
            <th id="effect" class="manage-column column-effect">
                <?php _e('Effect', 'u-next-story'); ?>
            </th>
            <th id="actions" class="manage-column column-actions" width="150">
            </th>
        </tr>
    </thead>
    <tbody>
        <tr class="no-items" <?php echo $rules && is_array($rules) ? 'style="display: none;"' : ''; ?>>
            <td colspan="8"><?php _e('No Rules'); ?></td>
        </tr>
        <?php
        if( $rules && is_array($rules)): ?>
            <?php
            foreach($rules as $rule_id => $rule) {
                $the_rule = new U_Next_Story_Rule($rule);
                include "html-rule-row.php";
            }
            ?>
        <?php endif; ?>
    </tbody>
</table>
<p class="submit">
<button class="button alignright" id="add_new_rule"><?php _e('Add rule', 'u-next-story'); ?></button>
</p>
