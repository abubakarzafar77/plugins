<div class="w-header">
    <div class="w-menu w-clearfix">
        <span class="w-hidden"<?php echo(($view == 'available_jobs' || $view == 't_scheduled_sessions' || $view == 't_finished_sessions')?' style="text-decoration: underline; font-weight: bold;"':' style="font-weight: bold;"');?>>My webinars</span>
        <ul class="w-clearfix left">
            <li><a href="/studiekamerat?page=available_jobs"<?php echo($view == 'available_jobs'?' style="text-decoration: underline;"':'');?>>Ledige jobber (<?php echo $model->count_available_jobs();?>)</a></li>
            <li><a href="/studiekamerat?page=t_scheduled_sessions"<?php echo($view == 't_scheduled_sessions'?' style="text-decoration: underline;"':'');?>>Avtalte jobber (<?php echo $model->count_teacher_scheduled_webinars();?>)</a></li>
            <li><a href="/studiekamerat?page=t_finished_sessions"<?php echo($view == 't_finished_sessions'?' style="text-decoration: underline;"':'');?>>Ferdige jobber (<?php echo $model->count_teacher_completed_webinars();?>)</a></li>
            <li><a href="/studiekamerat?page=group_webinars"<?php echo($view == 'group_webinars'?' style="text-decoration: underline;"':'');?>>pakketilbud (<?php echo $model->count_teacher_group_webinars();?>)</a></li>
        </ul>
        <h2 class="w-webinar-offer"><a href="/studiekamerat?page=group_webinar"<?php echo($view == 'group_webinar'?' style="text-decoration: underline;"':'');?>>Lag pakketilbud</a></h2>
    </div>
</div>
