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
                    <div class="col-md-12 aj-content-block">
                        <div class="aj-meta row">
                            <div class="col-md-6">Tidspunkt: <?php echo date('d.m.Y   H:i', strtotime($job->webinar_date_time));?></div>
                            <div class="col-md-6">Varighet: <?php echo str_replace("-", " ", $job->webinar_duration);?></div>
                        </div>

                        <div class="aj-student-name">Student Name: <?php echo get_user_meta($job->webinar_user_id, 'first_name', true).' '.get_user_meta($job->webinar_user_id, 'last_name', true)?></div>
                        <div class="aj-description">
                            <p><?php echo nl2br($job->webinar_description);?></p>
                        </div>
                        <div class="aj-attachments">
                            <?php $attachments = json_decode($job->webinar_files, true);?>
                            <strong>Vedlegg:</strong><br />
                            <?php if(count($attachments) > 0){?>
                                <?php foreach($attachments as $key=>$attachment){?>
                                    <div class="aj-attach"><a href="<?php echo $attachment;?>" target="_blank">Attachment <?php echo $key+1;?></a></div>
                                <?php }?>
                            <?php }else{?>
                                <div class="aj-attach"><p>Ingen vedlegg</p></div>
                            <?php }?>
                        </div>
                        <div class="aj-max-price">
                            <p>Max price: <?php echo ucfirst(str_replace("-", " ", $job->webinar_budget));?></p>
                        </div>
                    </div>
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
