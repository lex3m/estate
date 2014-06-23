<div class="<?php echo $divClass; ?>">
    <span class="search"><div class="<?php echo $textClass; ?>"><?php echo tc('Apartment square to'); ?>:</div></span>
    <span class="search">
        <input onblur="changeSearch();" type="text" name="land_square"
               class="width70 search-input-new"
               value="<?php echo isset($this->landSquare) && $this->landSquare ? CHtml::encode($this->landSquare) : ""; ?>"/>&nbsp;
        <span><?php echo tc("site_land_square"); ?></span>
    </span>
</div>