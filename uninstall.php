<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

// Liste des options à supprimer
$option_names = [
    'wadtoc_headings',
    'wadtoc_open',
    'wadtoc_title',
    'wadtoc_bgcolor',
    'wadtoc_linkcolor',
    'wadtoc_headercolor',
    'wadtoc_maxwidth',
    'wadtoc_iconcolor',
];

foreach ($option_names as $option) {
    delete_option($option);
}
