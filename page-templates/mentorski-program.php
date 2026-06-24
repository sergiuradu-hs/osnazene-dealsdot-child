<?php
/**
 * Template Name: Mentorski program
 *
 * Sections:
 *   1. Hero      – ACF image (left) + gold italic heading + intro text (right)
 *   2. Mentors   – 4-col grid from `mentor` CPT + "Postani mentor/menti" CTA buttons
 *   3. Program   – ACF image (left) + two text paragraphs + bullet list (right)
 *   4. Featured  – text content (left) + ACF image (right)
 *
 * ACF page fields:
 *   hero_image          – attachment ID (535×355)
 *   hero_text           – textarea
 *   postani_mentor_link – URL
 *   postani_mentij_link – URL
 *   program_image       – attachment ID (485×565)
 *   program_uvod_1      – textarea (first paragraph)
 *   program_uvod_2      – textarea (second paragraph, introduces bullet list)
 *   program_oblasti     – textarea, one item per line → gold-dot bullet list
 *   featured_naziv      – text (gold italic heading)
 *   featured_tekst_1    – textarea
 *   featured_tekst_2    – textarea
 *   featured_citat      – textarea / HTML (bold parts via editor)
 *   featured_image      – attachment ID (535×540)
 *
 * ACF mentor CPT fields:
 *   uloga    – text, optional role/subtitle
 *   cv_link  – URL for CV download
 */

get_header();

if ( have_posts() ) : while ( have_posts() ) : the_post();

$page_id = get_the_ID();

$hero_image_id       = (int) get_post_meta( $page_id, 'hero_image',          true );
$hero_text           =       get_post_meta( $page_id, 'hero_text',            true );
$postani_mentor_link =       get_post_meta( $page_id, 'postani_mentor_link',  true );
$postani_mentij_link =       get_post_meta( $page_id, 'postani_mentij_link',  true );
$program_image_id    = (int) get_post_meta( $page_id, 'program_image',        true );
$program_uvod_1      =       get_post_meta( $page_id, 'program_uvod_1',       true );
$program_uvod_2      =       get_post_meta( $page_id, 'program_uvod_2',       true );
$program_oblasti     =       get_post_meta( $page_id, 'program_oblasti',      true );
$featured_image_id   = (int) get_post_meta( $page_id, 'featured_image',       true );
$featured_naziv      =       get_post_meta( $page_id, 'featured_naziv',       true );
$featured_tekst_1    =       get_post_meta( $page_id, 'featured_tekst_1',     true );
$featured_tekst_2    =       get_post_meta( $page_id, 'featured_tekst_2',     true );
$featured_citat      =       get_post_meta( $page_id, 'featured_citat',       true );

endwhile; endif;
?>

<!-- ── Section 1: Hero ── -->
<section class="vc_section osnazene_section dark osn-mp-hero">

    <?php if ( $hero_image_id ) :
        echo wp_get_attachment_image( $hero_image_id, [ 535, 355 ], false, [
            'class'   => 'osn-mp-hero__img',
            'loading' => 'eager',
        ] );
    endif; ?>

    <div class="osn-mp-hero__body">
        <h1 class="osn-mp-hero__heading">Mentorski program</h1>
        <?php if ( $hero_text ) : ?>
        <p class="osn-mp-hero__text"><?php echo wp_kses_post( nl2br( $hero_text ) ); ?></p>
        <?php endif; ?>
    </div>

</section>

<!-- ── Section 2: Mentors grid ── -->
<section class="vc_section osnazene_section dark osn-mp-mentori">

    <?php
    $mentor_query = new WP_Query( [
        'post_type'      => 'mentor',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ] );

    if ( $mentor_query->have_posts() ) : ?>
    <div class="osn-mp-grid">
        <?php while ( $mentor_query->have_posts() ) : $mentor_query->the_post();
            $mentor_id = get_the_ID();
            $name      = get_the_title();
            $link      = get_permalink();
            $uloga     = get_post_meta( $mentor_id, 'uloga',    true );
            $cv_link   = get_post_meta( $mentor_id, 'cv',       true );
            $cv_link   = ! empty( $cv_link ) ? $cv_link : get_post_meta( $mentor_id, 'cv_link', true );
            $img_url   = get_the_post_thumbnail_url( $mentor_id, 'large' );
        ?>
        <div class="osn-mp-card">
            <div class="osn-mp-card__photo-wrap">
                <?php if ( $img_url ) : ?>
                <img src="<?php echo esc_url( $img_url ); ?>"
                     alt="<?php echo esc_attr( $name ); ?>"
                     class="osn-mp-card__photo"
                     loading="lazy">
                <?php endif; ?>
            </div>

            <div class="osn-mp-card__info">
                <h3 class="osn-mp-card__name"><?php echo esc_html( $name ); ?></h3>
                <?php if ( $uloga ) : ?>
                <p class="osn-mp-card__uloga"><?php echo esc_html( $uloga ); ?></p>
                <?php endif; ?>
            </div>

            <div class="osn-mp-card__actions">
                <a href="<?php echo esc_url( $link ); ?>"
                   class="osn-mp-card__btn">Pogledaj profil</a>
                <?php if ( $cv_link ) : ?>
                <a href="<?php echo esc_url( $cv_link ); ?>"
                   class="osn-mp-card__btn"
                   target="_blank"
                   rel="noopener noreferrer">CV</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php endif; ?>

    <div class="osn-mp-cta-row">
        <a href="<?php echo esc_url( $postani_mentor_link ?: '#' ); ?>"
           class="osn-mp-cta-btn">Postani mentor</a>
        <a href="<?php echo esc_url( $postani_mentij_link ?: '#' ); ?>"
           class="osn-mp-cta-btn">Postani menti</a>
    </div>

</section>

<!-- ── Section 3: Program description ── -->
<section class="vc_section osnazene_section dark osn-mp-program">

    <?php if ( $program_image_id ) :
        echo wp_get_attachment_image( $program_image_id, [ 485, 565 ], false, [
            'class'   => 'osn-mp-program__img',
            'loading' => 'lazy',
        ] );
    else : ?>
    <div class="osn-mp-program__img"></div>
    <?php endif; ?>

    <div class="osn-mp-program__body">
        <p class="osn-mp-program__text"><?php echo wp_kses_post( nl2br( $program_uvod_1 ?: 'Nakon sertifikacije, započinje novi mentorski ciklus „OsnaŽene Next Level“ – koji će trajati 4 meseca i odvijaće se kroz kombinaciju online i offline susreta tri puta mesečno.' ) ); ?></p>
        <p class="osn-mp-program__text"><?php echo wp_kses_post( nl2br( $program_uvod_2 ?: 'Tokom ovog perioda, kroz peer-to-peer mentorski rad i edukativne sesije, mentorke će stečeno znanje i iskustvo prenositi dalje, pružajući podršku drugim preduzetnicama u oblastima:' ) ); ?></p>
        <?php
        $oblasti_default = [
            'Menadžment micro i malih preduzeća',
            'Digitalnog marketinga i dizajn thinnking',
            'Storytellinga i veštine javnog nastupa',
            'Finansijske pismenosti',
            'Veštačke inteligencije i novih tehnologija',
            'Medijske pismenosti i sajber bezbednosti',
            'Ličnog razvoja i liderstva',
            'Rodne ravnopravnosti',
        ];
        $oblasti = $program_oblasti
            ? array_filter( array_map( 'trim', explode( "\n", $program_oblasti ) ) )
            : $oblasti_default;
        ?>
        <ul class="osn-mp-program__list">
            <?php foreach ( $oblasti as $oblast ) : ?>
            <li><?php echo esc_html( $oblast ); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

</section>

<!-- ── Section 4: Featured mentor ── -->
<section class="vc_section osnazene_section dark osn-mp-featured">

    <div class="osn-mp-featured__body">
        <h2 class="osn-mp-featured__naziv"><?php echo esc_html( $featured_naziv ?: 'dr Indira Popadić' ); ?></h2>
        <p class="osn-mp-featured__text"><?php echo wp_kses_post( nl2br( $featured_tekst_1 ?: 'Kao predavač u programu, dr Indira Popadić, osnivačica OsnaŽenih i ekspert za menadžment i žensko liderstvo. Takodje, Indira je ekspert konsultant za ekonomsko osnaživanje žena -UN Women za Evropu i jedan deo Azije. Svoje znanje i iskustvo prenela novoj generaciji mentora i podsetila da je znanje najvažnija valuta u današnjem poslovanju, a posebno je važno da svi zapamtimo da: „Znanje daje samopouzdanje“.' ) ); ?></p>
        <p class="osn-mp-featured__text"><?php echo wp_kses_post( nl2br( $featured_tekst_2 ?: 'Program se realizuje uz podršku Pokrajinskog sekretarijata za privredu i turizam AP Vojvodine, sa ciljem stvaranje mreže sertifikovanih mentorki koje će graditi lanac znanja, solidarnosti i podrške za buduće generacije žena preduzetnica.' ) ); ?></p>
        <p class="osn-mp-featured__citat"><?php echo $featured_citat ? wp_kses_post( $featured_citat ) : '<strong>OsnaŽene za budućnost – jer budućnost pripada ženama koje se ne boje da budu liderke</strong>'; ?></p>
    </div>

    <?php if ( $featured_image_id ) :
        echo wp_get_attachment_image( $featured_image_id, [ 535, 540 ], false, [
            'class'   => 'osn-mp-featured__img',
            'loading' => 'lazy',
        ] );
    else : ?>
    <div class="osn-mp-featured__img"></div>
    <?php endif; ?>

</section>

<?php get_footer(); ?>
