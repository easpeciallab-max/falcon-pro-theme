<?php
/**
 * Dev check: copied sentences between FALCON and a sister theme of the same owner.
 *
 * The owner runs several sites from one theme family. A sentence that reads the same on two of those domains is
 * duplicate content, so a port may reuse CODE from the sister theme but never its COPY. This script reads the public
 * copy of both themes and reports every Thai passage of 40 or more characters that appears verbatim in both.
 *
 * What is read (nothing in either folder is executed, the files are only parsed):
 *   - string literals of every PHP file: setting defaults, step data, template text between PHP tags. Literals joined
 *     with "." count as one text. Comments are skipped, and so are functions that only build wp-admin screens
 *     (Customizer labels and hints, the Setup page: see FX_COPY_ADMIN_FN), because visitors and Google never see them.
 *   - seed content, meta line values + body text: FALCON's page seeds (inc/content/pages/*.html) against ALL seeds of
 *     the sister theme, pages and articles. FALCON's own article seeds join with --articles.
 * Before comparing, HTML tags and theme shortcodes are removed, whitespace is collapsed and every brand name of the
 * theme family becomes the placeholder {B}, so "X คือ ..." copied with only the brand swapped is still caught.
 *
 * Each shared passage lands in one of three groups:
 *   NEW            not found in FALCON at the base ref: it came in with the uncommitted work. Rewrite it.
 *   ALREADY THERE  FALCON already had it at the base ref (mostly FALCON text the sister theme inherited at the fork).
 *                  Still duplicate content: rewrite it on one side when that text is next touched.
 *   allowed        made of the phrases in $allow below (legal boilerplate FALCON had before the fork). Counted,
 *                  not listed (list them with --allowed).
 *
 * Run from the repo root (mbstring needed):
 *   php -d extension=mbstring dev/check-copy.php <path to the sister theme folder> [options]
 *   e.g. php -d extension=mbstring dev/check-copy.php ../eawing-theme/eawing
 * Options:
 *   --base=<git ref>  what "already there" is measured against (default HEAD). Pass the commit before a port started
 *                     to keep seeing everything that port introduced, also after it was committed.
 *   --new-only        exit code looks at the NEW group only (use as a gate right before a commit)
 *   --articles        also read FALCON's article seeds (inc/content/articles/*.html)
 *   --min=<n>         shortest passage in characters (default 40, at least 30)
 *   --allowed         also list the passages skipped by the allow list
 * Exit code: 1 when a passage outside the allow list is found (NEW or ALREADY THERE; with --new-only: NEW only),
 *            2 on a usage error, 0 otherwise.
 *
 * Limits: a passage must be identical character for character (after the normalisation above). A sentence that was
 * reworded lightly is not found, so this check does not replace reading the two texts side by side.
 */

if ( ! function_exists( 'mb_strlen' ) ) {
	fwrite( STDERR, "mbstring is required: php -d extension=mbstring dev/check-copy.php <sister theme folder>\n" );
	exit( 2 );
}

/* Wording that may stay identical on both sites: legal boilerplate FALCON already had before the sister theme was
 * forked from it. Names of a law or an authority, and the standard risk warning (CLAUDE.md rule 4 forbids weakening
 * it, and "own words" for a warning only blur it).
 * One entry = one phrase. A shared passage is skipped only when it is made of these phrases: after cutting them out,
 * fewer than FX_COPY_ALLOW_REST Thai letters are left (a joining word such as "และ"). A sentence that merely contains
 * one of the phrases is still reported. Keep the list short: marketing copy, guide steps and FAQ answers never belong here. */
$allow = array(
	'พระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 (PDPA)',
	'สำนักงานคณะกรรมการคุ้มครองข้อมูลส่วนบุคคล (สคส.)',
	'คำเตือนความเสี่ยง',
	'การเทรด Forex ทองคำ และ CFD มีความเสี่ยงสูง',
	'การเทรด Forex ทองคำ และ CFD',
	'คุณอาจสูญเสียเงินทุนบางส่วนหรือทั้งหมด',
	'ผลในอดีตไม่ได้รับประกันผลลัพธ์ในอนาคต',
);
const FX_COPY_ALLOW_REST = 12;

/* Brand names of the theme family (longest first). Replaced by {B} on both sides before comparing. */
$brands = array( 'FALCON PRO EA', 'FENIX PRO EA', 'FALCON PRO', 'FENIX PRO', 'EA WING', 'EAWING', 'EA2000', 'FALCON', 'FENIX' );

/* Functions whose string literals are wp-admin only (matched on the function name, any theme prefix): Customizer
 * section / field / hint builders, the Customizer registration, validation messages of Customizer and REST saves,
 * everything of the Setup page. Also skipped: callbacks and closures hooked on a filter matching FX_COPY_ADMIN_HOOK. */
const FX_COPY_ADMIN_FN   = '/_customizer_sections$|_fields$|_rules$|_customize_register$|_setup_|_validate_|_mods_/';
const FX_COPY_ADMIN_HOOK = '/_customizer_sections$|^customize_register$/';

/* ---------- arguments ---------- */
$opt    = array(
	'base'     => 'HEAD',
	'min'      => 40,
	'new_only' => false,
	'allowed'  => false,
	'articles' => false,
);
$sister = '';
foreach ( array_slice( $argv, 1 ) as $arg ) {
	if ( 0 === strpos( $arg, '--base=' ) ) {
		$opt['base'] = substr( $arg, 7 );
	} elseif ( 0 === strpos( $arg, '--min=' ) ) {
		$opt['min'] = max( 30, (int) substr( $arg, 6 ) ); // shorter passages cannot be guaranteed by the window size below
	} elseif ( '--new-only' === $arg ) {
		$opt['new_only'] = true;
	} elseif ( '--allowed' === $arg ) {
		$opt['allowed'] = true;
	} elseif ( '--articles' === $arg ) {
		$opt['articles'] = true;
	} elseif ( '' !== $arg && '-' !== $arg[0] && '' === $sister ) {
		$sister = $arg;
	} else {
		fwrite( STDERR, "unknown argument: $arg\n" );
		exit( 2 );
	}
}
$repo   = realpath( __DIR__ . '/..' );
$falcon = realpath( __DIR__ . '/../falcon-pro' );
$sister = '' !== $sister ? realpath( $sister ) : false;
if ( ! $falcon || ! $sister || ! is_dir( $sister ) ) {
	fwrite( STDERR, "usage: php -d extension=mbstring dev/check-copy.php <path to the sister theme folder> [--base=<git ref>] [--new-only] [--articles] [--min=40] [--allowed]\n" );
	exit( 2 );
}
if ( $sister === $falcon ) {
	fwrite( STDERR, "the sister theme folder is the FALCON theme itself\n" );
	exit( 2 );
}

/* ---------- reading copy out of files ---------- */

/**
 * Plain comparable text: no tags, entities decoded, brands → {B}, whitespace collapsed.
 */
function fx_copy_norm( $text ) {
	static $brand_re = null;
	if ( null === $brand_re ) {
		$brand_re = '/' . implode( '|', array_map( 'preg_quote', $GLOBALS['brands'] ) ) . '/iu';
	}
	$text = html_entity_decode( strip_tags( (string) $text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$text = preg_replace( '/\[\/?[a-z0-9]+_(?:line|brand|broker|calc|email|if|figure)\b[^\]]*\]/u', ' ', $text ); // theme shortcodes are code, not copy
	$text = preg_replace( $brand_re, '{B}', (string) $text );
	$text = preg_replace( '/\s+/u', ' ', (string) $text );
	return trim( (string) $text );
}

/**
 * Split one source text into units (one per line / block) and add them to $units as array( where, text ).
 * Text too short to hold a passage, or without Thai letters, is dropped here.
 */
function fx_copy_add( &$units, $where, $raw, $min ) {
	foreach ( preg_split( '/\R/u', (string) $raw ) as $line ) {
		$line = fx_copy_norm( $line );
		if ( mb_strlen( $line, 'UTF-8' ) >= $min && preg_match( '/[\x{0E01}-\x{0E5B}]/u', $line ) ) {
			$units[] = array( $where, $line );
		}
	}
}

/**
 * String literals and template text of one PHP source.
 */
function fx_copy_php( &$units, $label, $code, $min, $skip_admin = true ) {
	$tokens = array();
	foreach ( @token_get_all( (string) $code ) as $tok ) { // phpcs:ignore -- a file that does not parse still gives its literals
		$type = is_array( $tok ) ? $tok[0] : $tok;
		if ( T_WHITESPACE !== $type && T_COMMENT !== $type && T_DOC_COMMENT !== $type ) {
			$tokens[] = $tok;
		}
	}

	/* named callbacks hooked on an admin-only filter: add_filter( '…_customizer_sections', 'callback' … ) */
	$hooked = array();
	foreach ( $tokens as $n => $tok ) {
		if ( is_array( $tok ) && T_CONSTANT_ENCAPSED_STRING === $tok[0] && preg_match( FX_COPY_ADMIN_HOOK, trim( $tok[1], '\'"' ) )
			&& isset( $tokens[ $n + 2 ] ) && ',' === $tokens[ $n + 1 ] && is_array( $tokens[ $n + 2 ] ) && T_CONSTANT_ENCAPSED_STRING === $tokens[ $n + 2 ][0] ) {
			$hooked[ trim( $tokens[ $n + 2 ][1], '\'"' ) ] = true;
		}
	}

	$prev  = '';     // the last token (type or character)
	$prev2 = '';     // the one before it
	$lit   = '';     // text of the last string literal (hook name in front of a closure)
	$key   = '';     // last 'key' => seen in the current statement (label only)
	$buf   = null;   // array( line, text ) of the literal being collected (joined across ".")
	$depth = 0;      // { } nesting
	$open  = false;  // an admin-only function was declared, its body starts at the next {
	$until = null;   // depth of the body of the admin-only function we are in (null = public code)
	$flush = function () use ( &$units, &$buf, &$key, &$until, $label, $min ) {
		if ( null !== $buf && null === $until ) {
			fx_copy_add( $units, $label . ':' . $buf[0] . ( '' !== $key ? ' [' . $key . ']' : '' ), $buf[1], $min );
		}
		$buf = null;
	};
	foreach ( $tokens as $tok ) {
		$type  = is_array( $tok ) ? $tok[0] : $tok;
		$admin = null !== $until;
		if ( T_FUNCTION === $prev && $skip_admin && ! $admin ) {
			/* function name( … ) or a closure: function ( … ) passed to add_filter( 'hook', function … */
			$open = T_STRING === $type
				? ( preg_match( FX_COPY_ADMIN_FN, $tok[1] ) || isset( $hooked[ $tok[1] ] ) )
				: ( ',' === $prev2 && preg_match( FX_COPY_ADMIN_HOOK, $lit ) );
		}
		if ( '{' === $type || T_CURLY_OPEN === $type || T_DOLLAR_OPEN_CURLY_BRACES === $type ) {
			$flush();
			$depth++;
			if ( $open ) {
				$until = $depth;
				$open  = false;
			}
		} elseif ( '}' === $type ) {
			$flush();
			if ( $depth === $until ) {
				$until = null;
			}
			$depth--;
		} elseif ( T_STRING === $type && T_FUNCTION === $prev ) {
			$flush();
			$key = '';
		} elseif ( ';' === $type ) {
			$flush();
			$key = '';
		} elseif ( T_INLINE_HTML === $type ) {
			$flush();
			if ( ! $admin ) {
				/* block ends become line ends so one paragraph / list item = one unit */
				fx_copy_add( $units, $label . ':' . $tok[2], preg_replace( '#</(?:p|li|h[1-6]|td|th|div|summary|button|label)>|<br\s*/?>#i', "\n", $tok[1] ), $min );
			}
		} elseif ( T_CONSTANT_ENCAPSED_STRING === $type || T_ENCAPSED_AND_WHITESPACE === $type ) {
			$raw = $tok[1];
			if ( T_CONSTANT_ENCAPSED_STRING === $type ) {
				$text = "'" === $raw[0] ? str_replace( array( "\\'", '\\\\' ), array( "'", '\\' ), substr( $raw, 1, -1 ) ) : stripcslashes( substr( $raw, 1, -1 ) );
			} else {
				$text = stripcslashes( $raw );
			}
			if ( null !== $buf && '.' === $prev ) {
				$buf[1] .= $text; // 'a' . 'b' = one text
			} else {
				$flush();
				$buf = array( $tok[2], $text );
			}
			$lit = $text;
		} elseif ( T_DOUBLE_ARROW === $type ) {
			/* 'key' => … : remember the key as a label for the values that follow */
			if ( null !== $buf && preg_match( '/^[A-Za-z0-9_\-]{1,60}$/', $buf[1] ) ) {
				$key = $buf[1];
			}
			$buf = null;
		} elseif ( '.' !== $type ) {
			$flush();
		}
		$prev2 = $prev;
		$prev  = $type;
	}
	$flush();
}

/**
 * Seed file: values of the meta line + body text (one unit per block).
 */
function fx_copy_seed( &$units, $label, $html, $min ) {
	if ( preg_match( '/^\s*<!--meta\s+(\{.*?\})\s*-->/s', (string) $html, $m ) ) {
		$meta = json_decode( $m[1], true );
		foreach ( is_array( $meta ) ? $meta : array() as $k => $v ) {
			if ( is_string( $v ) ) {
				fx_copy_add( $units, $label . ' [meta.' . $k . ']', $v, $min );
			}
		}
		$html = substr( $html, strlen( $m[0] ) );
	}
	$html = preg_replace( '/<!--.*?-->/s', '', (string) $html );
	$html = preg_replace( '#</(?:p|li|h[1-6]|td|th|div|summary|dt|dd|blockquote|figcaption)>|<br\s*/?>#i', "\n", (string) $html );
	fx_copy_add( $units, $label, $html, $min );
}

/**
 * All units of a theme folder on disk ($articles = false leaves the article seeds out).
 */
function fx_copy_read_dir( $root, $name, $min, $articles = true ) {
	$units = array();
	$it    = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
	$files = array();
	foreach ( $it as $file ) {
		$files[] = str_replace( '\\', '/', substr( $file->getPathname(), strlen( $root ) + 1 ) );
	}
	sort( $files );
	foreach ( $files as $rel ) {
		$ext = strtolower( pathinfo( $rel, PATHINFO_EXTENSION ) );
		if ( 'php' === $ext ) {
			fx_copy_php( $units, $name . '/' . $rel, file_get_contents( $root . '/' . $rel ), $min );
		} elseif ( 'html' === $ext && 0 === strpos( $rel, 'inc/content/' ) && ( $articles || 0 !== strpos( $rel, 'inc/content/articles/' ) ) ) {
			fx_copy_seed( $units, $name . '/' . $rel, file_get_contents( $root . '/' . $rel ), $min );
		}
	}
	return $units;
}

/**
 * FALCON's units at a git ref, joined into one searchable text (false when git cannot give it).
 * Read as widely as possible (admin strings and article seeds included): the only question is "did FALCON have this text".
 */
function fx_copy_read_ref( $repo, $ref, $min ) {
	$git  = 'git -C ' . escapeshellarg( $repo );
	$list = array();
	$code = 1;
	exec( $git . ' ls-tree -r --name-only ' . escapeshellarg( $ref ) . ' -- falcon-pro 2>&1', $list, $code );
	if ( 0 !== $code ) {
		return false;
	}
	$want = array();
	foreach ( $list as $path ) {
		$path = trim( $path, "\" \t\r\n" );
		$ext  = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
		if ( 'php' === $ext || ( 'html' === $ext && false !== strpos( $path, '/inc/content/' ) ) ) {
			$want[] = $path;
		}
	}
	/* one git process for every file: "<ref>:<path>" lines in, "<sha> blob <size>\n<bytes>\n" out */
	$proc = proc_open( $git . ' cat-file --batch', array( array( 'pipe', 'r' ), array( 'pipe', 'w' ), array( 'pipe', 'w' ) ), $pipes );
	if ( ! is_resource( $proc ) ) {
		return false;
	}
	$units = array();
	foreach ( $want as $path ) {
		fwrite( $pipes[0], $ref . ':' . $path . "\n" );
		fflush( $pipes[0] );
		$head = fgets( $pipes[1] );
		if ( false === $head || ! preg_match( '/^[0-9a-f]+ blob (\d+)$/', trim( $head ), $m ) ) {
			continue;
		}
		$size = (int) $m[1];
		$data = '';
		while ( strlen( $data ) < $size && ! feof( $pipes[1] ) ) {
			$data .= fread( $pipes[1], $size - strlen( $data ) );
		}
		fgets( $pipes[1] ); // the newline after the blob
		if ( 'php' === strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) ) {
			fx_copy_php( $units, $path, $data, $min, false );
		} else {
			fx_copy_seed( $units, $path, $data, $min );
		}
	}
	fclose( $pipes[0] );
	fclose( $pipes[1] );
	fclose( $pipes[2] );
	proc_close( $proc );
	$text = '';
	foreach ( $units as $u ) {
		$text .= $u[1] . "\n";
	}
	return $text;
}

/* ---------- matching ---------- */

/* Windows of WIN bytes taken from the sister text every STEP bytes: any shared passage of WIN + STEP - 1 bytes or more
 * contains one of them. 40 characters with Thai letters are at least 80 bytes, so nothing reportable can slip through. */
const FX_COPY_WIN  = 48;
const FX_COPY_STEP = 12;

/**
 * Trim a byte range of $text to whole UTF-8 characters.
 */
function fx_copy_whole_chars( $text, $from, $to ) {
	while ( $from < $to && 0x80 === ( ord( $text[ $from ] ) & 0xC0 ) ) {
		$from++;
	}
	if ( $to > $from ) {
		/* the last character: complete = keep · cut in the middle = drop its bytes */
		$k = $to - 1;
		while ( $k > $from && 0x80 === ( ord( $text[ $k ] ) & 0xC0 ) ) {
			$k--;
		}
		$lead = ord( $text[ $k ] );
		$need = $lead >= 0xF0 ? 4 : ( $lead >= 0xE0 ? 3 : ( $lead >= 0xC0 ? 2 : 1 ) );
		if ( $k + $need > $to ) {
			$to = $k;
		}
	}
	return array( $from, $to );
}

$f_units = fx_copy_read_dir( $falcon, 'falcon-pro', $opt['min'], $opt['articles'] );
$s_name  = basename( $sister );
$s_units = fx_copy_read_dir( $sister, $s_name, $opt['min'] );

$index = array(); // crc32 of a window => unit number * 2^24 + byte offset (first place seen)
foreach ( $s_units as $ui => $unit ) {
	$len = strlen( $unit[1] );
	for ( $p = 0; $p + FX_COPY_WIN <= $len; $p += FX_COPY_STEP ) {
		$h = crc32( substr( $unit[1], $p, FX_COPY_WIN ) );
		if ( ! isset( $index[ $h ] ) ) {
			$index[ $h ] = ( $ui << 24 ) | $p;
		}
	}
}

$found = array(); // passage text => array( 'f' => where[], 's' => where )
foreach ( $f_units as $unit ) {
	$text = $unit[1];
	$len  = strlen( $text );
	for ( $i = 0; $i + FX_COPY_WIN <= $len; $i++ ) {
		$h = crc32( substr( $text, $i, FX_COPY_WIN ) );
		if ( ! isset( $index[ $h ] ) ) {
			continue;
		}
		$ui    = $index[ $h ] >> 24;
		$p     = $index[ $h ] & 0xFFFFFF;
		$other = $s_units[ $ui ][1];
		if ( substr( $other, $p, FX_COPY_WIN ) !== substr( $text, $i, FX_COPY_WIN ) ) {
			continue; // hash collision
		}
		/* grow the match to both sides */
		$a  = $i;
		$pa = $p;
		while ( $a > 0 && $pa > 0 && $text[ $a - 1 ] === $other[ $pa - 1 ] ) {
			$a--;
			$pa--;
		}
		$b    = $i + FX_COPY_WIN;
		$pb   = $p + FX_COPY_WIN;
		$olen = strlen( $other );
		while ( $b < $len && $pb < $olen && $text[ $b ] === $other[ $pb ] ) {
			$b++;
			$pb++;
		}
		list( $a, $b ) = fx_copy_whole_chars( $text, $a, $b );
		$run           = trim( substr( $text, $a, $b - $a ) );
		if ( mb_strlen( $run, 'UTF-8' ) >= $opt['min'] && preg_match_all( '/[\x{0E01}-\x{0E5B}]/u', $run ) >= 20 ) {
			if ( ! isset( $found[ $run ] ) ) {
				$found[ $run ] = array(
					'f' => array(),
					's' => $s_units[ $ui ][0],
				);
			}
			if ( ! in_array( $unit[0], $found[ $run ]['f'], true ) ) {
				$found[ $run ]['f'][] = $unit[0];
			}
			$i = max( $i, $b - 1 ); // continue after this passage
		}
	}
}

/* ---------- grouping ---------- */
$allow_norm = array_map( 'fx_copy_norm', $allow );
$base_text  = $found ? fx_copy_read_ref( $repo, $opt['base'], $opt['min'] ) : '';
$groups     = array(
	'new'     => array(),
	'old'     => array(),
	'allowed' => array(),
);
foreach ( $found as $run => $info ) {
	$group = false === $base_text || false === strpos( $base_text, $run ) ? 'new' : 'old';
	$rest  = str_replace( $allow_norm, ' ', $run );
	if ( $rest !== $run && preg_match_all( '/[\x{0E01}-\x{0E5B}]/u', $rest ) < FX_COPY_ALLOW_REST ) {
		$group = 'allowed';
	}
	$groups[ $group ][ $run ] = $info;
}

/* ---------- report ---------- */
$print = function ( $title, $items ) {
	echo "\n$title: " . count( $items ) . "\n";
	foreach ( $items as $run => $info ) {
		$chars = mb_strlen( $run, 'UTF-8' );
		$more  = count( $info['f'] ) > 2 ? ' (+' . ( count( $info['f'] ) - 2 ) . ' more)' : '';
		echo '  ' . implode( ' · ', array_slice( $info['f'], 0, 2 ) ) . $more . "\n";
		echo '    = ' . $info['s'] . "\n";
		echo '    "' . mb_substr( $run, 0, 80, 'UTF-8' ) . ( $chars > 80 ? '…' : '' ) . '" (' . $chars . " chars)\n";
	}
};

printf( "copy check · falcon-pro: %d texts · %s: %d texts · passages of %d+ characters · base %s\n", count( $f_units ), $s_name, count( $s_units ), $opt['min'], $opt['base'] );
if ( false === $base_text ) {
	echo "note: git could not read falcon-pro at \"{$opt['base']}\" · every passage is listed as NEW\n";
}
if ( $groups['new'] ) {
	$print( 'NEW · not in FALCON at ' . $opt['base'] . ' (rewrite before committing)', $groups['new'] );
}
if ( $groups['old'] ) {
	$print( 'ALREADY THERE · FALCON had it at ' . $opt['base'] . ' (shared with the sister theme since before this work)', $groups['old'] );
}
if ( $opt['allowed'] && $groups['allowed'] ) {
	$print( 'allowed · legal boilerplate on the allow list', $groups['allowed'] );
}

$fail = count( $groups['new'] ) + ( $opt['new_only'] ? 0 : count( $groups['old'] ) );
printf(
	"\n%d new · %d already there · %d on the allow list%s\n",
	count( $groups['new'] ),
	count( $groups['old'] ),
	count( $groups['allowed'] ),
	$fail ? '' : ( $groups['old'] ? ' · nothing new (--new-only)' : ' · no copied passages' )
);
exit( $fail ? 1 : 0 );
