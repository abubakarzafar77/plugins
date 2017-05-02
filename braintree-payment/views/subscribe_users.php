<script>
    var imagesPath = '<?php echo home_url() . '/wp-content/themes/Mattevideo3/js/facebox' ?>';
</script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_url') . '/js/jquery-latest.js'; ?>"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_url') . '/js/jquery.tablesorter.js'; ?>"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_url') . '/js/facebox/facebox.js'; ?>"></script>
<link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo('template_url') . '/js/facebox/facebox.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo('template_url') . '/js/themes/blue/style.css'; ?>" />
<script type="text/javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/tiny_mce/tiny_mce.js"></script>
<?php
if ($users)
{
    $total = count($users);
}
else
{
    $total = 0;
}
?>
<div class="wrap">

    <h1>Subscriptions
        <?php if($need_migration){?>
            <a href="?page=brain_tree_menu&migrate=1&cnt=<?php echo $need_migration;?>" class="page-title-action">Migrate <?php echo $need_migration;?> NetAxept users</a>
        <?php }?>
    </h1>
    <?php if(isset($_REQUEST['migrate'])){?>
        <div id="message" class="updated notice notice-success is-dismissible below-h2"><p><?php echo $migrated_count;?> user(s) migrated from NextAxept to Braintree successfully.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
    <?php }?>
    <ul class="subsubsub">
        <li>
            Filters
        </li>
        <li>
            <?php
            if ($filter != '')
            {
                ?>
                <a title="Show only users with All status." href="?page=subscribed_users">
                    All (<?php echo $total_all; ?>)
                </a>
                <?php
            }
            else
            {
                ?>
                All (<?php echo $total_all; ?>)
            <?php } ?>
        </li>
        <li>
            <?php
            if ($filter == 'Initiated' && $plan == '')
            {
                ?>
                Initiated (<?php echo $total_initiated; ?>)
                <?php
            }
            else
            {
                ?>
                <a title="Show only users with Initiated status." href="?page=subscribed_users&filter=Initiated">
                    Initiated (<?php echo $total_initiated; ?>)
                </a>
            <?php } ?>

        </li>
        <li>

            <?php
            if ($filter == 'Active' && $plan == '')
            {
                ?>
                Active (<?php echo $total_active; ?>)
                <?php
            }
            else
            {
                ?>
                <a title="Show only users with Active status." href="?page=subscribed_users&filter=Active">
                    Active (<?php echo $total_active; ?>)
                </a>
            <?php } ?>



        </li>
        <li>
            <?php
            if ($filter == 'Expired' && $plan == '')
            {
                ?>
                Expired (<?php echo $total_expired; ?>)
                <?php
            }
            else
            {
                ?>
                <a title="Show only users with Expired status." href="?page=subscribed_users&filter=Expired">
                    Expired (<?php echo $total_expired; ?>)
                </a>
            <?php } ?>

        </li>
        <li>
            <?php
            if ($filter == 'Canceled' && $plan == '')
            {
                ?>
                Canceled (<?php echo $total_canceled; ?>)
                <?php
            }
            else
            {
                ?>
                <a title="Show only users with Cancelled status." href="?page=subscribed_users&filter=Canceled">
                    Canceled (<?php echo $total_canceled; ?>)
                </a>
            <?php } ?>

        </li>
        <li>
            <?php
            if ($filter == 'Active' && $plan == 'mattevideo')
            {
                ?>
                Old Plan (<?php echo $total_old; ?>)
                <?php
            }
            else
            {
                ?>
                <a title="Show only users with Old Plan." href="?page=subscribed_users&filter=Active&plan=mattevideo">
                    Old Plan (<?php echo $total_old; ?>)
                </a>
            <?php } ?>

        </li>
        <li>
            <?php
            if ($filter == 'Active' && $plan == '99_kr_plan')
            {
                ?>
                99 kr (<?php echo $total_99; ?>)
                <?php
            }
            else
            {
                ?>
                <a title="Show only users with 99 kr Plan." href="?page=subscribed_users&filter=Active&plan=99_kr_plan">
                    99 kr (<?php echo $total_99; ?>)
                </a>
            <?php } ?>

        </li>
        <li>
            <?php
            if ($filter == 'Active' && $plan == '149_kr_plan')
            {
                ?>
                149 kr (<?php echo $total_149; ?>)
                <?php
            }
            else
            {
                ?>
                <a title="Show only users with 149 kr Plan." href="?page=subscribed_users&filter=Active&plan=149_kr_plan">
                    149 kr (<?php echo $total_149; ?>)
                </a>
            <?php } ?>

        </li>
        <li>
            <?php
            if ($filter == 'Active' && $plan == '199_kr_plan')
            {
                ?>
                199 kr (<?php echo $total_199; ?>)
                <?php
            }
            else
            {
                ?>
                <a title="Show only users with 199 kr Plan." href="?page=subscribed_users&filter=Active&plan=old_plan">
                    199 kr (<?php echo $total_199; ?>)
                </a>
            <?php } ?>

        </li>
    </ul>
    <table id="myTable" class="wp-list-table widefat fixed posts tablesorter">
        <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Subcsription ID</th>
            <th>Plan Name</th>
            <th>Status</th>
            <?php
            if ($filter == 'Expired')
            {
                ?>
                <th>Expire Date</th>
                <?php
            }
            ?>
            <th>Last Logged In At</th>
            <th>Subscribed At / Canceled At</th>
            <?php
                if($filter == 'Expired'){
                    echo '<th>Cron Attempt</th>';
                    echo '<th>Next Cron Attempt</th>';
                }
            ?>
            <th><?php if ($filter == 'Expired'){?>
                    <input type="button" name="" class="button" value="Open all" onclick="openAll();" />
                <?php }else{echo '&nbsp;';}?></th>
        </tr>
        </thead>
        <tbody class="the-list">

        <?php
        if (isset($_GET['test']) && $_GET['test'] == 'test')
        {
            echo "<pre/>";
            print_r($users[0]);
            die();
        }
        if ($users)
        {
            foreach ($users as $user)
            {
                ?>
                <tr>
                    <td>
                        <?php echo $user->ID ?>
                    </td>
                    <td>
                        <a class="subscription_link" id="subscription_link_<?php echo $user->ID; ?>" target="_blank" href="?page=bt-payment_detail&user=<?php echo $user->ID; ?>&status=<?php echo $user->status;?>&subscrib_id=<?php echo $user->subscription_id ?>"><?php echo get_user_meta($user->ID, 'first_name', true) . " " . get_user_meta($user->ID, 'last_name', true); ?></a>
                        - <a href="<?php echo get_edit_user_link($user->ID) ?>">[wp]</a>
                    </td>
                    <td>
                        <?php echo $user->subscription_id ?>
                    </td>
                    <td>
                        <?php echo $user->subscription_plan ?>
                    </td>
                    <td>
                        <?php echo $user->status ?>
                    </td>
                    <?php
                    if ($filter == 'Expired')
                    {
                        echo "<td>";
                        echo date('Y-m-d h:m:s', $user->updated_at);
                        echo "</td>";
                    }
                    ?>
                    <td>
                        <?php
                        $last_login = get_user_meta($user->ID, 'last_login', true);
                        echo $last_login != '' ? $last_login : '00:00:00';
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($user->status == 'Canceled')
                        {
                            echo date("Y-m-d H:i:s", $user->updated_at);
                        }
                        else
                        {
                            echo date("Y-m-d H:i:s", $user->created_at);
                        }
                        ?>
                    </td>
                    <?php
                        if($filter == 'Expired'){
                            echo '<td>'.$user->previous_billing_date.'</td>';
                            echo '<td>'.$user->next_billing_date.'</td>';
                        }
                    ?>
                    <td><a href="javascript://" onclick="doAjax(<?php echo $user->ID; ?>);">Contact User</a>
                        <?php
                        if ($filter == 'Expired')
                        {
                            $link="?page=subscribed_users&&filter=Expired&action=cancel&sub_id=".$user->id."&subscrib_id=".$user->subscription_id;
                            ?>
                            | <a  href="<?php echo $link ?>">Cancel</a>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
        }
        else
        {
            ?>
            <tr>
                <td colspan="6">
                    No record found
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>
<script>
    $(window).ready(function () {
        if ($("#myTable").length > 0) {
            $("#myTable").tablesorter({
                // pass the headers argument and assing a object 
                headers: {
            // assign the sixth column (we start counting zero)
            <?php echo ($filter == 'Expired'?8:7)?>: {
                // disable it by setting the property sorter to false
                sorter: false
            }
        }
    });
    }
    });

    function doAjax(user_id) {
        var data = {
            action: 'my_action',
            user_id: user_id
        };
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data,
            dataType: 'json',
            cache: false,
            success: function (response) {
                $.facebox(response.html, 'my-groovy-style');
                bindEditor();
            }
        });
    }
    function sendEmail() {
        var data = {
            message: tinyMCE.get('message').getContent(),
            email: $('#email_address').val(),
            user_id: $('#user_id').val(),
            action: $('#action').val()
        };
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data,
            dataType: 'json',
            cache: false,
            success: function (response) {
                $.facebox("Email successfully sent to " + data.email, 'my-groovy-style');
            }
        });
    }
    function bindEditor() {
        tinyMCE.init({
            // General options
            mode: "textareas",
            theme: "advanced",
            plugins: "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
            // Theme options
            theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
            theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
            theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
            theme_advanced_toolbar_location: "top",
            theme_advanced_toolbar_align: "left",
            theme_advanced_statusbar_location: "bottom",
            theme_advanced_resizing: true,
            // Skin options
            skin: "o2k7",
            skin_variant: "silver",
            relative_urls: false,
            remove_script_host: false,
            // Drop lists for link/image/media/template dialogs
            template_external_list_url: "js/template_list.js",
            external_link_list_url: "js/link_list.js",
            external_image_list_url: "js/image_list.js",
            media_external_list_url: "js/media_list.js",
            // Replace values for the template plugin
            template_replace_values: {
                username: "Some User",
                staffid: "991234"
            }
        });
    }
    function openAll(){
        jQuery('.subscription_link').each(function(){
            console.log('IN LOOP! Wahooooooooo!');
            url = jQuery(this).attr('href');
            window.open(url);
        });
    }
</script>