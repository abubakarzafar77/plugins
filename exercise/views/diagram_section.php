<!-- mew menu top start -->
<div class="a_menu_section clearfix">
    <?php
    $course = get_category_parent(true);
    $course = $course['parent_id'];
    ?>
    <ul class="a_tab_menu clearfix"  data-course="<?php echo $course;?>">

        <?php
        $start = date('Y') - 3;
        $end = date('Y')-1;
            for($i=$start; $i<=$end; $i++) {
                $terms = array('spring'=>'vår', 'autumn'=>'høst');
                foreach ($terms as $t_eng=>$term):
                ?>

                <li data-year="<?php echo $i;?>" class="active" data-term="<?php echo $t_eng;?>">
                    <a href="javascript:;" class="year"><?php echo $i;?> <?php echo $term;?></a>
                </li>
                <?php
                endforeach;
            }
        ?>
    </ul>
    <ul class="a_radio_links clearfix">
        <?php foreach($_tools as $key=>$tool){
            $class = '';
            $text = '';
            $element = '';
            if($tool == 'yes'){
                $class = 'a-with-tools';
                $text = 'Med hjelpemidler';
                $element = 'ex|yes';
            }else if($tool == 'no'){
                $class = 'a-without-tools';
                $text = 'Uten hjelpemidler';
                $element = 'ex|no';
            }
            ?>
            <li>
                <input type="checkbox" checked class="<?php echo $class;?>" id="<?php echo $element;?>" name="<?php echo $element;?>" data-element="<?php echo $element;?>">
                <label for="<?php echo $element;?>">Del <?php echo $key+1;?>: <?php echo $text;?></label>
                <div class="check"></div>
            </li>
        <?php }?>
        <?php foreach($_question_type as $key=>$_calculation){
            $class = '';
            $text = '';
            $element = '';
            if($_calculation == 'calculation'){
                $class = 'a-calculation-questions';
                $text = 'Regne oppgaver';
                $element = 'ex|no|calculation';
            }else if($_calculation == 'text'){
                $class = 'a-text-questions';
                $text = 'Tekst oppgaver';
                $element = 'ex|no|text';
            }
            ?>
            <li>
                <input type="checkbox" checked id="<?php echo $element;?>" name="<?php echo $element;?>" data-element="<?php echo $element;?>">
                <label for="<?php echo $element;?>"><?php echo $text;?></label>
                <div class="check"></div>
            </li>
        <?php }?>
    </ul>
</div>
<!-- mew menu top end -->