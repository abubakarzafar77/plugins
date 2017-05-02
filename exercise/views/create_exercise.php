<div class="wrap">
    <?php $selected_category = ''; ?>
    <?php $selected_category_id = ''; ?>
    <?php
    $category = get_category($exercise['sub_chapter_id']);
    ?>
    <div id="exercisetuff">
        <form id="fe_createplan" method="post" class="create-plan-form" action="admin.php?page=create_exercise<?php echo (isset($exercise)?'&id='.$exercise['id']:'');?>">
            <input type="hidden" name="action" value="create" />
            <?php if(isset($exercise)){?>
                <?php wp_nonce_field( 'updateexercise_nounce', 'updateexercise_nounce' ) ?>
                <input type="hidden" name="exercise_id" id="exercise_id" value="<?php echo $exercise['id'];?>" />
            <?php }else{?>
                <?php wp_nonce_field( 'createexercise_nounce', 'createexercise_nounce' ) ?>
            <?php }?>
            <div id="post-body" class="metabox-holder columns-2">
                <div class="post-body-content">
                    <div class="recipe-line">
                        <?php if(isset($exercise)){?>
                            <h2 class="tr-primary-heading">Update Exercise</h2>
                        <?php }else{?>
                            <h2 class="tr-primary-heading">Create Exercise</h2>
                        <?php }?>
                        <?php if(isset($_REQUEST['m']) && $_REQUEST['m'] == 'success'){?>
                            <div class="notice notice-success is-dismissible">
                                <p><?php _e( 'Exercise updated!', 'success-messages' ); ?></p>
                            </div>
                        <?php }elseif(isset($_REQUEST['m']) && $_REQUEST['m'] == 'error'){?>
                            <div class="notice notice-error is-dismissible">
                                <p><?php _e( 'Unable to update exercise!', 'error-messages' ); ?></p>
                            </div>
                        <?php }?>
                        <div class="tr_form-box" style="padding-top: 10px;">
                            <label for="is_published">Publish admin:</label>
                            <input type="checkbox" class="publish" id="is_published_admin" name="is_published" value="1"<?php echo (isset($exercise) && $exercise['publish'] == '1'?' checked':'');?> />
                        </div>
                        <div class="tr_form-box" style="padding-top: 10px;">
                            <label for="is_published">Publish subscriber:</label>
                            <input type="checkbox" class="publish" id="is_published_subscriber" name="is_published" value="2"<?php echo (isset($exercise) && $exercise['publish'] == '2'?' checked':'');?> />
                        </div>
                        <div class="tr_form-box">
                            <label for="name">Course:</label>
                            <select name="course[]" id="course" class="js-category-multiple" multiple="multiple">
                                <?php
                                $categories = array();
                                if(isset($exercise)){
                                    $categories = explode(',', $exercise['course_id']);
                                }
                                ?>
                                <?php foreach ($filtered_categories as $cate) {
                                    if(in_array($cate->cat_ID, $categories)) {
                                        $selected_category = $cate->slug;
                                        $selected_category_id = $cate->term_id;
                                    }
                                    ?>
                                    <option value="<?php echo $cate->cat_ID ?>"<?php echo (in_array($cate->cat_ID, $categories)?' selected':'');?>><?php echo $cate->name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="tr_form-box">
                            <label for="name">Year:</label>
                            <select name="year" id="year">
                                <?php
                                $i = 2000;
                                $max = date('Y')+5;
                                while ($i <= $max) {?>
                                    <option value="<?php echo $i ?>"<?php echo (isset($exercise) && $exercise['year'] == $i?' selected':'');?>><?php echo $i ?></option>
                                    <?php $i++;
                                } ?>
                            </select>
                        </div>
                        <div class="tr_form-box">
                            <label for="name">Term:</label>
                            <select name="term" id="term">
                                <option value="spring"<?php echo (isset($exercise) && $exercise['term'] == 'spring'?' selected':'');?>>spring</option>
                                <option value="autumn"<?php echo (isset($exercise) && $exercise['term'] == 'autumn'?' selected':'');?>>autumn</option>
                            </select>
                        </div>
                        <div class="tr_form-box">
                            <label for="name">Exercise Name:</label>
                            <input type="text" value="<?php echo (isset($exercise)?$exercise['exercise_name']:'');?>" name="exercise_name" id="exercise_name" />
                        </div>
                        <div class="tr_form-box">
                            <label for="name">Sub-chapter:</label>
                            <select id="sub_chapter" name="sub_chapter">
                                <option value="">Select Chapter</option>
                            </select>
                            <input type="hidden" id="sub_chapter_hidden" value="<?php echo (isset($exercise)?$exercise['sub_chapter_id']:'');?>" />
                        </div>
                        <div class="tr_form-box">
                            <label for="name">relevant video:</label>
                            <input type="text" value="<?php echo (isset($exercise)?$exercise['relevant_video']:'');?>" name="relevant_video" id="relevant_video" />
                        </div>
                        <div class="tr_form-box">
                            <label for="name">Duration:</label>
                            <label for="duration_1min" class="label_checkbox"><input type="checkbox" class="duration" value="1-min" id="duration_1min" name="duration"<?php echo (isset($exercise) && $exercise['duration'] == '1-min'?' checked':'');?> /> 1 min</label>
                            <label for="duration_2min" class="label_checkbox"><input type="checkbox" class="duration" value="2-min" id="duration_2min" name="duration"<?php echo (isset($exercise) && $exercise['duration'] == '2-min'?' checked':'');?> /> 2 min</label>
                            <label for="duration_3min" class="label_checkbox"><input type="checkbox" class="duration" value="3-min" id="duration_3min" name="duration"<?php echo (isset($exercise) && $exercise['duration'] == '3-min'?' checked':'');?> /> 3 min</label>
                            <label for="duration_3-5min" class="label_checkbox"><input type="checkbox" class="duration" value="3|5-min" id="duration_3-5min" name="duration"<?php echo (isset($exercise) && $exercise['duration'] == '3|5-min'?' checked':'');?> /> 3-5 min</label>
                            <label for="duration_5-8min" class="label_checkbox"><input type="checkbox" class="duration" value="5|8-min" id="duration_5-8min" name="duration"<?php echo (isset($exercise) && $exercise['duration'] == '5|8-min'?' checked':'');?> /> 5-8 min</label>
                            <label for="duration_8-12min" class="label_checkbox"><input type="checkbox" class="duration" value="8|12-min" id="duration_8-12min" name="duration"<?php echo (isset($exercise) && $exercise['duration'] == '8|12-min'?' checked':'');?> /> 8-12 min</label>
                            <label for="duration_15min" class="label_checkbox"><input type="checkbox" class="duration" value="15-min" id="duration_15min" name="duration"<?php echo (isset($exercise) && $exercise['duration'] == '15-min'?' checked':'');?> /> 15 min</label>
                        </div>
                        <div class="tr_form-box">
                            <label for="name">Tool:</label>
                            <label for="tool_yes" class="label_checkbox"><input type="checkbox" class="tool" value="yes" id="tool_yes" name="tool"<?php echo (isset($exercise) && $exercise['tools'] == 'yes'?' checked':'');?> /> yes</label>
                            <label for="tool_no" class="label_checkbox"><input type="checkbox" class="tool" value="no" id="tool_no" name="tool"<?php echo (isset($exercise) && $exercise['tools'] == 'no'?' checked':'');?> /> no</label>
                        </div>
                        <div class="tr_form-box">
                            <label for="name">Exercise Type:</label>
                            <label for="ex_type_calculation" class="label_checkbox"><input type="checkbox" class="ex_type" value="calculation" id="ex_type_calculation" name="ex_type"<?php echo (isset($exercise) && $exercise['exercise_type'] == 'calculation'?' checked':'');?> /> calculation</label>
                            <label for="ex_type_text" class="label_checkbox"><input type="checkbox" class="ex_type" value="text" id="ex_type_text" name="ex_type"<?php echo (isset($exercise) && $exercise['exercise_type'] == 'text'?' checked':'');?> /> text</label>
                        </div>
                        <div class="tr_form-box">
                            <label for="solution_setup">Solutionsetup:</label>
                            <label for="solution_setup_multiple" class="label_checkbox"><input type="radio" class="solution_setup" value="multiple" id="solution_setup_multiple" name="solution_setup"<?php echo (isset($exercise) && $exercise['solution_setup'] == 'multiple'?' checked':'checked');?> /> multiple choice</label>
                            <label for="solution_setup_single" class="label_checkbox"><input type="radio" class="solution_setup" value="single" id="solution_setup_single" name="solution_setup"<?php echo (isset($exercise) && $exercise['solution_setup'] == 'single'?' checked':'');?> /> single solution</label>
                        </div>
                        <div class="tr_form-box">
                            <label for="name">Corr alternative:</label>
                            <label for="corr_alternative_alt1" class="label_checkbox"><input type="checkbox" value="alt1" class="corr_alternative" id="corr_alternative_alt1" name="corr_alternative"<?php echo (isset($exercise) && $exercise['corr_alternative'] == 'alt1'?' checked':'');?> /> alt 1</label>
                            <label for="corr_alternative_alt2" class="label_checkbox alt_ans"<?php echo (isset($exercise) && $exercise['solution_setup'] == 'single'?' style="display: none;"':'');?>><input type="checkbox" value="alt2" class="corr_alternative" id="corr_alternative_alt2" name="corr_alternative"<?php echo (isset($exercise) && $exercise['corr_alternative'] == 'alt2'?' checked':'');?> /> alt 2</label>
                            <label for="corr_alternative_alt3" class="label_checkbox alt_ans"<?php echo (isset($exercise) && $exercise['solution_setup'] == 'single'?' style="display: none;"':'');?>><input type="checkbox" value="alt3" class="corr_alternative" id="corr_alternative_alt3" name="corr_alternative"<?php echo (isset($exercise) && $exercise['corr_alternative'] == 'alt3'?' checked':'');?> /> alt 3</label>
                        </div>
                        <div class="tr_form-box">
                            <label for="name" style="max-width: 200px;margin-bottom: 10px;">Text & Content(HTML):</label>
                            <?php

                            $content = (isset($exercise)?$exercise['context']:'');
                            $editor_id = 'text_context_html';

                            wp_editor( stripslashes($content), $editor_id );

                            ?>
                        </div>
                        <div class="tr_form-box">
                            <label for="name" style="max-width: 200px;margin-bottom: 10px;">Alt_1 & Alt_1_exp:</label>
                            <?php
                            $alt_1_exp = '';
                            if(isset($exercise)){
                                $alt_1_exp = $exercise['alt_1']."\n----<alt_epx>----\n".$exercise['alt_1_exp'];
                            }
                            $editor_id = 'alt_1_exp';

                            wp_editor( stripslashes($alt_1_exp), $editor_id );

                            ?>
                        </div>
                        <div class="tr_form-box alt_ans"<?php echo (isset($exercise) && $exercise['solution_setup'] == 'single'?' style="display: none;"':'');?>>
                            <label for="name" style="max-width: 200px;margin-bottom: 10px;">Alt_2 & Alt_2_exp:</label>
                            <?php
                            $alt_2_exp = '';
                            if(isset($exercise)){
                                $alt_2_exp = $exercise['alt_2']."\n----<alt_epx>----\n".$exercise['alt_2_exp'];
                            }
                            $editor_id = 'alt_2_exp';

                            wp_editor( stripslashes($alt_2_exp), $editor_id );

                            ?>
                        </div>
                        <div class="tr_form-box alt_ans"<?php echo (isset($exercise) && $exercise['solution_setup'] == 'single'?' style="display: none;"':'');?>>
                            <label for="name" style="max-width: 200px;margin-bottom: 10px;">Alt_3 & Alt_3_exp:</label>
                            <?php
                            $alt_3_exp = '';
                            if(isset($exercise)){
                                $alt_3_exp = $exercise['alt_3']."\n----<alt_epx>----\n".$exercise['alt_3_exp'];
                            }
                            $editor_id = 'alt_3_exp';

                            wp_editor( stripslashes($alt_3_exp), $editor_id );

                            ?>
                        </div>
                    </div>
                </div>
                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable" style=""><div id="submitdiv" class="postbox ">
                            <div title="Click to toggle"><br></div>
                            <?php if(isset($exercise)){?>
                                <h3 class="hndle ui-sortable-handle"><span>Update Exercise</span></h3>
                            <?php }else{?>
                                <h3 class="hndle ui-sortable-handle"><span>Save Exercise</span></h3>
                            <?php }?>
                            <div class="inside">
                                <div class="submitbox" id="submitpost">
                                    <div id="minor-publishing">
                                        <div id="minor-publishing-actions">
                                            <div id="preview-action">
                                                <span class="spinner"></span>
                                                <input name="original_publish" type="hidden" id="original_publish" value="Save">
                                                <?php if(isset($exercise)){?>
                                                    <input name="save" type="submit" class="button button-primary button-large" id="publish" value="Update">&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a class="button" id="exercise-duplicate" href="admin.php?page=mattevideo_exercise&id=<?php echo $exercise['id'];?>&dup=true&redirect=update">Duplicate</a>&nbsp;&nbsp;
                                                    <a class="preview button" id="exercise-preview" target="_blank" href="<?php echo home_url('kurs/'.$selected_category.'?c='.$exercise['sub_chapter_id'].'&q='.$exercise['id'].'&y='.$exercise['year'].'&_p='.$category->category_parent)?>">Preview</a>
                                                <?php }else{?>
                                                    <input name="save" type="submit" class="button button-primary button-large" id="publish" value="Save">&nbsp;&nbsp;
                                                    <a class="preview button" id="exercise-preview" disabled="">Preview</a>
                                                <?php }?>
                                                <input type="hidden" name="wp-preview" id="wp-preview" value="">
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <br class="clear">
</div>
<script>
    jQuery(document).ready(function () {
        var editor = CodeMirror.fromTextArea(document.getElementById("text_context_html"), {
            lineNumbers: true,
            theme: 'material',
            mode: "text/html",
            scrollbarStyle: "null",
            extraKeys: {
                "F11": function(cm) {
                    cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                },
                "Esc": function(cm) {
                    if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                }
            }
        });
        var editor = CodeMirror.fromTextArea(alt_1_exp, {
            lineNumbers: true,
            theme: 'material',
            mode: "text/html",
            scrollbarStyle: "null",
            extraKeys: {
                "F11": function(cm) {
                    cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                },
                "Esc": function(cm) {
                    if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                }
            }
        });
        var editor = CodeMirror.fromTextArea(alt_2_exp, {
            lineNumbers: true,
            theme: 'material',
            mode: "text/html",
            scrollbarStyle: "null",
            extraKeys: {
                "F11": function(cm) {
                    cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                },
                "Esc": function(cm) {
                    if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                }
            }
        });
        var editor = CodeMirror.fromTextArea(alt_3_exp, {
            lineNumbers: true,
            theme: 'material',
            mode: "text/html",
            scrollbarStyle: "null",
            extraKeys: {
                "F11": function(cm) {
                    cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                },
                "Esc": function(cm) {
                    if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                }
            }
        });
    });
</script>
<style type="text/css">
    .wp-editor-tabs{display:none;}
</style>