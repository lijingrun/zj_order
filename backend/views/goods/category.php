<?php
/**
 * Created by PhpStorm.
 * User: lijingrun
 * Date: 2016/8/17
 * Time: 14:39
 */
?>
<style>
    li{
        margin:5px;
    }
</style>
<div>
    <ul>
        <?php foreach($category as $children): ?>
        <?php if(empty($children['child'])){ ?>
        <a href="index.php?r=goods&cat_id=<?php echo $children['cat_id']?>">
        <?php } ?>
        <li>
            <?php echo $children['cat_name']?>
            <?php if($children['child']){ ?>
            <ul>
                <?php foreach($children['child'] as $child): ?>
                <?php if(empty($child['child'])){ ?>
                <a href="index.php?r=goods&cat_id=<?php echo $child['cat_id']?>">
                <?php } ?>
                <li>
                    <?php echo $child['cat_name']; ?>
                </li>
                <?php if(empty($child['child'])){ ?>
                </a>
                <?php } ?>
                        <?php endforeach; ?>
            </ul>
            <?php } ?>
        </li>
            <?php if(empty($children['chile'])){ ?>
            </a>
            <?php } ?>
        <?php endforeach; ?>
    </ul>
</div>


