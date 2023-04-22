<?php

function dfGetCurrency(): string
{
    return apply_filters('digitfab/core/price_currency', 'руб.');
}

function dfGetPrice(WP_Post $post): string
{
    $price = get_post_meta($post->ID, '_price', true);
    $pricePrefix = get_post_meta($post->ID, '_price_prefix', true);
    $priceSuffix = get_post_meta($post->ID, '_price_suffix', true);

    $parts = [];

    if (!empty($pricePrefix)) {
        $parts[] = $pricePrefix;
    }

    if (!empty($price)) {
        $parts[] = $price;
        $parts[] = dfGetCurrency();
    }

    if (!empty($priceSuffix)) {
        $parts[] = $priceSuffix;
    }

    return apply_filters('digitfab/core/price_string', join(' ', apply_filters('digitfab/core/price_parts', $parts)));
}

function dfGetTitle(): string
{
    $title = get_the_title();

    if (is_front_page() || is_home()) {
        $title = get_bloginfo('name');
    }

    if (is_archive()) {
        $title = get_the_archive_title();
    }

    return apply_filters('digitfab/core/page_title', $title);
}