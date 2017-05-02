<?php 
	@include_once("../../../wp-config.php"); 
	//echo date_default_timezone_get();
?>
<link type="text/css" rel="stylesheet" href="<?php echo WP_PLUGIN_URL . "/chat-groups/css/style_chat.css" ?>" />  
<script>
    var PLUGIN_PATH = '<?php echo WP_PLUGIN_URL . "/chat-groups/"; ?>';
</script>


<script src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>js/MathJax/MathJax.js">
    MathJax.Hub.Config({
        extensions: ["tex2jax.js", "TeX/AMSmath.js", "TeX/AMSsymbols.js"],
        jax: ["input/TeX", "output/HTML-CSS"],
        tex2jax: {inlineMath: [['$', '$'], ["\\(", "\\)"]],
        }
    });
</script>
<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script src="http://js.pusher.com/2.1/pusher.min.js"></script>
<script src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>pusher/src/js/PusherChatWidget.js"></script>
<link href="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>pusher/src/pusher-chat-widget.css" rel="stylesheet" type="text/css" />
<script>
$(function() {     
  var pusher = new Pusher('5c441bb75eb209d53123');
  var chatWidget = new PusherChatWidget(pusher, {
    chatEndPoint: '<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>pusher/src/php/chat.php'
  });
});
</script>

<?php
//var_dump($group); 
?>

<div class="group_detail">

    <div class="listing_title">Grupperom #<?php echo $group_id ?></div>
    <div class="group_img_dsc">
        <p class="">
            Starttid: <?php echo formate_date($group->start_time) . "<br>"; ?>
            <?php
            $cat = get_categories('include='.$group->title);
            $teori_ID = get_cat_ID('Teori');
            $firstMovieInSubChapter = get_posts(array('numberposts' => 1, 'category__and' => array($cat[0]->cat_ID,$teori_ID ), 'orderby' => 'title', 'order' => 'ASC' ));
            $subCategoryLink = get_permalink($firstMovieInSubChapter[0]->ID);
            ?>
            Tema: <a href="<?php echo $subCategoryLink; ?>" target="_blank" style="color: #df8a2f;"> <?php
                $cat = get_categories('include=' . $group->title);
                //echo "<pre>";var_dump($cat);
                echo $cat[0]->name;
                echo"<br><br>";
                ?></a>
        </p>
        <div class="descn_style"><?php echo $group->description . "<br>"; ?></div>
    </div>
    <div class="register_div">
<?php if (user_exist_group($group_id, $user_id)) { ?>
            <div class="register_div_inner">
                Hei <?php echo $nickname; ?>! <br>Du er påmeldt som deltaker i denne kollokviegruppen:)<br /><a href="javascript: void(0)" id="unreg_user_for_chat">Melde deg av? Klikk her.</a> 
            </div>


<?php } else {
    ?>
            <p id="reg_user_for_chat_rsp">Melde deg på? <?php echo $remaining_seats; ?> av <?php echo $group->available_seats; ?> plasser er ledige!</p>
            <input type="text" id="nickname_reg" maxlength="9" placeholder="Skriv inn din foretrukne kallenavn">
            <input type="button" id="reg_user_for_chat" value="Påmelding" class="create_study_group">
<?php } ?>
    </div>

    <div class="clr"></div>    
</div>
<div class="clr"></div>
<div id="rsp" class="" style="width:81%; margin:0 auto 10px;"></div>
<div class="clr"></div>  
<div id="wrapper_chat">  
    <div id="menu_chat">  
        <?php if ($chat_disable == 0) { ?>
        	<div class="left_chat_menu">
            <?php if ($user_id != 0) { // if user is logged in ?>
                <p class="praticipant_style">
                    <strong>Deltakere:</strong>     
                    <?php
                    $users = get_chat_group_users($group_id);
                    if (count($users) > 0) {
                        foreach ($users as $i => $user) {
                            $user_name = ($user->nickname != "") ? $user->nickname : $user->user_nicename;
                            echo ($i == 0) ? "" : ",&nbsp;";
                            echo "<span>" . $user_name . "</span>";
                        }
                    } else {
                        echo "Ingen bruker nå";
                    }
                    ?>
                </p>
            <?php } else {  // if user is not logged in 
                ?>
                <p class="praticipant_style">
                    <strong>deltakere:</strong>     
                    <?php
                    $users = get_chat_group_users($group_id);
                    if (count($users) > 0) {
                        foreach ($users as $i => $user) {
                            $user_name = ($user->nickname != "") ? $user->nickname : $user->user_nicename;
                            echo ($i == 0) ? "" : ",&nbsp;";
                            echo "<span>" . $user_name . "</span>";
                        }
                    } else {
                        echo "Ingen bruker nå";
                    }
                    ?>
                </p>
    <?php }?>
    </div>
<?php } else {
    ?>
            <p class="welcome_chat">
            	<strong>Deltakere:</strong>     
                <?php
                $users = get_chat_group_users($group_id);
                if (count($users) > 0) {
                    foreach ($users as $i => $user) {
                        $user_name = ($user->nickname != "") ? $user->nickname : $user->user_nicename;
                        echo ($i == 0) ? "" : ",&nbsp;";
                        echo $user_name;
                    }
                } else {
                    echo "Ingen bruker nå";
                }
                ?>
                <br />
                Grupperommet er ikke åpent enda. Rommet vil være åpnet fra: <?php echo formate_date($start_time) . " " . " til " . formate_date($end_time) . " "; ?>
                <br>Nå er klokken:  <?php echo formate_date($now_time); ?>
            </p>  
<?php } ?>

        <p class="logout_chat">
            <button id="menu_btn_time" ><img class="right_button"  src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/btn2.png" alt="\alpha"></button>
            <button id="menu_btn_user"><img class="right_button" src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/btn3.png" alt="\alpha"></button>
        </p>  
        <div class="clr"></div>  
    </div>  
    <!--    <div id="height_fix" style="float: left; height: auto;">-->
    <div id="main_chat_window">
        <div id="chatbox">

<!--        <div class="chat_msg"><span class="time">12:57:02</span><span class="userName" >Ksondresen:</span>  <span class="message">Ksondresen has join the chanel  </span></div>
<div class="chat_msg"><span class="time">12:57:03</span><span class="userName" >Ksondresen:</span>  <span class="message">Hi math . some chat  </span> </div>
<div class="chat_msg"><span class="time">12:57:04</span><span class="userName" >Ksondresen:</span>  <span class="message">Sed in tellus vitae quam blandit sollicitudin. Nullam in imperdiet dolor, a tristique felis! Ut nec dolor urna. Cras varius metus nec leo convallis imperdiet. Etiam vehicula  </span></div>-->
        </div>
        <!--    </div>-->
        <div id="user_list">
            <?php
            $users = get_chat_group_users($group_id);
            //var_dump($users); 
            if (count($users) > 0) {
                foreach ($users as $user) {
                    $user_name = ($user->nickname != "") ? $user->nickname : $user->user_nicename;
                    echo "<span>" . $user_name . "</span>";
                }
            } else {
                echo "Ingen bruker nå";
            }
            ?>
        </div>
    </div>


    <div class="chat_txtField">


            <div style="float:right; width:300px; position:relative;">
                <div id="hover"></div>
                <div>
                    <div class="panel" id="panel13" style="height: 34px; overflow: hidden;"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/chat-groups/css/images/spaces.gif" title="Spaces" alt="Spaces Panel" usemap="#spaces_map" height="68" border="0" width="31">
                        <map name="spaces_map" id="spaces_map">
                            <area class="chat_math_symbols" shape="rect" id="space1" title="Single Space" coords="0,0,28,14">
                            <area class="chat_math_symbols" shape="rect" id="space2" title="Thin space" coords="0,17,28,31">
                            <area class="chat_math_symbols" shape="rect" id="space3" title="Wider space " coords="0,34,28,48">
                            <area class="chat_math_symbols" shape="rect" id="space4" title="Large spaces" coords="0,51,28,65">
                        </map>
                    </div>
                    <div class="panel" id="panel4" style="height: 34px; overflow: hidden;"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/chat-groups/css/images/binary.gif" title="Binary" alt="Binary Panel" usemap="#binary_map" height="238" border="0" width="68">
                        <map name="binary_map" id="binary_map">
                            <area class="chat_math_symbols" title="\pm" shape="rect" id="\pm" coords="0,0,14,14" >
                            <area class="chat_math_symbols" title="\mp" shape="rect" id="\mp" coords="0,17,14,31">
                            <area class="chat_math_symbols" title="\times" shape="rect" id="\times" coords="0,34,14,48">
                            <area class="chat_math_symbols" title="\ast" shape="rect" id="\ast" coords="0,51,14,65">
                            <area class="chat_math_symbols" title="\div" shape="rect" id="\div" coords="0,68,14,82">
                            <area class="chat_math_symbols" title="\setminus" shape="rect" id="\setminus" coords="0,85,14,99">
                            <area class="chat_math_symbols" title="\dotplus" shape="rect" id="\dotplus" coords="0,102,14,116">
                            <area class="chat_math_symbols" title="\amalg" shape="rect" id="\amalg" coords="0,119,14,133">
                            <area class="chat_math_symbols" title="\dagger" shape="rect" id="\dagger" coords="0,136,14,150">
                            <area class="chat_math_symbols" title="\ddagger" shape="rect" id="\ddagger" coords="0,153,14,167">
                            <area class="chat_math_symbols" title="\wr" shape="rect" id="\wr" coords="0,170,14,184">
                            <area class="chat_math_symbols" title="\diamond" shape="rect" id="\diamond" coords="0,187,14,201">
                            <area class="chat_math_symbols" title="\circledcirc" shape="rect" id="\circledcirc" coords="0,204,14,218">
                            <area class="chat_math_symbols" title="\circledast" shape="rect" id="\circledast" coords="0,221,14,235">
                            <area class="chat_math_symbols" title="\cap" shape="rect" id="\cap" coords="17,0,31,14">
                            <area class="chat_math_symbols" title="\Cap" shape="rect" id="\Cap" coords="17,17,31,31">
                            <area class="chat_math_symbols" title="\sqcap" shape="rect" id="\sqcap" coords="17,34,31,48">
                            <area class="chat_math_symbols" title="\wedge" shape="rect" id="\wedge" coords="17,51,31,65">
                            <area class="chat_math_symbols" title="\barwedge" shape="rect" id="\barwedge" coords="17,68,31,82">
                            <area class="chat_math_symbols" title="\triangleleft" shape="rect" id="\triangleleft" coords="17,85,31,99">
                            <area class="chat_math_symbols" title="\lozenge" shape="rect" id="\lozenge" coords="17,102,31,116">
                            <area class="chat_math_symbols" title="\circ" shape="rect" id="\circ" coords="17,119,31,133">
                            <area class="chat_math_symbols" title="\square" shape="rect" id="\square" coords="17,136,31,150">
                            <area class="chat_math_symbols" title="\triangle" shape="rect" id="\triangle" coords="17,153,31,167">
                            <area class="chat_math_symbols" title="\triangledown" shape="rect" id="\triangledown" coords="17,170,31,184">
                            <area class="chat_math_symbols" title="\ominus" shape="rect" id="\ominus" coords="17,187,31,201">
                            <area class="chat_math_symbols" title="\oslash" shape="rect" id="\oslash" coords="17,204,31,218">
                            <area class="chat_math_symbols" title="\circleddash" shape="rect" id="\circleddash" coords="17,221,31,235">
                            <area class="chat_math_symbols" title="\cup" shape="rect" id="\cup" coords="34,0,48,14">
                            <area class="chat_math_symbols" title="\Cup" shape="rect" id="\Cup" coords="34,17,48,31">
                            <area class="chat_math_symbols" title="\sqcup" shape="rect" id="\sqcup" coords="34,34,48,48">
                            <area class="chat_math_symbols" title="\vee" shape="rect" id="\vee" coords="34,51,48,65">
                            <area class="chat_math_symbols" title="\veebar" shape="rect" id="\veebar" coords="34,68,48,82">
                            <area class="chat_math_symbols" title="\triangleright" shape="rect" id="\triangleright" coords="34,85,48,99">
                            <area class="chat_math_symbols" title="\blacklozenge" shape="rect" id="\blacklozenge" coords="34,102,48,116">
                            <area class="chat_math_symbols" title="\bullet" shape="rect" id="\bullet" coords="34,119,48,133">
                            <area class="chat_math_symbols" title="\blacksquare" shape="rect" id="\blacksquare" coords="34,136,48,150">
                            <area class="chat_math_symbols" title="\blacktriangle" shape="rect" id="\blacktriangle" coords="34,153,48,167">
                            <area class="chat_math_symbols" title="\blacktriangledown" shape="rect" id="\blacktriangledown" coords="34,170,48,184">
                            <area class="chat_math_symbols" title="\oplus" shape="rect" id="\oplus" coords="34,187,48,201">
                            <area class="chat_math_symbols" title="\otimes" shape="rect" id="\otimes" coords="34,204,48,218">
                            <area class="chat_math_symbols" title="\odot" shape="rect" id="\odot" coords="34,221,48,235">
                            <area class="chat_math_symbols" title="\cdot" shape="rect" id="\cdot" coords="51,0,65,14">
                            <area class="chat_math_symbols" title="\uplus" shape="rect" id="\uplus" coords="51,17,65,31">
                            <area class="chat_math_symbols" title="\bigsqcup" shape="rect" id="\bigsqcup" coords="51,34,65,48">
                            <area class="chat_math_symbols" title="\bigtriangleup" shape="rect" id="\bigtriangleup" coords="51,51,65,65">
                            <area class="chat_math_symbols" title="\bigtriangledown" shape="rect" id="\bigtriangledown" coords="51,68,65,82">
                            <area class="chat_math_symbols" title="\star" shape="rect" id="\star" coords="51,85,65,99">
                            <area class="chat_math_symbols" title="\bigstar" shape="rect" id="\bigstar" coords="51,102,65,116">
                            <area class="chat_math_symbols" title="\bigcirc" shape="rect" id="\bigcirc" coords="51,119,65,133">
                            <area class="chat_math_symbols" title="\bigoplus" shape="rect" id="\bigoplus" coords="51,136,65,150">
                            <area class="chat_math_symbols" title="\bigotimes" shape="rect" id="\bigotimes" coords="51,153,65,167">
                            <area class="chat_math_symbols" title="\bigodot" shape="rect" id="\bigodot" coords="51,170,65,184">
                        </map>
                    </div>
                    <div class="panel" id="panel16" style="height: 34px; overflow: hidden;"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/chat-groups/css/images/symbols.gif" title="Symbols" alt="Symbols Panel" usemap="#symbols_map" height="136" border="0" width="68">
                        <map name="symbols_map" id="symbols_map">
                            <area class="chat_math_symbols" shape="rect" id="\therefore" title="therefore" coords="0,0,14,14">
                            <area class="chat_math_symbols" shape="rect" id="\because" title="because" coords="0,17,14,31">
                            <area class="chat_math_symbols" shape="rect" id="\cdots" title="horizontal dots" coords="0,34,14,48">
                            <area class="chat_math_symbols" shape="rect" id="\ddots" title="diagonal dots" coords="0,51,14,65">
                            <area class="chat_math_symbols" shape="rect" id="\vdots" title="vertical dots" coords="0,68,14,82">
                            <area class="chat_math_symbols" shape="rect" id="\S" title="section" coords="0,85,14,99">
                            <area class="chat_math_symbols" shape="rect" id="para" title="paragraph" coords="0,102,14,116">
                            <area class="chat_math_symbols" shape="rect" id="copy" title="copyright" coords="0,119,14,133">
                            <area class="chat_math_symbols" shape="rect" id="\partial" title="partial" coords="17,0,31,14">
                            <area class="chat_math_symbols" title="\imath" id="\imath" shape="rect" coords="17,17,31,31">
                            <area class="chat_math_symbols" title="\jmath" shape="rect" id="\jmath" coords="17,34,31,48">
                            <area class="chat_math_symbols" shape="rect" id="\Re" title="real" coords="17,51,31,65">
                            <area class="chat_math_symbols" shape="rect" id="\Im" title="imaginary" coords="17,68,31,82">
                            <area class="chat_math_symbols" title="\forall" shape="rect" id="\forall" coords="17,85,31,99">
                            <area class="chat_math_symbols" title="\exists" shape="rect" id="\exists" coords="17,102,31,116">
                            <area class="chat_math_symbols" title="\top" shape="rect" id="\top" coords="17,119,31,133">
                            <area class="chat_math_symbols" shape="rect" id="\mathbb{P}" title="prime" coords="34,0,48,14">
                            <area class="chat_math_symbols" shape="rect" id="\mathbb{N}" title="natural" coords="34,17,48,31">
                            <area class="chat_math_symbols" shape="rect" id="\mathbb{Z}" title="integers" coords="34,34,48,48">
                            <area class="chat_math_symbols" shape="rect" id="\mathbb{I}" title="irrational" coords="34,51,48,65">
                            <area class="chat_math_symbols" shape="rect" id="\mathbb{Q}" title="rational" coords="34,68,48,82">
                            <area class="chat_math_symbols" shape="rect" id="\mathbb{R}" title="real" coords="34,85,48,99">
                            <area class="chat_math_symbols" shape="rect" id="\mathbb{C}" title="complex" coords="34,102,48,116">
                            <area class="chat_math_symbols" title="\angle" shape="rect" id="\angle" coords="51,0,65,14">
                            <area class="chat_math_symbols" title="\measuredangle" shape="rect" id="\measuredangle" coords="51,17,65,31">
                            <area class="chat_math_symbols" title="\sphericalangle" shape="rect" id="\sphericalangle" coords="51,34,65,48">
                            <area class="chat_math_symbols" title="\varnothing" shape="rect" id="\varnothing" coords="51,51,65,65">
                            <area class="chat_math_symbols" title="\infty" shape="rect" id="\infty" coords="51,68,65,82">
                            <area class="chat_math_symbols" title="\mho" shape="rect" id="\mho" coords="51,85,65,99">
                            <area class="chat_math_symbols" title="\wp" shape="rect" id="\wp" coords="51,102,65,116">
                        </map>
                    </div>
                    <div style="height:10px;"></div>
                    <div class="panel" id="panel15" style="height: 34px; overflow: hidden;"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/chat-groups/css/images/subsupset.gif" title="Subsupset" alt="Subsupset Panel" usemap="#subsupset_map" height="153" border="0" width="34">
                        <map name="subsupset_map" id="subsupset_map">
                            <area class="chat_math_symbols" title="\sqsubset" shape="rect" id="\sqsubset" coords="0,0,14,14">
                            <area class="chat_math_symbols" title="\sqsubseteq" shape="rect" id="\sqsubseteq" coords="0,17,14,31">
                            <area class="chat_math_symbols" title="\subset" shape="rect" id="\subset" coords="0,34,14,48">
                            <area class="chat_math_symbols" title="\subseteq" shape="rect" id="\subseteq" coords="0,51,14,65">
                            <area class="chat_math_symbols" title="\nsubseteq" shape="rect" id="\nsubseteq" coords="0,68,14,82">
                            <area class="chat_math_symbols" title="\subseteqq" shape="rect" id="\subseteqq" coords="0,85,14,99">
                            <area class="chat_math_symbols" title="\nsubseteq" shape="rect" id="\nsubseteq" coords="0,102,14,116">
                            <area class="chat_math_symbols" title="\in" shape="rect" id="\in" coords="0,119,14,133">
                            <area class="chat_math_symbols" title="\notin" shape="rect" id="\notin" coords="0,136,14,150">
                            <area class="chat_math_symbols" title="\sqsupset" shape="rect" id="\sqsupset" coords="17,0,31,14">
                            <area class="chat_math_symbols" title="\sqsupseteq" shape="rect" id="\sqsupseteq" coords="17,17,31,31">
                            <area class="chat_math_symbols" title="\supset" shape="rect" id="\supset" coords="17,34,31,48">
                            <area class="chat_math_symbols" title="\supseteq" shape="rect" id="\supseteq" coords="17,51,31,65">
                            <area class="chat_math_symbols" title="\nsupseteq" shape="rect" id="\nsupseteq" coords="17,68,31,82">
                            <area class="chat_math_symbols" title="\supseteqq" shape="rect" id="\supseteqq" coords="17,85,31,99">
                            <area class="chat_math_symbols" title="\nsupseteqq" shape="rect" id="\nsupseteqq" coords="17,102,31,116">
                            <area class="chat_math_symbols" title="\ni" shape="rect" id="\ni" coords="17,119,31,133">
                        </map>
                    </div>
                    <div class="panel" id="panel6" style="height: 34px; overflow: hidden;"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/chat-groups/css/images/foreign.gif" title="Foreign" alt="Foreign Panel" usemap="#foreign_map" height="136" border="0" width="34">
                        <map name="foreign_map" id="foreign_map">
                            <area class="chat_math_symbols" title="\aa" shape="rect" id="aa" coords="0,0,14,14">
                            <area class="chat_math_symbols" title="\ae" shape="rect" id="ae" coords="0,17,14,31">
                            <area class="chat_math_symbols" title="\l" shape="rect" id="l" coords="0,34,14,48">
                            <area class="chat_math_symbols" title="\o" shape="rect" id="o" coords="0,51,14,65">
                            <area class="chat_math_symbols" title="\oe" shape="rect" id="oe" coords="0,68,14,82">
                            <area class="chat_math_symbols" title="\ss" shape="rect" id="ss1" coords="0,85,14,99">
                            <area class="chat_math_symbols" shape="rect" id="dollar" title="Dollar" coords="0,102,14,116">
                            <area class="chat_math_symbols" shape="rect" id="cent" title="Cent" coords="0,119,14,133">
                            <area class="chat_math_symbols" title="\AA" shape="rect" id="aa2" coords="17,0,31,14">
                            <area class="chat_math_symbols" title="\AE" shape="rect" id="ae2" coords="17,17,31,31">
                            <area class="chat_math_symbols" title="\L" shape="rect" id="L" coords="17,34,31,48">
                            <area class="chat_math_symbols" title="\O" shape="rect" id="O" coords="17,51,31,65">
                            <area class="chat_math_symbols" title="\OE" shape="rect" id="oe2" coords="17,68,31,82">
                            <area class="chat_math_symbols" title="\SS" shape="rect" id="SS" coords="17,85,31,99">
                            <area class="chat_math_symbols" shape="rect" id="pound" title="Pound" coords="17,102,31,116">
                            <area class="chat_math_symbols" shape="rect" id="euro" title="Euro" coords="17,119,31,133">
                        </map>
                    </div>
                    <div class="panel" id="panel1" style="height: 34px; overflow: hidden;"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/chat-groups/css/images/accents.gif" title="Accents" alt="Accents Panel" usemap="#accents_map" height="119" border="0" width="34">
                        <map name="accents_map" id="accents_map">
                            <area class="chat_math_symbols" title="a'" shape="rect" id="{a'}" coords="0,0,14,14">
                                                                            <area class="chat_math_symbols" title="\dot{a}" shape="rect" id="\dot{a}" coords="0,17,14,31">
                                                                            <area class="chat_math_symbols" title="\hat{a}" shape="rect" id="\hat{a}" coords="0,34,14,48">
                                                                            <area class="chat_math_symbols" title="\grave{a}" shape="rect" id="\grave{a}" coords="0,51,14,65">
                                                                            <area class="chat_math_symbols" title="\tilde{a}" shape="rect" id="\tilde{a}" coords="0,68,14,82">
                                                                            <area class="chat_math_symbols" title="\bar{a}" shape="rect" id="\bar{a}" coords="0,85,14,99">
                                                                            <area class="chat_math_symbols" title="\not{a}" shape="rect" id="\not{a}" coords="0,102,14,116">
                                                                            <area class="chat_math_symbols" title="a''" shape="rect" id="{a''}" coords="17,0,31,14">
                                                                            <area class="chat_math_symbols" title="\ddot{a}" shape="rect" id="\ddot{a}" coords="17,17,31,31">
                                                                            <area class="chat_math_symbols" title="\check{a}" shape="rect" id="\check{a}" coords="17,34,31,48">
                                                                            <area class="chat_math_symbols" title="\acute{a}" shape="rect" id="\acute{a}" coords="17,51,31,65">
                                                                            <area class="chat_math_symbols" title="\breve{a}" shape="rect" id="\breve{a}" coords="17,68,31,82">
                                                                            <area class="chat_math_symbols" title="\vec{a}" shape="rect" id="\vec{a}" coords="17,85,31,99">
                                                                            <area class="chat_math_symbols" shape="rect" id="degree" title="degrees" id="" coords="17,102,31,116">
                            </map>
                        </div>
                        <div class="panel" id="panel2" style="height: 34px; overflow: hidden;"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/chat-groups/css/images/accents_ext.gif" title="Accents_ext" alt="Accents_ext Panel" usemap="#accents_ext_map" height="170" border="0" width="25">
                            <map name="accents_ext_map" id="accents_ext_map">
                                <area class="chat_math_symbols" title="\widetilde{abc}" shape="rect" id="\widetilde{abc}" coords="0,0,22,14">
                                <area class="chat_math_symbols" title="\widehat{abc}" shape="rect" id="\widehat{abc}" coords="0,17,22,31">
                                <area class="chat_math_symbols" title="\overleftarrow{abc}" shape="rect" id="\overleftarrow{abc}" coords="0,34,22,48">
                                <area class="chat_math_symbols" title="\overrightarrow{abc}" shape="rect" id="\overrightarrow{abc}" coords="0,51,22,65">
                                <area class="chat_math_symbols" title="\overline{abc}" shape="rect" id="\overline{abc}" coords="0,68,22,82">
                                <area class="chat_math_symbols" title="\underline{abc}" shape="rect" id="\underline{abc}" coords="0,85,22,99">
                                <area class="chat_math_symbols" title="\overbrace{abc}" shape="rect" id="\overbrace{abc}" coords="0,102,22,116">
                                <area class="chat_math_symbols" title="\underbrace{abc}" shape="rect" id="\underbrace{abc}" coords="0,119,22,133">
                                <area class="chat_math_symbols" title="\overset{a}{abc}" shape="rect" id="\overset{a}{abc}" coords="0,136,22,150">
                                <area class="chat_math_symbols" title="\underset{a}{abc}" shape="rect" id="\underset{a}{abc}" coords="0,153,22,167">
                            </map>
                        </div>
                        <div class="panel" id="panel3" style="height: 34px; overflow: hidden;"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/chat-groups/css/images/arrows.gif" title="Arrows" alt="Arrows Panel" usemap="#arrows_map" height="170" border="0" width="56">
                            <map name="arrows_map" id="arrows_map">
                                <area class="chat_math_symbols" shape="rect" id="\mapsto" title="\mapsto" coords="0,0,25,14">
                                <area class="chat_math_symbols" title="\leftarrow" shape="rect" id="\leftarrow" coords="0,17,25,31">
                                <area class="chat_math_symbols" title="\Leftarrow" shape="rect" id="\Leftarrow" coords="0,34,25,48">
                                <area class="chat_math_symbols" title="\leftrightarrow" shape="rect" id="\leftrightarrow" coords="0,51,25,65">
                                <area class="chat_math_symbols" title="\leftharpoonup" shape="rect" id="\leftharpoonup" coords="0,68,25,82">
                                <area class="chat_math_symbols" title="\leftharpoondown" shape="rect" id="\leftharpoondown" coords="0,85,25,99">
                                <area class="chat_math_symbols" title="\leftrightharpoons" shape="rect" id="\leftrightharpoons" coords="0,102,25,116">
                                <area class="chat_math_symbols" title="\xleftarrow[text]{long}" shape="rect" id="\xleftarrow[text]{long}" coords="0,119,25,133">
                                <area class="chat_math_symbols" title="\overset{a}{\leftarrow}" shape="rect" id="\overset{a}{\leftarrow}" coords="0,136,25,150">
                                <area class="chat_math_symbols" title="\underset{a}{\leftarrow}" shape="rect" id="\underset{a}{\leftarrow}" coords="0,153,25,167">
                                <area class="chat_math_symbols" title="n \to" shape="rect" id="n \to" coords="28,0,53,14">
                                <area class="chat_math_symbols" title="\rightarrow" shape="rect" id="\rightarrow" coords="28,17,53,31">
                                <area class="chat_math_symbols" title="\Rightarrow" shape="rect" id="\Rightarrow" coords="28,34,53,48">
                                <area class="chat_math_symbols" title="\Leftrightarrow" shape="rect" id="\Leftrightarrow" coords="28,51,53,65">
                                <area class="chat_math_symbols" title="\rightharpoonup" shape="rect" id="\rightharpoonup" coords="28,68,53,82">
                                <area class="chat_math_symbols" title="\rightharpoondown" shape="rect" id="\rightharpoondown" coords="28,85,53,99">
                                <area class="chat_math_symbols" title="\rightleftharpoons" shape="rect" id="\rightleftharpoons" coords="28,102,53,116">
                                <area class="chat_math_symbols" title="\xrightarrow[text]{long}" shape="rect" id="\xrightarrow[text]{long}" coords="28,119,53,133">
                                <area class="chat_math_symbols" title="\overset{a}{\rightarrow}" shape="rect" id="\overset{a}{\rightarrow}" coords="28,136,53,150">
                                <area class="chat_math_symbols" title="\underset{a}{\rightarrow}" shape="rect" id="\underset{a}{\rightarrow}" coords="28,153,53,167">
                            </map>
                        </div>
                        <div style="height:10px;"></div>        
                        <div class="panel" id="panel11" style="height:28px"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/chat-groups/css/images/operators.gif" width="168" height="140" border="0" title="Operators" alt="Operators Panel" usemap="#operators_map">
                            <map name="operators_map" id="operators_map">
                                <area class="chat_math_symbols" shape="rect" id="x^{2}" title="superscript" coords="0,0,25,25">
                                <area class="chat_math_symbols" shape="rect" id="x_{2}" title="subscript" coords="0,28,25,53">
                                <area class="chat_math_symbols" shape="rect" id="2_{a}^{b}" coords="0,56,25,81" title="x_a^b">
                                <area class="chat_math_symbols" shape="rect" id="{x_a}^b" coords="0,84,25,109" title="{x_a}^b">
                                <area class="chat_math_symbols" shape="rect" id="_{a}^{b}\textrm{C}" title="_{a}^{b}\textrm{C}" coords="0,112,25,137">
                                <area class="chat_math_symbols" shape="rect" id="\frac{a}{b}" title="fraction" coords="28,0,53,25">
                                <area class="chat_math_symbols" shape="rect" id="\tfrac{a}{b}" title="tiny fraction" coords="28,28,53,53">
                                <area class="chat_math_symbols" shape="rect" id="\frac{\partial }{\partial x}" coords="28,56,53,81" title="\frac{\partial }{\partial x}">
                                <area class="chat_math_symbols" shape="rect" id="\frac{\partial^2 }{\partial x^2}" coords="28,84,53,109" title="\frac{\partial^2 }{\partial x^2}">
                                <area class="chat_math_symbols" shape="rect" id="\frac{\mathrm{d} }{\mathrm{d} x}" coords="28,112,53,137" title="\frac{\mathrm{d} }{\mathrm{d} x}">
                                <area class="chat_math_symbols" shape="rect" id="\int" coords="56,0,81,25" title="\int">
                                <area class="chat_math_symbols" shape="rect" id="\int_{a}^{b}" title="\int_{}^{}" coords="56,28,81,53">
                                <area class="chat_math_symbols" shape="rect" id="\oint" coords="56,56,81,81" title="\oint">
                                <area class="chat_math_symbols" shape="rect" id="\oint_{a}^{b}" title="\oint_{}^{}" coords="56,84,81,109" >
                                <area class="chat_math_symbols" shape="rect" id="\iint_{a}^{b}" title="\iint_{}^{}" coords="56,112,81,137">
                                <area class="chat_math_symbols" shape="rect" id="\bigcap" coords="84,0,109,25" title="\bigcap">
                                <area class="chat_math_symbols" shape="rect" id="\bigcap_{a}^{b}" title="\bigcap_{}^{}" coords="84,28,109,53">
                                <area class="chat_math_symbols" shape="rect" id="\bigcup" coords="84,56,109,81" title="\bigcup">
                                <area class="chat_math_symbols" shape="rect" id="\bigcup_{a}^{b}" title="\bigcup_{}^{}" coords="84,84,109,109">
                                <area class="chat_math_symbols" shape="rect" id="\lim_{x \to 0}" title="\lim_{x \to 0}" coords="84,112,109,137">
                                <area class="chat_math_symbols" shape="rect" id="\sum" coords="112,0,137,25" title="\sum">
                                <area class="chat_math_symbols" shape="rect" id="\sum_{a}^{b}" title="\sum_{}^{}" coords="112,28,137,53">
                                <area class="chat_math_symbols" shape="rect" id="\sqrt{a}" title="\sqrt{}" coords="112,56,137,81">
                                <area class="chat_math_symbols" shape="rect" id="\sqrt[a]{b}" title="\sqrt[]{}" coords="112,84,137,109">
                                <area class="chat_math_symbols" shape="rect" id="\prod" coords="140,0,165,25" title="\prod">
                                <area class="chat_math_symbols" shape="rect" id="\prod_{a}^{b}" title="\prod_{}^{}" coords="140,28,165,53">
                                <area class="chat_math_symbols" shape="rect" id="\coprod" coords="140,56,165,81" title="\coprod">
                                <area class="chat_math_symbols" shape="rect" id="\coprod_{a}^{b}" title="\coprod_{}^{}" coords="140,84,165,109">
                            </map>
                        </div>
                        <div class="panel" id="panel5" style="height: 28px; overflow: hidden;"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/chat-groups/css/images/brackets.gif" width="56" height="140" border="0" title="Brackets" alt="Brackets Panel" usemap="#brackets_map">
                            <map name="brackets_map" id="brackets_map">
                                <area class="chat_math_symbols" shape="rect" id="bracket_1" title="\left ( x \right )" coords="0,0,25,25">
                                <area class="chat_math_symbols" shape="rect" id="bracket_2" title="\left ( x \right )" coords="0,28,25,53">
                                <area class="chat_math_symbols" shape="rect" id="bracket_3" title="\left\{ \right\}" coords="0,56,25,81">
                                <area class="chat_math_symbols" shape="rect" id="bracket_4" title="\left | \right |" coords="0,84,25,109">
                                <area class="chat_math_symbols" shape="rect" id="bracket_5" title="\left \{ \right." coords="0,112,25,137">
                                <area class="chat_math_symbols" shape="rect" id="bracket_6" title="\left \| \right \|" coords="28,0,53,25">
                                <area class="chat_math_symbols" shape="rect" id="bracket_7" title="\left \langle \right \rangle" coords="28,28,53,53">
                                <area class="chat_math_symbols" shape="rect" id="bracket_8" title="\left \lfloor \right \rfloor" coords="28,56,53,81">
                                <area class="chat_math_symbols" shape="rect" id="bracket_9" title="\left \lceil \right \rceil" coords="28,84,53,109">
                                <area class="chat_math_symbols" shape="rect" id="bracket_10" title="\left. \right \}" coords="28,112,53,137">
                            </map>
                        </div>
                        <div style="height:10px;"></div>
                        <div class="panel" id="panel8" style="height:34px"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/chat-groups/css/images/greeklower.gif" width="68" height="136" border="0" title="Greeklower" alt="Greeklower Panel" usemap="#greeklower_map">
                            <map name="greeklower_map" id="greeklower_map">
                                <area class="chat_math_symbols" shape="rect" id="\alpha" coords="0,0,14,14" title="\alpha">
                                <area class="chat_math_symbols" shape="rect" id="\epsilon" coords="0,17,14,31" title="\epsilon">
                                <area class="chat_math_symbols" shape="rect" id="\theta" coords="0,34,14,48" title="\theta">
                                <area class="chat_math_symbols" shape="rect" id="\lambda" coords="0,51,14,65" title="\lambda">
                                <area class="chat_math_symbols" shape="rect" id="\pi" coords="0,68,14,82" title="\pi">
                                <area class="chat_math_symbols" shape="rect" id="\sigma" coords="0,85,14,99" title="\sigma">
                                <area class="chat_math_symbols" shape="rect" id="\phi" coords="0,102,14,116" title="\phi">
                                <area class="chat_math_symbols" shape="rect" id="\omega" coords="0,119,14,133" title="\omega">
                                <area class="chat_math_symbols" shape="rect" id="\beta" coords="17,0,31,14" title="\beta">
                                <area class="chat_math_symbols" shape="rect" id="\varepsilon" coords="17,17,31,31" title="\varepsilon">
                                <area class="chat_math_symbols" shape="rect" id="\vartheta" coords="17,34,31,48" title="\vartheta">
                                <area class="chat_math_symbols" shape="rect" id="\mu" coords="17,51,31,65" title="\mu">
                                <area class="chat_math_symbols" shape="rect" id="\varpi" coords="17,68,31,82" title="\varpi">
                                <area class="chat_math_symbols" shape="rect" id="\varsigma" coords="17,85,31,99" title="\varsigma">
                                <area class="chat_math_symbols" shape="rect" id="\varphi" coords="17,102,31,116" title="\varphi">
                                <area class="chat_math_symbols" shape="rect" id="\gamma" coords="34,0,48,14" title="\gamma">
                                <area class="chat_math_symbols" shape="rect" id="\zeta" coords="34,17,48,31" title="\zeta">
                                <area class="chat_math_symbols" shape="rect" id="\iota" coords="34,34,48,48" title="\iota">
                                <area class="chat_math_symbols" shape="rect" id="\nu" coords="34,51,48,65" title="\nu">
                                <area class="chat_math_symbols" shape="rect" id="\rho" coords="34,68,48,82" title="\rho">
                                <area class="chat_math_symbols" shape="rect" id="\tau" coords="34,85,48,99" title="\tau">
                                <area class="chat_math_symbols" shape="rect" id="\chi" coords="34,102,48,116" title="\chi">
                                <area class="chat_math_symbols" shape="rect" id="\delta" coords="51,0,65,14" title="\delta">
                                <area class="chat_math_symbols" shape="rect" id="\eta" coords="51,17,65,31" title="\eta">
                                <area class="chat_math_symbols" shape="rect" id="\kappa" coords="51,34,65,48" title="\kappa">
                                <area class="chat_math_symbols" shape="rect" id="\xi" coords="51,51,65,65" title="\xi">
                                <area class="chat_math_symbols" shape="rect" id="\varrho" coords="51,68,65,82" title="\varrho">
                                <area class="chat_math_symbols" shape="rect" id="\upsilon" coords="51,85,65,99" title="\upsilon">
                                <area class="chat_math_symbols" shape="rect" id="\psi" coords="51,102,65,116" title="\psi">
                            </map>
                        </div>
                        <div class="panel" id="panel9" style="height:34px"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/chat-groups/css/images/greekupper.gif" width="34" height="102" border="0" title="Greekupper" alt="Greekupper Panel" usemap="#greekupper_map">
                            <map name="greekupper_map" id="greekupper_map">
                                <area class="chat_math_symbols" shape="rect" id="\Gamma" coords="0,0,14,14" title="\Gamma">
                                <area class="chat_math_symbols" shape="rect" id="\Theta" coords="0,17,14,31" title="\Theta">
                                <area class="chat_math_symbols" shape="rect" id="\Xi" coords="0,34,14,48" title="\Xi">
                                <area class="chat_math_symbols" shape="rect" id="\Sigma" coords="0,51,14,65" title="\Sigma">
                                <area class="chat_math_symbols" shape="rect" id="\Phi" coords="0,68,14,82" title="\Phi">
                                <area class="chat_math_symbols" shape="rect" id="\Omega" coords="0,85,14,99" title="\Omega">
                                <area class="chat_math_symbols" shape="rect" id="\Delta" coords="17,0,31,14" title="\Delta">
                                <area class="chat_math_symbols" shape="rect" id="\Lambda" coords="17,17,31,31" title="\Lambda">
                                <area class="chat_math_symbols" shape="rect" id="\Pi" coords="17,34,31,48" title="\Pi">
                                <area class="chat_math_symbols" shape="rect" id="\Upsilon" coords="17,51,31,65" title="\Upsilon">
                                <area class="chat_math_symbols" shape="rect" id="\Psi" coords="17,68,31,82" title="\Psi">
                            </map>
                        </div>
                        <div class="panel" id="panel12" style="height:34px"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/chat-groups/css/images/relations.gif" width="51" height="221" border="0" title="Relations" alt="Relations Panel" usemap="#relations_map">
                            <map name="relations_map" id="relations_map">
                                <area class="chat_math_symbols" shape="rect" id="lessthan" coords="0,0,14,14" title="&lt;">
                                <area class="chat_math_symbols" shape="rect" id="\leq" coords="0,17,14,31" title="\leq">
                                <area class="chat_math_symbols" shape="rect" id="\leqslant" coords="0,34,14,48" title="\leqslant">
                                <area class="chat_math_symbols" shape="rect" id="\nless" coords="0,51,14,65" title="\nless">
                                <area class="chat_math_symbols" shape="rect" id="\nleqslant" coords="0,68,14,82" title="\nleqslant">
                                <area class="chat_math_symbols" shape="rect" id="\prec" coords="0,85,14,99" title="\prec">
                                <area class="chat_math_symbols" shape="rect" id="\preceq" coords="0,102,14,116" title="\preceq">
                                <area class="chat_math_symbols" shape="rect" id="\ll" coords="0,119,14,133" title="\ll">
                                <area class="chat_math_symbols" shape="rect" id="\vdash" coords="0,136,14,150" title="\vdash">
                                <area class="chat_math_symbols" shape="rect" id="\smile" title="smile" coords="0,153,14,167">
                                <area class="chat_math_symbols" shape="rect" id="\models" coords="0,170,14,184" title="\models">
                                <area class="chat_math_symbols" shape="rect" id="\mid" coords="0,187,14,201" title="\mid">
                                <area class="chat_math_symbols" shape="rect" id="\bowtie" coords="0,204,14,218" title="\bowtie">
                                <area class="chat_math_symbols" shape="rect" id="greaterthan" coords="17,0,31,14" title="&gt;">
                                <area class="chat_math_symbols" shape="rect" id="\geq" coords="17,17,31,31" title="\geq">
                                <area class="chat_math_symbols" shape="rect" id="\geqslant" coords="17,34,31,48" title="\geqslant">
                                <area class="chat_math_symbols" shape="rect" id="\ngtr" coords="17,51,31,65" title="\ngtr">
                                <area class="chat_math_symbols" shape="rect" id="\ngeqslant" coords="17,68,31,82" title="\ngeqslant">
                                <area class="chat_math_symbols" shape="rect" id="\succ" coords="17,85,31,99" title="\succ">
                                <area class="chat_math_symbols" shape="rect" id="\succeq" coords="17,102,31,116" title="\succeq">
                                <area class="chat_math_symbols" shape="rect" id="\gg" coords="17,119,31,133" title="\gg">
                                <area class="chat_math_symbols" shape="rect" id="\dashv" coords="17,136,31,150" title="\dashv">
                                <area class="chat_math_symbols" shape="rect" id="\frown" title="frown" coords="17,153,31,167">
                                <area class="chat_math_symbols" shape="rect" id="\perp" coords="17,170,31,184" title="\perp">
                                <area class="chat_math_symbols" shape="rect" id="\parallel" title="parallel" coords="17,187,31,201">
                                <area class="chat_math_symbols" shape="rect" id="\Join" coords="17,204,31,218" title="\Join">
                                <area class="chat_math_symbols" shape="rect" id="equal" coords="34,0,48,14" title="=">
                                <area class="chat_math_symbols" shape="rect" id="\doteq" coords="34,17,48,31" title="\doteq">
                                <area class="chat_math_symbols" shape="rect" id="\equiv" title="equivalent" coords="34,34,48,48">
                                <area class="chat_math_symbols" shape="rect" id="\neq" coords="34,51,48,65" title="\neq">
                                <area class="chat_math_symbols" shape="rect" id="\not\equiv" title="not equivalent" coords="34,68,48,82">
                                <area class="chat_math_symbols" shape="rect" id="\overset{\underset{\mathrm{def}}{}}{=}" title="define" coords="34,85,48,99">
                                <area class="chat_math_symbols" shape="rect" id="\sim" coords="34,102,48,116" title="\sim">
                                <area class="chat_math_symbols" shape="rect" id="\approx" coords="34,119,48,133" title="\approx">
                                <area class="chat_math_symbols" shape="rect" id="\simeq" coords="34,136,48,150" title="\simeq">
                                <area class="chat_math_symbols" shape="rect" id="\cong" coords="34,153,48,167" title="\cong">
                                <area class="chat_math_symbols" shape="rect" id="\asymp" coords="34,170,48,184" title="\asymp">
                                <area class="chat_math_symbols" shape="rect" id="\propto" title="proportional to" coords="34,187,48,201">
                            </map>
                        </div>
                        <div class="panel" id="panel10" style="height:34px"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/chat-groups/css/images/matrix.gif" width="102" height="170" border="0" title="Matrix" alt="Matrix Panel" usemap="#matrix_map">
                            <map name="matrix_map" id="matrix_map">
                                <area class="chat_math_symbols" shape="rect" id="matrix_1" title="\begin{matrix} ... \end{matrix}" coords="0,0,31,31">
                                <area class="chat_math_symbols" shape="rect" id="matrix_2" title="\begin{pmatrix} ... \end{pmatrix}" coords="0,34,31,65">
                                <area class="chat_math_symbols" shape="rect" id="matrix_3" title="\begin{vmatrix} ... \end{vmatrix}" coords="0,68,31,99">
                                <area class="chat_math_symbols" shape="rect" id="matrix_4" title="\begin{Vmatrix} ... \end{Vmatrix}" coords="0,102,31,133">
                                <area class="chat_math_symbols" shape="rect" id="matrix_5" title="\left.\begin{matrix}... \end{matrix}\right|" coords="0,136,31,167">
                                <area class="chat_math_symbols" shape="rect" id="matrix_6" title="\being{bmatrix} ... \end{bmatrix}" coords="34,0,65,31">
                                <area class="chat_math_symbols" shape="rect" id="matrix_7" title="\bigl(\begin{smallmatrix} ... \end{smallmatrix}\bigr)" coords="34,34,65,65">
                                <area class="chat_math_symbols" shape="rect" id="matrix_8" title="\begin{Bmatrix} ... \end{Bmatrix}" coords="34,68,65,99">
                                <area class="chat_math_symbols" shape="rect" id="matrix_9" title="\begin{Bmatrix} ... \end{matrix}" coords="34,102,65,133">
                                <area class="chat_math_symbols" shape="rect" id="matrix_10" title="\begin{matrix} ... \end{Bmatrix}" coords="34,136,65,167">
                                <area class="chat_math_symbols" shape="rect" id="matrix_11" title=" \binom{n}{r}" coords="68,0,99,31">
                                <area class="chat_math_symbols" shape="rect" id="matrix_12" title="\begin{cases} ... \end{cases}" coords="68,34,99,65">
                                <area class="chat_math_symbols" shape="rect" id="matrix_13" title="\begin{align} ... \end{align}" coords="68,68,99,99">
                            </map>
                        </div>
                    </div>
                </div>

                <!--div id="symbols">
                    
                    <img class="chat_math_symbols" id="008" src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/symbol_8.png" alt="\int_{a}^{b}">
                    <img class="chat_math_symbols" id="009" src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/symbol_9.png" alt="\lim_{x \to \infty}">
                    <img class="chat_math_symbols" id="010" src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/symbol_10.png" alt="\sum_{n=0}^\infty">
                    <img class="chat_math_symbols" id="011" src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/symbol_11.png" alt="\nabla \cdot \vec{E}">
                    <br>
                    <img class="chat_math_symbols" id="001" src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/symbol_1.png" alt="\alpha">
                    <img class="chat_math_symbols" id="002" src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/symbol_2.png" alt="\Delta">
                    <img class="chat_math_symbols" id="003" src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/symbol_3.png" alt="\pm" >
                    <img class="chat_math_symbols" id="004" src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/symbol_4.png" alt="t_0">
                    <img class="chat_math_symbols" id="005" src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/symbol_5.png" alt="x^2">
                    <img class="chat_math_symbols" id="007" src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/symbol_7.png" alt="\sqrt {x}">
                    <br>
                    
                    <img class="chat_math_symbols" id="006" src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/symbol_6.png" alt="\frac {a} {b}">
                    
                </div -->

                <div class="input_style">
                    <div class="sendCon">
                        <label>Skriv her</label>
                        <input type="text" id="msg" size="63" class="clr" />
                        <a href="javascript: void(0)" id="send_btn"><img src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>images/btn_send.png" height="32" /></a>  
                    </div>
                    <div class="preview_con">
                        <label>Forhåndsvisning</label>
                        <div class="chat_preview_style" >
                            &nbsp;&nbsp;&nbsp;<span id="previewArea"></span>
                        </div>
                    </div>

                </div>


            <input id="uid" type="hidden" size="63" value="<?php echo $user_id; ?>" />
            <input id="admin_nickname" type="hidden" size="63" value="<?php echo $nickname; ?>" />
            <input id="gid" type="hidden" size="63" value="<?php echo $group_id; ?>" />  
            <input id="chat_disable_ck" type="hidden" size="63" value="<?php echo $chat_disable; ?>" />  
            <input id="last_message" type="hidden" size="63" value="0" />

            <!--        <div id="msg" contenteditable="true"> </div>-->





        </div>
        <div class="clr"></div>
    </div>  
    <script type="text/javascript" src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>js/jquery.js"></script>  
    <script>
        //
        //  Use a closure to hide the local variables from the
        //  global namespace
        //
        (function() {
            var QUEUE = MathJax.Hub.queue;  // shorthand for the queue
            var math = null;                // the element jax for the math output.
            //var math = document.getElementById("previewArea");
            //QUEUE(["Typeset",MathJax.Hub,math]);
            //
            //  Get the element jax when MathJax has produced it.
            //
            QUEUE.Push(function() {
                math = MathJax.Hub.getAllJax("MathOutput")[0];
            });

            //
            //  The onchange event handler that typesets the
            //  math entered by the user
            //
            window.UpdateMath = function(TeX) {
                QUEUE.Push(["Text", math, TeX]);
            }
        })();
    </script>
    <script type="text/javascript">
        // jQuery Document  

        var c = jQuery.noConflict();
        var PLUGIN_IMG_PATH = '<?php echo WP_PLUGIN_URL . "/chat-groups/images/"; ?>';
        var user_exist_in_group = '<?php echo $user_exist_in_group ?>';
        var chat_disable = 0;
        var d = new Date();
        c("#now_t2").val(Math.round(d.getTime() / 1000));


        var show_time = 1;
        if (c("#uid").val() == 0 || user_exist_in_group == 0) {
            //c("#msg").prop('disabled', true);
        }

        

        
        
        

        c("#msg").keyup(function() {
            var msg = c.trim(c("#msg").val());
            c("#previewArea").html(msg);

            MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
        });
        c(".chat_math_symbols").click(function() {
//            if (c("#uid").val() == 0) {
//                var rsp_msg = 'Meld deg p� for � delta i gruppediskusjonen';
//                c("#rsp").html(rsp_msg);
//                c("#rsp").addClass("error");
//                c('html, body').animate({scrollTop:c('#rsp').position().top}, 'fast');
//                return false;
//            }
            var msg = c.trim(c("#msg").val());
            var symbolCode = c(this).attr("id");

            // spaces 
            if(symbolCode == "space1"){
                symbolCode = "\\,";
            }
            else if(symbolCode == "space2"){
                symbolCode = "\\;";
            }
            else if(symbolCode == "space3"){
                symbolCode = "\\quad";
            }
            else if(symbolCode == "space4"){
                symbolCode = "\\qquad";
            }
            

            // html entities
            else if (symbolCode == "degree") {
                symbolCode = "^{\\circ}";
            }
            else if (symbolCode == "lessthan") {
                symbolCode = " < ";
            } else if (symbolCode == "greaterthan") {
                symbolCode = " > ";
            } else if (symbolCode == "equal") {
                symbolCode = " = ";
            }

            // box 5
            else if (symbolCode == "aa") {
                symbolCode = " &aring; ";
            } else if (symbolCode == "aa2") {
                symbolCode = " &Aring; ";
            }
            else if (symbolCode == "ae") {
                symbolCode = " &aelig; ";
            }
            else if (symbolCode == "ae2") {
                symbolCode = " &AElig; ";
            }

            else if (symbolCode == "o") {
                symbolCode = " &Oslash; ";
            }
            else if (symbolCode == "oe") {
                symbolCode = " &oelig; ";
            }
            else if (symbolCode == "oe2") {
                symbolCode = " &OElig; ";
            }
            else if (symbolCode == "ss1") {
                symbolCode = " \\beta ";
            }


            //
            else if (symbolCode == "euro") {
                symbolCode = " &euro; ";
            }
            else if (symbolCode == "pound") {
                symbolCode = " &pound; ";
            }
            else if (symbolCode == "cent") {
                symbolCode = " &cent; ";
            }
            else if (symbolCode == "dollar") {
                symbolCode = " { \\&#36; } ";
            }
            else if (symbolCode == "copy") {
                symbolCode = " &copy; ";
            }
            else if (symbolCode == "para") {
                symbolCode = " &para; ";
            }


            // brackets 
            else if (symbolCode == "bracket_1") {
                symbolCode = "\\left ( x \\right  )";
            } else if (symbolCode == "bracket_2") {
                symbolCode = "\\left [ x \\right ]";
            } else if (symbolCode == "bracket_3") {
                symbolCode = "\\left \\{ x \\right \\}";
            } else if (symbolCode == "bracket_4") {
                symbolCode = "\\left | x \\right |";
            } else if (symbolCode == "bracket_5") {
                symbolCode = '\\left \\{ x \\right.';
            } else if (symbolCode == "bracket_6") {
                symbolCode = "\\left \\| x \\right \\|";
            } else if (symbolCode == "bracket_7") {
                symbolCode = "\\left \\langle x \\right \\rangle";
            } else if (symbolCode == "bracket_8") {
                symbolCode = "\\left \\lfloor x \\right \\rfloor";
            } else if (symbolCode == "bracket_9") {
                symbolCode = "\\left \\lceil x \\right \\rceil";
            } else if (symbolCode == "bracket_10") {
                symbolCode = "\\left. x \\right \\}";
            }

            // matrix
            else if (symbolCode == "matrix_1") {
                symbolCode = "\\begin{matrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{matrix}";
            } else if (symbolCode == "matrix_2") {
                symbolCode = "\\begin{pmatrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{pmatrix}";
            } else if (symbolCode == "matrix_3") {
                symbolCode = "\\begin{vmatrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{vmatrix}";
            } else if (symbolCode == "matrix_4") {
                symbolCode = "\\begin{Vmatrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{Vmatrix}";
            } else if (symbolCode == "matrix_5") {
                symbolCode = " \\left.\\begin{matrix} 1 & 2 \\\\  3 & 4 \\end{matrix}\\right| ";
            } else if (symbolCode == "matrix_6") {
                symbolCode = "\\begin{bmatrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{bmatrix}";
            } else if (symbolCode == "matrix_7") {
                symbolCode = "\\bigl(\\begin{smallmatrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{smallmatrix}\\bigr)";
            } else if (symbolCode == "matrix_8") {
                symbolCode = "\\begin{Bmatrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{Bmatrix}";
            } else if (symbolCode == "matrix_9") {
                symbolCode = "\\left\\{\\begin{matrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{matrix}\\right.";
            } else if (symbolCode == "matrix_10") {
                symbolCode = "\\left.\\begin{matrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{matrix}\\right\\}";
            } else if (symbolCode == "matrix_11") {
                symbolCode = "\\binom{n}{r}";
            } else if (symbolCode == "matrix_12") {
                symbolCode = "\\begin{cases} 1 & 2 \\\\ 3 & 4 \\\\ \\end{cases}";
            } else if (symbolCode == "matrix_13") {
                symbolCode = "\\begin{align} 1 & 2 \\\\ 3 & 4 \\\\ \\end{align}";
            }


            c("#previewArea").html("" + msg + " $" + symbolCode + "$ ");

            MathJax.Hub.Queue(["Typeset", MathJax.Hub]);


            c("#msg").val(msg + " $" + symbolCode + "$ ");
            
            c("#msg").focus();
        });
        c(".chat_math_symbols").hover(
                function() {

                    var symbolCode = c(this).attr("id");
                    var coords = c(this).attr("coords");
                    var div_pos = c(this).parent().parent().position();
                    var coords_ary = coords.split(",");
                    
                    
                    var left = parseInt(coords_ary[2]) + parseInt(div_pos.left) + 10;
                    var top = parseInt(coords_ary[3]) + parseInt(div_pos.top) - 20;
                    
                    
                    c("#hover").css({
                            "left":left+"px",
                            "top":top+"px",
                    });
                    
                    
                    if (symbolCode == "space1" || symbolCode == "space2" || symbolCode == "space3" || symbolCode == "space4") {
                        c("#hover").html(c(this).attr("title"));
                        c("#hover").show();
                    }
                    else {

                        // html entities
                        if (symbolCode == "degree") {
                            symbolCode = "^{\\circ}";
                        }
                        else if (symbolCode == "lessthan") {
                            symbolCode = " < ";
                        } else if (symbolCode == "greaterthan") {
                            symbolCode = " > ";
                        } else if (symbolCode == "equal") {
                            symbolCode = " = ";
                        }

                        // box 5
                        else if (symbolCode == "aa") {
                            symbolCode = " &aring; ";
                        } else if (symbolCode == "aa2") {
                            symbolCode = " &Aring; ";
                        }
                        else if (symbolCode == "ae") {
                            symbolCode = " &aelig; ";
                        }
                        else if (symbolCode == "ae2") {
                            symbolCode = " &AElig; ";
                        }

                        else if (symbolCode == "o") {
                            symbolCode = " &Oslash; ";
                        }
                        else if (symbolCode == "oe") {
                            symbolCode = " &oelig; ";
                        }
                        else if (symbolCode == "oe2") {
                            symbolCode = " &OElig; ";
                        }
                        else if (symbolCode == "ss1") {
                            symbolCode = " \\beta ";
                        }


                        //
                        else if (symbolCode == "euro") {
                            symbolCode = " &euro; ";
                        }
                        else if (symbolCode == "pound") {
                            symbolCode = " &pound; ";
                        }
                        else if (symbolCode == "cent") {
                            symbolCode = " &cent; ";
                        }
                        else if (symbolCode == "dollar") {
                            symbolCode = " { \\&#36; } ";
                        }
                        else if (symbolCode == "copy") {
                            symbolCode = " &copy; ";
                        }
                        else if (symbolCode == "para") {
                            symbolCode = " &para; ";
                        }


                        // brackets 
                        else if (symbolCode == "bracket_1") {
                            symbolCode = "\\left ( x \\right  )";
                        } else if (symbolCode == "bracket_2") {
                            symbolCode = "\\left [ x \\right ]";
                        } else if (symbolCode == "bracket_3") {
                            symbolCode = "\\left \\{ x \\right \\}";
                        } else if (symbolCode == "bracket_4") {
                            symbolCode = "\\left | x \\right |";
                        } else if (symbolCode == "bracket_5") {
                            symbolCode = '\\left \\{ x \\right.';
                        } else if (symbolCode == "bracket_6") {
                            symbolCode = "\\left \\| x \\right \\|";
                        } else if (symbolCode == "bracket_7") {
                            symbolCode = "\\left \\langle x \\right \\rangle";
                        } else if (symbolCode == "bracket_8") {
                            symbolCode = "\\left \\lfloor x \\right \\rfloor";
                        } else if (symbolCode == "bracket_9") {
                            symbolCode = "\\left \\lceil x \\right \\rceil";
                        } else if (symbolCode == "bracket_10") {
                            symbolCode = "\\left. x \\right \\}";
                        }

                        // matrix
                        else if (symbolCode == "matrix_1") {
                            symbolCode = "\\begin{matrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{matrix}";
                        } else if (symbolCode == "matrix_2") {
                            symbolCode = "\\begin{pmatrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{pmatrix}";
                        } else if (symbolCode == "matrix_3") {
                            symbolCode = "\\begin{vmatrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{vmatrix}";
                        } else if (symbolCode == "matrix_4") {
                            symbolCode = "\\begin{Vmatrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{Vmatrix}";
                        } else if (symbolCode == "matrix_5") {
                            symbolCode = " \\left.\\begin{matrix} 1 & 2 \\\\  3 & 4 \\end{matrix}\\right| ";
                        } else if (symbolCode == "matrix_6") {
                            symbolCode = "\\begin{bmatrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{bmatrix}";
                        } else if (symbolCode == "matrix_7") {
                            symbolCode = "\\bigl(\\begin{smallmatrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{smallmatrix}\\bigr)";
                        } else if (symbolCode == "matrix_8") {
                            symbolCode = "\\begin{Bmatrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{Bmatrix}";
                        } else if (symbolCode == "matrix_9") {
                            symbolCode = "\\left\\{\\begin{matrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{matrix}\\right.";
                        } else if (symbolCode == "matrix_10") {
                            symbolCode = "\\left.\\begin{matrix} 1 & 2 \\\\ 3 & 4 \\\\ \\end{matrix}\\right\\}";
                        } else if (symbolCode == "matrix_11") {
                            symbolCode = "\\binom{n}{r}";
                        } else if (symbolCode == "matrix_12") {
                            symbolCode = "\\begin{cases} 1 & 2 \\\\ 3 & 4 \\\\ \\end{cases}";
                        } else if (symbolCode == "matrix_13") {
                            symbolCode = "\\begin{align} 1 & 2 \\\\ 3 & 4 \\\\ \\end{align}";
                        }

                        c("#hover").html(" $" + symbolCode + "$ ");
                        MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                        c("#hover").show();
                    }
                    
                }, function() {
                    c("#hover").hide();

        });

        c("#menu_btn_time").click(function() {
            if (show_time == 1) {
                show_time = 0
            }
            else {
                show_time = 1;
            }
            c(".time").toggle();
        });

        var show_user_list_flag = 1;
        c("#menu_btn_user").click(function() {

            c("#user_list").toggle(000, function() {
                var cb_width = c("#chatbox").width();
                if (show_user_list_flag == 1) {
                    c("#chatbox").css("width", "96%");
                    show_user_list_flag = 0;
                }
                else {
                    c("#chatbox").css("width", "75%");
                    show_user_list_flag = 1;
                }
            });
        });

        c("#last_message").val(0);
        refresh_chat();



        function refresh_chat() {
            c.ajax({
                type: "POST",
                url: "<?php echo WP_PLUGIN_URL . "/chat-groups/load_chat.php"; ?>",
                data: {
                    gid: c.trim(c("#gid").val()),
                    last_id: c.trim(c("#last_message").val())
                },
                dataType: "json",
                async: false
            }).success(function(rsp) {
                c(".append").remove();

                c("#chatbox").append(rsp.html);
                c("#last_message").val(rsp.last_id);
                var totalHeight = c('#chatbox')[0].scrollHeight;
                c("#chatbox").scrollTop(totalHeight);
                MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
            });

        }

        <?php if ($chat_start_flag == 1) { ?>
                    var reload_flag_chat_start = true;
        <?php } else { ?>
                    var reload_flag_chat_start = false;
        <?php } ?>
        <?php if ($chat_end_flag == 1) { ?>
                    var reload_flag_chat_end = true;
        <?php } else { ?>
                    var reload_flag_chat_end = false;
        <?php } ?>
            


            function check_time() {
                var start_time = <?php echo $start_time; ?>;
                var end_time = <?php echo $end_time; ?>;
                var chat_start_flag;
                var chat_end_flag;

                var d = new Date();
                var now_time = Math.round(d.getTime() / 1000);
                //var now_time = 1382515477; // 8:04
                //var now_time = 1382515537; // 8:05
                // var now_time = 1382515837; // 8:10
                //var now_time = 1382602539; // 8:15
                //var now_time = 1382602599; // 8:16
                
                if (now_time < start_time ) {
                    chat_start_flag = false;
                }
                else {
                    chat_start_flag = true;
                }
                if(now_time > end_time ){
                    chat_end_flag = true;
                }
                else{
                    chat_end_flag = false;
                }
                
                

                if (chat_start_flag === true && reload_flag_chat_start === false) {
                    console.log("start reload");
                    location.reload();
                }
                else if (chat_end_flag === true && reload_flag_chat_end === false ) {
                    console.log("end reload");
                    location.reload();
                }

                

        }
        setInterval(check_time, 5000);

        //setInterval(refresh_chat, 1000000);
        //setInterval(refresh_chat,10000);
    </script>  