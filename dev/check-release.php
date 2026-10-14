<?php
/**
 * Dev check for the article release logic in falcon-pro/inc/setup.php
 * (fenix_setup_release_articles, fenix_setup_release_to_total, fenix_release_next_slot,
 *  fenix_seed_publish_order, fenix_setup_refresh_draft, fenix_seed_fingerprint, fenix_setup_article_counts).
 * Runs against an in-memory fake of the posts table whose wp_update_post() follows WordPress core's date rules
 * (wp-includes/post.php: wp_update_post "clear_date" for floating drafts, wp_insert_post post_modified and
 * publish/future switching). The fake site timezone is UTC, like a fresh WordPress install.
 * Run: php -d extension=mbstring dev/check-release.php
 */
require __DIR__ . '/preview/wp-stubs.php';

const FX_ZERO = '0000-00-00 00:00:00';

// ---- in-memory posts table -------------------------------------------------
$GLOBALS['fx_db']        = array(); // key => object
$GLOBALS['fx_meta']      = array();
$GLOBALS['fx_get_posts'] = function ( $args ) {
	$status = isset( $args['post_status'] ) ? (array) $args['post_status'] : array( 'publish' );
	$rows   = array();
	foreach ( $GLOBALS['fx_db'] as $p ) {
		if ( ! in_array( $p->post_status, $status, true ) ) {
			continue;
		}
		if ( isset( $args['name'] ) && $args['name'] !== $p->post_name ) {
			continue;
		}
		$rows[] = clone $p; // callers get a snapshot, like rows read from the database
	}
	return array_slice( $rows, 0, isset( $args['posts_per_page'] ) ? (int) $args['posts_per_page'] : 5 );
};
function current_time( $type, $gmt = false ) {
	return gmdate( 'Y-m-d H:i:s' );
}
function get_date_from_gmt( $gmt ) {
	return $gmt; // site timezone = UTC
}
function wp_slash( $v ) {
	return is_array( $v ) ? array_map( 'wp_slash', $v ) : ( is_string( $v ) ? addslashes( $v ) : $v );
}
/**
 * Mirrors core: wp_update_post() L5528-5544 (clear_date) and wp_insert_post() date / status rules.
 */
function wp_update_post( $arr, $wp_error = false ) {
	$floating = array( 'draft', 'pending', 'auto-draft' );
	foreach ( $GLOBALS['fx_db'] as $p ) {
		if ( $p->ID !== $arr['ID'] ) {
			continue;
		}
		$now   = gmdate( 'Y-m-d H:i:s' );
		$clear = in_array( $p->post_status, $floating, true ) && empty( $arr['edit_date'] ) && FX_ZERO === $p->post_date_gmt;
		$new   = array_merge( (array) $p, $arr );
		unset( $new['edit_date'] );
		foreach ( array( 'post_title', 'post_content', 'post_excerpt' ) as $k ) {
			if ( isset( $arr[ $k ] ) ) {
				$new[ $k ] = stripslashes( $arr[ $k ] ); // wp_insert_post() unslashes what the caller slashed
			}
		}
		if ( $clear ) {
			$new['post_date']     = $now;
			$new['post_date_gmt'] = '';
		}
		$no_gmt = empty( $new['post_date_gmt'] ) || FX_ZERO === $new['post_date_gmt'];
		if ( empty( $new['post_date'] ) || FX_ZERO === $new['post_date'] ) {
			$new['post_date'] = $no_gmt ? $now : $new['post_date_gmt'];
		}
		if ( $no_gmt ) {
			$new['post_date_gmt'] = in_array( $new['post_status'], $floating, true ) ? FX_ZERO : $new['post_date'];
		}
		$new['post_modified']     = $now;
		$new['post_modified_gmt'] = $now;
		if ( FX_ZERO !== $new['post_date_gmt'] ) {
			$ahead = strtotime( $new['post_date_gmt'] . ' UTC' ) - time();
			if ( 'publish' === $new['post_status'] && $ahead >= 60 ) {
				$new['post_status'] = 'future';
			} elseif ( 'future' === $new['post_status'] && $ahead < 60 ) {
				$new['post_status'] = 'publish';
			}
		}
		foreach ( $new as $k => $v ) {
			$p->$k = $v;
		}
		return $p->ID;
	}
	return 0;
}

// ---- in-memory categories (names stored escaped like core: & → &amp;) --------------------------------
$GLOBALS['fx_terms']    = array(); // term_id => name
$GLOBALS['fx_post_cat'] = array(); // post ID => array( term_id, … )
function fx_term_name( $name ) {
	return str_replace( '&', '&amp;', str_replace( '&amp;', '&', (string) $name ) );
}
function get_term_by( $field, $value, $taxonomy ) {
	foreach ( $GLOBALS['fx_terms'] as $id => $name ) {
		if ( 'name' === $field && fx_term_name( $value ) === $name ) {
			return (object) array( 'term_id' => $id, 'name' => $name );
		}
	}
	return false;
}
function term_exists( $name, $taxonomy = '' ) {
	$t = get_term_by( 'name', $name, $taxonomy );
	return $t ? array( 'term_id' => (string) $t->term_id ) : null;
}
function wp_insert_term( $name, $taxonomy ) {
	$id                           = 1 + ( $GLOBALS['fx_terms'] ? max( array_keys( $GLOBALS['fx_terms'] ) ) : 0 );
	$GLOBALS['fx_terms'][ $id ] = fx_term_name( $name );
	return array( 'term_id' => $id );
}
function wp_update_term( $id, $taxonomy, $args ) {
	$GLOBALS['fx_terms'][ $id ] = fx_term_name( $args['name'] );
	return array( 'term_id' => $id );
}
function wp_get_post_categories( $id, $args = array() ) {
	$ids = isset( $GLOBALS['fx_post_cat'][ $id ] ) ? $GLOBALS['fx_post_cat'][ $id ] : array();
	return array_map(
		function ( $tid ) {
			return $GLOBALS['fx_terms'][ $tid ];
		},
		$ids
	);
}
function wp_set_post_categories( $id, $cats ) {
	$GLOBALS['fx_post_cat'][ $id ] = array_map( 'intval', $cats );
	return true;
}

require __DIR__ . '/../falcon-pro/functions.php';

$problems = 0;
$check    = function ( $name, $ok, $detail = '' ) use ( &$problems ) {
	printf( "%-66s %s\n", $name, $ok ? '✓' : '✗ ' . $detail );
	$problems += $ok ? 0 : 1;
};
// 22 legacy drafts exactly as the live site has them: created by wp_insert_post, never saved again, no hash meta
$seed     = function () {
	$GLOBALS['fx_db']   = array();
	$GLOBALS['fx_meta'] = array();
	$id                 = 100;
	foreach ( array_keys( fenix_seed_article_covers() ) as $slug ) {
		$GLOBALS['fx_db'][ $slug ] = (object) array(
			'ID'                => $id++,
			'post_name'         => $slug,
			'post_title'        => 'T:' . $slug,
			'post_status'       => 'draft',
			'post_content'      => 'old import',
			'post_excerpt'      => '',
			'post_date'         => '2026-09-30 07:52:00',
			'post_date_gmt'     => FX_ZERO,
			'post_modified'     => '2026-09-30 07:52:00',
			'post_modified_gmt' => FX_ZERO,
		);
	}
};
$by       = function ( $status ) {
	return array_values(
		array_filter(
			$GLOBALS['fx_db'],
			function ( $p ) use ( $status ) {
				return $status === $p->post_status;
			}
		)
	);
};
$utc      = new DateTimeZone( 'UTC' );
$bkk      = new DateTimeZone( 'Asia/Bangkok' );
$slots    = function () use ( $by, $bkk, $utc ) {
	$out = array();
	foreach ( $by( 'future' ) as $p ) {
		if ( isset( $p->fx_foreign ) ) {
			continue;
		}
		$out[ $p->post_name ] = ( new DateTimeImmutable( $p->post_date_gmt, $utc ) )->setTimezone( $bkk );
	}
	uasort(
		$out,
		function ( $a, $b ) {
			return $a <=> $b;
		}
	);
	return $out;
};
$all      = array_keys( fenix_seed_article_covers() );
$arts     = fenix_seed_articles();
$n        = count( $all );

// ---- publish order ----------------------------------------------------------
$order = fenix_seed_publish_order();
$check( 'order: every seed article exactly once', count( $order ) === $n && ! array_diff( $all, $order ) && count( array_unique( $order ) ) === $n, count( $order ) . ' vs ' . $n );

// ---- slot maths: every weekday × times around 10:00 × 1/2/3 per week -------------
$ok  = true;
$why = '';
foreach ( array( 1, 2, 3, 7 ) as $per ) {
	$days = fenix_release_weekdays( $per );
	for ( $d = 12; $d <= 18; $d++ ) {
		foreach ( array( '00:30:00', '09:59:59', '10:00:00', '10:00:01', '23:59:59' ) as $t ) {
			$after = new DateTimeImmutable( "2026-10-$d $t", $bkk );
			$slot  = fenix_release_next_slot( $after->setTimezone( $utc ), $days );
			$good  = '10:00:00' === $slot->format( 'H:i:s' ) && in_array( (int) $slot->format( 'N' ), $days, true ) && $slot->format( 'Y-m-d' ) > $after->format( 'Y-m-d' ) && 'Asia/Bangkok' === $slot->getTimezone()->getName();
			// no allowed weekday may be skipped between "after" and the slot
			for ( $x = $after->modify( '+1 day' ); $good && $x->format( 'Y-m-d' ) < $slot->format( 'Y-m-d' ); $x = $x->modify( '+1 day' ) ) {
				$good = ! in_array( (int) $x->format( 'N' ), $days, true );
			}
			if ( ! $good ) {
				$ok  = false;
				$why = "per $per after 2026-10-$d $t → " . $slot->format( 'c' );
			}
		}
	}
}
$check( 'slots: next allowed weekday at 10:00 Bangkok, never the same day', $ok, $why );
$s = new DateTimeImmutable( '2026-10-11 14:00:00', $bkk ); // the real first press (a Sunday afternoon)
$chain = array();
for ( $i = 0; $i < 16; $i++ ) {
	$s       = fenix_release_next_slot( $s, array( 2, 5 ) );
	$chain[] = $s->format( 'D d/m/Y H:i' );
}
$check( 'slots: Sun 11/10/2026 14:00 → Tue 13/10 … Fri 04/12 (16 slots)', 'Tue 13/10/2026 10:00' === $chain[0] && 'Fri 16/10/2026 10:00' === $chain[1] && 'Fri 04/12/2026 10:00' === $chain[15], $chain[0] . ' … ' . $chain[15] );

// ---- A · 6 live, 2 per week, on untouched legacy drafts ----------------------------
$seed();
$log = fenix_setup_release_to_total( 6, 2 );
$pub = $by( 'publish' );
$check( 'A: 6 published, rest scheduled, none left as draft', 6 === count( $pub ) && $n - 6 === count( $by( 'future' ) ) && 0 === count( $by( 'draft' ) ), count( $pub ) . '/' . count( $by( 'future' ) ) . '/' . count( $by( 'draft' ) ) );
$check(
	'A: the first 6 of the publish order went live',
	! array_diff(
		array_slice( $order, 0, 6 ),
		array_map(
			function ( $p ) {
				return $p->post_name;
			},
			$pub
		)
	)
);
$ok  = true;
$why = '';
foreach ( $GLOBALS['fx_db'] as $slug => $p ) {
	if ( fenix_seed_content( $arts[ $slug ]['content'] ) !== $p->post_content || $arts[ $slug ]['title'] !== $p->post_title ) {
		$ok  = false;
		$why = "$slug still has the old import text";
	}
}
$check( 'A: untouched drafts were refreshed from the seed files before release', $ok, $why );
$ok  = true;
$why = '';
foreach ( $pub as $p ) {
	if ( abs( strtotime( $p->post_date_gmt . ' UTC' ) - time() ) > 5 || $p->post_date !== get_date_from_gmt( $p->post_date_gmt ) ) {
		$ok  = false;
		$why = $p->post_name . ' published with date ' . $p->post_date_gmt;
	}
}
$check( 'A: published articles carry the current time', $ok, $why );
$s    = $slots();
$now  = new DateTimeImmutable( 'now', $bkk );
$prev = null;
$ok   = true;
$why  = '';
foreach ( $s as $slug => $at ) {
	$row = $GLOBALS['fx_db'][ $slug ];
	if ( '10:00:00' !== $at->format( 'H:i:s' ) || ! in_array( (int) $at->format( 'N' ), array( 2, 5 ), true ) || $at->format( 'Y-m-d' ) <= $now->format( 'Y-m-d' ) ) {
		$ok  = false;
		$why = "$slug at " . $at->format( 'c' );
	}
	if ( $row->post_date !== get_date_from_gmt( $row->post_date_gmt ) ) {
		$ok  = false;
		$why = "$slug post_date does not match post_date_gmt";
	}
	if ( $prev && ! in_array( (int) $prev->diff( $at )->days, array( 3, 4 ), true ) ) {
		$ok  = false;
		$why = "$slug gap " . $prev->diff( $at )->days . ' days';
	}
	$prev = $at;
}
$check( 'A: scheduled Tue/Fri 10:00 Bangkok from tomorrow, 3–4 days apart', $ok, $why );
$check( 'A: scheduled in publish order', array_keys( $s ) === array_slice( $order, 6 ), implode( ',', array_slice( array_keys( $s ), 0, 3 ) ) );
$check( 'A: log = one refresh line + one result line per article, no errors', 2 * $n === count( $log ) && ! preg_grep( '/^✗/u', $log ), count( $log ) . ' lines' );

// ---- B · pressing again / re-sending the form does nothing ------------------------
$before = serialize( $GLOBALS['fx_db'] );
$log    = fenix_setup_release_to_total( 6, 2 );
$check( 'B: second press changes nothing', serialize( $GLOBALS['fx_db'] ) === $before && 1 === count( $log ) );

// ---- B2 · interrupted run: 3 went live, then the same request is sent again ----------
$seed();
fenix_setup_release_articles( 3, 2 );               // "died" after 3 … simulate: the rest scheduled is the worst case for a retry
foreach ( $by( 'future' ) as $p ) {                 // … so put the unscheduled ones back to draft as if the run stopped early
	$p->post_status   = 'draft';
	$p->post_date_gmt = FX_ZERO;
}
fenix_setup_release_to_total( 6, 2 );
fenix_setup_release_to_total( 6, 2 );
$check( 'B2: retry after an interrupted run tops up to 6 live, never more', 6 === count( $by( 'publish' ) ) && $n - 6 === count( $by( 'future' ) ), count( $by( 'publish' ) ) . ' live' );

// ---- C · queue continues after the last scheduled SEED article only ------------------
$seed();
fenix_setup_release_to_total( 6, 2 );
$s    = $slots();
$last = end( $s );
$GLOBALS['fx_db']['fx-owner-post'] = (object) array( // the owner schedules an unrelated post far in the future
	'ID'                => 999,
	'post_name'         => 'owner-announcement',
	'post_title'        => 'owner',
	'post_status'       => 'future',
	'post_content'      => '',
	'post_excerpt'      => '',
	'post_date'         => '2027-04-01 03:00:00',
	'post_date_gmt'     => '2027-04-01 03:00:00',
	'post_modified'     => '2026-10-11 00:00:00',
	'post_modified_gmt' => '2026-10-11 00:00:00',
	'fx_foreign'        => true,
);
$back = $GLOBALS['fx_db'][ $order[0] ];
$back->post_status   = 'draft';
$back->post_date_gmt = FX_ZERO;
fenix_setup_release_articles( 0, 2 );
$s2 = $slots();
$check( 'C: a new draft queues right after the last scheduled seed article', isset( $s2[ $order[0] ] ) && $s2[ $order[0] ] > $last && in_array( (int) $last->diff( $s2[ $order[0] ] )->days, array( 3, 4 ), true ), isset( $s2[ $order[0] ] ) ? $s2[ $order[0] ]->format( 'c' ) : 'not scheduled' );

// ---- D · 1 and 3 per week ---------------------------------------------------------
foreach ( array( 1 => array( 2 ), 3 => array( 1, 3, 5 ) ) as $per => $days ) {
	$seed();
	fenix_setup_release_articles( 0, $per );
	$ok = 0 === count( $by( 'publish' ) ) && $n === count( $by( 'future' ) );
	foreach ( $slots() as $at ) {
		$ok = $ok && in_array( (int) $at->format( 'N' ), $days, true ) && '10:00' === $at->format( 'H:i' );
	}
	$check( "D: $per per week uses weekdays " . implode( ',', $days ) . ' and publishes none now', $ok );
}

// ---- E · target above the number of drafts ------------------------------------------
$seed();
fenix_setup_release_to_total( 100, 2 );
$check( 'E: target >= drafts publishes everything, schedules nothing', $n === count( $by( 'publish' ) ) && 0 === count( $by( 'future' ) ) );

// ---- F · which drafts may be refreshed -------------------------------------------------
$seed();
$slug = $all[0];
$find = function () use ( $slug ) {
	return fenix_find_post( $slug );
};
$row  = $GLOBALS['fx_db'][ $slug ];
$check( 'F: untouched legacy draft (post_modified_gmt still zero) is refreshed', true === fenix_setup_refresh_draft( $find(), $arts[ $slug ] ) && fenix_seed_content( $arts[ $slug ]['content'] ) === $row->post_content && '' !== get_post_meta( $row->ID, 'fenix_seed_hash', true ) );
$check( 'F: nothing to do when the draft already matches the seed', false === fenix_setup_refresh_draft( $find(), $arts[ $slug ] ) );

$seed();
$row = $GLOBALS['fx_db'][ $slug ];
wp_update_post(
	array(
		'ID'           => $row->ID,
		'post_content' => 'edited by the owner',
	)
); // an ordinary editor save: core sets post_date = post_modified and a real post_modified_gmt
$check( 'F: fake save behaves like core (date = modified, modified_gmt set)', $row->post_date === $row->post_modified && FX_ZERO !== $row->post_modified_gmt && FX_ZERO === $row->post_date_gmt );
$check( 'F: a legacy draft the owner saved is left alone', false === fenix_setup_refresh_draft( $find(), $arts[ $slug ] ) && 'edited by the owner' === $row->post_content );

$seed();
$row = $GLOBALS['fx_db'][ $slug ];
update_post_meta( $row->ID, 'fenix_seed_hash', fenix_seed_fingerprint( $find() ) );
$check( 'F: a draft matching its import fingerprint is refreshed', true === fenix_setup_refresh_draft( $find(), $arts[ $slug ] ) && 'old import' !== $row->post_content );
$row->post_title = 'Owner headline';
$check( 'F: owner changed only the title → left alone', false === fenix_setup_refresh_draft( $find(), array_merge( $arts[ $slug ], array( 'title' => 'New seed title' ) ) ) && 'Owner headline' === $row->post_title );
$row->post_title = $arts[ $slug ]['title'];
$GLOBALS['fx_meta'][ $row->ID ]['_yoast_wpseo_title'] = 'Owner SEO title';
$check( 'F: owner changed only the Yoast title → left alone', false === fenix_setup_refresh_draft( $find(), array_merge( $arts[ $slug ], array( 'title' => 'New seed title' ) ) ) && 'Owner SEO title' === get_post_meta( $row->ID, '_yoast_wpseo_title', true ) );

$seed();
$row              = $GLOBALS['fx_db'][ $slug ];
$row->post_status = 'publish';
$check( 'F: published posts are never refreshed', false === fenix_setup_refresh_draft( $find(), $arts[ $slug ] ) && 'old import' === $row->post_content );
$row->post_status = 'future';
$check( 'F: scheduled posts are never refreshed', false === fenix_setup_refresh_draft( $find(), $arts[ $slug ] ) && 'old import' === $row->post_content );

// ---- G · the "update articles" button: unedited published and scheduled posts follow the seed, status and date stay -------
$seed();
fenix_setup_release_to_total( 6, 2 );
$pub = array_values( $by( 'publish' ) );
$fut = array_values( $by( 'future' ) );
$p1  = $pub[0];
$f1  = $fut[0];
$p2  = $pub[1];
$p1->post_content = 'old text of a published article';
$f1->post_content = 'old text of a scheduled article';
foreach ( array( $p1, $f1, $p2 ) as $r ) {
	update_post_meta( $r->ID, 'fenix_seed_hash', fenix_seed_fingerprint( fenix_find_post( $r->post_name ) ) );
}
$p2->post_content = 'the owner rewrote this published article';
$d1   = array( $p1->post_date, $p1->post_date_gmt, $f1->post_date, $f1->post_date_gmt );
$glog = implode( "\n", fenix_setup_refresh_articles() );
$check( 'G: unedited published article follows the seed file', fenix_seed_content( $arts[ $p1->post_name ]['content'] ) === $p1->post_content );
$check( 'G: it stays published with the same date', 'publish' === $p1->post_status && $d1[0] === $p1->post_date && $d1[1] === $p1->post_date_gmt, $p1->post_status . ' ' . $p1->post_date );
$check( 'G: unedited scheduled article follows the seed file', fenix_seed_content( $arts[ $f1->post_name ]['content'] ) === $f1->post_content );
$check( 'G: it stays scheduled for the same slot', 'future' === $f1->post_status && $d1[2] === $f1->post_date && $d1[3] === $f1->post_date_gmt, $f1->post_status . ' ' . $f1->post_date );
$check( 'G: a published article the owner edited is left alone', 'the owner rewrote this published article' === $p2->post_content && false !== strpos( $glog, $arts[ $p2->post_name ]['title'] ) );
$check( 'G: second run changes nothing', 0 === substr_count( implode( "\n", fenix_setup_refresh_articles() ), '↻' ) );

// ---- H · a draft the owner edited is released with the owner's text ------------------------
$seed();
$row = $GLOBALS['fx_db'][ $order[0] ];
wp_update_post(
	array(
		'ID'           => $row->ID,
		'post_content' => 'owner version',
	)
);
fenix_setup_release_to_total( 6, 2 );
$check( 'H: an owner-edited draft goes live with the owner text', 'publish' === $row->post_status && 'owner version' === $row->post_content );

// ---- I · "publish now" on a draft that still carries a future date ----------------------------
$seed();
$row                = $GLOBALS['fx_db'][ $order[0] ];
$row->post_date     = '2026-12-25 03:00:00';
$row->post_date_gmt = '2026-12-25 03:00:00'; // was scheduled, then pulled back to draft by the owner
$log                = fenix_setup_release_articles( 1, 2 );
$check( 'I: draft with a leftover future date is published now, not re-scheduled', 'publish' === $row->post_status && abs( strtotime( $row->post_date_gmt . ' UTC' ) - time() ) <= 5, $row->post_status . ' ' . $row->post_date_gmt );

// ---- J · every day: new drafts fill the days around the existing Tue/Fri queue --------------------
$k     = min( 5, $n - 6 );
$fresh = array_slice( $order, -$k );
$seed();
foreach ( $fresh as $slug ) { // the new articles are not imported yet while the old queue is set up
	$GLOBALS['fx_db'][ $slug ]->post_status = 'pending';
}
fenix_setup_release_to_total( 6, 2 );
$queue = $slots();
$GLOBALS['fx_db']['fx-owner-post'] = (object) array( // the owner scheduled a post of his own on a free day
	'ID'                => 998,
	'post_name'         => 'owner-note',
	'post_title'        => 'owner',
	'post_status'       => 'future',
	'post_content'      => '',
	'post_excerpt'      => '',
	'post_date'         => '',
	'post_date_gmt'     => '',
	'post_modified'     => '2026-10-11 00:00:00',
	'post_modified_gmt' => '2026-10-11 00:00:00',
	'fx_foreign'        => true,
);
$first = reset( $queue );
$from  = $first->modify( '+1 day' ); // a day after the first queued article (a Wed or a Sat)
while ( in_array( (int) $from->format( 'N' ), array( 2, 5 ), true ) ) {
	$from = $from->modify( '+1 day' );
}
$own = $from->modify( '+1 day' );
while ( in_array( (int) $own->format( 'N' ), array( 2, 5 ), true ) ) {
	$own = $own->modify( '+1 day' );
}
$GLOBALS['fx_db']['fx-owner-post']->post_date     = $own->setTime( 18, 0 )->setTimezone( $utc )->format( 'Y-m-d H:i:s' );
$GLOBALS['fx_db']['fx-owner-post']->post_date_gmt = $GLOBALS['fx_db']['fx-owner-post']->post_date;
foreach ( $fresh as $slug ) {
	$GLOBALS['fx_db'][ $slug ]->post_status = 'draft';
}
$before = array_map(
	function ( $at ) {
		return $at->format( 'c' );
	},
	$queue
);
$jlog  = fenix_setup_release_articles( 0, 7, $from->format( 'Y-m-d' ) );
$after = $slots();
$check( 'J: every new draft is scheduled, none published now', $k === count( array_intersect( array_keys( $after ), $fresh ) ) && 6 === count( $by( 'publish' ) ) && ! preg_grep( '/^✗/u', $jlog ), implode( ' | ', $jlog ) );
$kept = true;
foreach ( $before as $slug => $c ) {
	$kept = $kept && isset( $after[ $slug ] ) && $after[ $slug ]->format( 'c' ) === $c;
}
$check( 'J: the existing queue keeps its dates', $kept );
$days_used = array();
foreach ( $after as $slug => $at ) {
	$days_used[] = $at->format( 'Y-m-d' );
}
$days_used[] = $own->format( 'Y-m-d' );
$check( 'J: never two posts on one day (owner post included)', count( $days_used ) === count( array_unique( $days_used ) ) );
$busy = array( $own->format( 'Y-m-d' ) );
foreach ( $queue as $at ) {
	$busy[] = $at->format( 'Y-m-d' );
}
$want = array();
for ( $d = $from; count( $want ) < $k; $d = $d->modify( '+1 day' ) ) { // the first days from $from on that had no post yet
	if ( ! in_array( $d->format( 'Y-m-d' ), $busy, true ) ) {
		$want[] = $d->format( 'Y-m-d' );
	}
}
$got = array_map(
	function ( $s ) use ( $after ) {
		return $after[ $s ]->format( 'Y-m-d' ) . ' ' . $after[ $s ]->format( 'H:i' );
	},
	$fresh
);
$check(
	'J: new drafts take the first free days from the start date, in publish order, at 10:00',
	array_map(
		function ( $d ) {
			return $d . ' 10:00';
		},
		$want
	) === $got,
	implode( ',', $got ) . ' vs ' . implode( ',', $want )
);
$seed();
foreach ( $fresh as $slug ) {
	$GLOBALS['fx_db'][ $slug ]->post_status = 'pending';
}
fenix_setup_release_to_total( 6, 2 );
foreach ( $fresh as $slug ) {
	$GLOBALS['fx_db'][ $slug ]->post_status = 'draft';
}
fenix_setup_release_articles( 0, 7, 'not-a-date' );
$tomorrow = ( new DateTimeImmutable( 'now', $bkk ) )->modify( '+1 day' )->format( 'Y-m-d' );
$s        = $slots();
$check( 'J: no start date (or a bad one) starts tomorrow at the earliest', $s[ $fresh[0] ]->format( 'Y-m-d' ) >= $tomorrow, $s[ $fresh[0] ]->format( 'c' ) );

// ---- K · "update articles" renames the two old categories and moves articles into their group -------------
$seed();
fenix_setup_release_to_total( 6, 2 );
$GLOBALS['fx_terms']    = array(
	1 => 'Uncategorized',
	2 => 'พื้นฐาน EA',
	3 => 'บริหารความเสี่ยง',
	4 => 'MT5 &amp; VPS',
	9 => 'ประกาศของเว็บ',
);
$GLOBALS['fx_post_cat'] = array();
$id_of                  = function ( $slug ) {
	return $GLOBALS['fx_db'][ $slug ]->ID;
};
foreach ( $all as $slug ) {
	$GLOBALS['fx_post_cat'][ $id_of( $slug ) ] = array( 2 ); // everything starts in the old "basics" group
}
$GLOBALS['fx_post_cat'][ $id_of( 'ea-not-trading' ) ] = array( 4 );
$GLOBALS['fx_post_cat'][ $id_of( 'profit-factor' ) ]  = array( 3, 9 ); // the owner added a category of his own
$klog = implode( "\n", fenix_setup_refresh_articles() );
$cat  = function ( $slug ) use ( $id_of ) {
	return implode( ',', wp_get_post_categories( $id_of( $slug ) ) );
};
$check( 'K: old categories renamed in place (same term id)', 'EA และบอทเทรดทอง' === $GLOBALS['fx_terms'][2] && 'MT5 และบัญชีเทรด' === $GLOBALS['fx_terms'][4] && false !== strpos( $klog, 'เปลี่ยนชื่อหมวด' ), $GLOBALS['fx_terms'][2] . ' / ' . $GLOBALS['fx_terms'][4] );
$check( 'K: an article already in its group stays (renamed term)', array( 2 ) === $GLOBALS['fx_post_cat'][ $id_of( 'what-is-ea-mt5' ) ] );
$check( 'K: articles move to the group of their seed file', 'เทรดทองคำ XAUUSD' === $cat( 'ea-gold-xauusd' ) && 'EA และบอทเทรดทอง' === $cat( 'ea-not-trading' ) && 'ข่าวเศรษฐกิจ' === $cat( 'ea-news-trading' ), $cat( 'ea-gold-xauusd' ) . ' | ' . $cat( 'ea-not-trading' ) );
$check( 'K: an article with a category of the owner is left alone', array( 3, 9 ) === $GLOBALS['fx_post_cat'][ $id_of( 'profit-factor' ) ] );
$terms_before = $GLOBALS['fx_terms'];
$k2           = fenix_setup_refresh_articles();
$check( 'K: second run moves and renames nothing', $terms_before === $GLOBALS['fx_terms'] && ! preg_grep( '/หมวด/u', $k2 ), implode( ' | ', $k2 ) );
$GLOBALS['fx_terms']    = array();
$GLOBALS['fx_post_cat'] = array();

// ---- G · counts -------------------------------------------------------------------------
$seed();
fenix_setup_release_to_total( 6, 2 );
$c = fenix_setup_article_counts();
$s = $slots();
$check( 'G: counts, next and last date match the table', 6 === $c['publish'] && $n - 6 === $c['future'] && 0 === $c['draft'] && 0 === $c['none'] && reset( $s )->format( 'd/m/Y H:i' ) === $c['next'] && end( $s )->format( 'd/m/Y' ) === $c['last'], wp_json_encode( $c ) );

echo $problems ? "\n$problems issue(s)\n" : "\nall good\n";
exit( $problems ? 1 : 0 );
