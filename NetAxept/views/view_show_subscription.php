<?php 

$fileName = explode( ".",  basename(__FILE__) );
$currentFileData = "data_".$fileName[0];
$currentViewData = $$currentFileData;

?>
<script type="text/javascript" src="<?php echo get_bloginfo('template_url').'/js/jquery-latest.js'; ?>"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_url').'/js/jquery.tablesorter.js'; ?>"></script>
<link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo('template_url').'/js/themes/blue/style.css'; ?>" />
<div class="wrap">
	<h2><?php echo get_user_meta( $currentViewData['sbr']->sbr_wp_user_id, 'first_name', true) .' '. 
					get_user_meta( $currentViewData['sbr']->sbr_wp_user_id, 'last_name', true) .' '.
					$currentViewData['heading']?></h2>
					
	<form action="" method="post" id="collect_form">
		<input type="hidden" name="todo" value="collect_payment" />
		<input type="hidden" name="subscription" value="<?php echo $currentViewData['sbr']->sbr_id; ?>" /> 
	</form>
	<table class="wp-list-table widefat fixed posts tablesorter" id="myTable">
		<thead>
			<tr>
				<th>Payment id</th>
				<th>From</th>
				<th>To</th>
				<th>Status</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
<?php
	
	foreach($currentViewData['pmt'] as $row ){
		$initiated = ($row->pmt_status == 'Initiated' ) ? true : false;
		?>
		<tr>
			<td><?php echo $row->pmt_id ?></td>
			<td><?php echo $row->pmt_period_from ?></td>
			<td><?php echo $row->pmt_period_to ?></td>
			<td><?php echo $row->pmt_status ?></td>
			<td><?php 
				if($initiated){
					$disabled = ( strtotime( $row->pmt_period_from ) >= strtotime("+10 days")) ? 'disabled="disabled"' : '';
				?>
					
					<button form="collect_form" name="pmt_id" <?php echo $disabled ?> value="<?php echo $row->pmt_id ?>" >Collect</button>
					
				<?php } ?>
			</td>
		</tr>
		<?php
	}
	
?>
	</tbody>
		
	</table>
</div>
<script>
$(document).ready(function() { 
	$("#myTable").tablesorter(); 
});
</script>