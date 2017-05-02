<?php 
$user_info = $responce['data']['user_info'];
?>

<div id="payment_box">
    <?php 
    if($responce['status'] == 'error'){
    ?>
        <h2>Beklager, din transaksjon gikk ikke igjennom.</h2>
        <div class="error">
            <?php echo $responce['message']; ?>
        </div>
    <?php 
    }
    else{ ?>
    
    <!-- Facebook Conversion Code for FB customer registration -->
	<script>(function() {
    var _fbq = window._fbq || (window._fbq = []);
    if (!_fbq.loaded) {
    var fbds = document.createElement('script');
    fbds.async = true;
    fbds.src = '//connect.facebook.net/en_US/fbds.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(fbds, s);
    _fbq.loaded = true;
    }
    })();
    window._fbq = window._fbq || [];
    window._fbq.push(['track', '6013179277746', {'value':'0.00','currency':'USD'}]);
    </script>
    <noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6013179277746&amp;cd[value]=0.00&amp;cd[currency]=USD&amp;noscript=1" /></noscript>
    <!-- Facebook Conversion Code for FB customer registration -->
    
	<div class="confirm_information">
		<h2>Takk for din bestilling!</h2>
        <!--<p>Brukernavn og passord sendes på epost straks banktransaksjon er godkjent.</p>-->
        <p><?php echo ('Brukernavn og passord sendes på epost straks banktransaksjon er godkjent. Viktig! Noen epostklienter som hotmail.no og online.no kan filtrere eposter inn som søpplepost, eller spam. Sjekk derfor spam eller søppelfilter for å se om eposten kan ha havnet der, dersom du ikke mottar innloggingsepost på vanlig innboks.');?></p>
        
	</div>
    <?php 
    } ?>
</div>