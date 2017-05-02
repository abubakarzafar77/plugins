jQuery(document).ready(function(){ 

	jQuery("select#pensum").change(function(){ 

        var post_string = "book=" + jQuery(this).val();

        jQuery.ajax({ 
            type: "POST", 
            data: post_string,  
            cache: false, 
            dataType: 'html',
            url: 'wp-content/plugins/Kapittelvelger/books.php', 
            timeout: 2000, 
            error: function() { 
                alert("En feil oppstod!"); 
            }, 
            success: function(data) {  
                jQuery("select#chapter option").remove(); 
 				jQuery("select#chapter").html(data);
            } 
        }); 
    });
    
    jQuery("select#chapter").change(function(){
 	
 		var pensum = jQuery("select#pensum").val(); 
    	var post_string = "pensum="+pensum+"&chapter="+jQuery(this).val();
    	
    	jQuery.ajax({
    		type: "POST",
    		data: post_string,
    		cache: "false",
    		dataType: 'html',
    		url: 'wp-content/plugins/Kapittelvelger/books.php',
    		timeout: 2000,
    		error: function(){
    				alert("En feil oppstod");
    		},
    		success: function(data) {
    			jQuery("select#subchapter option").remove();
    			jQuery("select#subchapter").html(data);
    		}
    	});
    });  
    
    jQuery("select#subchapter").change(function(){
    	var pensum = jQuery("select#pensum").val(); 
    	var chapter = jQuery("select#chapter").val();
    	var subchapter = jQuery(this).val();
    	var post_string = "pensum="+pensum+"&chapter="+chapter+"&subchapter="+subchapter;
    	
    	jQuery.ajax({
    		type: "POST",
    		data: post_string,
    		cache: "false",
    		dataType: 'html',
    		url: 'wp-content/plugins/Kapittelvelger/books.php',
    		timeout: 2000,
    		error: function(){
    				alert("En feil oppstod.");
    		},
    		success: function(data) {
    			jQuery("#chapterlinks").html(data);
    			
    		}
    	});
    });   
});
