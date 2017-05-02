<div class="a-main-content">
    <section class="a-diagram-section">
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

                        <li data-year="<?php echo $i;?>" class="active"  data-term="<?php echo $t_eng;?>">
                            <a href="javascript:;" class="year" data-term="<?php echo $t_eng;?>"><?php echo $i;?> <?php echo $term;?></a>
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
                        $element = 'yes';
                    }else if($tool == 'no'){
                        $class = 'a-without-tools';
                        $text = 'Uten hjelpemidler';
                        $element = 'no';
                    }
                    ?>
                    <li>
                        <input type="checkbox" checked class="<?php echo $class;?> checkbox-tools" id="<?php echo $element;?>" name="<?php echo $element;?>" data-element="<?php echo $element;?>">
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
                        $element = 'calculation';
                    }else if($_calculation == 'text'){
                        $class = 'a-text-questions';
                        $text = 'Tekst oppgaver';
                        $element = 'text';
                    }
                    ?>
                    <li>
                        <input type="checkbox" checked class="checkbox-extype" id="<?php echo $element;?>" name="<?php echo $element;?>" data-element="<?php echo $element;?>">
                        <label for="<?php echo $element;?>"><?php echo $text;?></label>
                        <div class="check"></div>
                    </li>
                <?php }?>
            </ul>
        </div>
        <!-- mew menu top end -->
    </section>
    
    <div class="m-content-detail"></div>
    <?php $categories = get_categories(array('parent' => $course));?>
    <section class="a-rating-section">
        <div class="row max_width">
            <?php foreach($categories as $key=>$category){?>
            <div class="col-sm-4">
                <div class="a-regning">
                    <h3><?php echo $category->name;?></h3>
                    <ul class="a-rating-list">
                        <?php $_categories = get_categories(array('parent' => $category->term_id));?>
                        <?php foreach ($_categories as $_category){?>
                            <li>
                                <p>
                                    <a href="javascript:;"><?php echo $_category->name;?></a>
                                </p>
                            </li>
                        <?php }?>
                    </ul>
                </div>
            </div>
            <?php if($key == 2){?>
        </div>
        <div class="row max_width">
            <?php }?>
            <?php }?>
        </div>
    </section>

    <section class="a-mark-section">
        <div class="row max_width">
            <div class="col-sm-3">
                <div class="a-mark-box">
                    <p>Mark with color</p>
                </div>
            </div>
            <div class="col-sm-7">
                <div class="a-mark-rating-box">
                    <div class="row a-dott">
                        <div class="col-sm-5">
                            <a href="javascript:;" class="a-active">
                                <div>
                                    <input type="radio" id="last_completed" name="radio" class="custom-radio" value="i_completed" onclick="loadDiagram(jQuery('.a-year-list'), _selected);" checked />
                                    <label for="last_completed"><span></span>når jeg gjorde oppgaven sist</label>
                                </div>
                                <!--  -->
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <ul class="a-rating clearfix a-active last_completed">
                                <li>
                                    <p>
                                        <span class="a-green"></span>
                                        <span class="a-green"></span>
                                        <span class="a-green"></span>
                                        <span class="a-green"></span>
                                    </p>
                                    <a href="javascript:;">1-30 dager</a>
                                </li>
                                <li>
                                    <p>
                                        <span class="a-blue"></span>
                                        <span class="a-blue"></span>
                                        <span class="a-blue"></span>
                                        <span class="a-blue"></span>
                                    </p>
                                    <a href="javascript:;">30-90 dager</a>
                                </li>
                                <li>
                                    <p>
                                        <span class=""></span>
                                        <span class=""></span>
                                        <span class=""></span>
                                        <span class=""></span>
                                    </p>
                                    <a href="javascript:;">ubesvart</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row a-dott">
                        <div class="col-sm-5">
                            <a href="javascript:;" class="">
                                <div>
                                    <input type="radio" id="how_other" name="radio" value="other" class="custom-radio" onclick="loadDiagram(jQuery('.a-year-list'), _selected);" />
                                    <label for="how_other"><span></span>hvordan  andre elever svarer</label>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <ul class="a-rating clearfix disable how_other">
                                <li>
                                    <p>
                                        <span class="a-green"></span>
                                        <span class="a-green"></span>
                                        <span class="a-green"></span>
                                        <span class="a-green"></span>
                                    </p>
                                    <a href="javascript:;">80% riktig</a>
                                </li>
                                <li>
                                    <p>
                                        <span class="a-yellow"></span>
                                        <span class="a-yellow"></span>
                                        <span class="a-yellow"></span>
                                        <span class="a-yellow"></span>
                                    </p>
                                    <a href="javascript:;">60% riktig</a>
                                </li>
                                <li>
                                    <p>
                                        <span class="a-orange"></span>
                                        <span class="a-orange"></span>
                                        <span class="a-orange"></span>
                                        <span class="a-orange"></span>
                                    </p>
                                    <a href="javascript:;">40% riktig</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div><a class="tab5_view">Trykk her for den gamle quizen</a></div>
                </div>
            </div>
        </div>
    </section>
</div>