<?php if(isset($email_template_to_edit)){
    if($template_type == "job_posted"){
        $template_name = 'Job Posted';
        $email_subject = $body['subject'];
    }elseif($template_type == "offer_accepted"){
        $template_name = 'Offer Accepted';
        $email_subject = $body['subject'];
    }elseif($template_type == "payment_success"){
        $template_name = 'Payment Success';
        $email_subject = $body['subject'];
    }elseif($template_type == "offer_recieved"){
        $template_name = 'Offer Recieved';
        $email_subject = $body['subject'];
    }elseif($template_type == "webinar_setup_teacher"){
        $template_name = 'Webinar Setup Teacher';
        $email_subject = $body['subject'];
    }elseif($template_type == "webinar_setup_student"){
        $template_name = 'Webinar Setup Student';
        $email_subject = $body['subject'];
    }elseif($template_type == "webinar_setup_complete"){
        $template_name = 'Webinar Setup Complete';
        $email_subject = $body['subject'];
    }
    ?>
    <script type="text/javascript" src="<?php echo get_bloginfo('template_url');?>/js/tiny_mce/tiny_mce.js"></script>
    <div class="wrap">
        <form action="?page=<?php echo $this->config->plugin_email_template_page;?>&id=<?php echo $template_type;?>" method="post">
            <h2>Edit Email template "<strong><em><?php echo $template_name;?></em></strong>"</h2>
            <br />
            <p>
                Reserved words, please do not change these words [JOB_LINK], [TEACHER_NAME], [TEACHER_PROFILE], [USER_NAME], [USER_ID], [WEBINAR_LINK] and [JOB_PAYMENT]. These are replaced with proper variables while sending email.
            </p>
            <div>
                <h4 style="float:left; margin:4px 5px 0px 0px;">Subject:</h4>&nbsp;<input type="text" name="template_subject" id="template_subject" value="<?php echo $email_template_subject_to_edit;?>" style="width:250px;" />
            </div>
            <div style="clear:both; height:20px;"></div>
            <div>
                <h4 style="float:left; margin:4px 5px 0px 0px;">Keywords:</h4>&nbsp;
                <select name="keywords" id="keywords" style="width:250px;">
                    <option value="[JOB_LINK]">Job Link</option>
                    <option value="[TEACHER_NAME]">Teacher Name</option>
                    <option value="[TEACHER_PROFILE]">Teacher Profile Link</option>
                    <option value="[USER_NAME]">User Name</option>
                    <option value="[USER_ID]">User ID</option>
                    <option value="[JOB_PAYMENT]">Job Paid Amount</option>
                    <option value="[WEBINAR_LINK]">Webinar Link</option>
                </select>
                <input type="button" name="insert" id="insert" value=" Insert " onclick="tinyMCE.execInstanceCommand('template_text', 'mceInsertContent', false, document.getElementById('keywords').value);" class="button button-primary button-large" />
            </div>
            <div style="clear:both; height:20px;"></div>
            <div>
                <textarea name="template_text" id="template_text"><?php echo $email_template_to_edit;?></textarea>
            </div>
            <br /><br />
            <input type="submit" name="update" id="update" value=" Update " class="button button-primary button-large" />
        </form>
    </div>
    <script>
        jQuery(window).ready(function(){
            bindEditor();
        });
        function bindEditor(){
            tinyMCE.init({
                // General options
                mode : "textareas",
                theme : "advanced",
                plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

                // Theme options
                theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
                theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                theme_advanced_resizing : true,

                // Skin options
                skin : "o2k7",
                skin_variant : "silver",
                width : 800,
                height : 500,

                // Drop lists for link/image/media/template dialogs
                template_external_list_url : "js/template_list.js",
                external_link_list_url : "js/link_list.js",
                external_image_list_url : "js/image_list.js",
                media_external_list_url : "js/media_list.js",

                // Replace values for the template plugin
                template_replace_values : {
                    username : "Some User",
                    staffid : "991234"
                }
            });
        }
    </script>
<?php }else{?>
    <script type="text/javascript" src="<?php echo get_bloginfo('template_url').'/js/jquery.tablesorter.js'; ?>"></script>
    <link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo('template_url').'/js/themes/blue/style.css'; ?>" />
    <div class="wrap">
        <?php if(isset($_GET['updated'])){?>
            <div id="message" class="updated notice notice-success is-dismissible below-h2"><p>Emil text updated successfully.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
        <?php }?>
        <br />
        <h2>Email templates</h2>
        <table class="wp-list-table widefat fixed posts tablesorter" id="myTable">
            <thead>
            <tr>
                <th class="header" width="5%">Id</th>
                <th class="header" width="10%">Template Name</th>
                <th class="header" width="20%">Email Subject</th>
                <th class="header">Email Body</th>
                <th class="header" width="5%">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            foreach($email['email_templates'] as $name=>$body){
                $template_name = "";
                $email_subject = "";
                if($name == "job_posted"){
                    $template_name = 'Job Posted';
                    $email_subject = $body['subject'];
                }elseif($name == "offer_accepted"){
                    $template_name = 'Offer Accepted';
                    $email_subject = $body['subject'];
                }elseif($name == "payment_success"){
                    $template_name = 'Payment Success';
                    $email_subject = $body['subject'];
                }elseif($name == "offer_recieved"){
                    $template_name = 'Offer Recieved';
                    $email_subject = $body['subject'];
                }elseif($name == "webinar_setup_teacher"){
                    $template_name = 'Webinar Setup Teacher';
                    $email_subject = $body['subject'];
                }elseif($name == "webinar_setup_student"){
                    $template_name = 'Webinar Setup Student';
                    $email_subject = $body['subject'];
                }elseif($name == "webinar_setup_complete"){
                    $template_name = 'Webinar Setup Complete';
                    $email_subject = $body['subject'];
                }
                ?>
                <tr>
                    <td><?php echo $i;?></td>
                    <td><?php echo $template_name;?></td>
                    <td><?php echo $email_subject;?></td>
                    <td><?php echo stripslashes($body['body']);?></td>
                    <td><a href="?page=<?php echo $this->config->plugin_email_template_page;?>&id=<?php echo $name;?>">Edit</a></td>
                </tr>
                <?php
                $i++;
            }?>
            </tbody>
        </table>
    </div>
    <script>
        jQuery(window).ready(function(){
            jQuery("#myTable").tablesorter({
                // pass the headers argument and assing a object
                headers: {
                    // assign the sixth column (we start counting zero)
                    4: {
                        // disable it by setting the property sorter to false
                        sorter: false
                    }
                }
            });
        });
    </script>
<?php } ?>