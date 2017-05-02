<?php if(isset($error)){?>
    <div class="col-md-12">
        <div class="alert alert-danger" role="alert"><?php echo $error;?></div>
    </div>
<?php }elseif(isset($success)){?>
    <div class="col-md-12">
        <div class="alert alert-success" role="alert"><?php echo $success;?></div>
    </div>
<?php }?>
<?php
$count = 0;
$style = ' style="display: none;"';
if(count($finished_sessions) > 0){
    ?>
    <?php if(count($finished_sessions) > 1){?>
        <div class="w-clearfix">
            <div class="j-pagging">
                <a href="javascript:void(0);" class="previous_job" data-id="<?php echo $count-1;?>"><</a>
                <span class="shown">1</span><span>/</span><span class="of"><?php echo count($finished_sessions);?></span>
                <a href="javascript:void(0);" class="next_job" data-id="<?php echo $count+1;?>"> ></a>
            </div>
        </div>
    <?php }?>
    <div class=" my_jobs">
        <?php foreach($finished_sessions as $job):?>
            <?php
            if(!isset($_REQUEST['job_id']) && $count == 0){
                $style = ' style="display: block;"';
            }else if(isset($_REQUEST['job_id']) && $_REQUEST['job_id'] == $job->ID){
                $style = ' style="display: block;"';
            }else{
                $style = ' style="display: none;"';
            }
            ?>
            <div class="w-clearfix j-outer job_outer" id="job_<?php echo $count;?>"<?php echo $style;?>>
                <section class="job w-clearfix">
                    <div class="j-top-meta w-clearfix">
                        <div class="j-time"><strong>Time:</strong> <?php echo date('d.m.Y   H:i', strtotime($job->webinar_date_time));?></div>
                        <div class="j-duration"><strong>Duration:</strong> <?php echo str_replace("-", " ", $job->webinar_duration);?></div>
                        <div class="j-max-price">
                            <p><strong>Max price:</strong> <?php echo ucfirst(str_replace("-", " ", $job->webinar_budget));?></p></div>
                        <div class="j-delete-btn"><a href="<?php echo wp_nonce_url(home_url('studiekamerat?page=finished_sessions&action=delete_job&job_id='.$job->ID), 'delete_job', 'delete_job');?>" onclick="return confirm('Are you sure you want to delete?');" class="btn j-btn">Delete job</a></div>
                    </div>
                    <div class="j-description">
                        <p><?php echo nl2br($job->webinar_description);?></p>
                    </div>
                    </div>
                    <div class="j-attachments">
                        <?php $attachments = json_decode($job->webinar_files, true);?>
                        <strong>Attachments:</strong><br />
                        <?php if(count($attachments) > 0){?>
                            <?php foreach($attachments as $key=>$attachment){?>
                                <div class="j-attach"><a href="<?php echo $attachment;?>" target="_blank">- Attachment <?php echo $key+1;?></a></div>
                            <?php }?>
                        <?php }else{?>
                            <div class="j-attach"><p>No attachments</p></div>
                        <?php }?>
                    </div>
                </section>
                <?php $offers = $model->get_offers_by_id($job->ID);?>
                <section class="offers">
                    <?php foreach($offers as $offer):?>
                        <?php if($offer->is_deleted){?>
                            <div class="col-md-12">
                                <div class="alert alert-danger" role="alert"><strong>Oh snap!</strong> One offer has been deleted by teacher.</div>
                            </div>
                        <?php }else{?>
                            <div class="j-full-profile w-clearfix">
                                <?php $teacher = $model->get_all_teachers('all', $offer->offer_teacher_id);$teacher = $teacher[0];?>
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
                                <div class="j-description">
                                    <p><?php echo $offer->offer_description;?></p>
                                </div>
                                <div class="j-price">
                                    <strong>
                                        Price <br />
                                        <?php echo $offer->offer_amount;?>
                                    </strong>
                                </div>
                                <div class="j-choose-btn">
                                </div>
                            </div>
                        <?php }?>
                    <?php endforeach;?>
                </section>
            </div>
            <?php $count++;?>
        <?php endforeach;?>
    </div>
<?php }else{?>
    <div class="col-md-12">
        <div class="alert alert-warning" role="alert"><strong>Warning!</strong> No record found.</div>
    </div>
<?php }?>
