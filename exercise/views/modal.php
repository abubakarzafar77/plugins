<div class="modal-header">
    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true" class="close"><img src="/wp-content/plugins/exercise/images/Close_Icon_Dark.png" style="width: 16px;height: 16px;margin-top: 12px;" class="close" /></span></button>
    <h3 id="myModalLabel" class="modal-title"><?php echo stripslashes($category_name);?> (<?php echo count($exercises);?> oppgaver)</h3>
    <div class="head-btm">
        <span><?php echo $parent_category_name;?></span>
        <ul id="filters">
            <li>
                <select onchange="triggerFilter(this);">
                    <option value="duration"<?php echo ($sort_by == 'duration'?' selected="selected"':'');?>>Sorter på forventet tidsbruk</option>
                    <option value="times_completed"<?php echo ($sort_by == 'times_completed'?' selected="selected"':'');?>>Sorter på antall ganger fullført</option>
                    <option value="days_since_completion"<?php echo ($sort_by == 'days_since_completion'?' selected="selected"':'');?>>Sorter på dager siden fullført</option>
                    <option value="year"<?php echo ($sort_by == 'year'?' selected="selected"':'');?>>Sorter på eksamens dato</option>
                </select>
            </li>
            <li<?php echo ($sort_by == 'duration'?' class="active"':'');?> id="duration" style="display: none;">
                <?php
                    $img = '';
                    $sortby = 'DESC';
                    if($sort_by == 'duration' && $sort_type == 'DESC'){
                        $img = '<img src="'.$this->pluginUrl.'/images/sort_za.png" style="width: 11px;" />';
                        $sortby = 'ASC';
                    }else if($sort_by == 'duration' && $sort_type == 'ASC') {
                        $img = '<img src="'.$this->pluginUrl.'/images/sort_az.png" style="width: 11px;" />';
                        $sortby = 'DESC';
                    }
                ?>
                <a href="javascript://" class="sort" data-parent="<?php echo $parent_id;?>" data-element="<?php echo $_selected;?>" data-term="<?php echo $term_id;?>" data-sortby="duration" data-sorttype="<?php echo $sortby;?>">Forventet tidsbruk</a> <?php echo $img;?>
            </li>
            <li<?php echo ($sort_by == 'times_completed'?' class="active"':'');?> id="times_completed" style="display: none;">
                <?php
                    $img = '';
                    $sortby = 'DESC';
                    if($sort_by == 'times_completed' && $sort_type == 'DESC'){
                        $img = '<img src="'.$this->pluginUrl.'/images/sort_za.png" style="width: 11px;" />';
                        $sortby = 'ASC';
                    }else if($sort_by == 'times_completed' && $sort_type == 'ASC') {
                        $img = '<img src="'.$this->pluginUrl.'/images/sort_az.png" style="width: 11px;" />';
                        $sortby = 'DESC';
                    }
                ?>
                <a href="javascript://" class="sort" data-parent="<?php echo $parent_id;?>" data-element="<?php echo $_selected;?>" data-term="<?php echo $term_id;?>" data-sortby="times_completed" data-sorttype="<?php echo $sortby;?>">Antall ganger fullført</a> <?php echo $img;?>
            </li>
            <li<?php echo ($sort_by == 'days_since_completion'?' class="active"':'');?> id="days_since_completion" style="display: none;">
                <?php
                    $img = '';
                    $sortby = 'DESC';
                    if($sort_by == 'days_since_completion' && $sort_type == 'DESC'){
                        $img = '<img src="'.$this->pluginUrl.'/images/sort_za.png" style="width: 11px;" />';
                        $sortby = 'ASC';
                    }else if($sort_by == 'days_since_completion' && $sort_type == 'ASC') {
                        $img = '<img src="'.$this->pluginUrl.'/images/sort_az.png" style="width: 11px;" />';
                        $sortby = 'DESC';
                    }
                ?>
                <a href="javascript://" class="sort" data-parent="<?php echo $parent_id;?>" data-element="<?php echo $_selected;?>" data-term="<?php echo $term_id;?>" data-sortby="days_since_completion" data-sorttype="<?php echo $sortby;?>">Dager siden fullført</a> <?php echo $img;?>
            </li>
            <li<?php echo ($sort_by == 'year'?' class="active"':'');?> id="year" style="display: none;">
                <?php
                    $img = '';
                    $sortby = 'DESC';
                    if($sort_by == 'year' && $sort_type == 'DESC'){
                        $img = '<img src="'.$this->pluginUrl.'/images/sort_za.png" style="width: 11px;" />';
                        $sortby = 'ASC';
                    }else if($sort_by == 'year' && $sort_type == 'ASC') {
                        $img = '<img src="'.$this->pluginUrl.'/images/sort_az.png" style="width: 11px;" />';
                        $sortby = 'DESC';
                    }
                ?>
                <a href="javascript://" class="sort" data-parent="<?php echo $parent_id;?>" data-element="<?php echo $_selected;?>" data-term="<?php echo $term_id;?>" data-sortby="year" data-sorttype="<?php echo $sortby;?>">Eksamens dato</a> <?php echo $img;?>
            </li>
        </ul>
    </div>
</div>
<div class="modal-body">
    <?php
    if(count($exercises) > 0){
    foreach($exercises as $exercise){?>
        <?php
            $correct_alternative = str_replace('alt', 'alt_', $exercise['corr_alternative']);
            $correct_answer = str_replace('alt', '', $exercise['corr_alternative']);
            $exercise_id = $exercise['id'];
        ?>
        <div class="block" data-exercise-id="<?php echo $exercise['id'];?>" data-course="<?php echo $course;?>">
            <div class="text-holder">
                <p class="top-text"><?php echo do_shortcode(stripslashes($exercise['context']));?></p>
                <?php if($exercise['solution_setup'] == 'multiple'):?>
                    <form class="form-check form-two">
                        <div class="myrow">
                            <label for="alt_1" style="vertical-align: top;">Svar 1</label>
                            <input type="radio"  data-exercise-id="<?php echo $exercise['id'];?>" id="alt_1" name="alt" value="1" style="vertical-align: top; margin-top: 4px;" />
                            <label><?php echo do_shortcode(stripslashes($exercise['alt_1']));?></label>
                        </div>
                        <div class="myrow">
                            <label for="alt_2" style="vertical-align: top;">Svar 2</label>
                            <input type="radio"  data-exercise-id="<?php echo $exercise['id'];?>" id="alt_2" name="alt" value="2" style="vertical-align: top; margin-top: 4px;" />
                            <label><?php echo do_shortcode(stripslashes($exercise['alt_2']));?></label>
                        </div>
                        <div class="myrow">
                            <label for="alt_3" style="vertical-align: top;">Svar 3</label>
                            <input type="radio"  data-exercise-id="<?php echo $exercise['id'];?>" id="alt_3" name="alt" value="3" style="vertical-align: top; margin-top: 4px;" />
                            <label><?php echo do_shortcode(stripslashes($exercise['alt_3']));?></label>
                        </div>
                        <input type="hidden" data-exercise-id="<?php echo $exercise['id'];?>" id="correct" value="<?php echo $correct_answer;?>" />
                    </form>
                <?php if(is_user_logged_in()){?>
                        <?php for($i=1;$i<4;$i++){?>
                            <?php if($i == $correct_answer){?>
                <p style="margin-left: 30px;" class="messages hide right_<?php echo $i;?>"><span class="green">Hvorfor dette svaret er riktig</span> <br /> <?php echo do_shortcode(stripslashes(strip_tags($exercise[$correct_alternative.'_exp'], '<br><br /><br/><img>')));?></p>
                        <?php } else {?>
                            <p style="margin-left: 30px;" class="messages hide wrong_<?php echo $i;?>"><span class="red">Hvorfor dette var feil svar</span> <br /> <?php echo do_shortcode(stripslashes(strip_tags($exercise['alt_'.$i.'_exp'], '<br><br /><br/>')));?></p>
                        <?php }?>
                            <?php }?>
                <?php }else{?>
                            <p style="margin-left: 30px;" class="messages hide wrong_<?php echo $exercise_id;?>"><span><a style="color: #f3cba2;font-size: 16px;font-weight: bold;" href="/logginn">Logg inn</a> for å se riktig svar og registrer din fremgang!</span></p>
                <?php }?>
                <div class="btm-block">
                    <!--Removed Hide Class-->
                    <?php if(is_user_logged_in()){?>
                <div class="btn-holder hide_alt_block">
                    <a class="btn-color deliever" href="javascript://">Lever svar</a>
                    <a class="btn-color hide gray ex-reload1" href="javascript://">Nullstill</a>
                </div>
                    <?php }else{?>
                    <div class="btn-holder hide_alt_block">
                        <a class="btn-color not-deliever" href="javascript://">Lever svar</a>
                    </div>
                    <?php }?>
<!--                <div class="btn-holder alt_block">
                    <a class="btn-color see_alts" href="javascript://">Se svaralternativer</a>
                </div>-->
                <ul class="list-detail">
                    <li>
                        <a href="javascript://"><?php echo str_replace('-', ' ', str_replace('|', ' to ', $exercise['duration']));?> forventet løsningstid</a>
                    </li>
                    <li>
                        <a href="javascript://"><?php echo $exercise['times_completed'];?> gang fullført</a>
                    </li>
                    <li>
                        <?php if($exercise['last_answered']){?>
                            <a href="javascript://"><?php echo date('j F', $exercise['last_answered']);?> sist besvart</a>
                        <?php } else {?>
                            <a href="javascript://">ikke besvart</a>
                        <?php }?>
                    </li>
                    <?php
                    $term_s;
                    if($exercise['term'] == "spring"){
                        $term_s = "vår";
                    }else if($exercise['term'] == "autumn"){
                        $term_s = "høst";
                    }
                    ?>
                    <li>
                        <a href="javascript://"><?php echo get_cat_name($course) . " " .$exercise['year'] . " " . $term_s ;?></a>
                    </li>
                    <?php if(is_super_admin($current_user->ID)){?>
                        <li>
                            <a href="javascript://"><?php echo $exercise['id'];?></a>
                        </li>
                         <li>
                            <a href="javascript://"><?php echo $exercise['exercise_name'];?></a>
                        </li>
                    <?php }?>
                </ul>
            </div>
                <?php else:?>
                    <?php if(is_user_logged_in()){?>
                    <form class="form-check form-two1">
                        <div class="myrow">
                            <p style="color: green;margin-top: 30px;">Riktig løsning</p>
                            <?php echo do_shortcode(stripslashes($exercise['alt_1']));?>
                            <p style="color: green;margin-top: 40px;margin-bottom: 20px;">Registrer at du greide denne oppgaven?</p>
                        </div>
                        <div class="myrow">
                            <input class="alt_1" type="radio" id="alt_1" name="alt" value="1" style="vertical-align: top; margin-top: 4px;" />
                            <label for="alt_1" style="vertical-align: initial;">Ja - registrer nå</label>
                        </div>
                        <div class="myrow">
                            <input class="alt_2" type="radio" id="alt_2" name="alt" value="2" style="vertical-align: top; margin-top: 4px;" />
                            <label for="alt_2" style="vertical-align: initial;">Jeg vil registrer denne oppgaven som bestått på et senere tidspunk</label>
                        </div>
                        <input type="hidden" id="correct" value="<?php echo $correct_answer;?>" />
                    </form>
                    <?php }else{?>
                            <form class="form-check form-two1">
                                <p style="margin-left: 30px;" class="messages"><span><a style="color: #f3cba2;font-size: 16px;font-weight: bold;" href="/logginn">Logg inn</a> for å se riktig svar og registrer din fremgang!</span></p> 
                            </form>
                    <?php }?>
                            <div class="btm-block">
                                <!--class name chnaged from hide_alt_block to hide_alt_block1-->
                <div class="btn-holder hide hide_alt_block1">
                    <a class="btn-color gray hidealt" href="javascript://">Skjul svar</a>
                    <!--<a class="btn-color deliever" href="javascript://">Lever svar</a>-->
                </div>
                                <!--class name changed alt_block to alt_block1-->
                <div class="btn-holder alt_block1">
                    <a class="btn-color see_alts" href="javascript://">Se løsning og registrer oppgaven</a>
                </div>
                <ul class="list-detail">
                    <li>
                        <a href="javascript://"><?php echo str_replace('-', ' ', str_replace('|', ' to ', $exercise['duration']));?> forventet løsningstid</a>
                    </li>
                    <li>
                        <a href="javascript://"><?php echo $exercise['times_completed'];?> gang fullført</a>
                    </li>
                    <li>
                        <?php if($exercise['last_answered']){?>
                            <a href="javascript://"><?php echo date('j F', $exercise['last_answered']);?> sist besvart</a>
                        <?php } else {?>
                            <a href="javascript://">ikke besvart</a>
                        <?php }?>
                    </li>
                     <?php
                    $term_s;
                    if($exercise['term'] == "spring"){
                        $term_s = "vår";
                    }else if($exercise['term'] == "autumn"){
                        $term_s = "høst";
                    }
                    ?>
                    <li>
                        <a href="javascript://"><?php echo get_cat_name($course) . " " .$exercise['year'] . " " . $term_s ;?></a>
                    </li>
                    <?php if(is_super_admin($current_user->ID)){?>
                        <li>
                            <a href="javascript://"><?php echo $exercise['id'];?></a>
                        </li>
                        <li>
                            <a href="javascript://"><?php echo $exercise['exercise_name'];?></a>
                        </li>
                    <?php }?>
                </ul>
            </div>
                <?php endif;?>
            </div>
            <?php if(is_super_admin($current_user->ID)){?>
                <div class="edit_exercise"><a href="/wp-admin/admin.php?page=create_exercise&id=<?php echo $exercise['id'];?>" target="_blank">Edit exercise</a></div>
            <?php }?>
        </div>
    <?php }?>
    <?php } else {?>
        <div class="block">
            <div class="text-holder">
                <div class="myrow">no exercise found</div>
            </div>
        </div>
    <?php }?>
    <?php /*
    <div class="block b-normal">
        <div class="text-holder">
            <span class="bold-text">1,23 * 10<sup>-3</sup></span>
            <form class="form-check form-one">
                <div class="myrow">
                    <label for="r-1">Svar 1:</label>
                    <input type="radio" id="r-1" name="group-1">
                    <label>9,3</label>
                </div>
                <div class="myrow">
                    <label for="r-2">Svar 2:</label>
                    <input type="radio" id="r-2" name="group-1">
                    <label>1230</label>
                </div>
                <div class="myrow">
                    <label for="r-3">Svar 3:</label>
                    <input type="radio" id="r-3" name="group-1">
                    <label>0,00123</label>
                </div>
            </form>
        </div>
        <div class="btm-block">
            <div class="btn-holder">
                <a class="btn-color gray" href="#">Hide alternatives</a>
                <a class="btn-color" href="#">Deliver</a>
            </div>
            <ul class="list-detail">
                <li>
                    <a href="#">1 min duration</a>
                </li>
                <li>
                    <a href="#">0 times completed by you</a>
                </li>
                <li>
                    <a href="#">3 October last answered</a>
                </li>
                <li class="active">
                    <a href="#">relevant video</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="block b-error">
        <div class="text-holder">
            <strong class="top-text">Hva blir 5 - 2(-3)?</strong>
            <form class="form-check form-two">
                <div class="myrow">
                    <label for="r-2-1">Svar 1:</label>
                    <input type="radio" id="r-2-1" name="group-2">
                    <label>-9</label>
                </div>
                <div class="myrow a-wrong">
                    <label for="r-2-2">Svar 2:</label>
                    <input type="radio" checked="" id="r-2-2" name="group-1">
                    <label>9</label>
                </div>
                <div class="myrow">
                    <label for="r-2-3">Svar 3:</label>
                    <input type="radio" id="r-2-3" name="group-1">
                    <label>11</label>
                </div>
            </form>
            <p><span class="red">Why this answer is wrong</span> <br>"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.”</p>
        </div>
        <div class="btm-block">
            <div class="btn-holder">
                <a class="btn-color gray" href="#">Hide alternatives</a>
                <a class="btn-color" href="#">Deliver new answer</a>
            </div>
            <ul class="list-detail">
                <li>
                    <a href="#">1 min expected duration</a>
                </li>
                <li>
                    <a href="#">0 times completed by you</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="block b-success">
        <div class="text-holder">
            <strong class="top-text">Hva blir 5 - 2(-3)?</strong>
            <form class="form-check form-two">
                <div class="myrow">
                    <label for="r-2-1">Svar 1:</label>
                    <input type="radio" checked="" id="r-2-1" name="group-2">
                    <label>-9</label>
                </div>
                <div class="myrow a-wrong">
                    <label for="r-2-2">Svar 2:</label>
                    <input type="radio" id="r-2-2" name="group-1">
                    <label>9</label>
                </div>
                <div class="myrow">
                    <label for="r-2-3">Svar 3:</label>
                    <input type="radio" id="r-2-3" name="group-1">
                    <label>11</label>
                </div>
            </form>
            <p><span class="green">Hvorfor dette svaret er riktig</span> <br> "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.”</p>
        </div>
        <div class="btm-block">
            <div class="btn-holder">
                <a class="btn-color gray" href="#">Hide alternatives</a>
                <a class="btn-color" href="#">Deliver new answer</a>
            </div>
            <ul class="list-detail">
                <li>
                    <a href="#">1 min expected duration</a>
                </li>
                <li>
                    <a href="#">0 times completed by you</a>
                </li>
                <li>
                    <a href="#">12 April last answered</a>
                </li>
            </ul>
        </div>
    </div>
    */?>
</div>