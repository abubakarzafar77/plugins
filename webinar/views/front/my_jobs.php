<?php if(isset($error)){?>
    <div class="col-md-12">
        <div class="alert alert-danger" role="alert"><?php echo $error;?></div>
    </div>
<?php }elseif(isset($success)){?>
        <div class="col-md-12">
            <div class="alert alert-success" role="alert"><?php echo $success;?></div>
        </div>
<?php }?>
<?php if(isset($_REQUEST['add']) && $_REQUEST['add'] == 'no'){?>
    <div class="col-md-12">
        <div class="alert alert-danger" role="alert"><strong>Oh snap!</strong> Unable to create job.</div>
    </div>
<?php }elseif(isset($_REQUEST['add']) && $_REQUEST['add'] == 'yes'){?>
    <div class="col-md-12">
        <div class="alert alert-success" role="alert"><strong>Well done!</strong> Job created successfully.</div>
    </div>
<?php }?>
<?php
    $count = 0;
    $style = ' style="display: none;"';
if(count($my_jobs) > 0){
?>
    <?php if(count($my_jobs) > 1){?>
        <div class="w-clearfix">
            <div class="j-pagging">
                <a href="javascript:void(0);" class="previous_job" data-id="<?php echo $count-1;?>"><</a>
                <span class="shown">1</span><span>/</span><span class="of"><?php echo count($my_jobs);?></span>
                <a href="javascript:void(0);" class="next_job" data-id="<?php echo $count+1;?>"> ></a>
            </div>
        </div>
    <?php }?>
<div class="clearfix"></div>

    <div class="my_jobs">
        <?php foreach($my_jobs as $job):?>
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
                        <div class="j-time">Tidspunkt: <?php echo date('d.m.Y   H:i', strtotime($job->webinar_date_time));?></div>
                        <div class="j-duration"><strong>Varighet:</strong> <?php echo str_replace("-", " ", $job->webinar_duration);?></div>
                        <div class="j-max-price">
                            <p><strong>Pris:</strong> <?php echo ucfirst(str_replace("-", " ", $job->webinar_budget));?></p></div>
                        <div class="j-delete-btn"><a href="<?php echo wp_nonce_url(home_url('studiekamerat?page=my_jobs&action=delete_job&job_id='.$job->ID), 'delete_job', 'delete_job');?>" onclick="return confirm('Are you sure you want to delete?');" class="btn j-btn">Slett bestilling</a></div>
                    </div>
                    <div class="j-description">
                        <p><?php echo nl2br($job->webinar_description);?></p>
                    <div class="j-attachments">
                        <?php $attachments = json_decode($job->webinar_files, true);?>
                        <strong>Vedlegg:</strong><br />
                        <?php if(count($attachments) > 0){?>
                            <?php foreach($attachments as $key=>$attachment){?>
                                <div class="j-attach"><a href="<?php echo $attachment;?>" target="_blank">- Attachment <?php echo $key+1;?></a></div>
                            <?php }?>
                        <?php }else{?>
                            <div class="j-attach"><p>Ingen vedlegg</p></div>
                        <?php }?>
                    </div>
                </section>
                <?php $offers = $model->get_offers_by_id($job->ID);?>
                <section class="offers">
                    <?php $paid = 0;?>
                    <?php foreach($offers as $offer):?>
                        <?php if($offer->is_deleted){?>
                            <div class="col-md-12">
								<div class="row">
                                <div class="alert alert-danger" role="alert"><strong> Ops! </strong>Dette tilbudet er slettet.</div>
								</div>
							</div>
                        <?php }else{?>
                            <div class="j-full-profile w-clearfix">
                                <div class="j-profile j-profile-updated">
                                    <img src="<?php echo $model->get_teacher_avatar($offer->offer_teacher_id);?>"  />
                                    <div class="j-profile-commets">
                                        <span><i aria-hidden="true" class="fa fa-comment"></i>14</span>
                                        <span><i aria-hidden="true" class="fa fa-heart"></i>205</span>
                                    </div>
                                    <div class="w-hidden rateit" data-rateit-value="2.5" data-rateit-ispreset="true" data-rateit-readonly="true"></div>

                                   <!-- <div>Fra <?php //echo get_user_meta($offer->offer_teacher_id, 'webinar_rate', true);?> halvtimen</div>-->

                                    <div class="blue-clr">
										<a href="<?php echo home_url('studiekamerat?page=teacher&id=' . $offer->offer_teacher_id);?>" target="_blank">
											<?php echo get_user_meta($offer->offer_teacher_id, 'first_name', true);?> <?php echo get_user_meta($offer->offer_teacher_id, 'last_name', true);?>
										</a>
									</div>

                                    <div class="j-see-full-profile">
										Se min video<br />
										<!--<a href="<?php // echo home_url('studiekamerat?page=teacher&id=' . $offer->offer_teacher_id);?>" target="_blank">Se full profil</a>-->
										</div>
                                </div>
                                <div class="j-description">
                                    <p><?php echo $offer->offer_description;?></p>
                                </div>
                                <div class="j-price">
                                    <strong>
                                        
                                        <?php echo $offer->offer_amount;?>
                                    </strong>
                                </div>
                                <div class="j-choose-btn">
                                    <?php if(!$offer->is_accepted && !$paid){?>
                                        <a href="<?php echo home_url('studiekamerat?page=pay&offer='.$offer->ID.'&job='.$job->ID);?>" id="<?php echo 'btn_'.$offer->ID.'_'.$job->ID;?>" class="btn j-btn fancybox.ajax fancybox">Velg og forhåndsbetal</a>
                                    <?php }elseif($offer->is_accepted){$paid=1;?>
                                        <strong>PAID</strong>
                                    <?php }?>
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





