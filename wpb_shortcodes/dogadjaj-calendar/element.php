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

  // Build flat cell array: pad-before (prev month) + current days + pad-after (next month)
  $pad_before = $start_dow - 1;
  $cells = [];
  for ( $i = $pad_before; $i > 0; $i-- ) {
    $cell_day = $days_in_prev - $i + 1;
    $cell_ts  = mktime( 0, 0, 0, $prev_month, $cell_day, $prev_year );
    $cells[]  = [
      'day'       => $cell_day,
      'current'   => false,
      'timestamp' => $cell_ts,
      'date_key'  => date( 'Y-n-j', $cell_ts ),
    ];
  }
  for ( $d = 1; $d <= $days_in_month; $d++ ) {
    $cell_ts = mktime( 0, 0, 0, $month, $d, $year );
    $cells[] = [
      'day'       => $d,
      'current'   => true,
      'timestamp' => $cell_ts,
      'date_key'  => date( 'Y-n-j', $cell_ts ),
    ];
  }
  $total_cells = (int) ceil( count( $cells ) / 7 ) * 7;
  $next_overflow = 1;
  while ( count( $cells ) < $total_cells ) {
    $cell_ts = mktime( 0, 0, 0, $next_month, $next_overflow, $next_year );
    $cells[] = [
      'day'       => $next_overflow++,
      'current'   => false,
      'timestamp' => $cell_ts,
      'date_key'  => date( 'Y-n-j', $cell_ts ),
    ];
  }

  $visible_start_ts = ! empty( $cells ) ? (int) $cells[0]['timestamp'] : $first_ts;
  $last_cell        = ! empty( $cells ) ? $cells[ count( $cells ) - 1 ] : [ 'timestamp' => mktime( 0, 0, 0, $month, $days_in_month, $year ) ];
  $visible_end_ts   = mktime(
    23,
    59,
    59,
    (int) date( 'n', (int) $last_cell['timestamp'] ),
    (int) date( 'j', (int) $last_cell['timestamp'] ),
    (int) date( 'Y', (int) $last_cell['timestamp'] )
  );

  // Query events for the full visible grid so mobile can list overflow-month events.
  $events_raw = get_posts( [
    'post_type'      => 'dogadjaj',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'meta_key'       => 'datum',
    'orderby'        => 'meta_value_num',
    'order'          => 'ASC',
    'meta_query'     => [ [
      'key'     => 'datum',
      'value'   => [ $visible_start_ts, $visible_end_ts ],
      'compare' => 'BETWEEN',
      'type'    => 'NUMERIC',
    ] ],
  ] );

  $today_midnight = mktime( 0, 0, 0, (int) date( 'n' ), (int) date( 'j' ), (int) date( 'Y' ) );

  $weekday_names = [
    1 => 'Pon',
    2 => 'Uto',
    3 => 'Sre',
    4 => 'Čet',
    5 => 'Pet',
    6 => 'Sub',
    7 => 'Ned',
  ];

  // Index events by date. Keep desktop $events_by_day limited to current month.
  $events_by_day  = [];
  $events_by_date = [];
  $events_list    = [];
  foreach ( $events_raw as $ev ) {
    $ts = (int) get_post_meta( $ev->ID, 'datum', true );
    if ( ! $ts ) {
      continue;
    }

    $day      = (int) date( 'j', $ts );
    $ev_year  = (int) date( 'Y', $ts );
    $ev_month = (int) date( 'n', $ts );
    $date_key = date( 'Y-n-j', $ts );
    $anchor   = 'osn-cal-day-' . date( 'Ymd', $ts ) . '-' . (int) $ev->ID;
    $event    = [
      'title'   => get_the_title( $ev ),
      'date'    => date( 'd.m.Y.', $ts ),
      'link'    => get_permalink( $ev ),
      'expired' => $ts < $today_midnight,
      'ts'      => $ts,
      'anchor'  => $anchor,
    ];

    $events_by_date[ $date_key ][] = $event;
    if ( $ev_year === $year && $ev_month === $month ) {
      $events_by_day[ $day ][] = $event;
    }

    $events_list[] = [
      'day'      => $day,
      'weekday'  => $weekday_names[ (int) date( 'N', $ts ) ],
      'date'     => $event['date'],
      'title'    => $event['title'],
      'link'     => $event['link'],
      'in_month' => ( $ev_year === $year && $ev_month === $month ),
      'anchor'   => $anchor,
      'ts'       => $ts,
    ];
  }

  foreach ( $cells as &$cell ) {
    $date_key = $cell['date_key'];
    $cell['has_event'] = ! empty( $events_by_date[ $date_key ] );
    $cell['anchor']    = $cell['has_event'] ? $events_by_date[ $date_key ][0]['anchor'] : '';
  }
  unset( $cell );

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
