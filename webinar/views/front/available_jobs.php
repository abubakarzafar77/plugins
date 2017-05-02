<?php
global $webinar_config;
?>
<?php if(isset($error)){?>
    <div class="aj-alert">
        <div class="alert alert-danger" role="alert"><?php echo $error;?></div>
    </div>
<?php }elseif(isset($success)){?>
    <div class="aj-alert">
        <div class="alert alert-success" role="alert"><?php echo $success;?></div>
    </div>
<?php }?>
<?php if(isset($_REQUEST['add']) && $_REQUEST['add'] == 'no') { ?>
    <div class="aj-alert">
        <div class="alert alert-danger" role="alert"><strong>Oh snap!</strong> Unable to create job.</div>
    </div>
<?php } elseif(isset($_REQUEST['add']) && $_REQUEST['add'] == 'yes') { ?>
    <div class="aj-alert">
        <div class="alert alert-success" role="alert"><strong>Well done!</strong> Job created successfully.</div>
    </div>
<?php } ?>

    

<?php

///echo count($available_jobs);exit;

$count = 0;
$style = ' style="display: none;"';
if(count($available_jobs) > 0){
    ?>
    <?php /* if(count($available_jobs) > 1) { ?>
        <div class="w-clearfix">
            <div class="j-pagging">
                <a href="javascript:void(0);" class="_previous_job" data-id="<?php echo $count-1;?>">< </a> 
                <?php if(is_array($available_jobs) && count($available_jobs) > 0) { $cnt = 1; ?>
                    <?php foreach($available_jobs AS $job) { ?>    
                        <span <?php echo ($cnt==1? 'class="activePage"' : '') ?>><?php echo $cnt; ?> </span>
                    <?php $cnt++;} ?>
                <?php } ?>
                <a href="javascript:void(0);" class="_next_job" data-id="<?php echo $count+1;?>"> ></a>
                
                <!-- <span class="shown">1</span><span>/</span><span class="of"><?php // echo count($available_jobs);?></span>
                <a href="javascript:void(0);" class="next_job" data-id="<?php echo $count+1;?>"> ></a> -->
            </div>
        </div>
    <?php } */?>
    <?php if(count($available_jobs) > 1) { ?>

        <style> 

        #pagination { text-align: center; }
        
        #pagination a, #pagination i {
            display: inline-block;
            vertical-align: middle;
            width: 22px;
            color: #7D7D7D;
            text-align: center;
            font-size: 10px;
            padding: 3px 0 2px 0;
        }

        #pagination a {
            margin: 0 2px 0 2px;
            cursor: pointer;
            font-size: 14px;
        }

        #pagination i {
            margin: 0 3px 0 3px;
        }

        #pagination a.current {
            font-weight: bold;
            font-size: 14px;
        }

        </style>

        <script type="text/javascript">

        var init = function() {
            Pagination.Init(document.getElementById('pagination'), {
                size: <?php echo count($available_jobs) ?>, // pages size
                page: 1,  // selected page
                step: 3   // pages before and after current
            });
        };

        document.addEventListener('DOMContentLoaded', init, false);


        </script>

        <div id="pagination"></div>

    <?php } ?>
    

    <div class="my_jobs">
        <?php foreach($available_jobs as $job):?>
            <?php
            if(!isset($_REQUEST['job_id']) && $count == 0) {
                $style = ' style="display: block;"';
            } else if(isset($_REQUEST['job_id']) && $_REQUEST['job_id'] == $job->ID) {
                $style = ' style="display: block;"';
            } else {
                $style = ' style="display: none;"';
            }
            ?>
            <div class="w-clearfix j-outer job_outer" id="job_<?php echo $count+1;?>"<?php echo $style;?>>
                <section class="row job w-clearfix">
                    <div class="col-md-12">
                        <div class="col-md-6 aj-content-block">
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
                        <div class="col-md-6 aj-form-block">
                            <?php $offer = $model->get_offer_details_by_teacher_id($job->ID);?>
                            <?php if(!$offer){?>
                                <form class=" offer-form" method="post" action="?page=<?php echo $webinar_config->plugin_available_jobs_page;?>">
                                    <input type="hidden" name="action" value="add_offer">
                                    <input type="hidden" name="job_id" value="<?php echo $job->ID;?>">
                                    <?php wp_nonce_field( 'add_offer', 'add_offer' ); ?>
                                    <h2>Din tilbudstekst</h2>
                                    <div class="form-group aj-textarea">
                                        <textarea name="offer_description" id="offer_description" class="validate[required] form-control" rows="3" data-prompt-position="topRight:-70"></textarea>
                                    </div>
                                    <div class="form-group aj-text">
                                        <input type="text" name="offer_amount" id="offer_amount" class="validate[required] form-control" value="<?php echo ucfirst(str_replace("-", " ", $job->webinar_budget));?>" placeholder="Price offer" readonly />
                                    </div>
                                    <div class="aj-form-bottom">
                                        <div class="form-group aj-sent-btn">
                                            <input type="submit" name="send_offer" value="Send tilbud" id="send_offer" class="btn btn-warning" />
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </form>
                            <?php }else{?>
                                <form class=" offer-form" method="post" action="?page=<?php echo $webinar_config->plugin_available_jobs_page;?>">
                                    <input type="hidden" name="action" value="update_offer">
                                    <input type="hidden" name="offer_id" value="<?php echo $offer->ID;?>">
                                    <input type="hidden" name="job_id" value="<?php echo $job->ID;?>">
                                    <?php wp_nonce_field( 'update_offer', 'update_offer' ); ?>
                                    <h2>Your Offer</h2>
                                    <div class="form-group aj-textarea">
                                        <textarea name="offer_description" id="offer_description" class="validate[required] form-control" rows="3"><?php echo str_replace(array("<br />", "<br/>", "<br>"), "\n", $offer->offer_description);?></textarea>
                                    </div>
                                    <div class="form-group aj-text">
                                        <input type="text" name="offer_amount" id="offer_amount" class="validate[required] form-control" placeholder="Price offer" value="<?php echo $offer->offer_amount;?>" readonly />
                                    </div>
                                    <div class="aj-form-bottom">
                                        <div class="form-group aj-edit-btn">
                                            <input type="submit" name="edit_offer" value="Rediger" id="edit_offer" class="btn btn-success" />
                                        </div>
                                        <div class="form-group aj-delete-btn">
                                            <?php $delete_offer_url = wp_nonce_url('?page='.$webinar_config->plugin_available_jobs_page.'&offer_id='.$offer->ID.'&action=delete_offer', 'delete_offer', 'delete_offer');?>
                                            <a href="<?php echo $delete_offer_url;?>" class="btn btn-success">Slett tilbud</a>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </form>
                            <?php }?>
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
