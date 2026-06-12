<?php
/**
 * 404 – Not Found template.
 */

get_header();
?>

<section class="vc_section osnazene_section osn-404">

    <div class="osn-404__inner">

        <h1 class="osn-404__heading"><?php esc_html_e( 'Greška 404 - stranica nije pronađena', 'dealsdot-child' ); ?></h1>

        <p class="osn-404__code">404</p>

        <p class="osn-404__message">
            <?php esc_html_e( 'Stranica koju tražiš ne postoji ili je premeštena.', 'dealsdot-child' ); ?><br>
            <?php echo wp_kses( __( 'Ali Osna<strong>Žene</strong> zajednica je uvek ovde da te vrati na pravi put.', 'dealsdot-child' ), [ 'strong' => [] ] ); ?>
        </p>

        <div class="osn-404__actions">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="osn-404__btn">
                <?php esc_html_e( 'Vrati se na početnu', 'dealsdot-child' ); ?>
            </a>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'osnazena' ) ?: home_url( '/osnazene/' ) ); ?>" class="osn-404__btn">
                <?php esc_html_e( 'Istraži Zajednicu', 'dealsdot-child' ); ?>
            </a>
        </div>

    </div>

</section>

<?php get_footer(); ?>
