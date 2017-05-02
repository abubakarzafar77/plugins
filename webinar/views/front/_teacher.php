<?php
$first_name = get_user_meta($teacher->ID, 'first_name', true);
$last_name = get_user_meta($teacher->ID, 'last_name', true);
$age = get_user_meta($teacher->ID, 'webinar_age', true);
$rate = get_user_meta($teacher->ID, 'webinar_rate', true);
$video = get_user_meta($teacher->ID, 'webinar_video', true);
$education = get_user_meta($teacher->ID, 'webinar_education', true);
$experience = get_user_meta($teacher->ID, 'webinar_experience', true);
$why_teach = get_user_meta($teacher->ID, 'webinar_why_teach', true);
$photo = get_user_meta($teacher->ID, 'webinar_photo', true);
?>
<div class="col-md-12">
<!--------------------------------->
<div class="p-profile-page">
 
  <div class="p-profile-pic"><img height="228" src="http://dev.mattevideo.no/wp-content/uploads/2016/06/profile-picture1.jpeg" alt="Muhammad Saleem"></div>
  <div class="p-profile-detail">
    <div class="user-heading"><?php echo $first_name;?>  <?php echo $last_name;?>  <span>(<?php echo $age;?>)</span></div>
    <div class="p-education"><strong>Underviser.</strong> <?php echo $education;?></div>
    <div class="p-teach">
      	<strong>Hvorfor jeg er studiekamerat.</strong>
		<?php echo $why_teach;?>
    						
    </div>
    <div class="p-experience">
       <strong>Erfaring.</strong>
        <?php echo $experience;?>
    </div>
    <div class="p-web-row">
      <div class="p-completed-row">Underviste timer. <?php echo $model->get_completed_webinars_count($teacher->ID);?></div>
      <div class="p-hearts">Hjerter. <?php echo round($model->get_average_rating($teacher->ID));?></div>
      <div class="clearfix"></div>
    </div>
  </div>
  <div class="clearfix"></div>
</div>



<!------------------------------->

   
    <div class="video-box">
        <iframe src="<?php echo $video;?>" width="100%" height="400" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>
    </div>
</div>
