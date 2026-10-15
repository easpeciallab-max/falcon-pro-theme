/**
 * Button pair check: finds buttons in the same group (side by side or one above the other) that are not the same width.
 *
 * The owner spotted a LINE button and an OpenChat button stacked with different lengths (15 Oct 2026).
 * A "group" is the buttons that share a parent. The owner wants every pair equally wide (15 Oct 2026), so each row
 * is a grid of equal columns as wide as the longest button, and stacked buttons share one width.
 * Buttons inside a <p> or <li> of running text, the header, the bottom bar and hidden elements are skipped.
 *
 * Needs Node 22+ and Google Chrome. No packages to install.
 *   node dev/check-buttons.mjs                              every page of the preview server (localhost:8765)
 *   node dev/check-buttons.mjs /pricing/ /go/                only these paths
 *   --base=https://falconpro-th.com   another site          --query=a=b   query string added to every path
 *   --widths=1280,768,375             screen widths         --tol=2       allowed difference in px
 * The preview runs with ?fx_chrome_demo=1 by default (every contact channel filled in) unless --query is given.
 * Exit code 1 when anything is flagged.
 */

import { spawn } from 'node:child_process';
import { existsSync, mkdtempSync, rmSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';

const args = process.argv.slice( 2 );
const opt = ( name, fallback ) => {
	const hit = args.find( ( a ) => a.startsWith( '--' + name + '=' ) );
	return hit ? hit.slice( name.length + 3 ) : fallback;
};
const base = opt( 'base', process.env.FALCON_PREVIEW || 'http://localhost:8765' ).replace( /\/$/, '' );
const widths = opt( 'widths', '1280,768,375' ).split( ',' ).map( Number ).filter( Boolean );
const query = opt( 'query', /localhost|127\.0\.0\.1/.test( base ) ? 'fx_chrome_demo=1' : '' );
const tol = Number( opt( 'tol', '2' ) );
let paths = args.filter( ( a ) => ! a.startsWith( '--' ) );
if ( ! paths.length ) {
	paths = [
		'/', '/backtest/', '/open-mt5-account/', '/deposit/', '/mt5-login/',
		'/tools/', '/dashboard/', '/pricing/', '/risk-disclosure/',
		'/about/', '/privacy-policy/', '/terms-of-use/', '/data-deletion/', '/go/', '/articles/', '/article/cent-account/', '/404/', '/?s=falcon',
	];
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

/* ---------- runs inside the page ---------- */
async function audit( cfg ) {
	const sleep = ( ms ) => new Promise( ( r ) => setTimeout( r, ms ) );
	const st = document.createElement( 'style' );
	st.textContent = '*,*::before,*::after{animation:none!important;transition:none!important}.reveal{opacity:1!important;transform:none!important}';
	document.head.appendChild( st );
	if ( document.fonts && document.fonts.ready ) {
		await Promise.race( [ document.fonts.ready, sleep( 4000 ) ] );
	}
	await new Promise( ( r ) => requestAnimationFrame( () => requestAnimationFrame( r ) ) );

	const isBtn = ( el ) => /(^|\s)(btn|lh-btn)(\s|$)/.test( el.className && el.className.baseVal === undefined ? el.className : '' );
	const shown = ( el ) => {
		const r = el.getBoundingClientRect();
		if ( r.width < 2 || r.height < 2 ) {
			return false;
		}
		for ( let e = el; e && e !== document.body; e = e.parentElement ) {
			const s = getComputedStyle( e );
			if ( 'none' === s.display || 'hidden' === s.visibility || '0' === s.opacity ) {
				return false;
			}
		}
		return true;
	};
	const desc = ( el ) => el.tagName.toLowerCase() + ( el.id ? '#' + el.id : '' ) + Array.from( el.classList ).slice( 0, 3 ).map( ( c ) => '.' + c ).join( '' );
	const parents = new Set();
	document.querySelectorAll( 'a, button' ).forEach( ( el ) => {
		if ( isBtn( el ) && el.parentElement ) {
			parents.add( el.parentElement );
		}
	} );
	const out = [];
	for ( const par of parents ) {
		if ( par.closest( '.site-header, .mobile-app-nav, p, li, td, th, .admin-hint' ) ) {
			continue;
		}
		const btns = Array.from( par.children ).filter( ( c ) => isBtn( c ) && shown( c ) );
		if ( btns.length < 2 ) {
			continue;
		}
		const rects = btns.map( ( b ) => b.getBoundingClientRect() );
		const ws = rects.map( ( r ) => Math.round( r.width ) );
		const spread = Math.max( ...ws ) - Math.min( ...ws );
		if ( spread > cfg.tol ) {
			out.push( { group: desc( par ), widths: ws, buttons: btns.map( ( b ) => ( b.textContent || '' ).replace( /\s+/g, ' ' ).trim().slice( 0, 28 ) ) } );
		}
	}
	return out;
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

const profile = mkdtempSync( join( tmpdir(), 'falcon-buttons-' ) );
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

async function run( path, width ) {
	const { targetId } = await cdp.send( 'Target.createTarget', { url: 'about:blank' } );
	const { sessionId } = await cdp.send( 'Target.attachToTarget', { targetId, flatten: true } );
	try {
		await cdp.send( 'Page.enable', {}, sessionId );
		await cdp.send( 'Emulation.setDeviceMetricsOverride', { width, height: width < 768 ? 812 : 900, deviceScaleFactor: 1, mobile: width < 768 }, sessionId );
		const loaded = cdp.waitFor( 'Page.loadEventFired', sessionId );
		await cdp.send( 'Page.navigate', { url: base + path + ( query ? ( path.includes( '?' ) ? '&' : '?' ) + query : '' ) }, sessionId );
		await loaded;
		const res = await cdp.send( 'Runtime.evaluate', { expression: '(' + audit.toString() + ')(' + JSON.stringify( { tol } ) + ')', awaitPromise: true, returnByValue: true }, sessionId );
		if ( res.exceptionDetails ) {
			throw new Error( res.exceptionDetails.exception && res.exceptionDetails.exception.description || res.exceptionDetails.text );
		}
		return { path, width, groups: res.result.value };
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
			results.push( { path: p, width: w, error: String( e.message || e ), groups: [] } );
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
	total += r.groups.length;
	console.log( `${ r.groups.length ? '✗' : '✓' } ${ r.path } @${ r.width }` );
	for ( const g of r.groups ) {
		console.log( `   ${ g.group }  widths ${ g.widths.join( ' / ' ) }  ·  ${ g.buttons.map( ( b ) => '"' + b + '"' ).join( ' ' ) }` );
	}
}
console.log( total || failed ? `\n${ total } group(s) with buttons of different widths${ failed ? ', ' + failed + ' page(s) failed to load' : '' }` : '\nbuttons ok' );
process.exitCode = total || failed ? 1 : 0;
