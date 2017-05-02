<?php
$fileName        = explode(".", basename(__FILE__));
$currentFileData = "data_" . $fileName[0];
$currentViewData = $$currentFileData;

//	get all users

$users = get_users(array('include' => $currentViewData['user_ids'],
    'fields'  => 'all_with_meta'));

$collect = 0;
if (count($rows) > 0)
{
    global $wpdb;
    $config                  = parse_ini_file(dirname(__FILE__) . "/../NetAxept.ini", true);
    $pmt_tableNameWithPrefix = $wpdb->prefix . $config['plugin_parameters']['tn_payment'];
    foreach ($rows as $row)
    {
        $pmt_results = $wpdb->get_results("SELECT * FROM $pmt_tableNameWithPrefix WHERE pmt_sbr_id='" . $row->sbr_id . "' AND pmt_status='Initiated' ORDER BY pmt_id DESC", OBJECT);
        if (!empty($pmt_results) && $row->sbr_status == 'Active')
        {
            ( strtotime($pmt_results[0]->pmt_period_from) >= strtotime("+10 days")) ? '' : $collect++;
        }
    }
}
?>
<script>
    var imagesPath = '<?php echo home_url() . '/wp-content/themes/Mattevideo3/js/facebox' ?>';
</script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_url') . '/js/jquery-latest.js'; ?>"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_url') . '/js/jquery.tablesorter.js'; ?>"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_url') . '/js/facebox/facebox.js'; ?>"></script>
<link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo('template_url') . '/js/facebox/facebox.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo('template_url') . '/js/themes/blue/style.css'; ?>" />
<script type="text/javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/tiny_mce/tiny_mce.js"></script>
<div class="wrap">

    <h2><?php echo $currentViewData['heading'] ?> </h2>
    <ul class="subsubsub">
        <li>
            <?php if (isset($_REQUEST['status']))
            { ?>
                <a href="?page=netaxept" title="Show all users." id="fancybox-manual-b">All (<?php echo $All; ?>)</a>
            <?php }
            else
            { ?>
                All (<?php echo $All; ?>)
            <?php } ?>
        </li>
        <li>
            <?php if ((isset($_REQUEST['status']) && $_REQUEST['status'] != 'Initiated') || !isset($_REQUEST['status']))
            { ?>
                <a href="?page=netaxept&status=Initiated" title="Show only users with Initiated status.">Initiated (<?php echo $Initiated; ?>)</a>
            <?php }
            else
            { ?>
                Initiated (<?php echo $Initiated; ?>)
            <?php } ?>
        </li>
        <li>
            <?php if ((isset($_REQUEST['status']) && $_REQUEST['status'] != 'Started') || !isset($_REQUEST['status']))
            { ?>
                <a href="?page=netaxept&status=Started" title="Show only users with Started status.">Started (<?php echo $Started; ?>)</a>
            <?php }
            else
            { ?>
                Started (<?php echo $Started; ?>)
<?php } ?>
        </li>
        <li>
            <?php if ((isset($_REQUEST['status']) && !isset($_REQUEST['collect']) && $_REQUEST['status'] != 'Active') || !isset($_REQUEST['status']))
            { ?>
                <a href="?page=netaxept&status=Active" title="Show only users with Active status.">Active (<?php echo $Active; ?>)</a>
            <?php }
            elseif (isset($_REQUEST['collect']))
            { ?>
                <a href="?page=netaxept&status=Active" title="Show only users with Active status.">Active (<?php echo $Active; ?>)</a>
            <?php }
            else
            { ?>
                Active (<?php echo $Active; ?>)
<?php } ?>
        </li>
        <li>
            <?php if ((isset($_REQUEST['status']) && !isset($_REQUEST['collect'])) || !isset($_REQUEST['status']))
            { ?>
                <a href="?page=netaxept&status=Active&collect=yes" title="Show only users with Active status.">Active (Collect <?php echo $collect; ?>)</a>
            <?php }
            else
            { ?>
                Active (Collect <?php echo $collect; ?>)
<?php } ?>
        </li>
        <li>
<?php if ((isset($_REQUEST['status']) && $_REQUEST['status'] != 'Expired') || !isset($_REQUEST['status']))
{ ?>
                <a href="?page=netaxept&status=Expired" title="Show only users with Expired status.">Expired (<?php echo $Expired; ?>)</a>
<?php }
else
{ ?>
                Expired (<?php echo $Expired; ?>)
<?php } ?>
        </li>
        <li>
<?php if ((isset($_REQUEST['status']) && $_REQUEST['status'] != 'Cancelled') || !isset($_REQUEST['status']))
{ ?>
                <a href="?page=netaxept&status=Cancelled" title="Show only users with Cancelled status.">Cancelled (<?php echo $Cancelled; ?>)</a>
            <?php }
            else
            { ?>
                Cancelled (<?php echo $Cancelled; ?>)
            <?php } ?>
        </li>
    </ul>
    <table class="wp-list-table widefat fixed posts tablesorter" id="myTable">
        <thead>
            <tr>
                <th>Id</th>
                <th>Created</th>
                <th>Name</th>
                <th>Status</th>
                <th>Last Paid Date</th>
                <th>Last Logged In At</th>
                <th>Modified</th>
                <th>Contact User</th>

                <th>Collect Payment <?php echo((isset($_REQUEST['collect'])) ? ' <input type="button" value="Open All" class="button" onclick="openAll();" />' : ''); ?></th>
            </tr>
        </thead>
        <tbody class="the-list">

                    <?php
                    if (count($rows) > 0)
                    {
                        global $wpdb;
                        $config                  = parse_ini_file(dirname(__FILE__) . "/../NetAxept.ini", true);
                        $pmt_tableNameWithPrefix = $wpdb->prefix . $config['plugin_parameters']['tn_payment'];
                        foreach ($rows as $key => $row)
                        {
                            $pmt_results = $wpdb->get_results("SELECT * FROM $pmt_tableNameWithPrefix WHERE pmt_sbr_id='" . $row->sbr_id . "' AND pmt_status='Initiated' ORDER BY pmt_id DESC", OBJECT);
                            $last_paid_date = $wpdb->get_row("SELECT pmt_period_to FROM  `wptest_mna_payment` WHERE  `pmt_sbr_id` =$row->sbr_id AND pmt_status =  'Collected' ORDER BY pmt_id DESC LIMIT 1");
                            $disabled = ( strtotime($pmt_results[0]->pmt_period_from) >= strtotime("+10 days")) ? 'disabled="disabled"' : '';
                            if (!empty($pmt_results) && $row->sbr_status == 'Active' && isset($_REQUEST['collect']) && $disabled != '')
                            {
                                continue;
                            }
                            ?>
                    <tr>
                        <td><?php echo $row->sbr_id ?></td>
                        <td><?php echo $row->sbr_created ?></td>
                        <td><a class="openlink" target="_blank" href="?page=netaxept&action=subscription&sbr_id=<?php echo $row->sbr_id ?>"><?php echo get_user_meta($row->sbr_wp_user_id, 'first_name', true) ?>
        <?php echo get_user_meta($row->sbr_wp_user_id, 'last_name', true) ?> <a href="<?php echo get_edit_user_link($row->sbr_wp_user_id) ?>">[wp]</a></td>
                        <td>
                            <?php if ($row->sbr_status == 'Active')
                            { ?>
                                <a href="?page=netaxept&action=cancel_subscription&sbr_id=<?php echo $row->sbr_id ?><?php echo (isset($_REQUEST['status']) ? '&status=' . $_REQUEST['status'] : ''); ?>"><?php echo $row->sbr_status ?></a>
                    <?php }
                    elseif ($row->sbr_status == 'Cancelled')
                    { ?>
                                <a href="?page=netaxept&action=activate_subscription&sbr_id=<?php echo $row->sbr_id ?><?php echo (isset($_REQUEST['status']) ? '&status=' . $_REQUEST['status'] : ''); ?>"><?php echo $row->sbr_status ?></a>
                    <?php }
                    else
                    { ?>
            <?php echo $row->sbr_status ?>
        <?php } ?>
                        </td>
                        <td>
                        <?php  
                           $last_paid_date= $last_paid_date->pmt_period_to;
                           echo $last_paid_date!=''?$last_paid_date:"00:00:00";
                        ?>
                        </td>
                        <td>
        <?php $last_login = get_user_meta($row->sbr_wp_user_id, 'last_login', true);
        echo $last_login != '' ? $last_login : '00:00:00';
        ?>      
                        </td>
                        <td><?php echo $row->sbr_modified ?></td>
                        <td><a href="javascript://" onclick="doAjax(<?php echo $row->sbr_wp_user_id; ?>);">Contact User</a></td>

                        <td>
        <?php if (!empty($pmt_results) && $row->sbr_status == 'Active')
        { ?>
                                <form action="" method="post" id="collect_form_<?php echo $row->sbr_id; ?>" title="<?php echo $row->sbr_id; ?>" class="submit_form">
                                    <input type="hidden" name="recursive" id="recursive_<?php echo $row->sbr_id; ?>" />
                                    <input type="hidden" name="todo" value="collect_payment" />
                                    <input type="hidden" name="action" value="subscription" />
            <?php if (isset($_REQUEST['collect']))
            { ?>
                                        <input type="hidden" name="collect" value="yes" />
            <?php } ?>
                                    <input type="hidden" name="status" value="<?php echo (isset($_REQUEST['status']) ? $_REQUEST['status'] : ''); ?>" />
                                    <input type="hidden" name="subscription" value="<?php echo $row->sbr_id; ?>" /> 
                                    <button form="collect_form_<?php echo $row->sbr_id; ?>" name="pmt_id" <?php echo $disabled ?> value="<?php echo $pmt_results[0]->pmt_id ?>" id="collect_<?php echo $row->sbr_id; ?>">Collect</button>
                                </form>
        <?php } ?>
                        </td>
                    </tr>
        <?php
    }
}
else
{
    ?>
            <td align="center" colspan="5">No records found.</td>
<?php } ?>
    </table>
</div>
<script>
    var $ = jQuery.noConflict(true);
    $(document).ready(function () {
        $("#myTable").tablesorter({
            // pass the headers argument and assing a object 
            headers: {
                // assign the sixth column (we start counting zero) 
                5: {
                    // disable it by setting the property sorter to false 
                    sorter: false
                },
                // assign the seventh column (we start counting zero) 
                6: {
                    // disable it by setting the property sorter to false 
                    sorter: false
                }
            }
        });
<?php if (isset($_REQUEST['subscription']) && isset($_GET['recursive']) && $_GET['recursive'] == 'yes')
{ ?>
            collectPaymentFromAll();
<?php } ?>
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
		jQuery('.openlink').each(function(){
			console.log('IN LOOP! Wahooooooooo!');
			url = jQuery(this).attr('href');
			window.open(url);
		});
	}
    function collectPaymentFromAll() {
        var submit_index = 0;
        var flag = 0;
<?php if (isset($_REQUEST['subscription']) && isset($_GET['recursive']) && $_GET['recursive'] == 'yes')
{ ?>
            submit_index = <?php echo $_REQUEST['subscription']; ?>;
<?php } ?>
        $('.submit_form').each(function (index) {
            if (submit_index == 0) {
                if (index == 0) {
                    var form_id = $(this).attr('title');
                    $('#recursive_' + form_id).val('yes');
                    $('#collect_' + form_id).click();
                    return false;
                }
            } else {
                var form_id = $(this).attr('title');
                if (flag == 1) {
                    $('#recursive_' + form_id).val('yes');
                    $('#collect_' + form_id).click();
                    flag = 0;
                    return false;
                }
                if (form_id == submit_index) {
                    flag = 1;
                } else if ($('#collect_' + submit_index).length == 0) {
                    $('#recursive_' + form_id).val('yes');
                    $('#collect_' + form_id).click();
                    return false;
                }
            }
        });
    }
</script>