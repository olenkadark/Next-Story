<?php
/**
 * @var U_Next_Story_Settings $settings
 * @var array $section
 */
?>
<h2><?php echo $section['title']; ?></h2>
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
                <?php _e('In same term', 'u-next-story'); ?>
            </th>
            <th id="effect" class="manage-column column-effect">
                <?php _e('Effect', 'u-next-story'); ?>
            </th>
            <th id="actions" class="manage-column column-actions" width="150">
            </th>
        </tr>
    </thead>
    <tbody>
        <tr class="no-items" <?php echo $settings->rules && is_array($settings->rules) ? 'style="display: none;"' : ''; ?>>
            <td colspan="6"><?php _e('No Rules'); ?></td>
        </tr>
        <?php
        if( $settings->rules && is_array($settings->rules)): ?>
            <?php
            foreach($settings->rules as $rule_id => $the_rule) {
                include "html-rule-row.php";
            }
            ?>
        <?php endif; ?>
    </tbody>
</table>
<p class="submit">
<button class="button alignright" id="add_new_rule"><?php _e('Add rule', 'u-next-story'); ?></button>
</p>
