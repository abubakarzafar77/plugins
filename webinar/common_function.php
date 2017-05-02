<?php
require_once('model/model.php');
add_role('teacher', 'Teacher', array(
    'read' => true, // true allows this capability
));

if(!function_exists('webinar_clean_user_url')) {
    function webinar_clean_user_url($user_url){
        global $wpdb;
        $count = $wpdb->get_row( "SELECT count(*) AS total FROM $wpdb->usermeta WHERE meta_value = '{$user_url}'");
        $meta_value = $user_url;
        if($count->total > 0) {
            $data = $wpdb->get_row( "SELECT * FROM $wpdb->usermeta WHERE meta_value LIKE '%{$meta_value}%' ORDER BY umeta_id DESC LIMIT 1");
            $user_url = explode("-", $data->meta_value);
            if(count($user_url) > 0)  {
                $user_url =  $user_url[0] .'-' . ((int)$user_url[1]+1);
            }
        } else {
            $user_url = $meta_value;
        }
        return  $user_url;
    }
}

if(!function_exists('webinar_registration_save')) {
    function webinar_registration_save($user_id){
        global $webinar_config;
        # again do this only if you can
        if (!current_user_can('manage_options'))
            return false;
        if ($_POST['role'] == 'teacher') {
            # save my custom field
            $uploadedfile = $_FILES['photo'];
            $movefile = wp_handle_upload($uploadedfile, array('test_form' => FALSE));
            if (is_array($movefile) && !isset($movefile['error'])) {
                // Have to do here the S3 upload if plugin did not do auto upload
                update_user_meta($user_id, 'webinar_photo', $movefile['url']);
            }
            update_user_meta($user_id, 'webinar_age', $_POST['age']);
            update_user_meta($user_id, 'webinar_rate', $_POST['rate']);
            update_user_meta($user_id, 'webinar_video', $_POST['video']);
            update_user_meta($user_id, 'webinar_education', implode(',',$_POST['education']));
            update_user_meta($user_id, 'webinar_experience', $_POST['experience']);
            update_user_meta($user_id, 'webinar_why_teach', $_POST['why_teach']);
            update_user_meta($user_id, 'webinar_user_url', webinar_clean_user_url($_POST['user_url']));
            update_user_meta($user_id, 'webinar_role', 'teacher');
            if ($_POST['action'] == 'createuser') {
                wp_redirect('admin.php?page=' . $webinar_config->plugin_teachers_page . '&updated=true');
                exit;
            }
        }
    }
}




if(!function_exists('webinar_add_registration_fields')) {
    function webinar_add_registration_fields($user)
    {
        if ((isset($_REQUEST['role']) && $_REQUEST['role'] == 'teacher')) {
            echo '<script type="text/javascript">jQuery(document).ready(function(){jQuery("#role").val("teacher");jQuery("#teacher-extra-information").show();});</script>';
        } else if ($_REQUEST['user_id']) {
            echo '<script type="text/javascript">jQuery(document).ready(function(){var role = jQuery("#role :selected").val();if(role == "teacher"){jQuery("#teacher-extra-information").show();}});</script>';
        }
        echo '<script>jQuery("#your-profile").attr("enctype", "multipart/form-data");</script>';
        $model = new Model();
        $education_levels = $model->get_all_categories();
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('#role').on('change', function () {
                    if (jQuery(this).val() == 'teacher') {
                        jQuery('#teacher-extra-information').show();
                    } else {
                        jQuery('#teacher-extra-information').hide();
                    }
                })
            });
        </script>
        <div id="teacher-extra-information" style="display: none;">
            <h3>Teacher profile Extra information</h3>
            <table class="form-table">
                <tr>
                    <th><label for="age">Photo</label></th>
                    <td>
                        <input type="file" class="regular-text" name="photo" id="photo"/>
                    </td>
                </tr>
                <tr>
                    <th><label for="age">Age</label></th>
                    <td>
                        <input type="text" class="regular-text" name="age" value="<?php echo esc_attr(get_the_author_meta('webinar_age', $user->ID)); ?>" id="age"/>
                    </td>
                </tr>
                <tr>
                    <th><label for="rate">Rate</label></th>
                    <td>
                        <input type="text" class="regular-text" name="rate" value="<?php echo esc_attr(get_the_author_meta('webinar_rate', $user->ID)); ?>" id="rate"/><br/>
                        <span class="description">Price per 1/2 hour</span>
                    </td>
                </tr>
                <tr>
                    <th><label for="video">Video</label></th>
                    <td>
                        <input type="text" class="regular-text" name="video" value="<?php echo esc_attr(get_the_author_meta('webinar_video', $user->ID)); ?>" id="video"/><br/>
                        <span class="description">Insert vimeo URL</span>
                    </td>
                </tr>
                <tr>
                    <th><label for="education">Education</label></th>
                    <td>
                        <?php $webinar_education = explode(",", esc_attr(get_the_author_meta('webinar_education', $user->ID)));?>
                        <?php foreach ($education_levels as $level): ?>
                            <label><input type="checkbox" name="education[]" id="education_<?php echo $level['ID']; ?>" value="<?php echo $level['ID']; ?>"<?php echo(in_array($level['ID'], $webinar_education)? ' checked' : ''); ?> /><?php echo $level['name']; ?></label>
                        <?php endforeach; ?>
                        <?php /*<select class="regular-text" name="education" id="education">
                            <option value="">Formal education level</option>
                            <?php foreach ($education_levels as $level): ?>
                                <option value="<?php echo $level['ID']; ?>"<?php echo(esc_attr(get_the_author_meta('webinar_education', $user->ID)) == $level['ID'] ? ' selected' : ''); ?>><?php echo $level['name']; ?></option>
                            <?php endforeach; ?>
                        </select>*/?>
                    </td>
                </tr>
                <tr>
                    <th><label for="experience">Experience</label></th>
                    <td>
                        <input type="text" class="regular-text" name="experience" value="<?php echo esc_attr(get_the_author_meta('webinar_experience', $user->ID)); ?>" id="experience"/><br/>
                        <span class="description">Experience in years</span>
                    </td>
                </tr>
                <tr>
                    <th><label for="why_teach">Why I teach</label></th>
                    <td>
                        <textarea name="why_teach" id="why_teach" rows="3" cols="30"><?php echo esc_attr(get_the_author_meta('webinar_why_teach', $user->ID)); ?></textarea><br/>
                    </td>
                </tr>
                <tr>
                    <th><label for="user_url">URL</label></th>
                    <td>
                        <input type="text" class="regular-text" name="user_url" value="<?php echo esc_attr(get_the_author_meta('webinar_user_url', $user->ID)); ?>" id="user_url"/><br/>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }
}

if(!function_exists('sendEmail')){
    function sendEmail($job_id, $offer_id, $user_id, $teacher_id, $type, $email_type=''){
        $pluginPath = dirname(__FILE__).'/';
        $emails = json_decode(file_get_contents( $pluginPath.'/emails.json' ), true);
        $subject = $emails['email_templates'][$type]['subject'];
        $body = $emails['email_templates'][$type]['body'];
        $model = new Model();
        $payment = 0;
        if($email_type == 'group'){
            $job_details = $model->get_job_by_group($job_id);
        }else {
            $job_details = $model->get_job_by_id($job_id);
        }
        if ($offer_id) {
            $offer_details = $model->get_offer_details_by_id($offer_id);
            $payment = $offer_details->offer_amount;
        }
        if ($job_details) {
            $job_details = $job_details[0];
        } else {
            $job_details->webinar_url = '';
        }
        if($type == 'payment_success' || $type == 'webinar_setup_student' || $type == 'webinar_setup_complete'){
            $user_info = get_userdata($user_id);
            $teacher_info = get_userdata($teacher_id);
            $to = $user_info->user_email;
            $t_first_name = get_user_meta($teacher_id, 'first_name', true);
            $t_last_name = get_user_meta($teacher_id, 'last_name', true);
            $patterns = array(
                'TEACHER_NAME' => ($t_first_name?$t_first_name.' '.$t_last_name:$teacher_info->display_name),
                'JOB_LINK' => home_url('studiekamerat?page=available_jobs&job_id=' . $job_id),
                'USER_NAME' => get_user_meta($user_id, 'first_name', true).' '.get_user_meta($user_id, 'last_name', true),
                'JOB_INVOICE' => '',
                'JOB_PAYMENT' => $payment,
                'WEBINAR_LINK' => $job_details->webinar_url
            );
            foreach ($patterns as $pattern => $val) {
                $body = str_replace('[' . $pattern . ']', $val, $body);
                $subject = str_replace('[' . $pattern . ']', $val, $subject);
            }
        }
        elseif($type == 'offer_accepted' || $type == 'webinar_setup_teacher'){
            $teacher_info = get_userdata($teacher_id);
            $to = $teacher_info->user_email;
            $t_first_name = get_user_meta($teacher_id, 'first_name', true);
            $t_last_name = get_user_meta($teacher_id, 'last_name', true);
            $patterns = array(
                'TEACHER_NAME' => ($t_first_name?$t_first_name.' '.$t_last_name:$teacher_info->display_name),
                'JOB_LINK' => home_url('studiekamerat?page=available_jobs&job_id=' . $job_id),
                'USER_NAME' => get_user_meta($user_id, 'first_name', true).' '.get_user_meta($user_id, 'last_name', true),
                'JOB_INVOICE' => '',
                'JOB_PAYMENT' => $payment,
                'WEBINAR_LINK' => $job_details->webinar_url
            );
            foreach ($patterns as $pattern => $val) {
                $body = str_replace('[' . $pattern . ']', $val, $body);
                $subject = str_replace('[' . $pattern . ']', $val, $subject);
            }
        }
        elseif($type == 'offer_recieved'){
            $student_info = get_userdata($user_id);
            $teacher_info = get_userdata($teacher_id);
            $to = $student_info->user_email;
            $t_first_name = get_user_meta($teacher_id, 'first_name', true);
            $t_last_name = get_user_meta($teacher_id, 'last_name', true);
            $patterns = array(
                'TEACHER_NAME' => ($t_first_name?$t_first_name.' '.$t_last_name:$teacher_info->display_name),
                'JOB_LINK' => home_url('studiekamerat?page=my_jobs&job_id=' . $job_id),
                'USER_NAME' => get_user_meta($user_id, 'first_name', true).' '.get_user_meta($user_id, 'last_name', true),
                'JOB_INVOICE' => '',
                'JOB_PAYMENT' => $payment,
                'WEBINAR_LINK' => $job_details->webinar_url
            );
            foreach ($patterns as $pattern => $val) {
                $body = str_replace('[' . $pattern . ']', $val, $body);
                $subject = str_replace('[' . $pattern . ']', $val, $subject);
            }
        }
        elseif($type == 'job_posted') {
            if ($teacher_id == 'all') {
                $all_teachers = $model->getAllTeachers();
                foreach ($all_teachers as $teacher) {
                    $to = $teacher['user_email'];
                    $t_first_name = get_user_meta($teacher['ID'], 'first_name', true);
                    $t_last_name = get_user_meta($teacher['ID'], 'last_name', true);
                    $patterns = array(
                        'TEACHER_NAME' => ($t_first_name?$t_first_name.' '.$t_last_name:$teacher['display_name']),
                        'JOB_LINK' => home_url('studiekamerat?page=available_jobs&job_id=' . $job_id),
                        'USER_NAME' => get_user_meta($user_id, 'first_name', true).' '.get_user_meta($user_id, 'last_name', true),
                        'JOB_INVOICE' => '',
                        'JOB_PAYMENT' => $payment,
                        'WEBINAR_LINK' => $job_details->webinar_url
                    );
                    foreach ($patterns as $pattern => $val) {
                        $body = str_replace('[' . $pattern . ']', $val, $body);
                        $subject = str_replace('[' . $pattern . ']', $val, $subject);
                    }
                }
            } else if ($teacher_id != 0) {
                $teacher_info = get_userdata($teacher_id);
                $to = $teacher_info->user_email;
                $t_first_name = get_user_meta($teacher_id, 'first_name', true);
                $t_last_name = get_user_meta($teacher_id, 'last_name', true);
                $patterns = array(
                    'TEACHER_NAME' => ($t_first_name?$t_first_name.' '.$t_last_name:$teacher_info->display_name),
                    'JOB_LINK' => home_url('studiekamerat?page=available_jobs&job_id=' . $job_id),
                    'USER_NAME' => get_user_meta($user_id, 'first_name', true).' '.get_user_meta($user_id, 'last_name', true),
                    'JOB_INVOICE' => '',
                    'JOB_PAYMENT' => $payment,
                    'WEBINAR_LINK' => $job_details->webinar_url
                );
                foreach ($patterns as $pattern => $val) {
                    $body = str_replace('[' . $pattern . ']', $val, $body);
                    $subject = str_replace('[' . $pattern . ']', $val, $subject);
                }
            }
        }
        /*echo "To: ".$to."<br />";
        echo "Subject: ".$subject."<br />";
        echo "Body: ".$body."<br />";
        exit;*/
        sendActualEmail($to, $subject, $body);
        return true;
    }
}

if(!function_exists('sendActualEmail')){
    function sendActualEmail($to, $subject, $body){
        $headers = "MIME-Version: 1.0\n" . "From: Mattevideo <ksondresen@gmail.com>\n" . "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
        $admin = 'ksondresen@gmail.com';
        $admin_body = $body;
        $admin_body .= "<br />Email was sent to: $to";
        $attachments = array();

        $admin_email = wp_mail($admin, $subject, $admin_body, $headers, $attachments);

        $mail = wp_mail($to, $subject, $body, $headers, $attachments);
    }
}