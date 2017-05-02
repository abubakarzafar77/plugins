<div class="col-md-10 j-setup-popup">
    <form method="post" action="?page=<?php echo $view;?>&job_id=<?php echo $job_id;?>" id="setup-form">
        <?php $postid = url_to_postid( $scheduled_sessions[0]->webinar_url ); ?>
        <input type="hidden" name="post_id" value="<?php echo $postid;?>" />
        <h2>Webinar Setup</h2>
        <div class="clear" style="height: 10px;"></div>
        <div class="form-group">
            <label for="skype_name">Teacher Email</label>
            <input type="text" class="form-control validate[required,email]" id="teacher_email" name="teacher_email" placeholder="Teacher Email" data-prompt-position="topRight:-80" />
        </div>
        <div class="form-group">
            <label for="skype_name">Mobile</label>
            <input type="text" class="form-control validate[required]" id="mobile" name="mobile" placeholder="Mobile" data-prompt-position="topRight:-80" />
        </div>
        <div class="form-group">
            <label for="skype_name">Skype Name</label>
            <input type="text" class="form-control validate[required]" id="skype_name" name="skype_name" placeholder="Skype Name" data-prompt-position="topRight:-80" />
        </div>
        <div class="form-group">
            <label for="drawing_url">Drawing Document URL</label>
            <input type="text" class="form-control validate[required,custom[url]]" id="drawing_url" name="drawing_url" placeholder="Drawing Document URL" data-prompt-position="topRight:-80" />
        </div>
        <div class="form-group">
            <label for="text_url">Text Document URL</label>
            <input type="text" class="form-control validate[required,custom[url]]" id="text_url" name="text_url" placeholder="Text Document URL" data-prompt-position="topRight:-80" />
        </div>
        <div class="form-group">
            <label for="text_url">Join.me Meeting URL</label>
            <input type="text" class="form-control validate[required,custom[url]]" id="meeting_url" name="meeting_url" placeholder="Join.me Meeting URL" data-prompt-position="topRight:-80" />
        </div>
        <button type="submit" class="btn btn-success">Submit</button>
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#setup-form').validationEngine();
    });
</script>