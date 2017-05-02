<script type="text/javascript" src="<?php echo get_bloginfo('template_url');?>/js/tiny_mce/tiny_mce.js"></script>
<div class="wrap">
    <form action="?page=mass_emails" method="post" onSubmit="return validate();" id="mass_emails" name="mass_emails">
        <h2>Send Mass Emails</h2>
        <br />
        <?php if(isset($success)){?>
            <div class="update-nag"><?php echo $success;?></div>
        <?php }?>
        <br />
        <div class="parameterbox">
        	<label class="standar">check this to verify sending of emails? </label>
            <input type="checkbox" name="send_emails" id="send_emails" />
        </div>
        <div class="parameterbox">
			<label class="standar">Subscriptions Status: </label>
			<select name="subscription_status" id="subscription_status" style="width:300px;">
                <option value="all"<?php echo((isset($_POST['subscription_status']) && $_POST['subscription_status'] == 'all')?' selected':'');?>>All</option>
                <option value="Initiated"<?php echo((isset($_POST['subscription_status']) && $_POST['subscription_status'] == 'Initiated')?' selected':'');?>>Initiated</option>
                <option value="Started"<?php echo((isset($_POST['subscription_status']) && $_POST['subscription_status'] == 'Started')?' selected':'');?>>Started</option>
                <option value="Active"<?php echo((isset($_POST['subscription_status']) && $_POST['subscription_status'] == 'Active')?' selected':'');?>>Active</option>
                <option value="Expired"<?php echo((isset($_POST['subscription_status']) && $_POST['subscription_status'] == 'Expired')?' selected':'');?>>Expired</option>
                <option value="Cancelled"<?php echo((isset($_POST['subscription_status']) && $_POST['subscription_status'] == 'Cancelled')?' selected':'');?>>Cancelled</option>
            </select>            
		</div>
        <div class="parameterbox">
			<label class="standar">Email Subject: </label>
			<input type="text" name="subject" id="subject" style="width:300px;" value="<?php echo(isset($_POST['subject'])?$_POST['subject']:'');?>" />           
		</div>
        <div class="parameterbox">
			<label class="standar">Keywords: </label>
			<select name="keywords" id="keywords" style="width:300px;">
            	<option value="{FIRST_NAME}">First Name</option>
                <option value="{LAST_NAME}">Last Name</option>
                <option value="{EMAIL}">Email</option>
                <option value="{SBR_STATUS}">Subscription Status</option>
                <option value="{LAST_PAYMENT_DATE}">Last Payment Period</option>
            </select>
            <input type="button" name="insert" value=" Insert " onClick="insertText(document.getElementById('keywords').value);" />
		</div>
        <div class="parameterbox">
        	<label class="standar" style="float:left">Email Body: </label>
            <div style="width: 600px; float:left">
				<?php 
                 //$content = (isset($_POST['body'])?$_POST['body']:''); 
                 //$editor_id = 'body';
                 //wp_editor( $content, $editor_id, $settings = array('textarea_name'=>'body','textarea_rows'=> 20, 'media_buttons' => false) );
                ?>
                <textarea name="body" id="body"></textarea>
            </div>
        </div>
        <div class="parameterbox">
        	<label class="standar">Send Test Email?</label>
            <input type="text" name="test_email" id="test_email" />
        	<input type="button" name="test" id="test" value=" Test " class="button" onclick="sendTestEmail();" />
        </div>
        <div class="parameterbox">
        	<label class="standar">&nbsp;</label>
        	<input type="submit" name="send" id="send" value=" Send " class="button" />
        </div>
    </form>
</div>
<script>
	function sendTestEmail(){
		var data = {
			body: tinyMCE.get('body').getContent(),
			test_email: $('#test_email').val(),
			subject: $('#subject').val(),
			action: 'send_test_email'
		};
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: data,
			dataType: 'json',
			cache: false,
			success: function(response){
				alert(response.message);
			}
		});
	}
	function validate(){
		var subject=document.forms["mass_emails"]["subject"].value;
		var email_body=tinyMCE.get('body').getContent()
		if (subject==null || subject=="")
  		{
  			alert("Subject must be filled out");
			$("#subject").focus();
  			return false;
  		}else if(email_body==null || email_body==""){
			alert("Body must be filled out");
			$("#body").focus();
  			return false;
		}else{
			return true;
		}
	}
	function insertText(html){
		if(html != ''){
			 tinyMCE.execInstanceCommand("body","mceInsertContent",false,html);
		 }
	}
    $(window).ready(function(){
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
			
			relative_urls: false,
			remove_script_host : false,
    
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