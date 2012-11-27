<div id="brands_page">

    <?php foreach ($vocabulary as $key => $value){ ?>

    <div id="brands_bloc_<?php echo $key; ?>" class="brands-blocs">

        <?php foreach ($value as $cat){ ?>

        <ul>
            <span class="cat_title"><?php echo $cat['name'];?></span>
            <?php foreach ($cat['below'] as $brand){ ?>

            <li><?php echo l($brand['name'], 'brands/' . $brand['tid']); ?></li>

            <?php } ?>
        </ul>

        <?php } ?>

    </div>

    <?php } ?>

</div>