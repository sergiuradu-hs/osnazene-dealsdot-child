<?php
/**
 * Dogadjaj Calendar WPBakery element definition
 *
 * Pulls events from the 'dogadjaj' custom post type (JetListing).
 * Month navigation driven by ?cal_year=&cal_month= GET params.
 * Date stored as Unix timestamp in the 'datum' post meta key.
 */

$map = [
  'name'        => __( 'Dogadjaji Calendar', 'dealsdot-child' ),
  'base'        => 'wpb_dogadjaj_calendar',
  'description' => __( 'Monthly calendar of dogadjaji events', 'dealsdot-child' ),
  'category'    => __( 'Custom', 'dealsdot-child' ),
  'params'      => [],
];

$template_rel = 'wpb_shortcodes/dogadjaj-calendar/templates/template.php';

$render = function( $atts, $content = '' ) use ( $template_rel ) {

  // Progressive enhancement: JS intercepts nav clicks and updates month in-place.
  $script_rel = '/wpb_shortcodes/dogadjaj-calendar/assets/calendar.js';
  $script_abs = get_stylesheet_directory() . $script_rel;
  $script_uri = get_stylesheet_directory_uri() . $script_rel;
  wp_enqueue_script(
    'osn-dogadjaj-calendar',
    $script_uri,
    [],
    file_exists( $script_abs ) ? (string) filemtime( $script_abs ) : null,
    true
  );

  // Sanitize month/year from GET params (cast to int — safe, no injection possible)
  $year  = isset( $_GET['cal_year'] )  ? (int) $_GET['cal_year']  : (int) date( 'Y' );
  $month = isset( $_GET['cal_month'] ) ? (int) $_GET['cal_month'] : (int) date( 'n' );
  $month = max( 1, min( 12, $month ) );
  if ( $year < 2000 || $year > 2100 ) {
    $year = (int) date( 'Y' );
  }

  // Calendar math
  $first_ts      = mktime( 0, 0, 0, $month, 1, $year );
  $days_in_month = (int) date( 't', $first_ts );
  $start_dow     = (int) date( 'N', $first_ts ); // 1=Mon … 7=Sun (ISO)

  // Prev / next month
  $prev_month   = $month === 1 ? 12 : $month - 1;
  $prev_year    = $month === 1 ? $year - 1 : $year;
  $next_month   = $month === 12 ? 1 : $month + 1;
  $next_year    = $month === 12 ? $year + 1 : $year;
  $days_in_prev = (int) date( 't', mktime( 0, 0, 0, $prev_month, 1, $prev_year ) );

  // Serbian month names
  $month_names = [
    1  => 'Januar',    2  => 'Februar',   3  => 'Mart',      4  => 'April',
    5  => 'Maj',       6  => 'Jun',       7  => 'Jul',       8  => 'Avgust',
    9  => 'Septembar', 10 => 'Oktobar',   11 => 'Novembar',  12 => 'Decembar',
  ];
  $month_label = $month_names[ $month ] . ' ' . $year;

  // Build navigation URLs (esc_url applied; add_query_arg is XSS-safe with esc_url)
  $prev_url = esc_url( add_query_arg( [ 'cal_year' => $prev_year, 'cal_month' => $prev_month ] ) );
  $next_url = esc_url( add_query_arg( [ 'cal_year' => $next_year, 'cal_month' => $next_month ] ) );

  // Query events for this month
  $events_raw = get_posts( [
    'post_type'      => 'dogadjaj',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'meta_query'     => [ [
      'key'     => 'datum',
      'value'   => [ $first_ts, mktime( 23, 59, 59, $month, $days_in_month, $year ) ],
      'compare' => 'BETWEEN',
      'type'    => 'NUMERIC',
    ] ],
  ] );

  $today_midnight = mktime( 0, 0, 0, (int) date( 'n' ), (int) date( 'j' ), (int) date( 'Y' ) );

  // Index events by day-of-month
  $events_by_day = [];
  foreach ( $events_raw as $ev ) {
    $ts  = (int) get_post_meta( $ev->ID, 'datum', true );
    $day = (int) date( 'j', $ts );
    $events_by_day[ $day ][] = [
      'title'   => get_the_title( $ev ),
      'date'    => date( 'd.m.Y.', $ts ),
      'link'    => get_permalink( $ev ),
      'expired' => $ts < $today_midnight,
    ];
  }

  // Build flat cell array: pad-before (prev month) + current days + pad-after (next month)
  $pad_before = $start_dow - 1;
  $cells = [];
  for ( $i = $pad_before; $i > 0; $i-- ) {
    $cells[] = [ 'day' => $days_in_prev - $i + 1, 'current' => false ];
  }
  for ( $d = 1; $d <= $days_in_month; $d++ ) {
    $cells[] = [ 'day' => $d, 'current' => true ];
  }
  $total_cells = (int) ceil( count( $cells ) / 7 ) * 7;
  $next_overflow = 1;
  while ( count( $cells ) < $total_cells ) {
    $cells[] = [ 'day' => $next_overflow++, 'current' => false ];
  }
  $weeks = array_chunk( $cells, 7 );

  ob_start();
  $template_abs = trailingslashit( get_stylesheet_directory() ) . $template_rel;
  if ( file_exists( $template_abs ) ) {
    include $template_abs;
  }
  return ob_get_clean();
};

wpb_register_element( [
  'base'   => 'wpb_dogadjaj_calendar',
  'map'    => $map,
  'render' => $render,
] );
