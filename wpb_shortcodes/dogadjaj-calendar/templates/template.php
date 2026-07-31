<?php
/**
 * Template: Dogadjaj Calendar
 *
 * Variables provided by element.php render callback:
 * - $month_label    (string)   e.g. "Februar 2026"
 * - $prev_url       (string)   already esc_url'd URL for previous month
 * - $next_url       (string)   already esc_url'd URL for next month
 * - $weeks          (array)    array of week rows, each with 7 cells:
 *                               [ 'day' => int, 'current' => bool ]
 * - $events_by_day  (array)    events indexed by day-of-month number
 * - $events_list    (array)    chronological mobile event list
 * - $today_midnight (int)      midnight Unix timestamp of today
 * - $month          (int)      current month
 * - $year           (int)      current year
 */
?>
<div class="osn-cal-widget">
<div class="osn-cal">

  <div class="osn-cal__header">
    <div class="osn-cal__month-label"><?php echo esc_html( $month_label ); ?></div>
    <div class="osn-cal__nav">
      <a href="<?php echo $prev_url; ?>" class="osn-cal__nav-btn osn-cal__nav-btn--prev" aria-label="Prethodni mesec"></a>
      <a href="<?php echo $next_url; ?>" class="osn-cal__nav-btn osn-cal__nav-btn--next" aria-label="Sledeći mesec"></a>
    </div>
  </div>

  <div class="osn-cal__days-header">
    <?php foreach ( [ 'Pon', 'Uto', 'Sre', 'Čet', 'Pet', 'Sub', 'Ned' ] as $day_name ) : ?>
      <div class="osn-cal__day-name"><?php echo esc_html( $day_name ); ?></div>
    <?php endforeach; ?>
  </div>

  <div class="osn-cal__body">
    <?php
    $total_weeks = count( $weeks );
    foreach ( $weeks as $week_idx => $week ) :
      $is_last_row = ( $week_idx === $total_weeks - 1 );
    ?>
      <div class="osn-cal__week">
        <?php foreach ( $week as $col_idx => $cell ) :
          $is_current = $cell['current'];
          $day        = $cell['day'];
          $is_last_col = ( $col_idx === 6 );
          $has_event   = ! empty( $cell['has_event'] );
          $cell_anchor = ! empty( $cell['anchor'] ) ? (string) $cell['anchor'] : '';

          $ts_day   = $is_current ? mktime( 0, 0, 0, $month, $day, $year ) : 0;
          $is_today = $is_current && ( $ts_day === $today_midnight );
          $events   = ( $is_current && isset( $events_by_day[ $day ] ) ) ? $events_by_day[ $day ] : [];

          $cell_classes = [ 'osn-cal__cell' ];
          if ( ! $is_current )            { $cell_classes[] = 'osn-cal__cell--overflow'; }
          if ( $has_event )               { $cell_classes[] = 'osn-cal__cell--has-event'; }
          if ( $is_today )                { $cell_classes[] = 'osn-cal__cell--today'; }
          if ( $is_last_col )             { $cell_classes[] = 'osn-cal__cell--last'; }
          if ( $is_last_row && $col_idx === 0 ) { $cell_classes[] = 'osn-cal__cell--bl'; }
          if ( $is_last_row && $is_last_col )   { $cell_classes[] = 'osn-cal__cell--br'; }
        ?>
          <div class="<?php echo esc_attr( implode( ' ', $cell_classes ) ); ?>"<?php echo $cell_anchor ? ' data-osn-target="' . esc_attr( $cell_anchor ) . '"' : ''; ?>>

            <div class="osn-cal__date"><?php echo esc_html( $day ); ?></div>

            <?php if ( ! empty( $events ) ) :
              $ev = $events[0];
              $extra = count( $events ) - 1;
            ?>
              <a href="<?php echo esc_url( $ev['link'] ); ?>"
                 class="osn-cal-event <?php echo $ev['expired'] ? 'osn-cal-event--expired' : 'osn-cal-event--upcoming'; ?>">
                <div class="osn-cal-event__title"><?php echo esc_html( $ev['title'] ); ?></div>
                <div class="osn-cal-event__date"><?php echo esc_html( $ev['date'] ); ?></div>
              </a>
              <?php if ( $extra > 0 ) : ?>
                <span class="osn-cal-event__more">+<?php echo (int) $extra; ?></span>
              <?php endif; ?>
            <?php endif; ?>

          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>

</div>
<?php if ( ! empty( $events_list ) ) : ?>
  <div class="osn-cal-list" aria-label="<?php esc_attr_e( 'Lista događaja', 'dealsdot-child' ); ?>">
    <?php foreach ( $events_list as $list_event ) :
      $item_classes = [ 'osn-cal-list__item' ];
      if ( empty( $list_event['in_month'] ) ) {
        $item_classes[] = 'osn-cal-list__item--outside';
      }
    ?>
      <a
        id="<?php echo esc_attr( $list_event['anchor'] ); ?>"
        href="<?php echo esc_url( $list_event['link'] ); ?>"
        class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>"
      >
        <span class="osn-cal-list__day">
          <span class="osn-cal-list__day-number"><?php echo esc_html( $list_event['day'] ); ?></span>
          <span class="osn-cal-list__weekday"><?php echo esc_html( $list_event['weekday'] ); ?></span>
        </span>
        <span class="osn-cal-list__content">
          <span class="osn-cal-list__date"><?php echo esc_html( $list_event['date'] ); ?></span>
          <span class="osn-cal-list__title"><?php echo esc_html( $list_event['title'] ); ?></span>
        </span>
        <span class="osn-cal-list__chevron" aria-hidden="true">›</span>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
</div>
