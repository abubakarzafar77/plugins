<?php $plugin_url = WP_PLUGIN_URL . '/chat-groups/'; ?>
<link type="text/css" href="<?php echo $plugin_url; ?>css/style-admin.css" rel="stylesheet" />
<?php
?>

<script src="<?php echo WP_PLUGIN_URL . "/chat-groups/" ?>js/MathJax/MathJax.js">
    MathJax.Hub.Config({
        extensions: ["tex2jax.js","TeX/AMSmath.js","TeX/AMSsymbols.js"],
        jax: ["input/TeX", "output/HTML-CSS"],
        tex2jax: {inlineMath: [ ['$','$'], ["\\(","\\)"] ],
        }
    });
</script>


<?php
$group = get_group_by_id($_GET['group_id']);
$chats = get_chat_by_group_id($_GET['group_id']);
$users_invited = get_chat_group_users($_GET['group_id']);
$users_participated = get_chat_group_users_who_participated($_GET['group_id']);

 //var_dump($user_participated); 
?>


<style>
    #custom_form .form-field input {
        width: 25em;
    }
    #custom_form .form-field textarea {
        width: 25em;
    }
    #ui-datepicker-div{
        z-index: 1000000 !important;
    }
    #slider-range-max{
        width: 22.7em;
    }
</style>
<div class="wrap">

    <div  style="background-position: -310px -5px !important;" class="icon32" id="icon-users"><br></div>
    <h2>Group Chat History</h2>

    <p>Group Chat History.</p>

    <!--    GROUP INFO-->
    <table class="form-table">
        <tbody>

            <tr class="form-field form-required">
                <th scope="row"><label for="user_login"><b>Activation Time</b> <span class="description"></span></label></th>
                <td>
                    <div style="clear:both;"></div>
                    <?php echo formate_date($group->start_time) . " - " . formate_date($group->end_time); ?>
                </td>
            </tr>

            <tr class="form-field form-required">
                <th scope="row"><label for="user_login"><b>Seats</b> <span class="description"></span></label></th>
                <td>
                    <?php echo $group->available_seats; ?>
                </td>
            </tr>
            
            <tr class="form-field form-required">
                <th scope="row"><label for="user_login"><b>Participants</b><span class="description"></span></label></th>
                <td>
                    
                    <?php 
                    foreach ($users_participated as $user_participated) {
                        echo "<strong>".$user_participated->user_nicename."</strong>";
                        echo ( $user_participated->nickname != "") ? " Participated as "."<strong>".$user_participated->nickname."</strong>" : "";
                        echo '<br>';
                    }
                    ?>
                    
                    
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row"><label for="user_login"><b>Invitation </b><span class="description"></span></label></th>
                <td>
                    
                    <?php 
                    foreach ($users_invited as $user_invited) {
                        echo "<strong>".$user_invited->user_nicename."</strong>";
                        echo ( $user_invited->nickname != "") ? " Participated as "."<strong>".$user_invited->nickname."</strong>" : "";
                        echo '<br>';
                    }
                    ?>
                    
                    
                </td>
            </tr>
            

            <tr class="form-field form-required">
                <th scope="row"><label for="user_login"><b>Description</b> <span class="description"></span></label></th>
                <td>
                    <div style="width: 500px; height: 200px;"><?php echo $group->description; ?></div>
                </td>
            </tr>
            
            

        </tbody>
    </table>

    <!--    CHAT LIST-->
    <?php if (count($chats) > 0) { ?>
        <table cellspacing="0" class="wp-list-table widefat plugins percent_100 ">
            <thead>
                <tr>
                    <th style="" class="manage-column column-cb check-column" id="cb" scope="col"></th>
                    <th style="" class="manage-column column-name" id="name" scope="col">Time</th>
                    <th style="" class="manage-column column-name" id="name" scope="col">User</th>
                    <th style="" class="manage-column column-name" id="description" scope="col">Message</th>
                </tr>
            </thead>



            <tbody id="the-list">
                

                    <?php
                   // var_dump($chats);
                    foreach ($chats as $i => $chat) {
                        $i++;
                        ?>
                    <tr class="active bg_white" id="chatroom-setting">

                        <th class="check-column percent_10" scope="row">
                            <?php echo $i; ?>
                        </th>

                        <td class="percent_20">
                            <strong><?php echo formate_date($chat->time); ?></strong>
                        </td>
                        <td class="percent_20">
                            <?php
                            echo ($chat->nickname != "") ? $chat->nickname : $chat->user_nicename ;
                            ?>
                        </td>
                        <td class="percent_60">
                            <?php
                            echo $chat->msg;
                            ?>
                        </td>

                    </tr>	
                <?php } ?>

                
            </tbody>


        </table>

        <?php
    } else {

        echo '<div class="no_record">No record exist </div>';
    }
    ?>


</div>
