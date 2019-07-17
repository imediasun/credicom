<?php if($block->getDefaultValue() !== false && !$block->getDisabled()) {?>
<input type="hidden" name="<?= $block->getName() ?>" value="<?= $block->getDefaultValue()?>" />
<?php }?>
<?php if($block->getDisabled()) {?>
<input type="hidden" name="<?= $block->getName() ?>" value="<?= $block->getValue()?>" />
<?php }?>
<input type="checkbox" name="<?= $block->getName() ?>" id="<?= $block->getId() ?>" value="<?= $block->getValue() ?>" <?php if($block->getChecked()) { ?>checked<?php }?> <?php if($block->getClass()) {?>class="<?= $block->getClass() ?>"<?php }?> <?php if($block->getStyle()) {?>style="<?= $block->getStyle() ?>"<?php }?> <?php if($block->getOnChange()) {?>data-onchange="<?= $block->getOnChange()?>"<?php }?>  <?= ($block->getDisabled()) ? 'disabled' : '' ?> autocomplete="off" />