<?php if($block->getDisabled()) {?>
<input type="hidden" name="<?= $block->getName() ?>" value="<?= $block->getSelected()?>" />
<?php }?>

<select name="<?= $block->getName() ?>" id="<?= $block->getId() ?>" <?php if($block->getClass()) {?>class="<?= $block->getClass() ?>"<?php }?> <?php if($block->getOnChange()) {?>data-onchange="<?= $block->getOnChange()?>"<?php }?>  <?= ($block->getDisabled()) ? 'disabled' : '' ?> autocomplete="off">
    <?php if(!$block->getRequired()) {?>
        <option value="">------------</option>
    <?php }?>
    <?php foreach($block->getOptions() as $val => $title) {?>
        <?php if(is_array($title)) {?>
        <optgroup label="<?= $val ?>">
            <?php foreach($title as $subVal => $subTitle) {?>
            <option value="<?= $subVal ?>" <?= ($block->getSelected() == $subVal) ? 'selected' : ''?>><?= $subTitle?></option>
            <?php }?>
        </optgroup>
        <?php } else {?>
        <option value="<?= $val ?>" <?= ($block->getSelected() == $val) ? 'selected' : ''?>><?= $title?></option>
        <?php }?>
    <?php }?>
</select>	