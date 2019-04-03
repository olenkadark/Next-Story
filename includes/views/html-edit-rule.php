<?php
$sections = U_Next_Story()->settings->settings[ 'general']['sections'];
?>
<tr class="edit-rule" data-ruleid="<?php echo $rule_id; ?>">
    <td colspan="8">
        <form action="#" id="edit-rule-form">
            <input type="hidden" name="rule_id" value="<?php echo $rule_id; ?>">
            <input type="hidden" name="priority" value="<?php echo $the_rule->priority; ?>">
        <div class="form-field">
            <input type="text" name="title" value="<?php echo $the_rule->title; ?>" id="rule_title" placeholder="<?php _e('Enter title here..', 'u-next-story'); ?>">
        </div>
        <?php
        foreach ($sections as $sid => $section){
            ?>
            <div class="column-row">
            <h3>
                <?php echo $section['title']; ?>
                <?php

                if( $sid == 'styles'){
                    $f = array(
                        'id' 			=> 'apply_styles',
                        'description'	=> __( 'Tick this box to apply custom styles for this rule.', 'u-next-story' ),
                        'type'			=> 'checkbox',
                        'value'  		=> $the_rule->apply_styles
                    );
                    U_Next_Story()->admin->display_field($f);
                }
                ?></h3>
                <div id="<?php echo $sid; ?>-options" <?php echo $sid == 'styles' && false ? 'style="dispay:none;"' : ''; ?> >
                <?php foreach ($section['fields'] as $field ){ ?>
                    <div class="form-field">
                        <label for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label>
                        <?php U_Next_Story()->admin->display_field($field); ?>
                    </div>
               <?php } ?>
                </div>
            </div>
            <?php
        }
        ?>
            <div class="submit widefat ">
                <button type="submit" class="button-primary"><?php _e('Save rule', 'u-next-story'); ?></button>
                <button type="button" class="button u-cancel-edit"><?php _e('Cancel', 'u-next-story'); ?></button>
            </div>
        </form>
    </td>
</tr>