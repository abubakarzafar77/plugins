
<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/chat-groups/css/style-admin.css" rel="stylesheet" />

<?php
$msg = '';
$type = '';

$emails = get_all_email_templates();
// var_dump($setting); 
?>


<style>
    /*overwriting admin style*/
    .widefat{
        width: 98%;
    }
</style>
<h2>Email Management</h2>


 <?php 
            if(count($emails) > 0){ ?>
        <table cellspacing="0" class="wp-list-table widefat plugins">
        <thead>
            <tr>
                <th style="" class="manage-column column-cb check-column" id="cb" scope="col">&nbsp;</th>
                <th style="" class="manage-column column-name" id="name" scope="col">Email Type</th>
                <th style="" class="manage-column column-name" id="description" scope="col">Subject</th>	
                <th style="" class="manage-column column-name" id="description" scope="col">Body Text</th>	
                <th style="" class="manage-column column-name" id="description" scope="col">Actions</th>	
            </tr>
        </thead>



        <tbody id="the-list">
            
            <?php foreach($emails as $i => $email) { 
                $i++;
                ?>
            <tr class="active" id="chatroom-setting">

                    <th class="check-column" scope="row">
                        <?php echo $i; ?>
                    </th>

                    <td class="">
                        <strong><?php echo $email->type; ?></strong>
                    </td>
                    <td class="">
                        <?php echo substr($email->subject,0,30); echo (strlen($email->subject)>30) ? "...":""; ?>
                    </td>
                    <td class="">
                        <?php echo substr($email->body,0,100); echo (strlen($email->body)>100) ? "...":""; ?>
                    </td>
                    <td class="">
                        <a href="<?php echo admin_url('admin.php?page=email_edit&email_id='.$email->id); ?>">Edit</a> | <a href="<?php echo admin_url('admin.php?page=email_detail&email_id='.$email->id); ?>" >Detail</a>
                    </td>
            </tr>	
            <?php } ?>
            
        </tbody>


    </table>

            <?php 
            }
            else{
                
                echo '<div class="no_record">No record exist </div>';
            }
            ?>

