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
                            <a href="javascript:;" class="d-block-opener"><?php echo stripslashes($_category->name);?></a>
                        </p>
                        <p>
                            <?php echo $this->show_exercises($terms, $_category->term_id, $course);?>
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