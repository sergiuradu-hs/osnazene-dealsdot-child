<?php
$award_cards = [
	[
		'image'       => 'https://staging.osnazene.com/wp-content/uploads/2026/06/western-balkans-butterfly-innovation-award-2025.jpg',
		'alt_text'    => __( 'Regional Butterfly Innovation 2025', 'dealsdot-child' ),
		'title'       => __( 'Western Balkans Butterfly Innovation Award 2025', 'dealsdot-child' ),
		'description' => __( 'Women Innovation Award, dodeljena u okviru regionalne inicijative za promociju inovacija i održivog razvoja Zapadnog Balkana', 'dealsdot-child' ),
	],
	[
		'image'       => 'https://staging.osnazene.com/wp-content/uploads/2026/06/european-enterprise-promotion-award-eepa-2024.jpg',
		'alt_text'    => __( 'EEPA 2024 Winner', 'dealsdot-child' ),
		'title'       => __( 'European Enterprise Promotion Award (EEPA) 2024', 'dealsdot-child' ),
		'description' => __( 'Prva nagrada u kategoriji Digital Transition, dodeljena od strane Evropske komisije', 'dealsdot-child' ),
	],
];
?>

<div class="template-award-cards__wrapper">
  <div class="container">
    <div class="template-award-cards" data-template="award-cards.php">
      <?php foreach ( $award_cards as $award_card ) : ?>
        <article class="template-award-cards__item">
          <?php if ( ! empty( $award_card['image'] ) ) : ?>
            <img class="template-award-cards__image"
              src="<?php echo esc_url( $award_card['image'] ); ?>"
              alt="<?php echo esc_attr( $award_card['alt_text'] ?? '' ); ?>"
              loading="lazy">
          <?php endif; ?>

          <?php if ( ! empty( $award_card['title'] ) ) : ?>
            <h3 class="template-award-cards__title">
              <?php echo esc_html( $award_card['title'] ); ?>
            </h3>
          <?php endif; ?>

          <?php if ( ! empty( $award_card['description'] ) ) : ?>
            <p class="template-award-cards__description">
              <?php echo esc_html( $award_card['description'] ); ?>
            </p>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</div>