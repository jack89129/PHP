<?php
/* Shortcode for Static block */
function add_static_block( $atts ) {
    $attr = shortcode_atts(array(
           'type' => 'none',
    ), $atts);
    $content = "";
    $args = array(
        'name' => $attr['type'],
        'post_type' => 'block',
        'posts_per_page' => 1,
        'caller_get_posts'=> 1
    );
    $query = new WP_Query( $args );
    if( $query->have_posts() ) {
        while ($query->have_posts()) {
            $query->the_post();
            $content = get_the_content();
        }
    }
    wp_reset_query();
    echo $content;
}
add_shortcode('add-static-block', 'add_static_block');

/* Shortcodes for API Documentation elements */

// Blue Box
function mc_blue_box($atts, $content=null) {
    extract(shortcode_atts(array(
        'title' => '',
        ), $atts));
    return '<div class="blue-box api-box"><h5>' . $title . '</h5><p>' . $content . '</p></div>';
}
add_shortcode('blue-box', 'mc_blue_box');

// Orange Box
function mc_orange_box($atts, $content=null) {
    extract(shortcode_atts(array(
        'title' => '',
        ), $atts));
    return '<div class="orange-box api-box"><h5>' . $title . '</h5><p>' . $content . '</p></div>';
}
add_shortcode('orange-box', 'mc_orange_box');

// Quotation Block
function mc_quotation_block($atts, $content=null) {
    return '<div class="quotation-wrapper"><p><i class="quote quote-start"></i>' . $content . '<i class="quote quote-end"></i></p></div>';
}
add_shortcode('quotation', 'mc_quotation_block');
/* End API Documentation element shortcode define */
?>
