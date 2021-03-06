<?php $this->previewButton($post, 'live'); ?>
<div class="webinar_clear_fix"></div>
<div id="livep-accordian" class="ws-accordian">
    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('General', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">
        <div class="form-field">
            <label for="livep_title_show_yn"><?php _e('Hide Title', WebinarSysteem::$lang_slug); ?></label>
            <?php $livep_title_show_yn_value = get_post_meta($post->ID, '_wswebinar_livep_title_show_yn', true); ?>
            <input type="checkbox" data-switch="true" name="livep_title_show_yn" id="livep_title_show_yn" value="yes" <?php echo ($livep_title_show_yn_value == "yes" ) ? 'checked' : ''; ?> data-on-text="Yes" data-off-text="No">
            <div class="webinar_clear_fix"></div>
        </div>
        
        <div class="form-field">
            <label for="livep_title_clr"><?php _e('Title color', WebinarSysteem::$lang_slug); ?></label>
            <input type="text" name="livep_title_clr" class="color-field" id="livep_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_title_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_bckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
            <input type="text" name="livep_bckg_clr" class="color-field" id="livep_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_bckg_img"><?php _e('Background image', WebinarSysteem::$lang_slug); ?></label>         
            <input type="text" name="livep_bckg_img" id="livep_bckg_img" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_bckg_img', true)); ?>">
            <button class="button wswebinar_uploader" resultId="livep_bckg_img" uploader_title="<?php _e('Thankyou Page Background Image', WebinarSysteem::$lang_slug); ?>"><?php _e('Upload Image', WebinarSysteem::$lang_slug); ?></button>
            <div class="webinar_clear_fix"></div>

        </div>

        <div class="wsseparator"></div>

        <div class="form-field">
            <label for="livep_vidurl"><?php _e('Webinar type', WebinarSysteem::$lang_slug); ?></label>
            <?php $livep_vidurl_type = get_post_meta($post->ID, '_wswebinar_livep_vidurl_type', true); ?>
            <select class="form-control lookoutImageButton" valueField="livep_vidurl" imageUploadButton="livep_vidurl_upload_button" name="livep_vidurl_type" id="livep_vidurl_type">
                <option value="youtube" <?php echo $livep_vidurl_type == "youtube" ? 'selected' : ''; ?>>Hangouts / Youtube</option>
                <option value="vimeo" <?php echo $livep_vidurl_type == "vimeo" ? 'selected' : ''; ?>>Vimeo</option>
                <option value="image" <?php echo $livep_vidurl_type == "image" ? 'selected' : ''; ?>><?php _e('Image', WebinarSysteem::$lang_slug) ?></option>                
            </select>
        </div>

        <div class="form-field">
            <label for="livep_vidurl"><?php _e('Video or Image URL', WebinarSysteem::$lang_slug); ?></label>
            <input type="text" name="livep_vidurl" id="livep_vidurl" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_vidurl', true)); ?>">

            <button class="button wswebinar_uploader" style="<?php echo (!empty($livep_vidurl_type) && $livep_vidurl_type == 'image') ? '' : 'display:none;' ?>" id="livep_vidurl_upload_button" resultId="livep_vidurl" checktype="yes" uploader_title="<?php _e('Registration Page Video Image', WebinarSysteem::$lang_slug); ?>"><?php _e('Upload Image', WebinarSysteem::$lang_slug); ?></button>

            <p class="description livep_vidurl_desc livep_vidurl_for_youtube" style="<?php echo (empty($livep_vidurl_type) || $livep_vidurl_type == 'youtube') ? '' : 'display:none'; ?>"><?php _e('Paste Youtube/Hangouts URL here (Eg: https://www.youtube.com/watch?v=3TkeXaMpLe9-XM OR http://youtu.be/CqlB2SiG-ac)', WebinarSysteem::$lang_slug); ?></p>
            <p class="description livep_vidurl_desc livep_vidurl_for_vimeo" style="<?php echo (empty($livep_vidurl_type) || $livep_vidurl_type == 'vimeo') ? '' : 'display:none'; ?>"><?php _e('Paste Vimeo video ID here (Eg: 129673042)', WebinarSysteem::$lang_slug); ?></p>
            <p class="description livep_vidurl_desc livep_vidurl_for_image" style="<?php echo $livep_vidurl_type == 'image' ? '' : 'display:none'; ?>"><?php _e('Image URL (Eg: https://example.com/images/the_image.jpg)', WebinarSysteem::$lang_slug); ?></p>
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field hangouts-onair-btn">
            <label for="hangouts_onair_btn"><?php _e("Start a Hangout", WebinarSysteem::$lang_slug); ?></label>
            <script src="//apis.google.com/js/platform.js"></script>
            <div id="placeholder-div1"></div>
            <script>
                gapi.hangout.render('placeholder-div1', {
                    'render': 'createhangout',
                    'hangout_type': 'onair',
                    'initial_apps': [{'app_id': ''}],
                    'widget_size': 175
                });
            </script>
        </div>

        <div class="form-group">
            <label for="livep_video_auto_play_yn"><?php _e('Video autoplay', WebinarSysteem::$lang_slug); ?></label>
            <?php $livep_video_auto_play_yn_value = get_post_meta($post->ID, '_wswebinar_livep_video_auto_play_yn', true); ?>
            <input type="checkbox" data-switch="true" name="livep_video_auto_play_yn" id="livep_video_auto_play_yn" value="yes" <?php echo ($livep_video_auto_play_yn_value == "yes" ) ? 'checked' : ''; ?> >
            <p class="description"><?php printf(__("%s only.", WebinarSysteem::$lang_slug), "YouTube / Vimeo") ?></p>
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-group">
            <label for="livep_video_controls_yn"><?php _e('Video Controls', WebinarSysteem::$lang_slug); ?></label>
            <?php $livep_video_controls_yn_value = get_post_meta($post->ID, '_wswebinar_livep_video_controls_yn', true); ?>
            <input type="checkbox" data-switch="true" name="livep_video_controls_yn" id="livep_video_controls_yn" value="yes" <?php echo ($livep_video_controls_yn_value == "yes" ) ? 'checked' : ''; ?> >
            <p class="description"><?php printf(__("%s only.", WebinarSysteem::$lang_slug), "YouTube / Vimeo") ?></p>
            <div class="webinar_clear_fix"></div>
        </div>
    </div>
    <?php WebinarSysteemMetabox::_page_styling($post); ?>
</div>