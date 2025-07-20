<?php
if (!defined('ABSPATH')) exit;

// Page d'options
function wadtoc_render_settings_page() {
    // Gestion du reset
    if (isset($_POST['wadtoc_reset_defaults']) && check_admin_referer('wadtoc_reset_defaults_action')) {
        update_option('wadtoc_headings', ['h1','h2','h3']);
        update_option('wadtoc_open', '1');
        update_option('wadtoc_title', 'Sommaire');
        update_option('wadtoc_bgcolor', '#fefefe');
        update_option('wadtoc_linkcolor', '#444');
        update_option('wadtoc_headercolor', '#444');
        update_option('wadtoc_maxwidth', '');
        update_option('wadtoc_iconcolor', '#444');
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Paramètres réinitialisés aux valeurs par défaut.', 'wadtoc') . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Paramètre table de matière</h1>
        <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == '1') : ?>
            <div class="notice notice-success is-dismissible"><p>Modifications enregistrées avec succès !</p></div>
        <?php endif; ?>
        <p>Utilisez le shortcode <code>[table_matiere]</code> dans vos articles pour afficher une table des matières automatique. Sélectionnez ci-dessous les options d’affichage.</p>
        <form method="post" action="options.php">
            <?php
            settings_fields('wadtoc_settings_group');
            do_settings_sections('wadtoc-settings');
            submit_button();
            ?>
        </form>
        <form method="post" action="">
            <?php wp_nonce_field('wadtoc_reset_defaults_action'); ?>
            <input type="hidden" name="wadtoc_reset_defaults" value="1">
            <button type="submit" class="button button-secondary" onclick="return confirm('Réinitialiser tous les paramètres par défaut ?')">Réinitialiser les paramètres par défaut</button>
        </form>
    </div>
    <?php
}

add_action('admin_init', function() {
    register_setting('wadtoc_settings_group', 'wadtoc_headings');
    register_setting('wadtoc_settings_group', 'wadtoc_open');
    register_setting('wadtoc_settings_group', 'wadtoc_title');
    register_setting('wadtoc_settings_group', 'wadtoc_bgcolor');
    register_setting('wadtoc_settings_group', 'wadtoc_linkcolor');
    register_setting('wadtoc_settings_group', 'wadtoc_headercolor');
    register_setting('wadtoc_settings_group', 'wadtoc_maxwidth');
    register_setting('wadtoc_settings_group', 'wadtoc_iconcolor');

    add_settings_section('wadtoc_main', 'Réglages principaux', null, 'wadtoc-settings');

    add_settings_field('wadtoc_headings', 'Niveaux de titres à inclure', function() {
        $val = get_option('wadtoc_headings');
        if ($val === false) $val = ['h1','h2','h3'];
        foreach(['h1','h2','h3','h4','h5','h6'] as $hx) {
            $checked = is_array($val) && in_array($hx, $val) ? 'checked' : '';
            echo "<label style='margin-right:8px'><input type='checkbox' name='wadtoc_headings[]' value='$hx' $checked> $hx</label> ";
        }
    }, 'wadtoc-settings', 'wadtoc_main');

    add_settings_field('wadtoc_open', 'TOC ouverte par défaut', function() {
        $val = get_option('wadtoc_open', '1');
        echo "<select name='wadtoc_open'>
            <option value='1'" . selected($val,'1',false) . ">Oui</option>
            <option value='0'" . selected($val,'0',false) . ">Non</option>
        </select>";
    }, 'wadtoc-settings', 'wadtoc_main');

    add_settings_field('wadtoc_title', 'Titre du bloc', function() {
        $val = esc_attr(get_option('wadtoc_title', 'Sommaire'));
        echo "<input type='text' name='wadtoc_title' value='$val' size='30'>";
    }, 'wadtoc-settings', 'wadtoc_main');

    add_settings_field('wadtoc_bgcolor', 'Couleur de fond', function() {
        $val = esc_attr(get_option('wadtoc_bgcolor', '#fefefe'));
        echo "<input type='color' name='wadtoc_bgcolor' value='$val'>";
    }, 'wadtoc-settings', 'wadtoc_main');

    add_settings_field('wadtoc_linkcolor', 'Couleur des liens', function() {
        $val = esc_attr(get_option('wadtoc_linkcolor', '#111'));
        echo "<input type='color' name='wadtoc_linkcolor' value='$val'>";
    }, 'wadtoc-settings', 'wadtoc_main');

    add_settings_field('wadtoc_headercolor', "Couleur de l'en-tête", function() {
        $val = esc_attr(get_option('wadtoc_headercolor', '#111'));
        echo "<input type='color' name='wadtoc_headercolor' value='$val'>";
    }, 'wadtoc-settings', 'wadtoc_main');

    add_settings_field('wadtoc_iconcolor', "Couleur de l'icône", function() {
        $val = esc_attr(get_option('wadtoc_iconcolor', '#111'));
        echo "<input type='color' name='wadtoc_iconcolor' value='$val'>";
    }, 'wadtoc-settings', 'wadtoc_main');

    add_settings_field('wadtoc_maxwidth', 'Largeur maximale (px)', function() {
        $val = esc_attr(get_option('wadtoc_maxwidth', ''));
        echo "<input type='number' name='wadtoc_maxwidth' min='200' max='1200' value='$val' style='width:80px'>";
    }, 'wadtoc-settings', 'wadtoc_main');
});
