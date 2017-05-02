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

	//print_r($_POST);exit();
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
    $invites_nick = $_POST['invites_nick'];
    $subtopic = $_POST['sub_cat2'];
    $current_time = time();
    
    $group_id = create_new_group($user_id, $topic, $subtopic, $start_time, $end_time, $seats, $descn, $nickname, $invites, $invites_nick, $current_time, $setting);
    if($group_id){
        $type = "ok";
        $msg = $group_id;
		wp_redirect( $base_url."?msg=".$msg."&type=".$type);
    }
    else{
        $type = "nook";
		wp_redirect( $base_url."?type=".$type);
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
    <div class="listing_title">Lære i fellesskap? Bli med i en kollokviegruppe!</div>
    <div class="group_container">
        <div class="group_img">
            <img src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/group_img.png" width="360" />
        </div>
        <div class="group_img_dsc">
            &ldquo;Kollokviegruppe; en liten gruppe med 3-5 studenter som møtes for å diskutere et felles fagfelt.&rdquo;
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="box_wrap">
            <div class="clearfix"></div>
            <div class="all_group_link_style">
                <a href="<?php echo $base_url?>" <?php if(!isset($_REQUEST['list_type'])){echo 'class="selected"';}?>>Alle grupper</a>&nbsp;&nbsp;
                <a href="<?php echo $base_url?>?list_type=topic&topic=<?php echo get_cat_ID('1t'); ?>" <?php if(isset($_REQUEST['list_type']) && $_REQUEST['list_type'] == 'topic' && $_REQUEST['topic'] == get_cat_ID('1t')){echo 'class="selected"';}?>>1T</a>&nbsp;&nbsp;
                <a href="<?php echo $base_url?>?list_type=topic&topic=<?php echo get_cat_ID('1p'); ?>" <?php if(isset($_REQUEST['list_type']) && $_REQUEST['list_type'] == 'topic' && $_REQUEST['topic'] == get_cat_ID('1p')){echo 'class="selected"';}?>>1P </a>&nbsp;&nbsp;
                <a href="<?php echo $base_url?>?list_type=topic&topic=<?php echo get_cat_ID('R1'); ?>" <?php if(isset($_REQUEST['list_type']) && $_REQUEST['list_type'] == 'topic' && $_REQUEST['topic'] == get_cat_ID('R1')){echo 'class="selected"';}?>>R1</a>&nbsp;&nbsp;
                <a href="<?php echo $base_url?>?list_type=topic&topic=<?php echo get_cat_ID('R2'); ?>" <?php if(isset($_REQUEST['list_type']) && $_REQUEST['list_type'] == 'topic' && $_REQUEST['topic'] == get_cat_ID('R2')){echo 'class="selected"';}?>>R2</a>&nbsp;&nbsp;
                <?php if ($user_id != 0) { ?>
                	<a href="<?php echo $base_url?>?list_type=my_group" <?php if(isset($_REQUEST['list_type']) && $_REQUEST['list_type'] == 'my_group'){echo 'class="selected"';}?>>Mine grupper</a>
                <?php } ?>
            </div>
            <div class="miss_a_topic">
                Savner tema? Inviter til <a id="new_group_popupLink" href="#new_group_popupLink">ny kollokviegruppe!</a>
            </div>
            <div class="clearfix"></div>
            <div class="miss_topic" style="margin-top:15px; padding:0;"></div>
            <div class="clearfix"></div>
        <div class="signin_box">
            <div class="listing_view">
                <!-- PAGER FILES -->
                <link media="print, projection, screen" type="text/css" rel="stylesheet" href="<?php echo $plugin_link . "/js/tabsorter/themes/blue/style.css" ?>" />  

                <script type="text/javascript" src="<?php echo $plugin_link . "/js/tabsorter/jquery-latest.js" ?>" ></script>
                <script type="text/javascript" src="<?php echo $plugin_link . "/js/tabsorter/jquery.tablesorter.js" ?>"></script>
                <script type="text/javascript" src="<?php echo $plugin_link . "/js/tabsorter/addons/pager/jquery.tablesorter.pager.js" ?>"></script>

                <?php
                        
                        $groups = get_all_chat_groups_frontend(true);
                        if(isset($_GET['list_type']) && $_GET['list_type'] == 'my_group'){
                            $groups = get_all_chat_groups_frontend(true,"my_group" , $user_id );
                        }
                        else if (isset($_GET['list_type']) && $_GET['list_type'] == 'topic'){
                            $groups = get_all_chat_groups_frontend(true,"topic" , $_GET['topic'] );
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

                            <th width="150">Tidspunkt</th>
                            <th width="150">Tema</th>
                            <th>Beskrivelse</th>
                            <th width="100" align="center">Plasser</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (count($groups) > 0) {
                            foreach ($groups as $group) {
                                ?> 
                                <tr>
                                    <td style="border:0"><?php echo formate_date($group->start_time); ?></td>
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
                                        <b><?php echo ($group->nickname != "" ) ? $group->nickname : $group->user_nicename; ?> </b> <?php echo trim_string($group->description, 150); ?>&nbsp;Besøk&nbsp;<a href="<?php echo $base_url . "?action=group_detail&group=" . $group->id; ?>" target="_blank">grupperom #<?php echo $group->id; ?></a>&nbsp; for å registrere deltakelse.</td>
                                    <td align="center" style="border:0">
                                        <?php
                                        $reg_seats = get_group_register_seats($group->id);
                                        $remain_seats = $group->total_seats - $reg_seats;
                                        echo ($remain_seats > 0 ) ? $remain_seats . " tilgjengelig" : "registrert";
                                        ?>
                                    </td>

                                </tr>
                                <?php
                            }
                        }
                        else{
                            echo '<tr>
<td colspan="4" align="center">
Vær den første til å invitere til kollokviegruppe!
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
                var nicks_email = new Array();
				function addNick(){
                    $("#rsp").removeClass('error');
                    $("#rsp").html("");
					var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					var email = $.trim($("#invites").val());
					if ((email === "") || (!regex.test(email)) || nicks_email.indexOf(email) > -1)
					{
                        if(nicks_email.indexOf(email) > -1){
                            console.log("alredy exist");
                        }
						// do something with invalid email
					}
					else {
                        var invites_nick = $("#invites_nick").val()
                        if(invites_nick == ""){
                            $("#rsp").addClass("error");
                            $("#rsp").html("kallenavn venn pakrevd");
                            return;
                        }
						$("#invites_list").append("<div class='invite_email' >" + email + "&nbsp;{"+invites_nick+"}&nbsp;[<a href='javascript:void(0)' class='remove_invite_email'>fjerne</a>]<input type='hidden' name='invites[]' value='" + email + "'><input type='hidden' name='invites_nick[]' value='" + invites_nick + "'></div>");
						$("#invites").val("");
						$("#invites_nick").val("");
                        nicks_email.push(email);
					}
				}
                $(document).ready(function() {

                    $('#new_group_popupLink, .cross_sis').click(function() {
                        $("#new_group_div").toggle();
                        $("#bottom_content").toggle();

                        $("#rsp").removeClass('error');
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

					$('#invites').keyup(function(e) {
					   if (e.which == 13) {
						  addNick();
					   }
					});
                    $('#invites_nick').keyup(function(e) {
					   if (e.which == 13) {
						  addNick();
					   }
					});
                    $('#invites_list').on("click", ".remove_invite_email", function() {
                        $(this).parent().remove();
                    });

                    $("#add_invites").click(function() {
                        addNick();
                    });

                    $("#create_group_form").submit(function(e) {
						console.log('test');
                        $("#rsp").removeClass('error');
                        $("#rsp").html("");
                        var val_msg;
                        var flag = true;
                        if ($("#user_id").val() == 0) {
                            val_msg = "Logg inn for å registrere ny gruppe ";
                            flag = false;
                        }
                        else if ($("#time").val() === "") {
                            val_msg = "Vennligst velg tid";
                            flag = false;
                        }
                        else if ($("#topic").val() === "") {
                            val_msg = "Vennligst skriv inn emne ";
                            flag = false;
                        }else if ($("#sub_cat2").val() === "") {
                            val_msg = "Vennligst skriv inn emne ";
                            flag = false;
                        }
                        else if ($("#descn").val() === "") {
                            val_msg = "Vennligst fyll inn beskrivelse ";
                            flag = false;
                        }
                        

                        if (flag) {
                            $("#create_group_form").submit();
                        }
                        else {
                            $("#rsp").html(val_msg);
                            $("#rsp").addClass("error");
							e.preventDefault();
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
                                $("#sub_cat1_div").html("Ingen rekord eksisterer");
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
                                $("#sub_cat2_div").html("Ingen rekord eksisterer");
                            }
                        });
                    });

                });

            </script>
            <!--            Fancybox End-->
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
            if(isset($_GET['type']) && $_GET['type'] != ""){
                if($_GET['type'] == "ok"){
                	$class = "success";
                }
                else{
                    $class = "error";
                }
                ?>
            
            <?php } ?>
            <?php if(isset($_GET['type'])){
					if($class == 'success'){
						$group_id = $_GET['msg'];
						$msg = "Kollokviegruppen er nå opprettet, og brukere kan melde seg på. Link til <a href=' ".$base_url."?action=group_detail&group=".$group_id." ' >Grupperom #".$group_id."</a>";
					}else{
						$msg = "Oops somthing gikk galt prøv igjen";
					}
				?>
            	<div class="<?php echo $class; ?>"><?php echo $msg; ?></div>
            <?php }?>
            <div id="rsp"></div>
            <div id="new_group_div">
                <form id="create_group_form" action="" method="post" onkeypress="return event.keyCode != 13;">
                    <input type="hidden" id="user_id" value="<?php echo $user_id;?>" >                      
                    <table class="new_group_tbl">
                        <tbody>
                            <tr class="">
                                <td>
                                    <div style="clear:both;"></div>
                                    <span id="main_cat_div"><?php 
                                    $html = "<select class='topic_style' name='topic' id='topic' >";
                                    $html .= "<option value=''>Pensum</option>";
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
                                            <option value=''>Hovedtema</option>
                                        </select>
                                    </span>
                                    <span id="sub_cat2_div">
                                        <select class='sub_cat2' name='sub_cat2' id='sub_cat2' >
                                            <option value=''>Tema</option>
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
                                    <div class='max_style'>Antall plasser (max <?php echo $setting->max_seat; ?>)</div>
                                </td>
                            </tr>
                            
                            <tr class="">
                                <td>
                                    <div style="clear:both; height:10px;"></div>
                                    <input type="text" aria-required="true" value="" class="timepicker" id="time" name="time" placeholder="Tidspunkt for kollokvien" autocomplete="off">
                                </td>
                            </tr>

                            <tr class="">
                                <td>
                                    <textarea name="descn" id="descn" class="group_descn" maxlength="<?php echo $setting->descn_length; ?>" placeholder="Skriv en kort tekst til de som skal se invitasjonen..max <?php echo $setting->descn_length; ?> ord"></textarea>
                                </td>
                            </tr>
							
                            <tr class="">
                            	<td>
                                	<div class="nickname_style">
                        				<input type="text" value="" name="nickname" maxlength="9" placeholder="Ditt kallenavn" />
                    				</div>
                                </td>
                            </tr>





                        </tbody>
                    </table>

                    <div class="invite_div">
                        <span>Holde av plass til en venn? NB: må være registrert bruker.<?php //echo $setting->time_to_email_before; ?></span>
                        <input type="text" value="" id="invites" placeholder="epostadresse til venn" />
                        <input type="text" value="" id="invites_nick" placeholder="kallenavn venn" />
                        <input type="button" value="Legg til" id="add_invites" class="orange_btn" />
                    </div>
                    <div style="clear: both;"></div>
                    <div id="invites_list"><span class="on_list">Plass holdes av til:</span> </div>

                    <div class="sumbit_btn"><input type="submit" value="Opprett gruppe" class="button button-primary create_study_group" id="create_group" name="submit">
                        <div class="text_style_new">Gruppen publiseres og invitasjonsmail sendes til alle mattevideobrukere <?php echo $setting->time_to_email_before; ?> min etter opprettelse</div></div>
                </form>

            </div>

            <div id="bottom_content">
                <div class="group_img_dsc" style="padding:45px 0 0 !important;">
                	<p>
                    	<span>Hvem kan delta?</span> Alle mattevideo medlemmer kan melde seg på ledige plasser i en kollokviegruppe. Alle medlemmer kan også invitere til nye grupper.
                    </p>
                    <p>
                    	<span>Hvorfor delta?</span> Med kollokviegrupper kan medlemmer av mattevideo jobbe sammen for å lære seg et tema. Mange av våre medlemmer er svært seriøse studenter, og i fellesskap blir læring enklere.
                    </p>
                    <p>
                    	<span>Hvor holdes gruppene?</span> Gruppene holdes på individuelle "grupperom", der man diskuterer et aktuelt tema på et forum. Forumet åpner til en avtalt tid og er åpent i 3 timer. All dialog blir slettet etter 24 timer.
                    </p>
                </div>
                <div class="group_img" style="text-align:right; padding:20px 0;">
                    <img src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/chatroom.png" width="390" >
                </div>
            </div>

            <!--            NEW GROUP PAGE end-->



            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
