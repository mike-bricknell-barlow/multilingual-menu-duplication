<div class="mmd__menu-select">
    <label for="menu-select" class="mmd__menu-select__label">
        <strong>Menus to translate</strong>
    </label>
    <select name="menu-select" id="menu-select" multiple class="mmd__menu-select__multiselect">
        <?php foreach ($menus as $menu) : ?>
            <option value="<?php echo $menu->term_id ?>">
                <?php echo $menu->name ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
