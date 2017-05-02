<br>
<?php 

include_once 'function.php';

    if(isset($_POST['submit'])){
        update_plugin_setting($_POST['date'],$_POST['length']);
    }
?>

<div class="wrap">  
    <?php    echo "<h2>" . __( 'My First custom plugin ', 'oscimp_trdom' ) . "</h2>"; ?>  
<br><br>
    <form name="custom_plugin_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
        
        <table>
            
        <tr>
            <td>Date: </td>
            <td><input type="text" name="date" ></td>
        
            
        </tr>
        <tr>
            <td>Length </td>
            <td><input type="text" name="length" >  </td>
        
            
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" name="submit" value="submit" >  </td>
        
            
        </tr>
        
          
        </table>
        
    </form>  
</div>  

