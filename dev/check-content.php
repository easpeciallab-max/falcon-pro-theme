<?php
/**
 * Dev check for seed content in falcon-pro/inc/content/ (see dev/content-spec.md)
 * Also checks the shared tokens / shortcodes and the step pictures the guide pages ship with (files named in the step
 * data of inc/modules/guides.php must be in assets/img/guides/).
 *
 * Retired pages (13 Oct 2026: FALCON is used through the member dashboard only, the self-install guides and the Forward
 * Test page left the site): a manifest row with a 'retired' key in fenix_site_pages_manifest() (inc/setup.php). Their seed
 * files stay in the repo untouched and are not checked, their guide data needs no step pictures, a live page seed must not
 * link to them, and page seeds must not bring the two-ways wording back (row "one way of delivery" per file).
 * Article seeds are the owner's own plan: a link from an article to a retired page is listed as a note, not an issue
 * (the theme turns such links into plain text: fenix_unreachable_slugs()).
 *
 * Run: php -d extension=mbstring dev/check-content.php
 * Exit code: 0 = "all good", 1 = at least one issue (dev/deploy.sh stops the push on 1).
 */
require __DIR__ . '/preview/wp-stubs.php';
require __DIR__ . '/../falcon-pro/functions.php';

$root = __DIR__ . '/../falcon-pro/inc/content/';

// slug => redirect target of every retired page · guarded: a tree whose manifest has no 'retired' key gives an empty list
$retired = array();
if ( function_exists( 'fenix_retired_pages' ) ) {
	foreach ( (array) fenix_retired_pages() as $r_key => $r_value ) {
		if ( is_string( $r_key ) ) {
			$retired[ $r_key ] = is_string( $r_value ) ? $r_value : '';
		} else {
			$retired[ (string) $r_value ] = '';
		}
	}
} else {
	$r_rows = function_exists( 'fenix_site_pages_manifest' ) ? fenix_site_pages_manifest() : fenix_site_pages( true );
	foreach ( (array) $r_rows as $r_slug => $r_page ) {
		if ( is_array( $r_page ) && ! empty( $r_page['retired'] ) ) {
			$retired[ (string) $r_slug ] = is_string( $r_page['retired'] ) ? $r_page['retired'] : '';
		}
	}
}

$pages    = array_values( array_diff( array_keys( fenix_site_pages() ), array_keys( $retired ) ) );
$articles = array_map(
	function ( $f ) {
		return basename( $f, '.html' );
	},
	glob( $root . 'articles/*.html' )
);
$valid    = array_merge( $pages, $articles );
$problems = 0;

// Wording of the old model (two ways of delivery, files handed to the customer, a Forward Test page) that a page seed must
// not bring back. Kept to markers that have no meaning in the dashboard-only model: "no VPS needed" or "the MT5 app is
// installed to watch the account" stay allowed. pages/backtest keeps the words "Forward Test" (limitations quoted from the dashboard).
$old_model = array(
	'ติดตั้งเอง'   => 'self-install',
	'สองวิธี'      => 'two ways',
	'สองทาง'      => 'two ways',
	'วิธีที่ 1'     => 'way 1 / way 2',
	'วิธีที่ 2'     => 'way 1 / way 2',
	'.ex5'         => 'EA file',
	'Preset'       => 'preset file',
	'Forward Test' => 'Forward Test page',
);

foreach ( array_merge( glob( $root . 'pages/*.html' ), glob( $root . 'articles/*.html' ) ) as $file ) {
	$rel   = basename( dirname( $file ) ) . '/' . basename( $file, '.html' );
	$raw   = file_get_contents( $file );
	$issue = array();
	$notes = array();

	// a retired page: the file is kept as it was, it is no longer imported or linked, so nothing in it is checked
	if ( 0 === strpos( $rel, 'pages/' ) && isset( $retired[ basename( $file, '.html' ) ] ) ) {
		$r_to = $retired[ basename( $file, '.html' ) ];
		printf( "%-34s %s\n", $rel, '– retired' . ( '' !== $r_to ? ' (→ ' . $r_to . ')' : '' ) . ' · file kept, not checked' );
		continue;
	}

	if ( ! preg_match( '/^\s*<!--meta\s+(\{.*?\})\s*-->/s', $raw, $m ) || ! is_array( json_decode( $m[1], true ) ) ) {
		$issue[] = 'meta JSON missing/invalid';
		$meta    = array();
	} else {
		$meta = json_decode( $m[1], true );
		foreach ( array( 'title', 'seo_title', 'description', 'keyword' ) as $k ) {
			if ( empty( $meta[ $k ] ) ) {
				$issue[] = "meta.$k empty";
			}
		}
		if ( 0 === strpos( $rel, 'articles/' ) ) {
			foreach ( array( 'excerpt', 'category' ) as $k ) {
				if ( empty( $meta[ $k ] ) ) {
					$issue[] = "meta.$k empty";
				}
			}
		}
		if ( ! empty( $meta['seo_title'] ) && mb_strlen( $meta['seo_title'] ) > 65 ) {
			$issue[] = 'seo_title > 65 chars';
		}
	}
	if ( preg_match( '/<h1[\s>]/i', $raw ) ) {
		$issue[] = 'contains <h1>';
	}
	if ( preg_match( '/<img[\s>]/i', $raw ) ) {
		$issue[] = 'contains <img>';
	}
	if ( false === strpos( $raw, 'callout--warn' ) ) {
		$issue[] = 'no callout--warn (risk context)';
	}
	if ( substr_count( $raw, '[falcon_line' ) !== substr_count( $raw, '[/falcon_line]' ) ) {
		$issue[] = 'unbalanced [falcon_line]';
	}
	// shortcodes: only tags the theme registers, valid attribute values, balanced [falcon_if] (rules in dev/content-spec.md)
	if ( preg_match_all( '/\[\/?(falcon_[a-z_]+)/', $raw, $sc ) ) {
		foreach ( array_unique( $sc[1] ) as $tag ) {
			if ( ! in_array( $tag, fenix_shortcode_tags(), true ) ) {
				$issue[] = "unknown shortcode [$tag]";
			}
		}
	}
	if ( preg_match_all( '/\[falcon_broker\b([^\]]*)\]/', $raw, $bk ) ) {
		$fields = array();
		foreach ( $bk[1] as $attr ) {
			$a        = shortcode_parse_atts( $attr );
			$field    = isset( $a['field'] ) ? $a['field'] : 'name';
			$fields[] = $field;
			if ( ! in_array( $field, fenix_sc_broker_fields(), true ) ) {
				$issue[] = "[falcon_broker] unknown field \"$field\"";
			}
		}
		// a partner signup button must come with the partner disclosure on the same page
		if ( in_array( 'url', $fields, true ) && ! in_array( 'disclosure', $fields, true ) ) {
			$issue[] = '[falcon_broker field="url"] without [falcon_broker field="disclosure"]';
		}
	}
	// a hand-typed callout around the disclosure stays on the page as an empty frame while no partner link is set
	if ( preg_match( '#<div class="callout[^"]*">\s*(?:<p>\s*)?\[falcon_broker\b[^\]]*field="disclosure"[^\]]*\]\s*(?:</p>\s*)?</div>#i', $raw ) ) {
		$issue[] = '[falcon_broker field="disclosure"] inside its own callout: use box="1" instead';
	}
	// the theme uppercases headings: a server name inside one would be shown in the wrong case
	if ( preg_match( '#<h[2-6](\s[^>]*)?>(?:(?!</h[2-6]>).)*\[falcon_broker[^\]]*field="server"#is', $raw ) ) {
		$issue[] = '[falcon_broker field="server"] inside a heading';
	}
	$if_depth = 0;
	$if_bad   = false;
	if ( preg_match_all( '/\[(\/?)falcon_if\b([^\]]*)\]/', $raw, $ifs, PREG_SET_ORDER ) ) {
		foreach ( $ifs as $if ) {
			$if_depth += '/' === $if[1] ? -1 : 1;
			$if_bad    = $if_bad || $if_depth < 0 || $if_depth > 1;
			if ( '/' === $if[1] ) {
				continue;
			}
			$a = shortcode_parse_atts( $if[2] );
			if ( ! $a || array_diff( array_keys( $a ), array( 'has', 'not' ) ) ) {
				$issue[] = '[falcon_if] needs has="…" and/or not="…" (no other attribute)';
			}
			foreach ( array_intersect_key( $a, array( 'has' => 1, 'not' => 1 ) ) as $list ) {
				foreach ( preg_split( '/[\s,]+/', $list, -1, PREG_SPLIT_NO_EMPTY ) as $cond ) {
					if ( ! in_array( $cond, fenix_sc_if_keys(), true ) ) {
						$issue[] = "[falcon_if] unknown condition \"$cond\"";
					}
				}
			}
		}
	}
	if ( 0 !== $if_depth || $if_bad ) {
		$issue[] = 'unbalanced or nested [falcon_if] … [/falcon_if]';
	}
	// [falcon_email] prints nothing while no address is set: the sentence around it has to disappear with it
	if ( false !== strpos( (string) preg_replace( '/\[falcon_if\b[^\]]*\bhas="[^"]*\bemail\b[^"]*"[^\]]*\].*?\[\/falcon_if\]/s', '', $raw ), '[falcon_email' ) ) {
		$issue[] = '[falcon_email] outside [falcon_if has="email"] … [/falcon_if]';
	}
	// <p>[falcon_if]…[/falcon_if]</p>: WordPress drops that <p> when the condition holds and leaves an empty tag when it does not
	if ( preg_match( '#<(p|li|h[2-6]|td)(\s[^>]*)?>\s*\[falcon_if\b[^\]]*\](?:(?!\[/falcon_if\]).)*\[/falcon_if\]\s*</\1>#is', $raw ) ) {
		$issue[] = '[falcon_if] is the only content of a block tag: put the tags outside it ([falcon_if …]<p>…</p>[/falcon_if])';
	}
	if ( preg_match_all( '/\[falcon_figure\b([^\]]*)\]/', $raw, $figs ) ) {
		foreach ( $figs[1] as $attr ) {
			$a    = shortcode_parse_atts( $attr );
			$name = isset( $a['name'] ) ? $a['name'] : '';
			if ( '' === $name || null === fenix_figure( $name ) ) {
				$issue[] = "[falcon_figure name=\"$name\"] is not in fenix_figure_registry() or its file is missing in assets/img/illus/";
			}
		}
	}
	// [falcon_dashboard mode="…" pos="…"]label[/falcon_dashboard]: the button to the member dashboard (rules in dev/content-spec.md)
	if ( substr_count( $raw, '[falcon_dashboard' ) !== substr_count( $raw, '[/falcon_dashboard]' ) ) {
		$issue[] = 'unbalanced [falcon_dashboard]';
	}
	if ( preg_match_all( '/\[falcon_dashboard\b([^\]]*)\]/', $raw, $dsh ) ) {
		$dash_modes = array();
		foreach ( $dsh[1] as $attr ) {
			$a    = shortcode_parse_atts( $attr );
			$mode = isset( $a['mode'] ) ? $a['mode'] : 'visit';
			if ( ! in_array( $mode, fenix_sc_dashboard_modes(), true ) ) {
				$issue[] = "[falcon_dashboard] unknown mode \"$mode\"";
			}
			if ( array_diff( array_keys( $a ), array( 'mode', 'pos' ) ) ) {
				$issue[] = '[falcon_dashboard] takes mode="…" and pos="…" only';
			}
			$dash_modes[] = $mode;
		}
		// a block-level button: on a line of its own, never inside a sentence, a list item, a heading or an FAQ answer
		$dash_own = preg_match_all( '/^[ \t]*\[falcon_dashboard\b[^\]\r\n]*\][^\[\r\n]*\[\/falcon_dashboard\][ \t]*\r?$/m', $raw );
		if ( $dash_own !== count( $dsh[1] ) ) {
			$issue[] = '[falcon_dashboard] must stand on a line of its own (it prints a whole button block)';
		}
		if ( preg_match( '#<details\b(?:(?!</details>).)*\[falcon_dashboard\b#is', $raw ) ) {
			$issue[] = '[falcon_dashboard] inside an FAQ item (the button text would end up in the FAQ schema)';
		}
		// what the button leads to has to be said on the same page: visit mode shows sample data, sign-up needs an invitation code
		if ( in_array( 'visit', $dash_modes, true ) && false === mb_strpos( $raw, 'ข้อมูลตัวอย่าง' ) ) {
			$issue[] = '[falcon_dashboard mode="visit"] without the words "ข้อมูลตัวอย่าง" on the page (visit mode shows sample data, not results)';
		}
		if ( in_array( 'signup', $dash_modes, true ) && false === mb_strpos( $raw, 'รหัสเชิญ' ) ) {
			$issue[] = '[falcon_dashboard mode="signup"] without the words "รหัสเชิญ" on the page (sign-up needs an invitation code)';
		}
	}
	// the dashboard address lives in one Customizer setting (dashboard_url): a seed links to it through [falcon_dashboard] only
	$dash_all  = fenix_defaults();
	$dash_host = isset( $dash_all['dashboard_url'] ) ? (string) parse_url( (string) $dash_all['dashboard_url'], PHP_URL_HOST ) : '';
	if ( '' !== $dash_host && false !== stripos( $raw, $dash_host ) ) {
		$issue[] = "the dashboard address ($dash_host) is typed into the seed: use [falcon_dashboard] instead";
	}
	// HTML comments after the meta line (owner notes): a comment that reaches a page is readable in its public source.
	// Page seeds: an issue (the legal seeds were completed without them, so any comment is a leftover note).
	// The same goes for the owner marker text "เจ้าของเว็บ:" that the old drafts used for blanks still to be filled in.
	if ( 0 === strpos( $rel, 'pages/' ) ) {
		if ( preg_match_all( '/<!--(?!\s*(?:more|nextpage|noteaser|\/?wp:)).*?-->/s', preg_replace( '/^\s*<!--meta.*?-->/s', '', $raw ), $cm ) ) {
			$issue[] = count( $cm[0] ) . ' HTML comment(s) after the meta line (readable in the public page source: remove them)';
		}
		if ( false !== strpos( $raw, 'เจ้าของเว็บ:' ) ) {
			$issue[] = 'owner marker "เจ้าของเว็บ:" left in the seed (a blank that was never filled in)';
		}
		// one way of delivery: the dashboard only (meta line included, it becomes the page title and the meta description)
		$old_hits = array();
		foreach ( $old_model as $old_word => $old_what ) {
			if ( 'Forward Test' === $old_word && 'pages/backtest' === $rel ) {
				continue;
			}
			if ( false !== strpos( $raw, $old_word ) ) {
				$old_hits[] = '"' . $old_word . '" (' . $old_what . ')';
			}
		}
		if ( $old_hits ) {
			$issue[] = 'wording of the old two-ways model: ' . implode( ', ', $old_hits );
		}
	}
	if ( preg_match_all( '#\{\{home\}\}/([a-z0-9\-]+)/#', $raw, $links ) ) {
		$retired_links = array();
		foreach ( array_unique( $links[1] ) as $slug ) {
			if ( isset( $retired[ $slug ] ) ) {
				$retired_links[] = "/$slug/";
			} elseif ( ! in_array( $slug, $valid, true ) ) {
				$issue[] = "link to unknown slug /$slug/";
			}
		}
		if ( $retired_links ) {
			if ( 0 === strpos( $rel, 'pages/' ) ) {
				$issue[] = 'link to a retired page: ' . implode( ' ', $retired_links ) . ' (removed from the site: link to the page that replaced it)';
			} else {
				$notes[] = 'links to retired pages ' . implode( ' ', $retired_links ) . ' (shown as plain text on the site)';
			}
		}
	}
	// external links (internal ones are written {{home}}/slug/): new tab + rel="noopener", see "External links" in dev/content-spec.md
	if ( preg_match_all( '#<a\b[^>]*\bhref\s*=\s*(["\'])\s*(?:https?:)?//[^"\']*\1[^>]*>#i', $raw, $ext ) ) {
		foreach ( $ext[0] as $a_tag ) {
			if ( ! preg_match( '/\btarget\s*=\s*(["\'])_blank\1/i', $a_tag ) || ! preg_match( '/\brel\s*=\s*(["\'])[^"\']*\bnoopener\b[^"\']*\1/i', $a_tag ) ) {
				$issue[] = 'external link without target="_blank" rel="noopener": ' . ( preg_match( '#\bhref\s*=\s*["\']\s*([^"\']+)#i', $a_tag, $h ) ? $h[1] : $a_tag );
			}
		}
	}
	// balanced common block tags
	foreach ( array( 'p', 'div', 'ul', 'ol', 'li', 'table', 'details', 'h2', 'h3' ) as $tag ) {
		$open  = preg_match_all( "#<$tag(\s[^>]*)?>#i", $raw );
		$close = preg_match_all( "#</$tag>#i", $raw );
		if ( $open !== $close ) {
			$issue[] = "<$tag> open $open / close $close";
		}
	}
	// no invented performance claims (heuristic)
	if ( preg_match( '/(win\s*rate|อัตราชนะ)[^<]{0,20}\d{2}(\.\d+)?\s*%/iu', $raw ) ) {
		$issue[] = 'possible win-rate number — check it is hypothetical';
	}
	// while every seed article and legal page is still a draft, fenix_unlink_slugs() may only drop
	// related-links items, xref sentences and <a> tags — never headings, tables, FAQ or risk warnings
	// (retired pages are unreachable for good, so links to them are always part of the simulation)
	$all_drafts = array_merge( $articles, array( 'privacy-policy', 'terms-of-use', 'data-deletion' ), array_keys( $retired ) );
	$body       = fenix_seed_content( $rel );
	$after      = fenix_unlink_slugs( $body, $all_drafts );
	$core       = function ( $html ) {
		$html = preg_replace( '#<div class="related-links">.*?</div>#is', '', $html );
		$html = preg_replace( '#<(p|span) class="xref">.*?</\1>#is', '', $html );
		return array(
			'h2'   => preg_match_all( '#<h2[\s>]#i', $html ),
			'h3'   => preg_match_all( '#<h3[\s>]#i', $html ),
			'tbl'  => preg_match_all( '#<table[\s>]#i', $html ),
			'faq'  => substr_count( $html, 'faq-item' ),
			'warn' => substr_count( $html, 'callout--warn' ),
			'text' => mb_strlen( preg_replace( '/\s+/u', '', strip_tags( $html ) ) ),
		);
	};
	$b4 = $core( $body );
	$af = $core( $after );
	foreach ( array( 'h2', 'h3', 'tbl', 'faq', 'warn' ) as $k ) {
		if ( $b4[ $k ] !== $af[ $k ] ) {
			$issue[] = "unlink simulation lost $k ({$b4[ $k ]} → {$af[ $k ]})";
		}
	}
	if ( $af['text'] < $b4['text'] * 0.97 ) {
		$issue[] = "unlink simulation lost text ({$b4['text']} → {$af['text']} chars)";
	}
	foreach ( array( 'p', 'div', 'ul', 'li', 'span' ) as $tag ) {
		if ( preg_match_all( "#<$tag(\s[^>]*)?>#i", $after ) !== preg_match_all( "#</$tag>#i", $after ) ) {
			$issue[] = "unlink simulation left <$tag> unbalanced";
		}
	}

	$words = mb_strlen( trim( preg_replace( '/\s+/u', ' ', strip_tags( preg_replace( '/^\s*<!--meta.*?-->/s', '', $raw ) ) ) ) );
	printf( "%-34s %6d chars  %s%s\n", $rel, $words, $issue ? '✗ ' . implode( '; ', $issue ) : '✓', $notes ? ' · note: ' . implode( '; ', $notes ) : '' );
	$problems += count( $issue );
}
// fenix_unlink_slugs() fixtures: hand-typed or malformed HTML must never lose non-link content
// (cases reproduced by the pre-push review · D = draft slug, L = live page)
$H        = untrailingslashit( home_url() );
$big      = str_repeat( 'ก', 40000 );
$fixtures = array(
	'li link + text'        => array( "<ul><li><a href=\"$H/drawdown/\">DD</a> ข้อความต่อ</li></ul><h2>หัวข้อ</h2><div class=\"callout callout--warn\"><p>คำเตือน</p></div><ul><li><a href=\"$H/tools/\">T</a></li></ul>", array( 'ข้อความต่อ', '<h2>หัวข้อ</h2>', 'callout--warn', "href=\"$H/tools/\"" ), array( "$H/drawdown/" ) ),
	'end tag </a >'         => array( "<ul><li><a href=\"$H/drawdown/\">DD</a ></li><li>สำคัญ ห้ามหาย</li><li><a href=\"$H/tools/\">T</a></li></ul>", array( 'สำคัญ ห้ามหาย', "href=\"$H/tools/\"" ), array( "$H/drawdown/" ) ),
	'unclosed draft <a>'    => array( "<ul><li><a href=\"$H/drawdown/\">DD</li><li>สำคัญ ห้ามหาย</li><li><a href=\"$H/tools/\">T</a></li></ul><h2>หัวข้อ</h2><div class=\"callout callout--warn\"><p>คำเตือน</p></div>", array( 'สำคัญ ห้ามหาย', "href=\"$H/tools/\">T</a>", '<h2>หัวข้อ</h2>', 'callout--warn' ), array() ),
	'p.xref without </p>'   => array( "<p class=\"xref\">อ่าน <a href=\"$H/drawdown/\">DD</a>\n<ul><li>ขั้นตอนสำคัญ</li></ul>\n<h2>หัวข้อถัดไป</h2>\n<p>คำเตือนความเสี่ยง</p>", array( 'ขั้นตอนสำคัญ', 'หัวข้อถัดไป', 'คำเตือนความเสี่ยง' ), array( "$H/drawdown/" ) ),
	'span.xref nested span' => array( "<p>ก <span class=\"xref\">อ่าน <span style=\"color:#15803d\"><a href=\"$H/drawdown/\">DD</a></span> เพิ่มเติม</span> ข</p>", array( '<p>ก ', 'เพิ่มเติม', ' ข</p>' ), array( "$H/drawdown/" ) ),
	'span.xref removed'     => array( "<p>ก <span class=\"xref\">อ่าน <a href=\"$H/drawdown/\">DD</a> เพิ่มเติม</span></p>", array( '<p>ก</p>' ), array( 'DD', 'xref' ) ),
	'related box emptied'   => array( "<div class=\"related-links\"><h2 id=\"toc-3\">ที่เกี่ยวข้อง</h2><ul class=\"wp-block-list\">\n<li><a href=\"$H/drawdown/\">DD</a></li>\n</ul></div><p>ท้าย</p>", array( '<p>ท้าย</p>' ), array( 'related-links', 'ที่เกี่ยวข้อง' ) ),
	'href variants'         => array( "<p><a href='$H/drawdown/'>A</a> <a href=\"$H/drawdown/#faq\">B</a> <a href=\"/drawdown/?x=1\">C</a> <A HREF=\"$H/drawdown/\">E</A> <a href=\"https://other.example/drawdown/\">F</a></p>", array( 'A B C E', 'https://other.example/drawdown/' ), array( "$H/drawdown/", "'$H/drawdown/'", '"/drawdown/' ) ),
	'huge link text'        => array( "<p><a href=\"$H/drawdown/\">$big</a> <a href=\"$H/leverage/\">LV</a></p>", array( $big, 'LV' ), array( "$H/drawdown/", "$H/leverage/" ) ),
);
foreach ( $fixtures as $name => $fx ) {
	$out  = fenix_unlink_slugs( $fx[0], array( 'drawdown', 'leverage' ) );
	$fail = array();
	foreach ( $fx[1] as $must ) {
		if ( false === strpos( $out, $must ) ) {
			$fail[] = 'lost "' . mb_substr( $must, 0, 30 ) . '"';
		}
	}
	foreach ( $fx[2] as $gone ) {
		if ( false !== stripos( $out, $gone ) ) {
			$fail[] = 'kept "' . mb_substr( $gone, 0, 30 ) . '"';
		}
	}
	// tags that were balanced before must stay balanced (no orphan end tags or half-removed wrappers)
	$bal = function ( $html, $tag ) {
		return preg_match_all( "#<$tag(\s[^>]*)?>#i", $html ) - preg_match_all( "#</$tag\s*>#i", $html );
	};
	foreach ( array( 'p', 'ul', 'li', 'span', 'div', 'a' ) as $tag ) {
		if ( 0 === $bal( $fx[0], $tag ) && 0 !== $bal( $out, $tag ) ) {
			$fail[] = "<$tag> unbalanced";
		}
	}
	printf( "%-34s %s\n", 'unlink: ' . $name, $fail ? '✗ ' . implode( '; ', $fail ) : '✓' );
	$problems += count( $fail );
}

// Customizer text links [label](/slug/) to an unreachable slug must render as plain text
$GLOBALS['fenix_unpublished_seed'] = array( 'drawdown' );
$rich = fenix_rich_inline( 'อ่าน [Drawdown](/drawdown/) และ [เครื่องมือ](/tools/)' );
unset( $GLOBALS['fenix_unpublished_seed'] );
$ok = false === strpos( $rich, '/drawdown/' ) && false !== strpos( $rich, 'Drawdown' ) && false !== strpos( $rich, '/tools/"' );
printf( "%-34s %s\n", 'rich_inline: draft link', $ok ? '✓' : '✗ ' . $rich );
$problems += $ok ? 0 : 1;

// Shared tokens, the partner disclosure gate, content shortcodes and schema text (functions.php, inc/shortcodes.php, inc/seo.php).
// $GLOBALS['fx_mods'] fakes saved Customizer values · every name, link and amount below is a test value, not the owner's setting.
$sc_rows = array();
$sc      = function ( $name, $ok, $got = '' ) use ( &$sc_rows ) {
	$sc_rows[] = array( $name, (bool) $ok, is_string( $got ) ? $got : wp_json_encode( $got, JSON_UNESCAPED_UNICODE ) );
};
$d       = fenix_defaults();

$GLOBALS['fx_mods'] = array(); // defaults only: no partner link, no e-mail
$sc( 'default disclosure is tokenised', false !== strpos( $d['broker_disclosure'], '{broker}' ) && false !== strpos( $d['broker_disclosure'], '{brand}' ) && false === strpos( $d['broker_disclosure'], ' | ' ) && 0 !== strpos( $d['broker_disclosure'], '- ' ) );
$sc( 'no link: disclosure prints nothing', '' === fenix_broker_disclosure() && '' === do_shortcode( '[falcon_broker field="disclosure"]' ) && '' === do_shortcode( '[falcon_broker field="disclosure" box="1"]' ) && 'ก' === trim( fenix_tokens( 'ก {disclosure}' ) ) );
$sc( 'no link: signup button hidden', '' === do_shortcode( '[falcon_broker field="url"]' ) );
$sc( 'no link: [falcon_if] has / not', '' === do_shortcode( '[falcon_if has="partner"]X[/falcon_if]' ) && 'Y' === do_shortcode( '[falcon_if not="partner"]Y[/falcon_if]' ) );
$sc( 'no e-mail: [falcon_email] empty', '' === do_shortcode( '[falcon_email]' ) && '' === do_shortcode( '[falcon_if has="email"]X[/falcon_if]' ) );
$sc( '[falcon_if] typo or no condition', '' === do_shortcode( '[falcon_if has="partnr"]X[/falcon_if]' ) . do_shortcode( '[falcon_if not="partnr"]X[/falcon_if]' ) . do_shortcode( '[falcon_if]X[/falcon_if]' ) );
$plain = fenix_seo_plain_text( '<p>ก [falcon_brand] ข [falcon_broker] [falcon_broker field="url"][falcon_line pos="x"]ปุ่ม[/falcon_line][falcon_dashboard mode="visit" pos="x"]ปุ่มแดชบอร์ด[/falcon_dashboard][falcon_dashboard mode="signup"][/falcon_dashboard][falcon_if has="partner"]ซ่อน[/falcon_if][falcon_email]</p>' );
$sc( 'plain text: values in, buttons out', 'ก FALCON PRO EA ข ' . $d['broker_name'] === $plain, $plain );
$sc( 'unregistered figure prints nothing', '' === do_shortcode( '[falcon_figure name="no-such-figure"]' ) );

$GLOBALS['fx_mods'] = array(
	'broker_name'        => 'Test Broker X',
	'broker_server'      => 'TestX-Live01',
	'broker_signup_url'  => 'https://example.com/signup?ref=1',
	'broker_signup_text' => 'เปิดบัญชี <b>{broker}</b>',
	'contact_email'      => 'team@example.com',
);
$dis = fenix_broker_disclosure();
$sc( 'link set: disclosure names broker + brand', false !== strpos( $dis, 'Test Broker X' ) && false !== strpos( $dis, 'FALCON PRO EA' ) && false === strpos( $dis, '{' ), $dis );
$one = do_shortcode( '[falcon_broker field="disclosure"]' );
$sc( 'link set: disclosure is one <p> block', 0 === strpos( $one, '<p class="broker-disclosure"' ) && '</p>' === substr( $one, -4 ) && 1 === substr_count( $one, '<p' ), $one );
$box = do_shortcode( '[falcon_broker field="disclosure" box="1"]' );
$sc( 'link set: box="1" is a callout', 0 === strpos( $box, '<div class="callout callout--info' ) && '</div>' === substr( $box, -6 ), $box );
$btn = do_shortcode( '[falcon_broker field="url"]' );
$sc( 'link set: sponsored button, escaped label', false !== strpos( $btn, 'rel="noopener sponsored"' ) && false !== strpos( $btn, 'เปิดบัญชี &lt;b&gt;Test Broker X&lt;/b&gt;' ), $btn );
$sc( 'link set: name / server / tokens', 'Test Broker X' === do_shortcode( '[falcon_broker]' ) && 'TestX-Live01' === do_shortcode( '[falcon_broker field="server"]' ) && 'Test Broker X / TestX-Live01 / FALCON PRO EA' === fenix_tokens( '{broker} / {server} / {brand}' ) );
$mail = do_shortcode( '[falcon_email]' );
$sc( 'e-mail: mailto link, keep-case', 0 === strpos( $mail, '<a class="keep-case" href="mailto:' ) && false !== strpos( $mail, 'team@example.com</a>' ), $mail );
$sc( '[falcon_if] runs inner shortcodes', 'อีเมล ' . $mail === do_shortcode( '[falcon_if has="email,partner"]อีเมล [falcon_email][/falcon_if]' ) && '' === do_shortcode( '[falcon_if not="partner"]Y[/falcon_if]' ) );
$sc( '[falcon_if] block form: stray </p> <p>', '<h2>ก</h2>' === trim( fenix_sc_if( array( 'has' => 'partner' ), "</p>\n<h2>ก</h2>\n<p>" ) ) );
$faq = fenix_faq_from_html( '<details class="faq-item"><summary><span>ใช้ [falcon_broker] ได้ไหม?</span></summary><div class="faq-answer"><p>ได้ [falcon_broker field="disclosure"][falcon_if not="partner"]ซ่อน[/falcon_if]</p></div></details>[falcon_if not="partner"]<details class="faq-item"><summary>ซ่อน?</summary><p>ซ่อน</p></details>[/falcon_if]' );
$sc( 'FAQ schema follows shortcodes', 1 === count( $faq ) && 'ใช้ Test Broker X ได้ไหม?' === $faq[0]['name'] && 'ได้' === $faq[0]['acceptedAnswer']['text'], $faq );

$GLOBALS['fx_mods']['broker_disclosure'] = '';
/* An emptied disclosure field falls back to the default text while the partner is ready: the disclosure cannot be switched off while a sponsored button is on the site */
$sc( 'disclosure text cleared: default text', $dis === fenix_broker_disclosure() && false !== strpos( do_shortcode( '[falcon_broker field="disclosure"]' ), 'Test Broker X' ), fenix_broker_disclosure() );
/* A signup link without a real broker name: one shared guard (fenix_partner_ready) keeps the button, the disclosure and [falcon_if has="partner"] off together */
$GLOBALS['fx_mods'] = array( 'broker_signup_url' => 'https://example.com/signup?ref=1' );
$sc( 'link without a broker name: partner off', ! fenix_partner_ready() && fenix_partner_pending() && '' === fenix_broker_disclosure() && '' === do_shortcode( '[falcon_broker field="url"]' ) && '' === do_shortcode( '[falcon_if has="partner"]X[/falcon_if]' ) );
$GLOBALS['fx_mods'] = array( 'broker_name' => ' ', 'contact_email' => 'not-an-email' );
$sc( 'blank name → default, bad e-mail hidden', $d['broker_name'] === fenix_tokens( '{broker}' ) && '' === do_shortcode( '[falcon_email]' ) );

$sc( 'free price words', fenix_is_free_price( 'ฟรี' ) && fenix_is_free_price( ' FREE ' ) && fenix_is_free_price( '0' ) && ! fenix_is_free_price( 'X,XXX' ) && ! fenix_is_free_price( '1,234' ) && ! fenix_is_free_price( '' ) );
$GLOBALS['fx_mods'] = array( 'pricing_mode' => 'contact', 'seo_pricing_confirmed' => true, 'pkg1_price' => 'ฟรี', 'pkg2_price' => '1,234', 'pkg3_price' => '5,678' );
$sc( 'Offer schema: none in contact mode', null === fenix_seo_product_offers() );
$GLOBALS['fx_mods']['pricing_mode'] = 'price';
$offers = fenix_seo_product_offers();
$sc( 'Offer schema: free tier skipped', is_array( $offers ) && 2 === count( $offers ) && '1234' === $offers[0]['price'], $offers );
$GLOBALS['fx_mods']['seo_pricing_confirmed'] = false;
$sc( 'Offer schema: none until confirmed', null === fenix_seo_product_offers() );

// Dashboard links, button and shortcode (inc/modules/dashboard.php, [falcon_dashboard] in inc/shortcodes.php).
// The address comes from one setting: the tests below read the default, they never spell the host themselves.
$GLOBALS['fx_mods'] = array();
$dash_base          = fenix_dashboard_url();
$sc( 'dashboard: default address + modes', '' !== $dash_base && '/' === substr( $dash_base, -1 ) && $dash_base . '#visit' === fenix_dashboard_url( 'visit' ) && $dash_base . '#signup' === fenix_dashboard_url( 'signup' ) && $dash_base . '#signin' === fenix_dashboard_url( 'signin' ) && $dash_base === fenix_dashboard_url( 'nonsense' ), $dash_base );
// button labels wrap Thai loan words in <span class="nobr"> so they never break mid-word: unwrapped before comparing
$dash_plain = function ( $html ) {
	return (string) preg_replace( '#<span class="nobr">([^<]*)</span>#', '$1', (string) $html );
};
$dash_btn   = $dash_plain( do_shortcode( '[falcon_dashboard mode="visit" pos="Test Pos"]ปุ่ม <b>ทดสอบ</b>[/falcon_dashboard]' ) );
$sc( 'dashboard: shortcode button', 0 === strpos( $dash_btn, '<span class="sc-line sc-dash"><a class="btn btn-fire" href="' . $dash_base . '#visit" target="_blank" rel="noopener" data-dash="visit" data-dash-pos="testpos">' ) && false !== strpos( $dash_btn, '<span>ปุ่ม ทดสอบ</span>' ) && '</a></span>' === substr( $dash_btn, -11 ), $dash_btn );
$dash_esc = fenix_dashboard_esc( 'ดูแดชบอร์ด <b>x</b> & ออเดอร์ Stop-Loss' );
$sc( 'dashboard: nobr words, text unchanged', 'ดู<span class="nobr">แดชบอร์ด</span> &lt;b&gt;x&lt;/b&gt; &amp; <span class="nobr">ออเดอร์</span> <span class="nobr">Stop-Loss</span>' === $dash_esc && 'ดูแดชบอร์ด &lt;b&gt;x&lt;/b&gt; &amp; ออเดอร์ Stop-Loss' === strip_tags( $dash_esc ), $dash_esc );
$dash_up = $dash_plain( do_shortcode( '[falcon_dashboard mode="signup"][/falcon_dashboard]' ) );
$sc( 'dashboard: empty label → setting', false !== strpos( $dash_up, '<span>' . esc_html( $d['dashboard_signup_label'] ) . '</span>' ) && false !== strpos( $dash_up, 'data-dash="signup"' ) && false !== strpos( $dash_up, 'btn btn-dark' ), $dash_up );
$sc( 'dashboard: mode typo prints nothing', '' === do_shortcode( '[falcon_dashboard mode="vist"]X[/falcon_dashboard]' ) );
$sc( 'dashboard: button() args', false !== strpos( fenix_dashboard_button( 'signin', array( 'class' => 'my-class', 'pos' => 'footer', 'icon' => false, 'arrow' => false ) ), '<a class="my-class" href="' . $dash_base . '#signin" target="_blank" rel="noopener" data-dash="signin" data-dash-pos="footer"><span>' ) );
$sc( 'dashboard: notes name both cases', false !== strpos( fenix_dashboard_notes(), 'ข้อมูลตัวอย่าง' ) && false !== strpos( fenix_dashboard_notes(), 'รหัสเชิญ' ) && false !== mb_strpos( $d['dashboard_sample_note'], 'ไม่ใช่ผลการเทรด' ) );
ob_start();
$dash_printed = fenix_dashboard_block( 'pricing' );
$dash_block   = (string) ob_get_clean();
$sc( 'dashboard: promo block prints', true === $dash_printed && false !== strpos( $dash_block, 'dash-promo--pricing' ) && false !== strpos( $dash_block, 'data-dash="visit"' ) && false !== strpos( $dash_block, 'data-dash="signup"' ) && false !== strpos( $dash_block, 'data-dash-pos="pricing-dashboard"' ) && false !== strpos( $dash_block, 'dash-caution' ) && false !== strpos( $dash_block, 'dash-note--sample' ) && false !== strpos( $dash_block, 'dash-note--invite' ) && substr_count( $dash_block, '<section' ) === substr_count( $dash_block, '</section>' ) && substr_count( $dash_block, '<div' ) === substr_count( $dash_block, '</div>' ) );
$sc( 'dashboard: no result-like text in defaults', ! preg_match( '/[+-]?\d[\d.,]*\s*%/u', implode( ' ', array_intersect_key( $d, array_flip( preg_grep( '/^(dashboard_|dash_)/', array_keys( $d ) ) ) ) ) ) );
$sc( 'dashboard: host only in its URL setting', 1 === count( preg_grep( '/' . preg_quote( (string) parse_url( $d['dashboard_url'], PHP_URL_HOST ), '/' ) . '/i', array_map( 'strval', array_filter( $d, 'is_scalar' ) ) ) ) );

$GLOBALS['fx_mods'] = array( 'dashboard_url' => 'https://app.example.com#visit', 'dashboard_visit_label' => '' );
ob_start();
fenix_dashboard_block( 'home' );
$dash_block = (string) ob_get_clean();
$sc( 'dashboard: pasted #hash + no path', 'https://app.example.com/' === fenix_dashboard_url() && 'https://app.example.com/#signup' === fenix_dashboard_url( 'signup' ) );
$sc( 'dashboard: blank label hides button + note', '' === fenix_dashboard_button( 'visit' ) && '' === fenix_dashboard_button( 'visit', array( 'text' => 'X' ) ) && '' === do_shortcode( '[falcon_dashboard mode="visit"][/falcon_dashboard]' ) && '' === do_shortcode( '[falcon_dashboard mode="visit"]X[/falcon_dashboard]' ) && false === strpos( $dash_block, 'data-dash="visit"' ) && false === strpos( $dash_block, 'dash-note--sample' ) && false !== strpos( $dash_block, 'data-dash="signup"' ) );

foreach ( array( 'feature off' => array( 'show_dashboard' => false ), 'address empty' => array( 'dashboard_url' => '' ), 'address not http' => array( 'dashboard_url' => 'javascript:alert(1)' ) ) as $dash_case => $dash_mods ) {
	$GLOBALS['fx_mods'] = $dash_mods;
	ob_start();
	$dash_printed = fenix_dashboard_block( 'home' );
	$dash_block   = (string) ob_get_clean();
	$sc( 'dashboard: ' . $dash_case . ' = nothing', '' === fenix_dashboard_url() && '' === fenix_dashboard_url( 'visit' ) && '' === fenix_dashboard_button( 'visit' ) && '' === fenix_dashboard_notes() && '' === do_shortcode( '[falcon_dashboard mode="visit"]X[/falcon_dashboard]' ) && false === $dash_printed && '' === $dash_block );
}
unset( $GLOBALS['fx_mods'] );

foreach ( $sc_rows as $row ) {
	printf( "%-34s %s\n", 'sc: ' . $row[0], $row[1] ? '✓' : '✗ ' . $row[2] );
	$problems += $row[1] ? 0 : 1;
}

// Guide step pictures bundled with the theme (5th element of a step in falcon-pro/inc/modules/guides.php):
// the named file must be in falcon-pro/assets/img/guides/ (a missing file silently leaves the slot empty on the site)
// and a step that ships a picture needs its alt text.
// The step data of a retired page (the VPS guides, the install guide) may stay in the module for old pages that are still
// in WordPress, but it is not required to ship pictures any more: those sets are skipped.
$gd_rows    = array();
$gd_skipped = array();
if ( function_exists( 'fenix_guide_data' ) && function_exists( 'fenix_guide_image_url' ) ) {
	$gd_sets = (array) fenix_guide_data();
	if ( function_exists( 'fenix_guide_install_data' ) ) {
		$gd_sets['inst'] = fenix_guide_install_data();
	}
	// prefix of a data set => slug of its page
	$gd_slug         = function_exists( 'fenix_guide_map' ) ? array_flip( (array) fenix_guide_map() ) : array();
	$gd_slug['inst'] = 'how-to-install';
	foreach ( $gd_sets as $gd_p => $gd_data ) {
		if ( isset( $gd_slug[ $gd_p ] ) && isset( $retired[ $gd_slug[ $gd_p ] ] ) ) {
			$gd_skipped[] = $gd_p;
			continue;
		}
		foreach ( array_values( isset( $gd_data['steps'] ) ? (array) $gd_data['steps'] : array() ) as $gd_i => $gd_step ) {
			if ( empty( $gd_step[4] ) ) {
				continue;
			}
			if ( '' === fenix_guide_image_url( $gd_step[4] ) ) {
				$gd_rows[] = 'file not in assets/img/guides/ · ' . $gd_p . '_step' . ( $gd_i + 1 ) . ': ' . $gd_step[4];
			}
			if ( '' === trim( (string) $gd_step[2] ) ) {
				$gd_rows[] = 'picture without alt text · ' . $gd_p . '_step' . ( $gd_i + 1 );
			}
		}
	}
} else {
	$gd_rows[] = 'fenix_guide_data() / fenix_guide_image_url() not found (inc/modules/guides.php)';
}
printf( "%-34s %s%s\n", 'guide step pictures', $gd_rows ? '✗ ' . implode( '; ', $gd_rows ) : '✓', $gd_skipped ? ' · retired sets skipped: ' . implode( ', ', $gd_skipped ) : '' );
$problems += count( $gd_rows );

// Backtest figures live in one place: the result tables of pages/backtest.html (the section that starts at the heading with
// id="backtest-results"), next to their test conditions and limitations. Every figure printed in those tables must not appear
// anywhere else: not in the rest of that page (meta line, lead, FAQ, related links), not in another page seed and not in a
// Customizer default (home page, pricing cards, /go, meta descriptions and schema all read from those).
// Each table must also be followed by its list of limitations, and the section needs the dashboard button.
$bt_rows = array();
$bt_html = is_file( $root . 'pages/backtest.html' ) ? (string) file_get_contents( $root . 'pages/backtest.html' ) : '';
if ( preg_match( '/<h2\b[^>]*\sid="backtest-results"[^>]*>.*?(?=<h2\b)/s', $bt_html, $bt_m ) ) {
	$bt_section = $bt_m[0];
	$bt_figs    = array();
	preg_match_all( '#<table class="data-table bt-table">.*?</table>(.*?)(?=<table\b|\z)#s', $bt_section, $bt_tables, PREG_SET_ORDER );
	foreach ( $bt_tables as $bt_i => $bt_table ) {
		if ( false === strpos( $bt_table[1], '<ul class="crosslist">' ) ) {
			$bt_rows[] = 'result table ' . ( $bt_i + 1 ) . ' has no list of limitations after it';
		}
		preg_match_all( '#<td\b[^>]*>(.*?)</td>#s', $bt_table[0], $bt_cells );
		foreach ( $bt_cells[1] as $bt_cell ) {
			if ( preg_match_all( '/\d[\d,]*(?:\.\d+)?/', wp_strip_all_tags( $bt_cell ), $bt_n ) ) {
				foreach ( $bt_n[0] as $bt_fig ) {
					// a result figure has a thousands separator or decimals · round amounts (test deposits) are conditions, not results
					if ( ( false !== strpos( $bt_fig, ',' ) || false !== strpos( $bt_fig, '.' ) ) && ! preg_match( '/^\d{1,3}(?:,000)+$/', $bt_fig ) ) {
						$bt_figs[ $bt_fig ] = true;
					}
				}
			}
		}
	}
	if ( count( $bt_tables ) < 1 || count( $bt_figs ) < 10 ) {
		$bt_rows[] = 'no result tables (table.data-table.bt-table) found in the results section';
	}
	if ( false === strpos( $bt_section, '[falcon_dashboard ' ) ) {
		$bt_rows[] = 'results section has no [falcon_dashboard] button';
	}
	$bt_scan = array( 'pages/backtest.html outside the results section' => str_replace( $bt_section, ' ', $bt_html ) );
	foreach ( glob( $root . 'pages/*.html' ) as $bt_file ) {
		// seed files of retired pages are frozen and no longer imported: not scanned
		if ( 'backtest.html' !== basename( $bt_file ) && ! isset( $retired[ basename( $bt_file, '.html' ) ] ) ) {
			$bt_scan[ 'pages/' . basename( $bt_file ) ] = (string) file_get_contents( $bt_file );
		}
	}
	unset( $GLOBALS['fx_mods'] );
	foreach ( fenix_defaults() as $bt_key => $bt_value ) {
		if ( is_string( $bt_value ) && '' !== $bt_value ) {
			$bt_scan[ 'default ' . $bt_key ] = $bt_value;
		}
	}
	foreach ( $bt_scan as $bt_where => $bt_text ) {
		foreach ( array_keys( $bt_figs ) as $bt_fig ) {
			if ( preg_match( '/(?<![\d.,])' . preg_quote( (string) $bt_fig, '/' ) . '(?!\d|[.,]\d)/', $bt_text ) ) {
				$bt_rows[] = 'backtest figure ' . $bt_fig . ' in ' . $bt_where;
			}
		}
	}
} elseif ( '' !== $bt_html ) {
	echo "backtest figures                   (no results section in pages/backtest.html · nothing to check)\n";
}
printf( "%-34s %s\n", 'backtest figures stay in tables', $bt_rows ? '✗ ' . implode( '; ', $bt_rows ) : '✓' );
$problems += count( $bt_rows );

echo $problems ? "\n$problems issue(s)\n" : "\nall good\n";
// exit code for dev/deploy.sh (it runs this script with output hidden and stops the push on a non-zero code)
exit( $problems ? 1 : 0 );
