<?php
//
//$headers = "MIME-Version: 1.0" . "\r\n";
//$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
//$headers .= 'From: From: <numan.hassan@purelogics.net>'. "\r\n"; // sender
//$subject = 'Test';
//$message = 'this is a test';
//// message lines should not exceed 70 characters (PHP rule), so wrap it
//// send mail
//$to = 'qudrat.ullah@purelogics.net';
//if(mail($to,$subject,$message,$headers)){
//    echo "email send"; 
//}
//
//die;


     $type = "payment_email";
     $to = "qudrat.ullah@purelogics.net";
        $headers = "MIME-Version: 1.0\n" .
                "From: Mattevideo <kontakt@mattevideo.no>\n" .
                "Content-Type: text/html; charset=\"" .
                get_option('blog_charset') . "\"\n";





        $config = json_decode(file_get_contents(dirname(__FILE__) . '/config.json'), true);

        $subject = $config['email_templates'][$type]['subject'];

        $body = $config['email_templates'][$type]['body'];


        if($type='payment_email')
        {
            $attachments = dirname(__FILE__) . '/julekort.pdf';
        }

        /* if ($type == 'payment_email')
          {

          $attachments = dirname(__FILE__) . '/Kjøpsvilkår og 100% fornøyd garanti.pdf';

          $password = $patterns['{PASSWORD}'];

          //Send email to admin

          $admin = 'ksondresen@gmail.com';

          //$admin = 'muhammad.saleem@purelogics.net';

          $admin_msg = 'Mattevideo bruker har registrert med e-post: ' . $to . ' og passord:' . $password;

          $headers_admin = "From: $to <$to>" . "\r\n";



          wp_mail($admin, $subject, $admin_msg, $headers_admin);

          //$admin = 'numan.hassan@purelogics.net';
          //wp_mail( $admin, $subject, $admin_msg, $headers_admin);
          }
          else
          { */
//        $admin       = 'ksondresen@gmail.com';
//        $attachments = dirname(__FILE__) . '/julekort.pdf';
        $admin       = 'qudrat.ullah@purelogics.net';
        $admin_body  = $body;
        $admin_body .= "<br />Email was sent to: $to";
        $admin_email = wp_mail($admin, $subject, $admin_body, $headers, $attachments);
        //}


        $mail = wp_mail($to, $subject, $body, $headers, $attachments);

?>
