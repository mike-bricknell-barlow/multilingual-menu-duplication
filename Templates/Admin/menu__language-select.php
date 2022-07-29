<div class="mmd__language-select">
    <label for="<?php echo $fieldId ?>" class="mmd__language-select__label">
        <strong><?php echo $fieldLabel ?></strong>
    </label>
    <select name="<?php echo $fieldId ?>" id="<?php echo $fieldId ?>">
        <option value="">Select</option>
        <?php foreach ($languages as $language) : ?>
            <?php $selected = ($default && pll_current_language() == $language['slug']) ? 'selected="selected"' : ''; ?>
            
            <option value="<?php echo $language['slug'] ?>" <?php echo $selected; ?>>
                <?php echo $language['name'] ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
