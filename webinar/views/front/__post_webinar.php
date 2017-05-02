<?php
global $ts;
?>
<div class="w-heading">
    <!--<h1><?php echo utf8_encode('F� hjelp av en studiekamerat i dag!')?></h1>-->
</div>
<div class="col-md-12 w-video-section" xmlns="http://www.w3.org/1999/html">
    <form method="post" id="post-webinar-form" enctype="multipart/form-data">
        <input type="hidden" name="action" value="addwebinar" />
        <?php wp_nonce_field( 'add_webinar', 'add_webinar' ) ?>
        <div class="col-md-6 w-video">
            <div class="featured-video" id="teaser_movie_box">
                <iframe src="https://player.vimeo.com/video/129644664?api=1" width="442px" height="248.625px" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
            </div>
        </div>
        <div class="col-md-6 w-video-detail">
            <div class="w-date-block new-date-block">
                <div class="w-row w-clearfix">
                    <div class="w-datepicker">
                        <input type="text" name="date_time" id="date_time" value="" placeholder="Velg tidspunkt" readonly class="validate[required] form-control" data-errormessage-value-missing="Vennligst fyll ut" />
                        <span class="caret"></span>
                    </div>
                    <div class="w-duration">
                        <select name="duration" id="duration" class="validate[required] form-control" data-errormessage-value-missing="Vennligst fyll ut">
                            <option value="">Varighet</option>
                            <?php foreach($webinar_durations as $key=>$duration):
                                    $_key = explode("|", $key);
                                ?>
                                <option value="<?php echo $_key[0];?>" data-budget="<?php echo $_key[1];?>"><?php echo $duration;?></option>
                            <?php endforeach;?>
                        </select>
                        <span class="caret"></span>
                        <input type="hidden" name="budget" id="budget" />
                    </div>
                </div>
				  <div class="w-checkboxes-holder">
                <span>Kurs</span>
                <?php
                foreach($education_levels as $category):
                ?>
                    <div class="w-checkbox-group">
                        <input type="checkbox" id="category_<?php echo $category['ID'];?>" class="check_class validate[required]" data-errormessage-value-missing="Vennligst fyll ut" name="webinar_education_level" value="<?php echo $category['ID'];?>" data-name="<?php echo $category['name'];?>" />
                        <label for="category_<?php echo $category['ID'];?>">
                            <?php echo $category['name'];?>
                        </label>
                    </div>
                    
                <?php
                endforeach;
                ?>
            </div>
                <div class="w-row">
                    <div class="w-textarea">
                        <textarea name="description" rows="3" id="description" placeholder="<?php echo utf8_encode('Skriv litt her om hva du �nsker � snakke om....');?>" class="validate[required] form-control" data-errormessage-value-missing="Vennligst fyll ut"></textarea>
                    </div>
                </div>
                <div class="w-row  w-clearfix  mbn">
                    <div class="w-file-upload">
                        <div class="w-upload-btn">
                            <input type="file" name="webinar_files[]" id="webinar_files" multiple />
                            <span>Velg vedlegg</span>
                        </div>
                        <p class="last-pop">Last opp</p>
						<div class="w-bottom-lists w-clearfix new-bottom-list">
							<div class="w-teacher">
								<select name="teacher" id="teacher" class="validate[required] form-control" data-errormessage-value-missing="Vennligst fyll ut">
									<option value="">Studiekamerat</option>
								</select>
								<span class="caret"></span>
							</div>
						</div>
                    </div>
                    <div class="w-row" id="file_upload_section">
                        
                    </div>
                </div>
            </div>
          
            <div class="w-bottom-lists w-clearfix new-bottom-list" style="display:none;">
               
			  
              <!--  <div class="w-teacher">
                    <select name="teacher" id="teacher" class="validate[required] form-control" data-errormessage-value-missing="Vennligst fyll ut">
                        <option value="">Studiekamerat</option>
                    </select>
                    <span class="caret"></span>
                </div>-->
                <?php /*<div class="w-budget">
                    <select name="budget" id="budget" class="validate[required] form-control" data-errormessage-value-missing="Budget is required!" data-prompt-position="topRight:-40">
                        <option value="">Budget</option>
                        <?php foreach($webinar_budget as $key=>$budget):?>
                            <option value="<?php echo $key;?>"><?php echo $budget;?></option>
                        <?php endforeach;?>
                    </select>
                    <span class="caret"></span>
                </div>*/?>
            </div>
        </div>
        <div class="w-bottom-btn">
            <input type="submit" class="btn btn-success" name="send_offer" id="send_offer" value="Send bestilling">
        </div>
    </form>
</div>
<div class="col-md-12 w-education-levels">
    <div class="category_row">
        <h2><?php echo utf8_encode('V�re studiekamerater');?></h2>
    <?php /*foreach($education_levels as $level):
        $teachers = null;
        ?>
        <div class="category_row category_<?php echo $level['ID'];?>">
            <h2>
                <?php echo $level['name'];?>

                <?php //echo utf8_encode('V�re studiekamerater');?>
            </h2>*/?>
            <?php $teachers = $model->get_all_teachers('all'); ?>
                <div class="w-education-row w-clearfix">
                    <?php foreach($teachers as $teacher):?>
                        <div class="col-md-3 w-profile-box" data-teacher-id="<?php echo $teacher['ID'];?>">
                            <div class="teacher-image">
                                <img src="<?php echo (get_user_meta($teacher['ID'], 'webinar_photo', true)?get_user_meta($teacher['ID'], 'webinar_photo', true):plugins_url('webinar/images/no_image.png', 'webinar'));?>" style="" />
                            </div>
                            <div class="w-profile-caption">
                                <div class="w-profile-commets">
                                    <span><i class="fa fa-comment" aria-hidden="true"></i><?php echo $model->get_completed_webinars_count($teacher['ID']);?></span>
                                    <span><i class="fa fa-heart" aria-hidden="true"></i><?php echo round($model->get_average_rating($teacher['ID']));?></span>
                                </div>
                                <p> <?php //echo get_user_meta($teacher['ID'], 'webinar_rate', true);?> 129 kr pr 1/2 time</p>
                                <p class="w-name">
                                    <a href="<?php echo home_url('studiekamerat?page=teacher&id='.$teacher['ID']);?>" target="_blank">
                                <?php echo (get_user_meta($teacher['ID'], 'first_name', true)?get_user_meta($teacher['ID'], 'first_name', true):$teacher['display_name']);?> <?php echo get_user_meta($teacher['ID'], 'last_name', true);?> <?php // echo get_user_meta($teacher['ID'], 'webinar_age', true)?>
                                    </a>
                                </p>
                                <!--<p><a href="<?php //echo home_url('studiekamerat?page=teacher&id='.$teacher['ID']);?>" target="_blank">About me video</a></p>-->
                                <p>Se min video</p>
                                <p class="w-hidden"><a href="<?php echo home_url('studiekamerat?page=teacher&id='.$teacher['ID']);?>" target="_blank">See full profile</a></p>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
    </div>
                <?php /*<div class="clear"></div>
        </div>
    <?php endforeach;*/?>
    <footer class="w-footer-section">
        <h1><a href="javescript:;">Would you like to work as a teacher?</a></h1>
    </footer>
</div>