<div class="wrap">
    <div class="icon32" id="icon-themes"></div>
    <h2>WP BrainTree Options</h2>
    
    <?php if(isset($responce) && $responce['status'] == 'error' ) { ?>
    <div class="error">
        <?php echo $responce['message']; ?>
    </div>
    <?php } ?>
    <?php if(isset($responce) && $responce['status'] == 'save' ) { ?>
    <div class="updated"><p><?php echo $responce['message']; ?></p></div>
    <?php } ?>

    <h2 class="nav-tab-wrapper">  
        <a class="nav-tab <?php echo ($_GET['tab'] == "api_key" || !(isset($_GET['tab']))) ? "nav-tab-active" : "" ; ?>" href="?page=manage_setting&tab=api_key">API Keys</a>
        <a class="nav-tab <?php echo ($_GET['tab'] == "options") ? "nav-tab-active" : "" ; ?>" href="?page=manage_setting&amp;tab=options">Options</a>
        <a class="nav-tab <?php echo ($_GET['tab'] == "help") ? "nav-tab-active" : "" ; ?>" href="?page=manage_setting&amp;tab=help">Help</a>
    </h2>  
    
    <?php if( $_GET['tab'] == "api_key" || !(isset($_GET['tab'])) ) { ?>

    <form action="?page=manage_setting&tab=api_key" method="post">

        <div class="postbox">
            <h3>Acquire API Keys</h3>
            <input type="hidden" value="api_key" name="tab">
            
            <p>
                It is first necessary to register for an account with <a href="https://www.braintreepayments.com/" target="_blank">BrainTree</a>.                        <br>
                Once an account is acquired, the following information can be found by logging in and clicking "Account -&gt; My User -&gt; API Keys".                        </p>
            <table class="form-table">
                <tbody>
                    <tr valign="top"><th scope="row">Merchant ID:</th>
                        <td><input type="text" value="<?php echo (isset($_POST['merchant_id'])) ? $_POST['merchant_id'] : $setting->merchant_id ; ?>" name="merchant_id" id="merchant_id"></td>
                    </tr>
                    <tr valign="top"><th scope="row">Public Key:</th>
                        <td><input type="text" value="<?php echo (isset($_POST['public_key'])) ? $_POST['public_key'] : $setting->public_key ; ?>" name="public_key" id="public_key"></td>
                    </tr>
                    <tr valign="top"><th scope="row">Private Key:</th>
                        <td><input type="text" value="<?php echo (isset($_POST['private_key'])) ? $_POST['private_key'] : $setting->private_key ; ?>" name="private_key" id="private_key"></td>
                    </tr>
                    <tr valign="top"><th scope="row">CSE Key:</th>
                        <td><textarea name="cse_key" type="text" id="cse_key"><?php echo (isset($_POST['cse_key'])) ? $_POST['cse_key'] : $setting->cse_key ; ?></textarea></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="submit">
            <input type="submit" name="submit" value="Save Changes" class="button-primary">
        </p>
    </form>
    <?php } 
    else if($_GET['tab'] == "options"){
    ?>
    <form action="?page=manage_setting&tab=options" method="post">
        
        <input type="hidden" value="options" name="tab">
        <div class="postbox">
            <h3>Additional Options:</h3>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">Sandbox Mode:</th>
                        <td>
                            <input type="checkbox" <?php echo (isset($setting->sandbox) && $setting->sandbox == '1') ? 'checked="checked" ' : ''; ?> value="1" name="sandbox" id="sandbox">
                            <br>
                            Check to run the plugin in sandbox mode.    
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="submit">
            <input type="submit" name="submit" value="Save Changes" class="button-primary">
        </p>
        
    </form>
    
    
    <?php 
    }
    else if($_GET['tab'] == "help"){
    ?>
    

    <div class="postbox">
            <h3>Acquire API Keys</h3>
            <p>
                It is first necessary to register for an account with <a href="https://www.braintreepayments.com/" target="_blank">BrainTree</a>.                        <br>
                Once an account is acquired, the following information can be found by logging in and clicking "Account -&gt; My User -&gt; API Keys".                        <br>
                This plugin is set to run in the BrainTree "Production" environment. If desired, the plugin may be switched to the "Sandbox" environment via the appropriate option.                        
            </p>

            <h3>Sandbox Mode</h3>
            <p>
                By default, this plugin will perform all transactions assuming the API keys are from a BrainTree Live Production Account.                        <br>
                The plugin may be switched to perform transactions into a BrainTree Sandbox Account; commonly used for testing.                        <br>
                Remember; a BrainTree Production Account and a BrainTree Sandbox Account will have different API keys.                        
            </p>

        </div>

    
    
    <?php 
    }
    ?>

</div>