<?php if(isset($email_template_to_edit)){
			if($template_type == "payment_email"){
				$template_name = 'Payment Email';
			}elseif($template_type == "recovery_email"){
				$template_name = 'Recovery Email';
			}elseif($template_type == "deactivate_email"){
				$template_name = 'Deactivate Subscription';
			}elseif($template_type == "reactivate_email"){
				$template_name = 'Reactivate Subscription';
			}elseif($template_type == "update_card_info"){
				$template_name = 'Update Card Info';
			}
			else if($template_type == "subscription_expired_to_active")
			{
				$template_name = 'Subscription Expired To Active';
			}
			else if($template_type == "subscription_expired")
			{
				$template_name = 'Subscription Expired';
			}			
?>
	<script type="text/javascript" src="<?php echo get_bloginfo('template_url');?>/js/tiny_mce/tiny_mce.js"></script>
	<div class="wrap">
    	<form action="?page=bt-email_templates&id=<?php echo $template_type;?>" method="post">
            <h2>Edit Email template "<strong><em><?php echo $template_name;?></em></strong>"</h2>
            <br />
			<?php if(isset($success)){?>
        		<div class="update-nag"><?php echo $success;?></div>
       	 	<?php }?>
            <br />
            <p>
            	Reserved words, please do not change these words {PASSWORD}, [HOME_PAGE], and [USER_ID]. These are replaced with proper variables while sending email.
            </p>
            <div>
            	<h4 style="float:left;margin:4px 5px 0px 0px;">Subject:</h4>&nbsp;<input type="text" name="template_subject" id="template_subject" value="<?php echo $email_template_subject_to_edit;?>" style="width:250px;" />
            </div>
            <div style="clear:both; height:20px;"></div>
            <div>
            	<textarea name="template_text" id="template_text"><?php echo $email_template_to_edit;?></textarea>
            </div>
            <br /><br />
            <?php if($template_type == "payment_email"){?>
            	<div>Email attachment: <a href="../garanti.pdf" target="_blank">Kjøpsvilkår og 100% fornøyd garanti.pdf</a></div>
            	<div>Email attachment: <a href="../julekort.pdf" target="_blank">julekort.pdf</a></div>
            <?php }?>
            <br /><br />
            <input type="submit" name="update" id="update" value=" Update " class="button" />
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
				/*echo "<pre>";
					print_r($config);
				echo "</pre>";*/
                $i = 1;
                foreach($config['email_templates'] as $name=>$body){
                    $template_name = "";
					$email_subject = "";
                    if($name == "payment_email"){
                        $template_name = 'Payment Email';
						$email_subject = $body['subject'];
                    }elseif($name == "recovery_email"){
                        $template_name = 'Recovery Email';
						$email_subject = $body['subject'];
                    }elseif($name == "deactivate_email"){
                        $template_name = 'Deactivate Subscription';
						$email_subject = $body['subject'];
                    }elseif($name == "reactivate_email"){
						$email_subject = $body['subject'];
                        $template_name = 'Reactivate Subscription';
                    }
                    elseif($name == "payment_rejection"){
						$email_subject = $body['subject'];
                        $template_name = 'Payment Rejection';
                    }
                    elseif($name == "update_card_info"){
						$email_subject = $body['subject'];
                        $template_name = 'Update Card Info';
                    }
					else if($name == "subscription_expired_to_active")
					{
						$email_subject = $body['subject'];
						$template_name = 'Subscription Expired To Active';
					}
					else if($name == "subscription_expired")
					{
						$email_subject = $body['subject'];
						$template_name = 'Subscription Expired';
					}		
                ?>
                <tr>
                    <td><?php echo $i;?></td>
                    <td><?php echo $template_name;?></td>
                    <td><?php echo $email_subject;?></td>
                    <td><?php echo stripslashes($body['body']);?></td>
                    <td><a href="?page=bt-email_templates&id=<?php echo $name;?>">Edit</a></td>
                </tr>
                <?php 
                    $i++;
                }?>
            </tbody>
        </table>
    </div>
    <script>
		jQuery(window).ready(function(){
			$("#myTable").tablesorter({ 
				// pass the headers argument and assing a object 
				headers: { 
					// assign the sixth column (we start counting zero) 
					3: { 
						// disable it by setting the property sorter to false 
						sorter: false 
					}
				}
			});
		});
	</script>
<?php } ?>