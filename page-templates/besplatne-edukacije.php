<?php
/**
 * Template Name: Besplatne edukacije
 *
 * Page template that lists all edukacija posts
 * with taxonomy vrsta-edukacije → besplatne-edukacije,
 * ordered by datum DESC, with AJAX load-more.
 *
 * osn_edu_render_card() is defined in functions.php.
 */

get_header();

$per_page  = 6;
$base_args = [
    'post_type'      => 'edukacija',
    'post_status'    => 'publish',
    'posts_per_page' => $per_page,
    'orderby'        => 'meta_value_num',
    'meta_key'       => 'datum',
    'order'          => 'DESC',
    'tax_query'      => [ [
        'taxonomy' => 'vrsta-edukacije',
        'field'    => 'slug',
        'terms'    => 'besplatne-edukacije',
    ] ],
    'meta_query'     => [
        'relation' => 'OR',
        [
            'key'     => 'sakri-iz-kategorije',
            'value'   => 'Da',
            'compare' => '!=',
        ],
        [
            'key'     => 'sakri-iz-kategorije',
            'compare' => 'NOT EXISTS',
        ],
    ],
];

$query = new WP_Query( $base_args );
?>

<section class="vc_section osnazene_section dark osn-edukacija-archive">
    <div class="container">
        <div class="osn-edu-block">
            <h2 class="osn-edu-heading">Besplatne edukacije</h2>

            <div class="osn-edu-grid" id="osn-besplatne-grid">
                <?php while ( $query->have_posts() ) :
                    $query->the_post();
                    osn_edu_render_card( get_the_ID(), false );
                endwhile;
                wp_reset_postdata(); ?>
            </div>

            <?php if ( $query->found_posts > $per_page ) : ?>
            <div class="osn-edu-loadmore">
                <button
                    class="osn-edu-btn"
                    id="osn-besplatne-loadmore"
                    data-offset="<?php echo esc_attr( $per_page ); ?>"
                    data-nonce="<?php echo esc_attr( wp_create_nonce( 'osn_besplatne_loadmore' ) ); ?>"
                >Prikaži još</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
(function () {
    var btn = document.getElementById('osn-besplatne-loadmore');
    if (!btn) return;

    btn.addEventListener('click', function () {
        var offset  = parseInt(btn.dataset.offset, 10);
        var nonce   = btn.dataset.nonce;
        var grid    = document.getElementById('osn-besplatne-grid');

        btn.disabled = true;
        btn.textContent = '...';

        var data = new URLSearchParams({
            action: 'osn_besplatne_loadmore',
            nonce:  nonce,
            offset: offset,
        });

        fetch('<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: data.toString(),
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.html) {
                grid.insertAdjacentHTML('beforeend', res.html);
                btn.dataset.offset = offset + res.count;
            }
            if (!res.has_more) {
                btn.closest('.osn-edu-loadmore').remove();
            } else {
                btn.disabled = false;
                btn.textContent = 'Prikaži još';
            }
        })
        .catch(function () {
            btn.disabled = false;
            btn.textContent = 'Prikaži još';
        });
    });
}());
</script>

<?php get_footer(); ?>
