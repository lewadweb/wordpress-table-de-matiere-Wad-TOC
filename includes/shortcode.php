<?php
if (!defined('ABSPATH')) exit;

// Shortcode principal : [table_matiere]
add_shortcode('table_matiere', function($atts, $content = null) {
    $options = [
        'headings' => get_option('wadtoc_headings', ['h1','h2','h3','h4','h5','h6']),
        'open' => get_option('wadtoc_open', '1'),
        'title' => get_option('wadtoc_title', 'Sommaire'),
        'bgcolor' => get_option('wadtoc_bgcolor', '#fefefe'),
        'linkcolor' => get_option('wadtoc_linkcolor', '#444'),
        'headercolor' => get_option('wadtoc_headercolor', '#444'),
        'maxwidth' => get_option('wadtoc_maxwidth', ''),
        'iconcolor' => get_option('wadtoc_iconcolor', '#444'),
    ];
    if (!is_array($options['headings'])) $options['headings'] = array('h1','h2','h3','h4','h5','h6');
    $data = esc_attr(json_encode($options));
    $open = $options['open'] === '1';
    $accordion_class = $open ? 'wadtoc-open' : 'wadtoc-closed';
    $style = 'background:' . esc_attr($options['bgcolor']) . ';max-width:' . intval($options['maxwidth']) . 'px;';
    return '<div class="wadtoc-wrap ' . esc_attr($accordion_class) . '" data-wadtoc="' . $data . '" style="' . $style . '"></div>';
});

// Génération de la TOC et injection des ancres
function wadtoc_generate_toc($content, $levels) {
    $pattern = '/<(' . implode('|', array_map('preg_quote', $levels)) . ')([^>]*)>(.*?)<\/\\1>/i';
    $matches = [];
    preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
    if (empty($matches[0])) return ['toc'=>'','content_with_anchors'=>$content];
    $toc = '<ul class="wadtoc-list">';
    $offset = 0;
    $content_with_anchors = $content;
    foreach ($matches[0] as $i => $match) {
        $tag = strtolower($matches[1][$i][0]);
        $title = strip_tags($matches[3][$i][0]);
        $anchor = 'wadtoc-' . sanitize_title($title) . '-' . substr(md5($title . $i), 0, 6);
        $toc .= '<li class="wadtoc-item wadtoc-' . esc_attr($tag) . '"><a href="#' . esc_attr($anchor) . '" style="color:inherit;text-decoration:none;">' . esc_html($title) . '</a></li>';
        // Injecter l'ancre dans le contenu
        $with_anchor = '<' . $tag . $matches[2][$i][0] . ' id="' . $anchor . '">' . $matches[3][$i][0] . '</' . $tag . '>';
        $pos = strpos($content_with_anchors, $match[0], $offset);
        if ($pos !== false) {
            $content_with_anchors = substr_replace($content_with_anchors, $with_anchor, $pos, strlen($match[0]));
            $offset = $pos + strlen($with_anchor);
        }
    }
    $toc .= '</ul>';
    return ['toc'=>$toc, 'content_with_anchors'=>$content_with_anchors];
}
