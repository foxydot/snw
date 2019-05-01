<div class="msdlab_meta_control" id="msdlab_meta_control">
    <p id="warning" style="display: none;background:lightYellow;border:1px solid #E6DB55;padding:5px;">Order has changed. Please click Save or Update to preserve order.</p>
    <div class="table">
    <?php $s = 1; ?>
    <?php while($mb->have_fields_and_multi('sections')): ?>
    <?php $mb->the_group_open(); ?>
    <?php $section_name = strlen($mb->get_the_value('section-name'))==0?'Section '.$s:$mb->get_the_value('section-name')?>
    <div id="<?php print $section_name; ?>" class="msdlab_section row <?php print $s%2==1?'even':'odd'; ?>">
        <h2 class="section_handle <?php print $s%2==1?'even':'odd'; ?>"><span><?php print $section_name; ?></span></h2>
        <div class="section_data">
        <div class="section_params">
        <div class="cell">
            <label>Section Name*</label>            
            <div class="input_container">
                <input type="text" name="<?php $mb->the_name('section-name'); ?>" value="<?php $mb->the_value('section-name'); ?>"/><br />
                <i>This section name is for styling and identification only. It will not appear as readable text on the site</i>
            </div>
        </div>
        <div class="cell">  
            <label>Hide Section</label>
            <div class="input_container">
                <?php $mb->the_field('section-hidden-bool'); ?>
                <div class="ui-toggle-btn">
                    <input type="checkbox" name="<?php $mb->the_name(); ?>" value="1"<?php $mb->the_checkbox_state('1'); ?>/> 
                    <div class="handle" data-on="ON" data-off="OFF"></div>
                </div>
            </div>
        </div>
        <div class="cell">
            <?php $mb->the_field('layout'); ?>
            <label><?php print $section_name; ?> Layout</label>            
            <div class="input_container">
                <select name="<?php $mb->the_name(); ?>" class="layout"> //switch to radio with images?
                    <option value=""<?php $mb->the_select_state('default'); ?>>Default (One Column)</option>
                    <option value="two-col"<?php $mb->the_select_state('two-col'); ?>>Two Columns</option>
                    <option value="three-col"<?php $mb->the_select_state('three-col'); ?>>Three Columns</option>
                    <option value="four-col"<?php $mb->the_select_state('four-col'); ?>>Four Columns</option>
                </select>
            </div>
        </div>
        <div class="cell">
            <label>CSS Classes</label>
            <div class="input_container" style="-moz-column-count: 2;
-moz-column-gap: 1em;
-webkit-column-count: 2;
-webkit-column-gap: 1em;
column-count: 2;
column-gap: 1em;">
                <?php $items = array(
                    'Dark grey background' => 'bkg-dkgrey',
                    'Light grey background' => 'bkg-ltgrey',
                    'Offwhite background' => 'bkg-offwhite',
                    'Green background' => 'bkg-green',
                    'Navy background' => 'bkg-dkblue',
                    'Diamond backgrond' => 'bkg-diamond',
                    'Dark grey text' => 'text-dkgrey',
                    'Navy text' => 'text-dkblue',
                    'White text' => 'text-white',
                    'Red Button' => 'btn-red',
                    'White Button' => 'btn-white',
                    'Center titles' => 'cntr-titles',); ?>
                <?php foreach ($items as $i => $item): ?>
                    <?php $mb->the_field('css-classes', WPALCHEMY_FIELD_HINT_CHECKBOX_MULTI); ?>
                    <input type="checkbox" name="<?php $mb->the_name(); ?>" value="<?php echo $item; ?>"<?php $mb->the_checkbox_state($item); ?>/> <?php echo $i; ?><br/>
                <?php endforeach; ?>
            </div>
        </div>
            <div class="cell">
		        <?php $mb->the_field('custom-css-classes'); ?>
                <label>Custom Classes</label>
                <div class="input_container">
                    <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/><br />
                </div>
            </div>
        </div>
        <div class="content-area box">
            <div class="cell">
                <?php $mb->the_field('content-area-title'); ?>
                <label>Title</label>            
                <div class="input_container">
                    <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
                </div>
            </div>
            <div class="cell">
                <?php $mb->the_field('content-area-subtitle'); ?>
                <label>Subtitle</label>            
                <div class="input_container">
                    <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
                </div>
            </div>
            <div class="cell">
                <label>Use Header Text</label>
                <div class="input_container">
                    <?php $mb->the_field('header-area-bool'); ?>
                    <div class="ui-toggle-btn">
                      <input type="checkbox" name="<?php $mb->the_name(); ?>" value="1"<?php $mb->the_checkbox_state('1');?> />
                      <div class="handle" data-on="ON" data-off="OFF"></div>
                    </div>
                    <div class="switchable">
                        <?php 
                        $mb->the_field('header-area-content');
                        $mb_content = html_entity_decode($mb->get_the_value(), ENT_QUOTES, 'UTF-8');
                        $mb_editor_id = sanitize_key($mb->get_the_name());
                        $mb_settings = array('textarea_name'=>$mb->get_the_name(),'textarea_rows' => '10',);
                        wp_editor( $mb_content, $mb_editor_id, $mb_settings );
                        ?>
                   </div>
               </div>
            </div>
            <div class="cell column-1">
                <label class="cols-2 cols-3 cols-4">Column 1 Width</label>
                <div class="input_container cols-2 cols-3 cols-4">
                    <?php 
                    $mb->the_field('content-area-width');
                    ?>
                    <input type="text" class="small column-width" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /> How many columns out of 12?
                </div>
                <label><span class="cols-2 cols-3 cols-4">Column 1 </span>Content</label>
                <div class="input_container">
                    <?php 
                    $mb->the_field('content-area-content');
                    $mb_content = html_entity_decode($mb->get_the_value(), ENT_QUOTES, 'UTF-8');
                    $mb_editor_id = sanitize_key($mb->get_the_name());
                    $mb_settings = array('textarea_name'=>$mb->get_the_name(),'textarea_rows' => '10',);
                    wp_editor( $mb_content, $mb_editor_id, $mb_settings );
                    ?>
               </div>
            </div>
            <?php for($i=2;$i<=4;$i++){ ?>
            <div class="cell column-<?php print $i; ?> <?php print get_hidden($i) ?>">
                <label>Column <?php print $i; ?> Width</label>
                <div class="input_container">
                    <?php 
                    $mb->the_field('column-'.$i.'-area-width');
                    ?>
                    <input type="text" class="small column-width" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /> How many columns out of 12?
                </div>
                <label>Column <?php print $i; ?> Content</label>
                <div class="input_container">
                    <?php 
                    $mb->the_field('column-'.$i.'-area-content');
                    $mb_content = html_entity_decode($mb->get_the_value(), ENT_QUOTES, 'UTF-8');
                    $mb_editor_id = sanitize_key($mb->get_the_name());
                    $mb_settings = array('textarea_name'=>$mb->get_the_name(),'textarea_rows' => '10',);
                    wp_editor( $mb_content, $mb_editor_id, $mb_settings );
                    ?>
               </div>
            </div>
            <?php } ?>
            <div class="cell">
                <label>Use Footer Text</label>
                <div class="input_container">
                    <?php $mb->the_field('footer-area-bool'); ?>
                    <div class="ui-toggle-btn">
                      <input type="checkbox" name="<?php $mb->the_name(); ?>" value="1"<?php $mb->the_checkbox_state('1');?> />
                      <div class="handle" data-on="ON" data-off="OFF"></div>
                    </div>
                    <div class="switchable">
                        <?php 
                        $mb->the_field('footer-area-content');
                        $mb_content = html_entity_decode($mb->get_the_value(), ENT_QUOTES, 'UTF-8');
                        $mb_editor_id = sanitize_key($mb->get_the_name());
                        $mb_settings = array('textarea_name'=>$mb->get_the_name(),'textarea_rows' => '10',);
                        wp_editor( $mb_content, $mb_editor_id, $mb_settings );
                        ?>
                   </div>
               </div>
            </div>
        </div>
        <div class="cell footer">
            <a href="#" class="dodelete button alignright">Remove <?php print strlen($mb->get_the_value('section-name'))==0?'Section '.$s:$mb->the_value('section-name')?></a>
        </div>
        </div>
    </div>
    <?php $s++; ?>
    <?php $mb->the_group_close(); ?>
    <?php endwhile; ?>
    </div>
    <p style="margin-bottom:15px; padding-top:5px;"><a href="#" class="docopy-sections button">Add Section</a>
</div>

<?php
function get_hidden($i){
    $ret = '';
    switch($i){
        case 2:
            $ret .= 'cols-2 ';
        case 3:
            $ret .= 'cols-3 ';
        case 4:
            $ret .= 'cols-4 ';
            break;
    }
    return $ret;
}
