<?php
/**
 * Dev check for seed content in falcon-pro/inc/content/ (see dev/content-spec.md)
 * Run: php -d extension=mbstring dev/check-content.php
 */
require __DIR__ . '/preview/wp-stubs.php';
require __DIR__ . '/../falcon-pro/functions.php';

$root     = __DIR__ . '/../falcon-pro/inc/content/';
$pages    = array_keys( fenix_site_pages() );
$articles = array_map(
	function ( $f ) {
		return basename( $f, '.html' );
	},
	glob( $root . 'articles/*.html' )
);
$valid    = array_merge( $pages, $articles );
$problems = 0;

foreach ( array_merge( glob( $root . 'pages/*.html' ), glob( $root . 'articles/*.html' ) ) as $file ) {
	$rel   = basename( dirname( $file ) ) . '/' . basename( $file, '.html' );
	$raw   = file_get_contents( $file );
	$issue = array();

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
	if ( preg_match_all( '#\{\{home\}\}/([a-z0-9\-]+)/#', $raw, $links ) ) {
		foreach ( array_unique( $links[1] ) as $slug ) {
			if ( ! in_array( $slug, $valid, true ) ) {
				$issue[] = "link to unknown slug /$slug/";
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
	$all_drafts = array_merge( $articles, array( 'privacy-policy', 'terms-of-use', 'data-deletion' ) );
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
	printf( "%-34s %6d chars  %s\n", $rel, $words, $issue ? '✗ ' . implode( '; ', $issue ) : '✓' );
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

echo $problems ? "\n$problems issue(s)\n" : "\nall good\n";
