<script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" type="text/css" rel="stylesheet" />
<div class="wrap">
	<form action="admin.php?page=bt-outside_user" method="post">
    	<input type="hidden" name="action" value="add_user" />
        <h2>Users Paid Outside the NetAxept</h2>
        <br />
        <div class="parameterbox">
        	<label class="standar">Select User Email: </label>
            <select name="user_id" id="user_id">
            	<?php foreach($all_users as $user){?>
            		<option value="<?php echo $user->ID;?>"><?php echo $user->user_email;?></option>
                <?php }?>
            </select>&nbsp;type first letters of email to higlight it in dropdown menu
        </div>
        <div class="parameterbox">
        	<label class="standar">Date From: </label>
            <input type="text" name="start_date" id="start_date" value="<?php echo date('Y-m-d');?>" readonly />
        </div>
        <div class="parameterbox">
        	<label class="standar">Date To: </label>
            <input type="text" name="end_date" id="end_date" value="<?php echo date('Y-m-d');?>" readonly />
        </div>
        <input id="submit" class="button-primary" type="submit" value="Add User" name="submit">
    </form>
    <br />
    <table class="wp-list-table widefat fixed posts">
    	<thead>
            <tr>
                <th width="50">id</th>
                <th>User Name</th>
                <th>Email</th>
                <th>Date From</th>
                <th>Date To</th>
                <th width="140"></th>
            </tr>
        </thead>
        <?php 
			if(count($payments) > 0){
				foreach($payments as $key=>$payment){
		?>
                <tbody>
                    <td><?php echo $key+1;?></td>
                    <td><?php echo get_user_meta( $payment['user_id'], 'first_name', true ).' '.get_user_meta( $payment['user_id'], 'last_name', true );?></td>
                    <td><?php $user_info = get_userdata( $payment['user_id'] );echo $user_info->user_email;?></td>
                    <td><?php echo $payment['start_date'];?></td>
                    <td><?php echo $payment['end_date'];?></td>
                    <td><a href="admin.php?page=bt-outside_user&key=<?php echo $key;?>&action=delete">Delete</a></td>
                </tbody>
        <?php 
				}
			}else{
		?>
        	<tbody>
            	<td colspan="5">No record found.</td>
            </tbody>
        <?php 
			}
		?>
    </table>
</div>
<script>
jQuery().ready(function(){
	jQuery( "#start_date" ).datepicker({
		dateFormat:'yy-mm-dd',
		minDate: 0,
		onSelect: function( selectedDate ) {
			jQuery( "#end_date" ).datepicker( "option", "minDate", selectedDate );
		}
	});
	jQuery( "#end_date" ).datepicker({
		dateFormat:'yy-mm-dd',
		minDate: jQuery('#start_date').datepicker('getDate'),
		onSelect: function( selectedDate ) {
			jQuery( "#start_date" ).datepicker( "option", "maxDate", selectedDate );
		}
	});
});
</script>