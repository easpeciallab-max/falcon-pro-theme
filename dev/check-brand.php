<?php
/**
 * Dev check: brand leaks from sister/source brands + files that must never ship in the public theme.
 *
 * Scans every file under falcon-pro/ (text files line by line, binary files for readable metadata, and every
 * file/folder NAME) for: FENIX · fenixpro · @fenixpro · EA2000 · ea2000 · Zaurix (matched case-insensitively,
 * so "Fenix" / "Ea2000" are caught too).
 *
 * Sister theme EA WING (code is ported from it, so its names can leak through comments, identifiers and asset names):
 *   eawing · "EA WING" / ea-wing / ea_wing · its design-system name "Glass Sky" (all case-insensitive)
 *   eaw_… / eaw-…   leftover identifiers, CSS classes and handles (no exemption: FALCON's internal prefix is fenix)
 *   eawX…           camelCase JS globals such as eawConsent (case-sensitive: "eaw" followed by a capital letter)
 * The three-letter prefix forms are only searched in text files and names (in image bytes they would match by chance).
 *
 * Allowed on purpose (CLAUDE.md "เรื่อง prefix ภายใน"): lowercase internal identifiers that are kept as fenix* to avoid
 * breaking PHP <-> CSS <-> JS links. They are never shown to visitors:
 *   fenix_…   PHP functions, settings, the fenix_consent cookie, AJAX action fenix_load_more
 *   fenix-…   enqueue handles, CSS classes (fenix-has, fenix-el, fenix-elementor …)
 *   fenixX…   camelCase JS globals/properties (fenixLoadMore, fenixTracking, fenixConsent, fenixChrome …)
 *   fenix:…   DOM event names (fenix:consent)
 *   window.fenix   the shared JS namespace object
 * Still flagged even with those prefixes: brand-like forms "fenix-pro", "fenix-ea", "fenixpro".
 * File and folder NAMES get no exemption (they are visible in URLs).
 * Mentions inside code comments are reported too (the repo is public) and tagged [comment].
 * Also flags archives and MetaTrader binaries/sources: .zip .rar .7z .ex4 .ex5 .mq4 .mq5
 *
 * Run from the repo root:  php dev/check-brand.php        (exit code 1 when anything is found)
 * Optional first argument: another folder to scan (used to test this script against fixtures).
 * Every run starts with a self test of the matcher (the $fixtures list below): a line that must be flagged and is
 * not, or an allowed identifier that is flagged, also ends the run with exit code 1.
 */

$root = realpath( isset( $argv[1] ) && '' !== $argv[1] ? $argv[1] : __DIR__ . '/../falcon-pro' );
if ( ! $root || ! is_dir( $root ) ) {
	fwrite( STDERR, "theme folder not found\n" );
	exit( 2 );
}
$label = basename( $root );

/* longest first so "@fenixpro" / "fenixpro" are reported as themselves, not as "fenix" */
$brand_long    = '@fenixpro|fenixpro|fenix|ea2000|zaurix|eawing|(?<![a-z])ea[ _-]wing|glass[ _-]?sky';
$brand_bin_re  = '/' . $brand_long . '/i';                           // binary files: long forms only
$brand_re      = '/' . $brand_long . '|(?<![a-z0-9])eaw(?=[_-])/i';  // text + names: also the eaw_ / eaw- prefix
$camel_re      = '/(?<![A-Za-z0-9+\/=])eaw(?=[A-Z])/';               // case-sensitive: eawConsent, eawTracking …
$allowed_re    = '/^fenix(?:[_:\-]|[A-Z])/';           // case-sensitive: only the lowercase internal prefix
$brandlike_re  = '/^fenix[\- ]?(?:pro|ea)(?![a-z0-9])/i'; // fenix-pro / fenix ea … are brand text, not identifiers
$forbidden_ext = array( 'zip', 'rar', '7z', 'ex4', 'ex5', 'mq4', 'mq5' );
$text_ext      = array( 'php', 'css', 'js', 'html', 'htm', 'txt', 'json', 'md', 'svg', 'xml', 'po', 'pot', 'csv', 'yml', 'yaml', 'map', 'webmanifest' );

/**
 * Is this hit an allowed internal identifier?
 */
function fx_brand_is_identifier( $line, $word, $offset, $allowed_re, $brandlike_re ) {
	if ( 'fenix' !== $word ) {                    // exact lowercase only; FENIX / Fenix are always visible brand text
		return false;
	}
	$rest = substr( $line, $offset );
	if ( preg_match( $brandlike_re, $rest ) ) {
		return false;
	}
	if ( preg_match( $allowed_re, $rest ) ) {
		return true;
	}
	/* window.fenix (namespace object) followed by a non-word character */
	$before = substr( $line, max( 0, $offset - 7 ), min( 7, $offset ) );
	$next   = substr( $line, $offset + 5, 1 );
	return 'window.' === $before && ! preg_match( '/[A-Za-z0-9]/', $next );
}

/**
 * Rough "is this inside a comment" label (for the report only; comment hits still count).
 */
function fx_brand_in_comment( $line, $offset, $ext, $in_block ) {
	if ( $in_block ) {
		return true;
	}
	$head    = substr( $line, 0, $offset );
	$trimmed = ltrim( $line );
	if ( '' !== $trimmed && ( '*' === $trimmed[0] || 0 === strpos( $trimmed, '/*' ) || 0 === strpos( $trimmed, '<!--' ) ) ) {
		return true;
	}
	if ( in_array( $ext, array( 'php', 'js', 'css' ), true ) && preg_match( '#(?<![:"\'])//|/\*#', $head ) ) {
		return true;
	}
	if ( 'php' === $ext && preg_match( '/^\s*#/', $line ) ) {
		return true;
	}
	return false !== strpos( $head, '<!--' );
}

/**
 * Every brand hit in one line of text (or one file name): array of array( word, offset ), in reading order.
 */
function fx_brand_hits( $line, $brand_re, $camel_re ) {
	$hits = array();
	foreach ( array( $brand_re, $camel_re ) as $re ) {
		if ( preg_match_all( $re, $line, $mm, PREG_OFFSET_CAPTURE ) ) {
			foreach ( $mm[0] as $hit ) {
				$hits[] = $hit;
			}
		}
	}
	usort(
		$hits,
		function ( $a, $b ) {
			return $a[1] - $b[1];
		}
	);
	return $hits;
}

/**
 * The hits of a line that count as findings (all hits minus the allowed internal identifiers).
 */
function fx_brand_line_findings( $line, $brand_re, $camel_re, $allowed_re, $brandlike_re ) {
	$out = array();
	foreach ( fx_brand_hits( $line, $brand_re, $camel_re ) as $hit ) {
		if ( ! fx_brand_is_identifier( $line, $hit[0], $hit[1], $allowed_re, $brandlike_re ) ) {
			$out[] = $hit;
		}
	}
	return $out;
}

/* self test of the matcher · line => number of findings it must give (0 = the line must pass) */
$fixtures = array(
	'ธีม EA WING ผู้ช่วยเทรด'                        => 1,
	'/* ported from eawing/inc/setup.php */'        => 1,
	'Ea-Wing and ea_wing'                           => 2,
	'function eaw_mod( $key ) {'                    => 1,
	'<div class="eaw-card eaw-card--on">'           => 2,
	'window.eawConsent = eawTracking || {};'        => 2,
	'assets/img/illus/eawing-vps-flow-v2.webp'      => 1,
	'การ์ดกระจกแบบ Glass Sky'                          => 1,
	'FENIX PRO EA'                                  => 1,
	'body class fenix-pro'                          => 1,
	'ติดต่อ @fenixpro'                               => 1,
	'server Zaurix-Server on ea2000.co'             => 2,
	'fenix_mod( "line_url" ); fenix_defaults();'    => 0,
	'body.fenix-has .fenix-el { }'                  => 0,
	'window.fenixLoadMore; window.fenix.consent;'   => 0,
	'document.addEventListener( "fenix:consent" )'  => 0,
	'seaweed, leeway, reawaken, the idea wing'      => 0,
	'following showing drawing seaw_x'              => 0,
	'FALCON PRO EA · wing of the falcon'            => 0,
);
$self_fail = array();
foreach ( $fixtures as $fx_line => $fx_want ) {
	$fx_got = count( fx_brand_line_findings( $fx_line, $brand_re, $camel_re, $allowed_re, $brandlike_re ) );
	if ( $fx_got !== $fx_want ) {
		$self_fail[] = sprintf( '"%s" gave %d finding(s), expected %d', $fx_line, $fx_got, $fx_want );
	}
}
/* image bytes: the three-letter prefix must not count, the full name still must */
if ( 0 !== preg_match( $brand_bin_re, "\x00eaw_\x00eawQ\x00" ) || 1 !== preg_match( $brand_bin_re, "\x00EA WING\x00" ) ) {
	$self_fail[] = 'binary pattern: must ignore the bare prefix and still catch the full name';
}
if ( $self_fail ) {
	echo "brand check · self test failed (the matcher is broken, nothing was scanned):\n  " . implode( "\n  ", $self_fail ) . "\n";
	exit( 1 );
}
echo 'brand check · self test: ' . ( count( $fixtures ) + 1 ) . " fixtures ok\n";

$findings = array();
$scanned  = 0;

$it = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ),
	RecursiveIteratorIterator::SELF_FIRST
);

foreach ( $it as $file ) {
	$path = $file->getPathname();
	$rel  = $label . '/' . str_replace( '\\', '/', substr( $path, strlen( $root ) + 1 ) );
	$name = $file->getFilename();

	$name_hits = fx_brand_hits( $name, $brand_re, $camel_re );
	if ( $name_hits ) {
		$findings[] = sprintf( '%s  [name] "%s" in a file/folder name', $rel, $name_hits[0][0] );
	}
	if ( $file->isDir() ) {
		continue;
	}

	$ext = strtolower( $file->getExtension() );
	if ( in_array( $ext, $forbidden_ext, true ) ) {
		$findings[] = sprintf( '%s  [file] .%s must not be in the theme (EA files are sent privately, never from the public repo)', $rel, $ext );
		continue;
	}

	$data = file_get_contents( $path );
	if ( false === $data ) {
		$findings[] = sprintf( '%s  [read] could not read file', $rel );
		continue;
	}
	$scanned++;

	if ( ! in_array( $ext, $text_ext, true ) ) {
		/* images / fonts: look for readable brand text in metadata (XMP, PNG tEXt, EXIF) */
		if ( preg_match_all( $brand_bin_re, $data, $bm ) ) {
			$findings[] = sprintf( '%s  [binary] contains "%s" (image/font metadata?)', $rel, implode( '", "', array_unique( $bm[0] ) ) );
		}
		continue;
	}

	$in_block = false; // inside a /* … */ or <!-- … --> block that started on an earlier line
	foreach ( preg_split( '/\r\n|\r|\n/', $data ) as $i => $line ) {
		foreach ( fx_brand_line_findings( $line, $brand_re, $camel_re, $allowed_re, $brandlike_re ) as $hit ) {
			list( $word, $offset ) = $hit;
			$tag        = fx_brand_in_comment( $line, $offset, $ext, $in_block ) ? ' [comment]' : '';
			$start      = max( 0, $offset - 40 );
			$snippet    = trim( function_exists( 'mb_strcut' ) ? mb_strcut( $line, $start, strlen( $word ) + 80, 'UTF-8' ) : substr( $line, $start, strlen( $word ) + 80 ) );
			$findings[] = sprintf( '%s:%d%s  "%s"  … %s', $rel, $i + 1, $tag, $word, $snippet );
		}
		/* track multi-line comment blocks for the [comment] label */
		$opens  = max( substr_count( $line, '/*' ), substr_count( $line, '<!--' ) );
		$closes = max( substr_count( $line, '*/' ), substr_count( $line, '-->' ) );
		if ( $opens > $closes ) {
			$in_block = true;
		} elseif ( $closes > $opens ) {
			$in_block = false;
		}
	}
}

echo "brand check · scanned $scanned files under $label/\n";
if ( $findings ) {
	echo count( $findings ) . " finding(s):\n  " . implode( "\n  ", $findings ) . "\n";
	exit( 1 );
}
echo "no brand leaks, no forbidden files\n";
exit( 0 );
