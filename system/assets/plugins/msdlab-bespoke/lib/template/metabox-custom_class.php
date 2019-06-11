<?php
$postid = is_admin()?$_GET['post']:$post->ID;
$template_file = get_post_meta($postid,'_wp_page_template',TRUE);
?>
<table class="form-table custom-class-controls">
    <tbody>
    <?php $mb->the_field('bodyclass'); ?>
    <tr valign="top" class="switchable">
        <td><label for="bodyclass">Custom class names</label><br/>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /><br />
            <i>Separate with spaces.</i>
        </td>
    </tr>
    </tbody>
</table>