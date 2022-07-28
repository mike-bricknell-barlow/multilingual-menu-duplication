<div class="wrap">
    <h1>Multilingual Menu Duplication</h1>

    <div>
        <p><strong>1.</strong> Select the source and destination languages.</p>
        <?php
            $fieldId = 'source-language';
            $fieldLabel = 'Source language';
            $default = 'en';
            include 'menu__language-select.php';
        ?>

        <?php
            $fieldId = 'dest-language';
            $fieldLabel = 'Destination language';
            $default = false;
            include 'menu__language-select.php';
        ?>
    </div>

    <div>
        <p><strong>2.</strong> Choose the menus to be translated.</p>
        <p><i>Use Control+Click or Command+Click to select multiple.</i></p>
        <?php include 'menu__menu-select.php'; ?>
    </div>

    <input type="hidden" id="nonce" name="_wpnonce" value="<?php echo wp_create_nonce('wp_rest') ?>" />

    <div>
        <input
            type="submit"
            name="translate-menus"
            id="translate-menus"
            class="button button-primary button-large"
            value="Translate menus"
        >
    </div>

    <div class="mmd__result">
        <p>
            The selected menus have been added to the queue for translation.
            You will receive an email when all the menus have been translated.
        </p>
    </div>
</div>
