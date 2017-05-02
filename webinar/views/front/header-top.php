<?php

?>
<link href='https://fonts.googleapis.com/css?family=Droid+Serif:400,700' rel='stylesheet' type='text/css'>
<div class="w-header">
    <div class="w-hidden">
        <span<?php echo($view == 'post_webinar'?' style="text-decoration: underline; font-weight: bold;"':' style="font-weight: bold;"');?>>Post webinar</span>
    </div>
    <div class="w-menu col-md-12">
        <span class="w-hidden"> <?php echo(($view == 'post_webinar' || $view == 'my_jobs' || $view == 'scheduled_sessions' || $view == 'finished_sessions' || $view == 'past_due')?' style="text-decoration: underline; font-weight: bold;"':' style="font-weight: bold;"');?>>My webinars</span>
        <ul class="w-clearfix">
            <li><a href="/studiekamerat?page=post_webinar"<?php echo($view == 'post_webinar'?' style="text-decoration: underline;"':'');?>>Bestillingsskjema</a></li>
            <li><a href="/studiekamerat?page=my_jobs"<?php echo($view == 'my_jobs'?' style="text-decoration: underline;"':'');?>>Dine bestillinger (<?php echo $model->count_user_posted_webinars();?>)</a></li>
            <li><a href="/studiekamerat?page=scheduled_sessions"<?php echo($view == 'scheduled_sessions'?' style="text-decoration: underline;"':'');?>><?php echo utf8_encode('Avtalte møter');?> (<?php echo $model->count_user_scheduled_webinars();?>)</a></li>
            <li><a href="/studiekamerat?page=finished_sessions"<?php echo($view == 'finished_sessions'?' style="text-decoration: underline;"':'');?>><?php echo utf8_encode('Avsluttede møter');?> (<?php echo $model->count_user_completed_webinars();?>)</a></li>
            <li><a href="/studiekamerat?page=past_due"<?php echo($view == 'past_due'?' style="text-decoration: underline;"':'');?>><?php echo utf8_encode('Utgåtte bestillinger');?> (<?php echo $model->count_user_pastdue_webinars();?>)</a></li>
        </ul>
    </div>
</div>
