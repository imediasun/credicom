<?php foreach($block->getOptions() as $val => $title) { ?>
    <div class="checkboxWrapper"> 
        <label for="<?= sprintf('%s-%s', $block->getId(), $val) ?>"><?= $title?></label>
        <?php 
            echo new \Core\Block\Form\Element\Checkbox(array(
                'name' => sprintf('%s[]', $block->getName()),
                'id' => sprintf('%s-%s', $block->getId(), $val),
                'value' => $val,
                'checked' => in_array($val, $block->getSelected()),
                'class' => $block->getClass(),
                'onChange' => $block->getOnChange(),
                'disabled' => $block->getDisabled(),
            ));
        ?>
    </div>
<?php }?>