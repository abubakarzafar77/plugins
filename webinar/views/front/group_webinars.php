<?php
$count = 0;
$style = ' style="display: none;"';
if(count($teacher_groups) > 0){
    ?>
    <?php if(count($teacher_groups) > 1){?>
        <div class="w-clearfix">
            <div class="j-pagging">
                <a href="javascript:void(0);" class="previous_job" data-id="<?php echo $count-1;?>"><</a>
                <span class="shown">1</span><span>/</span><span class="of"><?php echo count($teacher_groups);?></span>
                <a href="javascript:void(0);" class="next_job" data-id="<?php echo $count+1;?>"> ></a>
            </div>
        </div>
    <?php }?>
    <div class=" my_jobs">
        <?php foreach($teacher_groups as $group):?>
            <?php
            if(!isset($_REQUEST['id']) && $count == 0){
                $style = ' style="display: block;"';
            }else if(isset($_REQUEST['id']) && $_REQUEST['id'] == $group->id){
                $style = ' style="display: block;"';
            }else{
                $style = ' style="display: none;"';
            }
            $percent = 5;
            if($group->packages_deal == 10){
                $percent = 5;
            }else if($group->packages_deal == 20){
                $percent = 10;
            }else if($group->packages_deal == 40){
                $percent = 15;
            }
            ?>
            <div class="w-clearfix j-outer job_outer" id="job_<?php echo $count;?>"<?php echo $style;?>>
                <section class="job w-clearfix">
                    <div class="j-description w-clearfix">
                        <div class="aj-description"><?php echo $group->packages_deal;?> hours - <?php echo $teacher_budget->{$group->packages_deal};?> kr (<?php echo $percent;?>% discount)</div>
                        <p>&nbsp;</p>
                        <?php $offers = $model->get_teacher_group_time($group->id);?>
                        <?php foreach($offers as $offer){?>
                            <div class="aj-description"><?php echo date('l d.m.Y, H:i', strtotime($offer['webinar_date_time']));?> - <?php echo str_replace('-', ' ', $offer['webinar_duration']);?></div>
                        <?php }?>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <div class="aj-description">Send to: <?php echo $group->send_to;?></div>
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