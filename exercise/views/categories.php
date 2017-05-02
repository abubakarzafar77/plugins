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
                            <a id="term_<?php echo $_category->term_id;?>" class="d-block-opener" data-parent="<?php echo $category->term_id;?>" data-element="<?php echo (isset($_POST['selected'])?$_POST['selected']:'');?>" data-term="<?php echo $_category->term_id;?>"><?php echo stripslashes($_category->name);?></a>
                        </p>
                        <?php echo $this->get_category_solution($where, $course, $__terms, $_category->term_id, $color);?>
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