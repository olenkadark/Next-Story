<tr class="edit-rule" data-id="<?php echo $rule_id; ?>">
    <td colspan="7">
        <form action="#" id="edit-rule-form">
            <input type="hidden" name="rule_id" value="<?php echo $rule_id; ?>">
        <div class="form-field">
            <input type="text" name="rule_title" id="rule_title" placeholder="<?php _e('Enter title here..', 'u-next-story'); ?>">
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
                <button class="button"><?php _e('Cancel', 'u-next-story'); ?></button>
            </div>
        </form>
    </td>
</tr>