/**
 * Spacing check: finds text and boxes that sit flush against the edge of the surface they are on.
 *
 * What it flags, per page and per screen width:
 *   surface  text or a box ends closer than the limit to the place where the background changes
 *            (the end of a band, the edge of a card, the next band starting right under a row of buttons)
 *   border   text or a box sits closer than the limit to a visible border line of a frame around it
 *   stack    a box and the text or box right above or below it on the same surface are closer than 6px
 *   viewport text or a box touches the side of the screen
 *   overflow the page scrolls sideways
 * Limits (px): a band across the page 20 above/below and 10 at the sides · a box 8 (a box inside a box 5) · a control (button, chip, field) 1.
 * A band is a surface across the whole screen and at least 160px tall (a slim bar counts as a box).
 * Boxes are measured above and below only; text on all four sides. Blocks cut with clip-path are decoration and are ignored.
 * A measured gap is from the edge of the text line box, which is about 3px larger than the glyphs on each side.
 *
 * Needs Node 22+ and Google Chrome. No packages to install.
 *   node dev/check-spacing.mjs                              every page of the preview server (localhost:8765)
 *   node dev/check-spacing.mjs /articles/ /pricing/          only these paths
 *   node dev/check-spacing.mjs --base=https://example.com /  another site
 *   --widths=1280,768,375   screen widths            --shots=<dir>   save a marked crop of every finding
 *   --json=<file>           full result as JSON      --query=a=b     query string added to every path
 *   --all                   also list the gaps between the limit and twice the limit ("narrow")
 *   --pages=<dir>           save every page as full-width slices (1400px tall) for a look by eye
 * Exit code 1 when anything is flagged, so it can run next to the other dev checks.
 * Known and accepted spots go in the ALLOW list below with the reason.
 */

import { spawn } from 'node:child_process';
import { existsSync, mkdirSync, mkdtempSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';

const args = process.argv.slice( 2 );
const opt = ( name, fallback ) => {
	const hit = args.find( ( a ) => a.startsWith( '--' + name + '=' ) );
	return hit ? hit.slice( name.length + 3 ) : fallback;
};
const base = opt( 'base', process.env.FALCON_PREVIEW || 'http://localhost:8765' ).replace( /\/$/, '' );
const widths = opt( 'widths', '1280,768,375' ).split( ',' ).map( Number ).filter( Boolean );
const shots = opt( 'shots', '' );
const jsonOut = opt( 'json', '' );
const pagesDir = opt( 'pages', '' );
const query = opt( 'query', '' );
const showAll = args.includes( '--all' );
let paths = args.filter( ( a ) => ! a.startsWith( '--' ) );

// Accepted spots: { path: regex or null, item: regex, edge: regex, why }
const ALLOW = [];

const DEFAULT_PATHS = [
	'/', '/backtest/', '/forward-test/', '/how-to-install/', '/open-mt5-account/', '/deposit/', '/mt5-login/',
	'/vps-windows/', '/vps-android/', '/vps-ios/', '/tools/', '/dashboard/', '/pricing/', '/risk-disclosure/',
	'/about/', '/privacy-policy/', '/terms-of-use/', '/data-deletion/', '/go/', '/articles/', '/article/cent-account/', '/404/', '/?s=falcon',
];
if ( ! paths.length ) {
	paths = DEFAULT_PATHS;
}

let chrome = process.env.CHROME_BIN;
if ( ! chrome ) {
	chrome = [
		'C:/Program Files/Google/Chrome/Application/chrome.exe',
		'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe',
		'/usr/bin/google-chrome',
		'/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
	].find( ( c ) => existsSync( c ) );
}
if ( ! chrome ) {
	console.error( 'Chrome not found. Set CHROME_BIN.' );
	process.exit( 2 );
}

/* ---------- the measuring code that runs inside the page ---------- */
async function audit( cfg ) {
	const sleep = ( ms ) => new Promise( ( r ) => setTimeout( r, ms ) );
	if ( ! window.__fxSpacingReady ) {
		window.__fxSpacingReady = true;
		const st = document.createElement( 'style' );
		st.textContent = '*,*::before,*::after{animation:none!important;transition:none!important;scroll-behavior:auto!important}.reveal{opacity:1!important;transform:none!important}';
		document.head.appendChild( st );
		const imgs = Array.from( document.images );
		imgs.forEach( ( i ) => {
			if ( 'lazy' === i.loading ) {
				i.loading = 'eager';
			}
		} );
		await Promise.race( [
			Promise.all( imgs.map( ( i ) => ( i.complete ? 0 : new Promise( ( r ) => {
				i.addEventListener( 'load', r, { once: true } );
				i.addEventListener( 'error', r, { once: true } );
			} ) ) ) ),
			sleep( 6000 ),
		] );
		if ( document.fonts && document.fonts.ready ) {
			await Promise.race( [ document.fonts.ready, sleep( 4000 ) ] );
		}
		await new Promise( ( r ) => requestAnimationFrame( () => requestAnimationFrame( r ) ) );
	}
	if ( cfg.openDetails ) {
		document.querySelectorAll( 'details' ).forEach( ( d ) => {
			d.open = true;
		} );
		await new Promise( ( r ) => requestAnimationFrame( () => requestAnimationFrame( r ) ) );
	}

	const vw = document.documentElement.clientWidth;
	const vh = window.innerHeight;
	const root = document.documentElement;
	const styles = new WeakMap();
	const cs = ( el ) => {
		let s = styles.get( el );
		if ( ! s ) {
			s = getComputedStyle( el );
			styles.set( el, s );
		}
		return s;
	};
	const parse = ( str ) => {
		const m = String( str ).match( /-?[\d.]+(e-?\d+)?/g );
		if ( ! m ) {
			return [ 0, 0, 0, 0 ];
		}
		let [ r, g, b, a ] = m.map( Number );
		if ( 0 === String( str ).indexOf( 'color(' ) ) {
			r *= 255;
			g *= 255;
			b *= 255;
		}
		return [ r, g, b, undefined === a ? 1 : a ];
	};

	// position: fixed / sticky subtrees float over the page; they are neither measured nor used as a surface
	const floatCache = new WeakMap();
	const floats = ( el ) => {
		if ( ! el || el === root || el === document.body ) {
			return false;
		}
		if ( floatCache.has( el ) ) {
			return floatCache.get( el );
		}
		const p = cs( el ).position;
		const own = 'fixed' === p || 'sticky' === p;
		if ( own ) {
			el.setAttribute( 'data-fx-float', '' );
		}
		const v = own || floats( el.parentElement );
		floatCache.set( el, v );
		return v;
	};

	const bodyBg = parse( cs( document.body ).backgroundColor );
	const htmlBg = parse( cs( root ).backgroundColor );
	const pageBg = bodyBg[ 3 ] > 0.5 ? bodyBg.slice( 0, 3 ) : ( htmlBg[ 3 ] > 0.5 ? htmlBg.slice( 0, 3 ) : [ 255, 255, 255 ] );
	const PAGE = { c: pageBg, el: root };

	// what an element paints as its own background: null (nothing), { c: [r,g,b], el } or { c: 'img', el }
	const paintCache = new WeakMap();
	const paint = ( el ) => {
		if ( el === root || el === document.body ) {
			return PAGE;
		}
		if ( paintCache.has( el ) ) {
			return paintCache.get( el );
		}
		const s = cs( el );
		let out = null;
		if ( 'inline' === s.display || 'text' === s.backgroundClip || 'text' === s.webkitBackgroundClip || 0 === s.clipPath.indexOf( 'polygon' ) ) {
			out = null; // a highlight behind a few words, or a diagonal block cut with clip-path: decoration, not a surface
		} else if ( s.backgroundImage && 'none' !== s.backgroundImage ) {
			out = { c: 'img', el };
		} else {
			const c = parse( s.backgroundColor );
			if ( c[ 3 ] >= 0.99 ) {
				out = { c: c.slice( 0, 3 ), el };
			} else if ( c[ 3 ] > 0.015 ) {
				const b = behind( el );
				out = 'img' === b.c ? { c: 'img', el } : { c: [ 0, 1, 2 ].map( ( i ) => b.c[ i ] * ( 1 - c[ 3 ] ) + c[ i ] * c[ 3 ] ), el };
			}
		}
		paintCache.set( el, out );
		return out;
	};
	const behind = ( el ) => {
		for ( let p = el.parentElement; p; p = p.parentElement ) {
			const s = paint( p );
			if ( s ) {
				return s;
			}
		}
		return PAGE;
	};
	const differ = ( a, b ) => {
		if ( 'img' === a.c || 'img' === b.c ) {
			return a.el !== b.el;
		}
		return Math.abs( a.c[ 0 ] - b.c[ 0 ] ) + Math.abs( a.c[ 1 ] - b.c[ 1 ] ) + Math.abs( a.c[ 2 ] - b.c[ 2 ] ) >= cfg.colorDelta;
	};
	// the surface an element visibly sits on: nearest ancestor (or itself) whose paint differs from what is behind it
	const surfCache = new WeakMap();
	const surface = ( el, self ) => {
		let p = self ? el : el.parentElement;
		const start = p;
		if ( start && surfCache.has( start ) ) {
			return surfCache.get( start );
		}
		let out = PAGE;
		for ( ; p && p !== root && p !== document.body; p = p.parentElement ) {
			const s = paint( p );
			if ( s && differ( s, behind( p ) ) ) {
				out = s;
				break;
			}
		}
		if ( start ) {
			surfCache.set( start, out );
		}
		return out;
	};
	const hitAt = ( x, y, skip ) => {
		const stack = document.elementsFromPoint( x, y );
		for ( const e of stack ) {
			if ( floats( e ) || skip && ( e === skip || skip.contains( e ) ) ) {
				continue;
			}
			if ( e === root || e === document.body ) {
				return PAGE;
			}
			const s = paint( e );
			if ( s ) {
				return surface( e, true ).el === e ? s : { c: s.c, el: surface( e, true ).el };
			}
		}
		return PAGE;
	};

	const desc = ( el ) => {
		if ( ! el || el === root ) {
			return 'page';
		}
		const one = ( e ) => {
			let s = e.tagName.toLowerCase();
			if ( e.id ) {
				s += '#' + e.id;
			}
			const cl = Array.from( e.classList ).filter( ( c ) => ! /^(reveal|in|is-dark|is-light)$/.test( c ) ).slice( 0, 3 );
			if ( cl.length ) {
				s += '.' + cl.join( '.' );
			}
			return s;
		};
		const parts = [ one( el ) ];
		let p = el.parentElement;
		while ( p && p !== document.body && parts.length < 3 ) {
			parts.unshift( one( p ) );
			p = p.parentElement;
		}
		return parts.join( ' > ' );
	};
	const borderOn = ( el, side ) => {
		const s = cs( el );
		const w = parseFloat( s[ 'border' + side + 'Width' ] ) || 0;
		if ( w < 1 || 'none' === s[ 'border' + side + 'Style' ] || 'hidden' === s[ 'border' + side + 'Style' ] ) {
			return 0;
		}
		return parse( s[ 'border' + side + 'Color' ] )[ 3 ] >= 0.12 ? w : 0;
	};

	// what the overflow of the element and its ancestors lets through (viewport coordinates at the time of the call)
	const clipOf = ( el, self ) => {
		const c = { l: -1e6, t: -1e6, r: 1e6, b: 1e6 };
		for ( let p = self ? el : el.parentElement; p && p !== document.body && p !== root; p = p.parentElement ) {
			const s = cs( p );
			if ( 'visible' === s.overflowX && 'visible' === s.overflowY && 'auto' === s.clip && 'none' === s.clipPath ) {
				continue;
			}
			const r = p.getBoundingClientRect();
			if ( 'auto' !== s.clip && 'absolute' === s.position || /inset\(\s*50%/.test( s.clipPath ) ) {
				return null; // screen-reader-only text
			}
			if ( 'visible' !== s.overflowX ) {
				c.l = Math.max( c.l, r.left );
				c.r = Math.min( c.r, r.right );
			}
			if ( 'visible' !== s.overflowY ) {
				c.t = Math.max( c.t, r.top );
				c.b = Math.min( c.b, r.bottom );
			}
		}
		return c;
	};

	// ----- collect the things to measure -----
	const skipTag = /^(SCRIPT|STYLE|NOSCRIPT|TEMPLATE|HEAD|META|LINK|TITLE|BR|WBR|OPTION|OPTGROUP|SOURCE|TRACK|PATH|G|CIRCLE|RECT|LINE|POLYLINE|POLYGON|USE|DEFS|SYMBOL|ELLIPSE|TEXT|TSPAN)$/i;
	const scope = cfg.openDetails ? Array.from( document.querySelectorAll( 'details *' ) ) : Array.from( document.body.querySelectorAll( '*' ) );
	const sx0 = window.scrollX;
	const sy0 = window.scrollY;
	const items = [];
	for ( const el of scope ) {
		if ( skipTag.test( el.tagName ) || el.closest( 'svg' ) && 'svg' !== el.tagName.toLowerCase() ) {
			continue;
		}
		if ( floats( el ) || ! el.checkVisibility( { checkOpacity: true, checkVisibilityCSS: true } ) ) {
			continue;
		}
		const s = cs( el );
		// text directly inside this element
		let tr = null;
		let sample = '';
		for ( const n of el.childNodes ) {
			if ( 3 !== n.nodeType || ! n.nodeValue.trim() ) {
				continue;
			}
			const range = document.createRange();
			range.selectNodeContents( n );
			for ( const r of range.getClientRects() ) {
				if ( r.width < 2 || r.height < 4 ) {
					continue;
				}
				tr = tr ? { l: Math.min( tr.l, r.left ), t: Math.min( tr.t, r.top ), r: Math.max( tr.r, r.right ), b: Math.max( tr.b, r.bottom ) } : { l: r.left, t: r.top, r: r.right, b: r.bottom };
			}
			if ( ! sample ) {
				sample = n.nodeValue.trim().slice( 0, 34 );
			}
		}
		let cut = {};
		if ( tr ) {
			const c = clipOf( el, true );
			if ( ! c || c.r - c.l <= 2 || c.b - c.t <= 2 ) {
				tr = null;
			} else {
				cut = { left: tr.l < c.l - 0.5, right: tr.r > c.r + 0.5, top: tr.t < c.t - 0.5, bottom: tr.b > c.b + 0.5 };
				tr = { l: Math.max( tr.l, c.l ), t: Math.max( tr.t, c.t ), r: Math.min( tr.r, c.r ), b: Math.min( tr.b, c.b ) };
				if ( tr.r - tr.l < 3 || tr.b - tr.t < 3 ) {
					tr = null;
				}
			}
		}
		if ( tr ) {
			const own = el.getBoundingClientRect();
			if ( own.width <= 2 || own.height <= 2 || el.closest( '.sr-only, .screen-reader-text' ) ) {
				tr = null;
			}
		}
		if ( tr ) {
			items.push( { el, kind: 'text', cut, text: sample, l: tr.l + sx0, t: tr.t + sy0, r: tr.r + sx0, b: tr.b + sy0, surf: surface( el, true ) } );
		}
		// a box of its own: paints a different background than what is behind it, or has a border all round
		if ( 'inline' !== s.display && 'contents' !== s.display ) {
			const r = el.getBoundingClientRect();
			if ( r.width >= 24 && r.height >= 14 ) {
				const p = paint( el );
				const own = p && differ( p, behind( el ) );
				const framed = borderOn( el, 'Top' ) && borderOn( el, 'Bottom' ) && borderOn( el, 'Left' ) && borderOn( el, 'Right' );
				const c = clipOf( el, false );
				if ( ( own || framed ) && c && r.left >= c.l - 0.5 && r.right <= c.r + 0.5 && r.top >= c.t - 0.5 && r.bottom <= c.b + 0.5 ) {
					items.push( { el, kind: 'box', cut: {}, text: ( el.innerText || '' ).trim().replace( /\s+/g, ' ' ).slice( 0, 34 ), l: r.left + sx0, t: r.top + sy0, r: r.right + sx0, b: r.bottom + sy0, surf: surface( el, false ) } );
				}
			}
		}
	}

	// ----- measure -----
	const flags = [];
	const limitsFor = ( sr ) => {
		const type = sr.height < 76 ? 'control' : ( sr.width >= vw - 4 && sr.height >= 160 ? 'band' : 'box' );
		return { type, v: cfg.limit[ type ][ 0 ], h: cfg.limit[ type ][ 1 ] };
	};
	const scrollToY = ( y ) => {
		window.scrollTo( { left: 0, top: Math.max( 0, y - vh / 2 ), behavior: 'instant' } );
		return window.scrollY;
	};
	const pageRect = ( el ) => {
		if ( el === root ) {
			return { left: 0, right: vw, top: 0, bottom: root.scrollHeight, width: vw, height: root.scrollHeight };
		}
		const r = el.getBoundingClientRect();
		return { left: r.left, right: r.right, top: r.top + window.scrollY, bottom: r.bottom + window.scrollY, width: r.width, height: r.height };
	};

	for ( const it of items ) {
		const w = it.r - it.l;
		const h = it.b - it.t;
		if ( w < 3 || h < 3 || it.r <= 0 || it.l >= vw ) {
			continue;
		}
		const S = it.surf;
		const srect = pageRect( S.el );
		const lim = limitsFor( srect );
		const mult = cfg.all ? 2 : 1;
		// the element must really be on screen at its own centre (not clipped by a scroller, not covered)
		let sy = scrollToY( it.t + h / 2 );
		const cx = Math.min( vw - 2, Math.max( 1, it.l + w / 2 ) );
		const seen = document.elementsFromPoint( cx, it.t + h / 2 - sy );
		const top = seen.find( ( e ) => ! floats( e ) );
		if ( ! top || ! ( top === it.el || it.el.contains( top ) || top.contains( it.el ) && ! paint( top ) ) ) {
			continue;
		}
		const painted = hitAt( cx, it.t + h / 2 - sy, 'box' === it.kind ? it.el : null );
		if ( painted.el !== S.el && differ( painted, S ) ) {
			it.surf = painted;
		}
		const S2 = it.surf;
		if ( S2 !== S ) {
			Object.assign( srect, pageRect( S2.el ) );
			Object.assign( lim, limitsFor( srect ) );
		}
		const isBandItem = 'box' === it.kind && w >= srect.width * 0.96;
		const edgeHit = ( x, y, vertical ) => {
			if ( x < 0 || x >= vw || y < 0 || y >= vh ) {
				return null;
			}
			const hit = hitAt( x, y, 'box' === it.kind ? it.el : null );
			if ( ! differ( hit, it.surf ) || it.el.contains( hit.el ) && hit.el !== it.el ) {
				return null;
			}
			if ( hit.el === it.el ) {
				return null;
			}
			const hr = pageRect( hit.el );
			if ( vertical ) {
				// the other surface has to cut across (nearly) the whole surface the item sits on
				const span = Math.min( hr.right, srect.right ) - Math.max( hr.left, srect.left );
				return span >= srect.width * 0.96 || hit.el.contains( it.surf.el ) ? hit : null;
			}
			const spanV = Math.min( hr.bottom, srect.bottom ) - Math.max( hr.top, srect.top );
			return spanV >= srect.height * 0.96 || hit.el.contains( it.surf.el ) ? hit : null;
		};
		const inset = Math.min( 10, w * 0.2 );
		const xs = [ it.l + inset, it.l + w / 2, it.r - inset ].map( ( x ) => Math.min( vw - 2, Math.max( 1, x ) ) );
		const insetV = Math.min( 8, h * 0.2 );

		for ( const side of [ 'bottom', 'top', 'left', 'right' ] ) {
			const vertical = 'bottom' === side || 'top' === side;
			if ( vertical && isBandItem || ! vertical && 'box' === it.kind || it.cut[ side ] ) {
				continue;
			}
			const base = 'box' === it.kind && 'box' === lim.type ? cfg.boxInBox : ( vertical ? lim.v : lim.h );
			const max = base * mult;
			let found = null;
			if ( vertical ) {
				const edge = 'bottom' === side ? it.b : it.t;
				sy = scrollToY( edge );
				const at = ( k ) => {
					const y = ( 'bottom' === side ? edge + k - 0.5 : edge - k + 0.5 ) - sy;
					let n = 0;
					let last = null;
					for ( const x of xs ) {
						const hres = edgeHit( x, y, true );
						if ( hres ) {
							n++;
							last = hres;
						}
					}
					return n >= 2 ? last : null;
				};
				if ( at( max ) || at( Math.ceil( max / 2 ) ) || at( 1 ) ) {
					for ( let k = 1; k <= max; k++ ) {
						const hres = at( k );
						if ( hres ) {
							found = { gap: k - 1, hit: hres };
							break;
						}
					}
				}
			} else {
				const edge = 'right' === side ? it.r : it.l;
				const toScreen = 'right' === side ? vw - it.r : it.l;
				if ( toScreen < cfg.viewportSide && 'text' === it.kind ) {
					flags.push( { kind: 'viewport', side, gap: Math.round( toScreen ), type: 'screen', item: desc( it.el ), text: it.text, edge: 'screen edge', x: it.l, y: it.t, w, h } );
					continue;
				}
				sy = scrollToY( it.t + h / 2 );
				const ys = [ it.t + insetV, it.t + h / 2, it.b - insetV ].map( ( y ) => y - sy );
				const at = ( k ) => {
					const x = 'right' === side ? edge + k - 0.5 : edge - k + 0.5;
					let n = 0;
					let last = null;
					for ( const y of ys ) {
						const hres = edgeHit( x, y, false );
						if ( hres ) {
							n++;
							last = hres;
						}
					}
					return n >= 2 ? last : null;
				};
				if ( at( max ) || at( Math.ceil( max / 2 ) ) || at( 1 ) ) {
					for ( let k = 1; k <= max; k++ ) {
						const hres = at( k );
						if ( hres ) {
							found = { gap: k - 1, hit: hres };
							break;
						}
					}
				}
			}
			if ( found && 'box' === it.kind && found.gap <= 2 ) {
				const hr = pageRect( found.hit.el );
				if ( found.hit.el.contains( it.surf.el ) || hr.width < vw * 0.9 ) {
					found = null; // cell in a hairline grid, tab in a frame, tile next to tile
				}
			}
			if ( found ) {
				flags.push( { kind: 'surface', side, gap: found.gap, type: lim.type, limit: base, item: desc( it.el ), text: it.text, edge: desc( found.hit.el ), on: desc( it.surf.el ), x: it.l, y: it.t, w, h } );
				continue;
			}
			// a border line of a frame between the item and its surface
			const cap = side.charAt( 0 ).toUpperCase() + side.slice( 1 );
			for ( let a = it.el.parentElement; a && a !== it.surf.el && a !== document.body; a = a.parentElement ) {
				const bw = borderOn( a, cap );
				if ( ! bw ) {
					continue;
				}
				const ar = pageRect( a );
				let gap;
				if ( 'bottom' === side ) {
					gap = ar.bottom - bw - it.b;
				} else if ( 'top' === side ) {
					gap = it.t - ( ar.top + bw );
				} else if ( 'left' === side ) {
					gap = it.l - ( ar.left + bw );
				} else {
					gap = ar.right - bw - it.r;
				}
				const small = ar.height < 76;
				const blim = ( small ? cfg.limit.control[ 0 ] - 1 : cfg.limit.box[ 0 ] - 2 ) * mult;
				if ( gap > -40 && gap < blim && ! ( 'box' === it.kind && gap <= 2 ) ) {
					flags.push( { kind: 'border', side, gap: Math.round( gap ), type: small ? 'control' : 'box', limit: small ? cfg.limit.control[ 0 ] - 1 : cfg.limit.box[ 0 ] - 2, item: desc( it.el ), text: it.text, edge: desc( a ), on: desc( it.surf.el ), x: it.l, y: it.t, w, h } );
				}
				break;
			}
		}
	}
	// ----- a box and the block right above or below it on the same surface -----
	const boxes = items.filter( ( it ) => 'box' === it.kind );
	for ( const B of boxes ) {
		const bw = B.r - B.l;
		if ( bw < 120 && B.b - B.t < 32 ) {
			continue; // a small tag inside a line of text follows the line spacing
		}
		for ( const side of [ 'bottom', 'top' ] ) {
			let best = null;
			for ( const n of items ) {
				if ( n === B || n.el === B.el || n.surf.el !== B.surf.el || B.el.contains( n.el ) || n.el.contains( B.el ) ) {
					continue;
				}
				const over = Math.min( n.r, B.r ) - Math.max( n.l, B.l );
				if ( over < Math.min( bw, n.r - n.l ) * 0.3 ) {
					continue;
				}
				const gap = 'bottom' === side ? n.t - B.b : B.t - n.b;
				if ( gap < -1 || gap >= cfg.stack * ( cfg.all ? 2 : 1 ) ) {
					continue;
				}
				if ( ! best || gap < best.gap ) {
					best = { gap, n };
				}
			}
			if ( ! best ) {
				continue;
			}
			if ( 'box' === best.n.kind && best.gap <= 2 ) {
				// tiles of a hairline grid, table cells, joined rows of one list and bands under bands touch on purpose;
				// two different blocks of running content (a table, then a note box) do not
				const par = B.el.parentElement;
				const sig = ( e ) => e.tagName + '.' + ( e.classList[ 0 ] || '' );
				const sw = pageRect( B.surf.el ).width;
				const flow = par && par === best.n.el.parentElement && /^(block|flow-root)$/.test( cs( par ).display )
					&& sig( B.el ) !== sig( best.n.el ) && ! /^(TD|TH|TR|LI)$/.test( B.el.tagName )
					&& bw < sw * 0.96 && best.n.r - best.n.l < sw * 0.96;
				if ( ! flow ) {
					continue;
				}
			}
			flags.push( { kind: 'stack', side, gap: Math.max( 0, Math.round( best.gap ) ), type: best.n.kind, limit: cfg.stack, item: desc( B.el ), text: B.text, edge: desc( best.n.el ) + ( best.n.text ? ' "' + best.n.text.slice( 0, 20 ) + '"' : '' ), on: desc( B.surf.el ), x: B.l, y: B.t, w: bw, h: B.b - B.t } );
		}
	}

	window.scrollTo( { left: 0, top: 0, behavior: 'instant' } );
	return {
		flags,
		items: items.length,
		overflowX: Math.max( 0, root.scrollWidth - vw ),
		height: root.scrollHeight,
		title: document.title,
	};
}

/* ---------- Chrome over the DevTools protocol ---------- */
class CDP {
	constructor( ws ) {
		this.ws = ws;
		this.id = 0;
		this.pending = new Map();
		this.listeners = [];
		ws.addEventListener( 'message', ( e ) => {
			const m = JSON.parse( e.data );
			if ( m.id ) {
				const p = this.pending.get( m.id );
				if ( p ) {
					this.pending.delete( m.id );
					if ( m.error ) {
						p.reject( new Error( m.error.message ) );
					} else {
						p.resolve( m.result );
					}
				}
				return;
			}
			for ( const l of this.listeners.slice() ) {
				l( m );
			}
		} );
	}
	send( method, params = {}, sessionId ) {
		const id = ++this.id;
		const msg = { id, method, params };
		if ( sessionId ) {
			msg.sessionId = sessionId;
		}
		this.ws.send( JSON.stringify( msg ) );
		return new Promise( ( resolve, reject ) => this.pending.set( id, { resolve, reject } ) );
	}
	waitFor( method, sessionId, timeout = 45000 ) {
		return new Promise( ( resolve, reject ) => {
			const off = () => {
				this.listeners = this.listeners.filter( ( x ) => x !== l );
			};
			const t = setTimeout( () => {
				off();
				reject( new Error( 'timeout waiting for ' + method ) );
			}, timeout );
			const l = ( m ) => {
				if ( m.method === method && ( ! sessionId || m.sessionId === sessionId ) ) {
					off();
					clearTimeout( t );
					resolve( m.params );
				}
			};
			this.listeners.push( l );
		} );
	}
}

const profile = mkdtempSync( join( tmpdir(), 'falcon-spacing-' ) );
const proc = spawn( chrome, [ '--headless=new', '--disable-gpu', '--hide-scrollbars', '--no-first-run', '--no-default-browser-check', '--remote-debugging-port=0', '--user-data-dir=' + profile, 'about:blank' ], { stdio: [ 'ignore', 'ignore', 'pipe' ] } );
const wsUrl = await new Promise( ( resolve, reject ) => {
	let buf = '';
	const t = setTimeout( () => reject( new Error( 'Chrome did not start' ) ), 20000 );
	proc.stderr.on( 'data', ( d ) => {
		buf += d.toString();
		const m = buf.match( /DevTools listening on (ws:\/\/\S+)/ );
		if ( m ) {
			clearTimeout( t );
			resolve( m[ 1 ] );
		}
	} );
	proc.on( 'exit', () => reject( new Error( 'Chrome exited early' ) ) );
} );
const ws = new WebSocket( wsUrl );
await new Promise( ( resolve, reject ) => {
	ws.addEventListener( 'open', resolve, { once: true } );
	ws.addEventListener( 'error', reject, { once: true } );
} );
const cdp = new CDP( ws );

const cfg = {
	colorDelta: 9,
	boxInBox: 5,
	stack: 6,
	viewportSide: 8,
	all: showAll,
	limit: { band: [ 20, 10 ], box: [ 8, 8 ], control: [ 1, 3 ] },
};
const slugOf = ( path ) => ( path.replace( /[^a-z0-9]+/gi, '-' ).replace( /^-|-$/g, '' ) || 'home' );
const allowed = ( path, f ) => ALLOW.some( ( a ) => ( ! a.path || a.path.test( path ) ) && a.item.test( f.item ) && a.edge.test( f.edge ) );
if ( shots ) {
	mkdirSync( shots, { recursive: true } );
}
if ( pagesDir ) {
	mkdirSync( pagesDir, { recursive: true } );
}

async function run( path, width ) {
	const { targetId } = await cdp.send( 'Target.createTarget', { url: 'about:blank' } );
	const { sessionId } = await cdp.send( 'Target.attachToTarget', { targetId, flatten: true } );
	try {
		await cdp.send( 'Page.enable', {}, sessionId );
		await cdp.send( 'Emulation.setDeviceMetricsOverride', { width, height: width < 768 ? 812 : 900, deviceScaleFactor: 1, mobile: width < 768 }, sessionId );
		const loaded = cdp.waitFor( 'Page.loadEventFired', sessionId );
		const url = base + path + ( query ? ( path.includes( '?' ) ? '&' : '?' ) + query : '' );
		await cdp.send( 'Page.navigate', { url }, sessionId );
		await loaded;
		const out = { path, width, flags: [], overflowX: 0, items: 0 };
		for ( const openDetails of [ false, true ] ) {
			const res = await cdp.send( 'Runtime.evaluate', { expression: '(' + audit.toString() + ')(' + JSON.stringify( { ...cfg, openDetails } ) + ')', awaitPromise: true, returnByValue: true }, sessionId );
			if ( res.exceptionDetails ) {
				throw new Error( res.exceptionDetails.exception && res.exceptionDetails.exception.description || res.exceptionDetails.text );
			}
			const v = res.result.value;
			if ( ! openDetails ) {
				out.overflowX = v.overflowX;
				out.title = v.title;
				if ( pagesDir ) {
					await cdp.send( 'Runtime.evaluate', { expression: "(() => { const st = document.createElement('style'); st.id = '__fxhide'; st.textContent = '[data-fx-float]{visibility:hidden!important}'; document.head.appendChild(st); })()" }, sessionId );
					for ( let y = 0, i = 1; y < v.height; y += 1400, i++ ) {
						const img = await cdp.send( 'Page.captureScreenshot', { format: 'png', clip: { x: 0, y, width, height: Math.min( 1400, v.height - y ), scale: 1 }, captureBeyondViewport: true }, sessionId );
						writeFileSync( join( pagesDir, `${ slugOf( path ) }-${ width }-p${ String( i ).padStart( 2, '0' ) }.png` ), Buffer.from( img.data, 'base64' ) );
					}
				}
			}
			out.items += v.items;
			// one line per edge and side: keep the tightest item
			const groups = new Map();
			for ( const f of v.flags ) {
				if ( allowed( path, f ) ) {
					continue;
				}
				const key = [ f.kind, f.side, f.edge, f.on || '', f.type ].join( '|' );
				const g = groups.get( key );
				if ( ! g ) {
					groups.set( key, { ...f, count: 1, open: openDetails } );
				} else {
					g.count++;
					if ( f.gap < g.gap ) {
						Object.assign( g, f, { count: g.count, open: openDetails } );
					}
				}
			}
			const list = Array.from( groups.values() ).sort( ( a, b ) => a.gap - b.gap );
			if ( shots ) {
				let n = out.flags.length;
				for ( const f of list.slice( 0, 14 ) ) {
					n++;
					const mark = `(() => { if (!document.getElementById('__fxhide')) { const st = document.createElement('style'); st.id = '__fxhide'; st.textContent = '[data-fx-float]{visibility:hidden!important}'; document.head.appendChild(st); } const d = document.createElement('div'); d.id = '__fxmark'; d.style.cssText = 'position:absolute;z-index:2147483647;pointer-events:none;outline:2px solid #ff00c8;outline-offset:2px;left:${ f.x }px;top:${ f.y }px;width:${ f.w }px;height:${ f.h }px'; document.body.appendChild(d); })()`;
					await cdp.send( 'Runtime.evaluate', { expression: mark }, sessionId );
					const pad = 90;
					const clip = { x: Math.max( 0, f.x - pad ), y: Math.max( 0, f.y - pad ), width: Math.min( width, f.w + pad * 2 ), height: Math.min( 620, f.h + pad * 2 ), scale: 1 };
					if ( 'bottom' === f.side && f.h + pad * 2 > 620 ) {
						clip.y = Math.max( 0, f.y + f.h - 620 + pad );
					}
					const img = await cdp.send( 'Page.captureScreenshot', { format: 'png', clip, captureBeyondViewport: true }, sessionId );
					f.shot = `${ slugOf( path ) }-${ width }-${ String( n ).padStart( 2, '0' ) }.png`;
					writeFileSync( join( shots, f.shot ), Buffer.from( img.data, 'base64' ) );
					await cdp.send( 'Runtime.evaluate', { expression: "document.getElementById('__fxmark')?.remove()" }, sessionId );
				}
			}
			out.flags.push( ...list );
		}
		return out;
	} finally {
		await cdp.send( 'Target.closeTarget', { targetId } ).catch( () => {} );
	}
}

const jobs = [];
for ( const p of paths ) {
	for ( const w of widths ) {
		jobs.push( [ p, w ] );
	}
}
const results = [];
let failed = 0;
const worker = async () => {
	while ( jobs.length ) {
		const [ p, w ] = jobs.shift();
		try {
			results.push( await run( p, w ) );
		} catch ( e ) {
			failed++;
			results.push( { path: p, width: w, error: String( e.message || e ), flags: [], overflowX: 0 } );
		}
	}
};
await Promise.all( [ worker(), worker(), worker() ] );
ws.close();
proc.kill();
setTimeout( () => {
	try {
		rmSync( profile, { recursive: true, force: true } );
	} catch ( e ) { /* Chrome may still hold the folder for a moment */ }
}, 500 );

results.sort( ( a, b ) => paths.indexOf( a.path ) - paths.indexOf( b.path ) || b.width - a.width );
let total = 0;
for ( const r of results ) {
	if ( r.error ) {
		console.log( `✗ ${ r.path } @${ r.width } · ${ r.error }` );
		continue;
	}
	const lines = [];
	if ( r.overflowX > 1 ) {
		lines.push( `   overflow   page is ${ r.overflowX }px wider than the screen` );
	}
	for ( const f of r.flags ) {
		lines.push( `   ${ f.kind.padEnd( 8 ) } ${ f.side.padEnd( 6 ) } ${ String( f.gap ).padStart( 3 ) }px ${ ( f.type || '' ).padEnd( 7 ) } ${ f.item }${ f.text ? ' "' + f.text + '"' : '' }  →  ${ f.edge }${ f.count > 1 ? '  (×' + f.count + ')' : '' }${ f.open ? '  [opened]' : '' }${ f.shot ? '  ' + f.shot : '' }` );
	}
	total += lines.length;
	console.log( `${ lines.length ? '✗' : '✓' } ${ r.path } @${ r.width }${ lines.length ? '' : ' · ' + r.items + ' items' }` );
	lines.forEach( ( l ) => console.log( l ) );
}
if ( jsonOut ) {
	writeFileSync( jsonOut, JSON.stringify( results, null, 1 ) );
}
console.log( total || failed ? `\n${ total } spot(s) to look at${ failed ? ', ' + failed + ' page(s) failed to load' : '' }` : '\nspacing ok' );
process.exitCode = total || failed ? 1 : 0;
