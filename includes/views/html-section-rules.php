<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
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
        <?php
        if($rules && !empty($rules) && is_array($rules)): ?>
            <?php foreach($rules as $rule_id => $rule): $the_rule = new U_Next_Story_Rule($rule); ?>
            <tr data-id="<?php echo $rule_id; ?>">
                <td class="column-name"><?php echo $the_rule->get_title(); ?></td>
                <td class="column-include">
                    <?php echo $the_rule->get_post_types(); ?>
                </td>
                <td class="column-exclude"><b>Post_types:sd</b></td>
                <td class="column-actions">
                    <button class="">Edit</button>
                    <button class="">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr class="no-items">
                <td colspan="7"><?php _e('No Rules'); ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<p class="submit">
<button class="button alignright" id="add_new_rule"><?php _e('Add rule', 'u-next-story'); ?></button>
</p>