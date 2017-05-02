<?php @include_once("../../../wp-config.php"); ?>
<?php
//$main_categories = get_categories('include=341,13,3,130');
//$html = "<select class='topic_style' name='topic' id='topic' >";
//$html .= '<option value="1">Select Topic</optgroup>';
//if ($main_categories) {
//    foreach ($main_categories as $main_cat) {
//
//        $html .= '<optgroup label="' . $main_cat->name . '"></optgroup>';
//        $childs_cat1 = get_categories('parent=' . $main_cat->term_id);
//        foreach ($childs_cat1 as $child_cat1) {
//            $html .= '<optgroup style="padding-left:15px" label="' . $child_cat1->name . '"></optgroup>';
//            $childs_cat2 = get_categories('parent=' . $child_cat1->term_id);
//            foreach ($childs_cat2 as $child_cat2) {
//                $html .= '<option style="padding-left:32px" value="'.$child_cat2->term_id.'"> '.$child_cat2->name.'</option>';
//            }
//        }
//    }
//}
//$html .= "</select>";



$plugin_link = WP_PLUGIN_URL . "/chat-groups/";
$setting = get_chat_group_setting();
//var_dump($setting);
?>
<?php
$plugin_link = WP_PLUGIN_URL . "/chat-groups/";
$setting = get_chat_group_setting();
//var_dump($setting);
?>
<?php
$type = "";
$msg = "";
if (isset($_POST['submit'])) {

    // calculating start and end time 
    $time = $_POST['time'];
    $time = strtotime($time);
//    var_dump($time);
//    var_dump(formate_date($time));
//    var_dump(time());
//    var_dump(formate_date(time()));
//    die;
    //var_dump($time);

    $start_time = $time;
    $h = $setting->hours_duration;
    $end_time = strtotime("+$h hours", $time);
    $seats = $_POST['seats'];
    $topic = $_POST['topic'];
    $descn = $_POST['descn'];
    $nickname = $_POST['nickname'];
    $invites = $_POST['invites'];
    $subtopic = $_POST['sub_cat2'];
    
    $group_id = create_new_group($user_id, $topic, $subtopic, $start_time, $end_time, $seats, $descn, $nickname, $invites, $time, $setting);
    if($group_id){
        $type = "ok";
        $msg = "Study group created successfully";
        $msg .= "<br>chat room #".$group_id." ";
        $msg .= "<br>Your Link: <a href=' ".$base_url."?action=group_detail&group=".$group_id." ' >".$base_url."?action=group_detail&group=".$group_id."</a>   ";
    }
    else{
        $type = "ok";
        $msg = "Oops somthing went wrong please try again";
    }
    
}
?>

<link type="text/css" rel="stylesheet" href="<?php echo $plugin_link . "/css/style_group_list.css" ?>" />  

<style>
    .entry-content{
        max-width: 1000px !important;
    }
</style>


<div class="outer_wrap">
    <div class="listing_title">Want to discuss a topic? Join a study group!</div>
    <div class="group_container">
        <div class="group_img">
            <img src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/group_img.png" >
        </div>
        <div class="group_img_dsc">
            &ldquo;A study group is a small group of people
            who regularly meet to discuss shared
            fields of study.&rdquo;
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="box_wrap">
            <div class="clearfix"></div>
            <div class="all_group_link_style">
                <a href="<?php echo $base_url?>" <?php if(!isset($_REQUEST['list_type'])){echo 'class="selected"';}?>>All Groups</a>&nbsp;&nbsp;
                <a href="<?php echo $base_url?>?list_type=topic&topic=<?php echo get_cat_ID('1t'); ?>" <?php if(isset($_REQUEST['list_type']) && $_REQUEST['list_type'] == 'topic' && $_REQUEST['topic'] == get_cat_ID('1t')){echo 'class="selected"';}?>>1T</a>&nbsp;&nbsp;
                <a href="<?php echo $base_url?>?list_type=topic&topic=<?php echo get_cat_ID('1p'); ?>" <?php if(isset($_REQUEST['list_type']) && $_REQUEST['list_type'] == 'topic' && $_REQUEST['topic'] == get_cat_ID('1p')){echo 'class="selected"';}?>>1P </a>&nbsp;&nbsp;
                <a href="<?php echo $base_url?>?list_type=topic&topic=<?php echo get_cat_ID('R1'); ?>" <?php if(isset($_REQUEST['list_type']) && $_REQUEST['list_type'] == 'topic' && $_REQUEST['topic'] == get_cat_ID('R1')){echo 'class="selected"';}?>>R1</a>&nbsp;&nbsp;
                <a href="<?php echo $base_url?>?list_type=topic&topic=<?php echo get_cat_ID('R2'); ?>" <?php if(isset($_REQUEST['list_type']) && $_REQUEST['list_type'] == 'topic' && $_REQUEST['topic'] == get_cat_ID('R2')){echo 'class="selected"';}?>>R2</a>&nbsp;&nbsp;
                <?php if ($user_id != 0) { ?>
                	<a href="<?php echo $base_url?>?list_type=my_group" <?php if(isset($_REQUEST['list_type']) && $_REQUEST['list_type'] == 'my_group'){echo 'class="selected"';}?>>My Groups</a>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        <div class="signin_box">
            <div class="listing_view">
                <!-- PAGER FILES -->
                <link media="print, projection, screen" type="text/css" rel="stylesheet" href="<?php echo $plugin_link . "/js/tabsorter/themes/blue/style.css" ?>" />  

                <script type="text/javascript" src="<?php echo $plugin_link . "/js/tabsorter/jquery-latest.js" ?>" ></script>
                <script type="text/javascript" src="<?php echo $plugin_link . "/js/tabsorter/jquery.tablesorter.js" ?>"></script>
                <script type="text/javascript" src="<?php echo $plugin_link . "/js/tabsorter/addons/pager/jquery.tablesorter.pager.js" ?>"></script>

                <?php
                        
                        $groups = get_all_chat_groups_frontend('');
                        if(isset($_GET['list_type']) && $_GET['list_type'] == 'my_group'){
                            $groups = get_all_chat_groups_frontend('',$list_type = "my_group" , $user_id );
                        }
                        else if (isset($_GET['list_type']) && $_GET['list_type'] == 'topic'){
                            $groups = get_all_chat_groups_frontend('',$list_type = "topic" , $_GET['topic'] );
                        }
                        ?>
                <?php if (count($groups) > 0) { ?>
                <script>
                    var ts = jQuery.noConflict();
                    ts(document).ready(function() {
                        ts("#group_list")
                                .tablesorter({widthFixed: true, widgets: ['zebra'] })
                                .tablesorterPager({
                            container: $("#pager"),
                            size: <?php echo $setting->per_page; ?>
                        });
                    });
                </script>
                <?php } ?> 

                <!-- PAGER FILES END -->
                <style>
                    .header{
                        color: #535353 !important;
                        cursor: pointer !important;
                    }
                    #content tr.odd td,#content tr.even td{
                        background: #FFF !important;
                    }
                </style>
                <table id="group_list" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:0; border:0;">
                    <thead>
                        <tr>

                            <th width="150">Time</th>
                            <th width="150">Topic</th>
                            <th>Description from creator</th>
                            <th width="100" align="center">Seats</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (count($groups) > 0) {
                            foreach ($groups as $group) {
                                ?> 
                                <tr>
                                    <td style="border:0"><?php echo formate_date($group->created_at, "M j, g:i a"); ?></td>
                                    <td style="border:0">
                                        
                                        <?php 
                                        $main_cat = get_categories('include='.$group->topic_id);
                                        $cat = get_categories('include='.$group->title);
                                        
                                        $teori_ID = get_cat_ID('Teori');
                                        $firstMovieInSubChapter = get_posts(array('numberposts' => 1, 'category__and' => array($cat[0]->cat_ID,$teori_ID ), 'orderby' => 'title', 'order' => 'ASC' ));
                                        $subCategoryLink = get_permalink($firstMovieInSubChapter[0]->ID);
                                        
                                        
                                    echo '<a href="'.$subCategoryLink.'" target="_blank" >'; echo $cat[0]->name; ?>
                                        </a>
                                    </td>
                                    <td style="border:0">
                                        <b><?php echo ($group->nickname != "" ) ? $group->nickname : $group->user_nicename; ?> </b> <?php echo trim_string($group->description, 150); ?>&nbsp;Visit:&nbsp;<a href="<?php echo $base_url . "?action=group_detail&group=" . $group->id; ?>">group room #<?php echo $group->id; ?></a>&nbsp; to register participation 
                                    </td>
                                    <td align="center" style="border:0">
                                        <?php
                                        $reg_seats = get_group_register_seats($group->id);
                                        $remain_seats = $group->total_seats - $reg_seats;
                                        echo ($remain_seats > 0 ) ? $remain_seats . " available" : "Registered";
                                        ?>
                                    </td>

                                </tr>
                                <?php
                            }
                        }
                        else{
                            echo '<tr>
<td colspan="4" align="center">
No record exist
 </td>

                                </tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <style>
                    #pager{
                        position: static !important;
                    }
                </style>
                <?php 
                        if (count($groups) > 0) { ?>
                <div class="pagination " id="pager" >

                    <form>
                        <ul style="color: #000 !important;">
                            <li><a href="#" class="first">|<</a></li>
                            <li><a href="#" class="prev">&lt;&lt;</a></li>
                            <span class="pagedisplay"></span>
                            <input type="hidden" class="pagedisplay"/>
                            <li><a href="#" class="next">&gt;&gt;</a></li>
                            <li><a href="#" class="last">&gt;|</a></li>
                        </ul>
                        <!--                        
                                                <img src="<?php echo $plugin_link . "/images" ?>/p_prev.png" class="prev"/>
                                                <input type="hidden" class="pagedisplay"/>
                                                <img src="<?php echo $plugin_link . "/images" ?>/p_next.png" class="next"/>
                                                <img src="<?php echo $plugin_link . "/images" ?>/p_last.png" class="last"/>-->
                        <input type="hidden" class="pagesize" value="<?php echo $setting->per_page; ?>" >

                    </form>

                    <!--                    <ul>
                    
                                            <li><a href="#" class="first">|<</a></li>
                                            <li><a href="#" class="prev">&lt;&lt;</a></li>
                    <?php //echo paginate_links_custom($total_rows, $per_page, $page);    ?>
                                            <li><a href="#" class="next">&gt;&gt;</a></li>
                                            <li><a href="#" class="last">&gt;|</a></li>
                                        </ul>-->
                </div>
                        <?php } ?>
            </div>

            <!--            Fancybox Start -->
            <script type="text/javascript" src="<?php echo WP_PLUGIN_URL . "/chat-groups/js/fancybox/jquery-1.9.0.min.js" ?>"></script>
            <script type="text/javascript" src="<?php echo WP_PLUGIN_URL . "/chat-groups/js/fancybox/jquery.fancybox.js?v=2.1.4" ?>" ></script>
            <link rel="stylesheet" type="text/css" href="<?php echo WP_PLUGIN_URL . "/chat-groups/js/fancybox/jquery.fancybox.css?v=2.1.4"; ?>" media="screen" />
            <script>

                $(document).ready(function() {

                    $('#new_group_popupLink, .cross_sis').click(function() {
                        $("#new_group_div").toggle();
                        $("#bottom_content").toggle();

                        $("#rsp").removeClass();
                        $("#rsp").html("");
                    });

                    // JQ UI timer
                    $(".timepicker").datetimepicker({
                        minDate: '<?php echo 0; ?>',
                        maxDate: '<?php echo $setting->days_in_calender; ?>',
                        dateFormat: 'yy-mm-dd'
                    });

                    // JQ UI slider
//                    $("#slider-range-max").slider({
//                        range: "max",
//                        min: '0',
//                        max: '<?php $setting->days_in_calender; ?>',
//                        value: 1,
//                        slide: function(event, ui) {
//                            $("#seats").val(ui.value);
//                            $("#seat_num").html(ui.value);
//                        }
//                    });
//                    $("#seats").val($("#slider-range-max").slider("value"));
//                    $("#seat_num").val($("#slider-range-max").slider("value"));


                    $('#invites_list').on("click", ".remove_invite_email", function() {
                        $(this).parent().remove();
                    });

                    $("#add_invites").click(function() {
                        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                        var email = $.trim($("#invites").val());
                        if ((email === "") || (!regex.test(email)))
                        {
                            // do something with invalid email
                        }
                        else {
                            $("#invites_list").append("<div class='invite_email' >" + email + "&nbsp;[<a href='javascript:void(0)' class='remove_invite_email'>remove</a>]<input type='hidden' name='invites[]' value='" + email + "'></div>");
                            $("#invites").val("");
                        }

                    });

                    $("#create_group_form").submit(function(e) {
                        $("#rsp").removeClass();
                        $("#rsp").html("");
                        var val_msg;
                        var flag = true;
                        if ($("#user_id").val() == 0) {
                            val_msg = "Log in to register new group ";
                            flag = false;
                        }
                        else if ($("#time").val() === "") {
                            val_msg = "Please Select time";
                            flag = false;
                        }
                        else if ($("#topic").val() === "") {
                            val_msg = "Please enter topic ";
                            flag = false;
                        }else if ($("#sub_cat2").val() === "") {
                            val_msg = "Please enter topic ";
                            flag = false;
                        }
                        else if ($("#descn").val() === "") {
                            val_msg = "Please enter description ";
                            flag = false;
                        }
                        

                        if (flag) {

                            $("#create_group_form").submit();
                        }
                        else {
                            e.preventDefault();
                            $("#rsp").html(val_msg);
                            $("#rsp").addClass("error");
                        }
                    });
                    
                    $("#topic").change(function(){
//                        $("#sub_cat1_div").html("");
//                        $("#sub_cat2_div").html("");
                        $.ajax({
                            type: "POST",
                            url: "<?php echo WP_PLUGIN_URL . "/chat-groups/topic_dropdown.php"; ?>",
                            data: {
                                id: $.trim($(this).val()),
                                type: "main"
                            },
                            dataType: "html"
                        }).success(function(rsp) {
                            if(rsp != ""){
                                $("#sub_cat1_div").html(rsp);
                            }
                            else{
                                $("#sub_cat1_div").html("No record exist");
                            }
                            
                        });
                    });
                    $("#sub_cat1_div").on("change",function(){
                        $.ajax({
                            type: "POST",
                            url: "<?php echo WP_PLUGIN_URL . "/chat-groups/topic_dropdown.php"; ?>",
                            data: {
                                id: $.trim($("#sub_cat1").val()),
                                type: "sub_cat1"
                            },
                            dataType: "html"
                        }).success(function(rsp) {
                            if(rsp != ""){
                                $("#sub_cat2_div").html(rsp);
                            }
                            else{
                                $("#sub_cat2_div").html("No record exist");
                            }
                        });
                    });

                });

            </script>
            <!--            Fancybox End-->
            <div class="miss_topic">
            </div>
            <div class="clearfix"></div>
            <div class="miss_a_topic">
                Miss a topic? Register <a id="new_group_popupLink" href="javascript://">new study group!</a>
            </div>
            <div class="clear20"></div>

            <!--   NEW GROUP PAGE-->

            <!-- JQ UI -->
            <script src="<?php echo $plugin_link . 'js/jquery-1.9.1.js' ?>"></script>
            <link rel="stylesheet" href="<?php echo $plugin_link . 'css/jquery-ui.css' ?>" />
            <script src="<?php echo $plugin_link . 'js/jquery-ui.js' ?>"></script>
            <script src="<?php echo $plugin_link . 'js/timepicker-ui.js' ?>"></script>

            <!--JQ UI end -->
            <link type="text/css" rel="stylesheet" href="<?php echo $plugin_link . 'css/style_group_new.css' ?>" /> 
            <?php 
            $class = "";
            if($type != ""){
                if($type == "ok"){
                $class = "success";
                }
                else{
                    $class = "error";
                }
                ?>
            
            <?php } ?>
            <div id="rsp" class="<?php echo $class; ?>"><?php echo $msg; ?></div>
            <div id="new_group_div">
                <form id="create_group_form" action="" method="post" >
                    <input type="hidden" id="user_id" value="<?php echo $user_id;?>" >                      
                    <table class="new_group_tbl">
                        <tbody>
                            <tr class="">
                                <td>
                                    <div style="clear:both;"></div>
                                    <span id="main_cat_div"><?php 
                                    $html = "<select class='topic_style' name='topic' id='topic' >";
                                    $html .= "<option value=''>Choose curriculum</option>";
                                    $main_categories = get_categories('include=341,13,3,130');
                                    if($main_categories){
                                        foreach ($main_categories as $main_cat) {
                                           $html .= '<option value="'.$main_cat->term_id.'"> '.$main_cat->name.'</option>'; 
                                        }
                                    }
                                    $html .= "</select>";
                                    $topic_drop_down_html = $html;
                                    echo $topic_drop_down_html ?>
                                    </span>
                                    <span id="sub_cat1_div">
                                        <select class='sub_cat1' name='sub_cat1' id='sub_cat1' >
                                            <option value=''>Choose main topic</option>
                                        </select>
                                    </span>
                                    <span id="sub_cat2_div">
                                        <select class='sub_cat2' name='sub_cat2' id='sub_cat2' >
                                            <option value=''>Choose topic</option>
                                        </select>
                                    </span>
                                </td>
                            </tr>

                            <tr class="">
                                <td>

                                    <select class="" name="seats" id="seats">
                                        <?php
                                        for ($i = $setting->max_seat; $i >= 1; $i--) {
                                            ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php } ?>

                                    </select>
                                    <div class='max_style'>max <?php echo $setting->max_seat; ?> seats in one study group</div>
                                </td>
                            </tr>
                            
                            <tr class="">
                                <td>
                                    <div style="clear:both; height:10px;"></div>
                                    <input type="text" aria-required="true" value="" class="timepicker" id="time" name="time" placeholder="Click to choose time" autocomplete="off">
                                </td>
                            </tr>

                            <tr class="">
                                <td>
                                    <textarea name="descn" id="descn" class="group_descn" maxlength="<?php echo $setting->descn_length; ?>" placeholder="Write a short txt for those that shall see the invitation..max <?php echo $setting->descn_length; ?> words "></textarea>
                                </td>
                            </tr>
							
                            <tr class="">
                            	<td>
                                	<div class="nickname_style">
                        				<input type="text" value="" name="nickname" maxlength="9" placeholder="Type in your preferred nickname">
                    				</div>
                                </td>
                            </tr>





                        </tbody>
                    </table>

                    <div class="invite_div">
                        <span>Want to give someone an early invite, <?php echo $setting->time_to_email_before; ?> min before the others?</span>
                        <input type="text" value="" id="invites" placeholder="type email here">
                        <input type="button" value="Add" id="add_invites" class="orange_btn" >
                    </div>
                    <div style="clear: both;"></div>
                    <div id="invites_list"><span class="on_list">On early email list:</span> </div>

                    <div class="sumbit_btn"><input type="submit" value="Create group" class="button button-primary create_study_group" id="create_group" name="submit">
                        <div class="text_style_new"><?php echo $setting->time_to_email_before; ?> minutes after group is created email invitation is sent to all users</div></div>
                </form>

            </div>

            <div id="bottom_content">
                <div class="group_img_dsc" style="padding:45px 0 0 !important;">
                    <p><span>How are the study groups arranged?</span> The study groups are
                        held on individual chat room, at a planned time, and usually lasts
                        about one hour. The chat room opens 15 before the startup time,
                        so that all participants start the discussion at the same time. All
                        dialog is deleted after 24 hours.
                    </p>
                    <p>
                        <span>Who participates in the study groups?</span> To participate you have
                        to be a registered member of mattevideo.no. You can press the
                        link &ldquo;group room&rdquo; to see who is listed to participate in the
                        different groups.
                    </p>
                </div>
                <div class="group_img" style="text-align:right;">
                    <img src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/chatroom.png" >
                </div>
            </div>

            <!--            NEW GROUP PAGE end-->



            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
