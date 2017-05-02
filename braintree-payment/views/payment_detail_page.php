<script type="text/javascript" src="<?php echo get_bloginfo('template_url').'/js/jquery.tablesorter.js'; ?>"></script>
<link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo('template_url').'/js/themes/blue/style.css'; ?>" />
<div class="wrap">

    <h2>Payment Details <?php if($_GET['status'] == 'Expired'){?><a href="?page=bt-payment_detail&user=<?php echo $_GET['user'];?>&status=<?php echo $_GET['status'];?>&subscrib_id=<?php echo $_GET['subscrib_id'];?>&action=retry">Retry Charge</a><?php }?></h2>

    <?php if(isset($_GET['message'])){?>
        <div id="message" class="updated below-h2"><p><?php echo $_GET['message'];?></p></div>
    <?php }?>

    <table id="myTable" class="wp-list-table widefat fixed posts tablesorter">
        <thead>
        <tr>
            <th>Subscription ID</th>
            <th>Billing From</th>
            <th>Billing To</th>
            <th>transaction Date</th>
            <th>Billed Amount</th>
            <th>Reason</th>
        </tr>
        </thead>
        <tbody class="the-list">

        <?php
        if($users_sub){
            foreach($users_sub as $sub){ ?>
                <tr>
                    <td>
                        <?php echo $sub->subscription_id; ?>
                    </td>
                    <td>
                        <?php
                        if($sub->billing_start_date != "0"){
                            echo ( date("Y-m-d H:i:s",$sub->billing_start_date) );
                        }

                        ?>
                    </td>
                    <td>
                        <?php
                        if($sub->billing_end_date != "0"){
                            echo ( date("Y-m-d H:i:s",$sub->billing_end_date) );
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if($sub->webhook_date != "0"){
                            echo ( date("Y-m-d H:i:s",$sub->webhook_date) );
                        }
                        ?>
                    </td>

                    <td>
                        <?php echo $sub->billed_amount;?>
                    </td>

                    <td>
                        <?php echo ucfirst(str_replace("_", " ", $sub->type)); ?>
                    </td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="4">
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
    $(window).ready(function(){
        if($("#myTable").length > 0){
            $("#myTable").tablesorter({
                // pass the headers argument and assing a object 
//                headers: { 
//                    // assign the sixth column (we start counting zero) 
//                    3: { 
//                        // disable it by setting the property sorter to false 
//                        sorter: false 
//                    }
//                }
            });
        }
    });
</script>