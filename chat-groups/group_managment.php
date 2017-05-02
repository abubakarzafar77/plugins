
<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/chat-groups/css/style-admin.css" rel="stylesheet" />

<?php
$msg = '';
$type = '';

if (isset($_GET['active'])) {
    if ($_GET['active'] == true) {
        $is_active = true;
    } else if ($_GET['active'] == false) {
        $is_active = false;
    }
} else {
    $is_active = "";
}
$groups = get_all_chat_groups($is_active);
// var_dump($groups);
?>


<style>
    /*overwriting admin style*/
    .widefat{
        width: 98%;
    }
</style>
<h2>Group Management</h2>

<!--<h3><a href="<?php echo admin_url('admin.php?page=new_group'); ?>">Create New Group</a></h3>-->
<a href="<?php echo admin_url('admin.php?page=manage_groups'); ?>">All Groups</a>&nbsp;
<a href="<?php echo add_query_arg('active', 'true') ?>">Active Groups Only</a>&nbsp;


<br>
<br>
<?php if (count($groups) > 0) { ?>
    <table cellspacing="0" class="wp-list-table widefat plugins">
        <thead>
            <tr>
                <th style="" class="manage-column column-cb check-column" id="cb" scope="col"></th>
                <th style="" class="manage-column column-name" id="name" scope="col">Topic</th>
                <th style="" class="manage-column column-name" id="description" scope="col">Description</th>	
                <th style="" class="manage-column column-name" id="description" scope="col">&nbsp;</th>	
            </tr>
        </thead>



        <tbody id="the-list">
            <tr class="active" id="chatroom-setting">

    <?php
    foreach ($groups as $i => $group) {
        $i++;
        ?>
                <tr class="active" id="chatroom-setting">

                    <th class="check-column" scope="row">
                        <?php echo $i; ?>
                    </th>

                    <td class="">
                        <strong><?php $cat = get_categories('include='.$group->title);
                                    //echo "<pre>";var_dump($cat);
                                    echo $cat[0]->name; ?></strong>
                    </td>
                    <td class="">
                        <?php echo substr($group->description, 0, 150);
                        echo (strlen($group->description) > 150) ? "..." : ""; ?>
                    </td>
                    <td class="">
                        <a href="<?php echo admin_url('admin.php?page=chat_history&group_id=' . $group->id); ?>">See History</a> 
                    </td>
                </tr>	
            <?php } ?>

            </tr>	
        </tbody>


    </table>

    <?php
} else {

    echo '<div class="no_record">No record exist </div>';
}
?>

