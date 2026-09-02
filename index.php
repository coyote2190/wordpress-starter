<?php get_header(); ?>

<main class="site-main">
<?php
starter_component('hero', [
    'title' => 'Bienvenue',
    'subtitle' => 'Un sous-titre engageant ici',
    'button_text' => 'Découvrir',
    'button_url' => home_url('/contact'),
]);

starter_component('section-title', [
    'overline' => 'Actualités',
    'title' => 'Nos derniers articles',
    'align' => 'center',
]);

$posts = get_posts(['numberposts' => 6]);
$cards = array_map(function ($post) {
    return [
        'title' => get_the_title($post),
        'text' => get_the_excerpt($post),
        'image_id' => get_post_thumbnail_id($post),
        'url' => get_permalink($post),
        'meta' => get_the_date('', $post),
    ];
}, $posts);

starter_component('card-grid', ['cards' => $cards, 'columns' => 3]);

starter_component('cta', [
    'title' => 'Une question ?',
    'text' => 'Notre équipe vous répond sous 24h.',
    'button_text' => 'Nous contacter',
    'button_url' => home_url('/contact'),
]);

// get_template_part('template-parts/components/hero', null, [
//     'title'       => 'Bienvenue',
//     'subtitle'    => 'Un sous-titre engageant ici',
//     'button_text' => 'Découvrir',
//     'button_url'  => home_url('/contact'),
// ]);
?>
</main>

<?php get_footer(); ?>
