/**
 * FALCON PRO EA · PDPA consent engine · HTML มาจาก inc/modules/consent.php
 *
 * - HTML ของการ์ดเหมือนกันทุกคน สคริปต์นี้อ่านคุกกี้ในเบราว์เซอร์แล้วเลือกเองว่าจะเปิดการ์ดหรือไม่
 * - gtag.js กับ fbevents.js ถูกใส่เข้าหน้าหลังได้รับอนุญาตหมวดของมันเท่านั้น · ปุ่ม X / Esc = ยังไม่ตอบ (ถามอีกทีในการเข้าชมครั้งหน้า)
 * - fenix_consent = v<รุ่น>.a<0|1|->.m<0|1|->.t<unix> · 365 วัน · SameSite=Lax
 *   "-" หมายถึงตอนตอบยังไม่มีบริการในหมวดนั้น · ถ้าเจ้าของเว็บเพิ่มบริการทีหลัง คนกลุ่มนี้จะเห็นการ์ดอีกครั้ง
 * - Consent Mode v2: <head> ประกาศ denied ไว้แล้ว (consent.php) · ได้รับอนุญาตเมื่อไรจึงส่ง update แล้วค่อยใส่ gtag.js
 * - เปลี่ยนใจจากอนุญาตเป็นไม่อนุญาต: ตั้ง ga-disable, ส่ง update denied, fbq('consent','revoke'), ล้างคุกกี้ของบริการ แล้วโหลดหน้าใหม่
 * - ตัวเปิดการ์ด (ดู TRIGGERS): a[href$="#cookie-settings"], .cookie-reopen, [data-cookie-settings]
 * - API: window.fenixConsent.has('analytics'|'marketing') / .get() / .open() · เหตุการณ์ 'fenix:consent' บน document
 */
(function () {
	'use strict';

	var cfg = window.fenixConsentConfig || {};
	var COOKIE = 'fenix_consent';
	var DISMISS_KEY = 'fenixConsentDismissed';
	var MAX_AGE = 365 * 24 * 60 * 60;
	var TRIGGERS = 'a[href$="#cookie-settings"], .cookie-reopen, [data-cookie-settings]';

	var version = /^\d+$/.test(String(cfg.version || '')) ? String(cfg.version) : '1';
	var gaId = /^G-[A-Z0-9]{4,20}$/.test(String(cfg.ga || '')) ? String(cfg.ga) : '';
	var pixelId = /^\d{10,20}$/.test(String(cfg.pixel || '')) ? String(cfg.pixel) : '';
	var tools = { analytics: !!gaId, marketing: !!pixelId };
	var hasOptional = tools.analytics || tools.marketing;
	var loaded = { analytics: false, marketing: false };
	var current = null;
	var box = null;
	var bound = false;
	var trigger = null;
	var frame = 0;

	/* ---------------- คุกกี้ความยินยอม ---------------- */

	function read() {
		var m = document.cookie.match(/(?:^|;\s*)fenix_consent=([^;]+)/);
		var p = m ? /^v(\d+)\.a([01-])\.m([01-])\.t(\d+)$/.exec(m[1]) : null;
		if (!p || p[1] !== version) {
			return null;
		}
		return {
			analytics: p[2] === '1',
			marketing: p[3] === '1',
			asked: { analytics: p[2] !== '-', marketing: p[3] !== '-' }
		};
	}

	function write(choice) {
		function flag(cat) {
			return tools[cat] ? (choice[cat] ? '1' : '0') : '-';
		}
		var value = 'v' + version + '.a' + flag('analytics') + '.m' + flag('marketing') + '.t' + Math.floor(Date.now() / 1000);
		document.cookie = COOKIE + '=' + value + '; max-age=' + MAX_AGE + '; path=/; SameSite=Lax' + (window.location.protocol === 'https:' ? '; Secure' : '');
	}

	/* ยังไม่มีคำตอบ หรือมีเครื่องมือใหม่ที่ผู้เข้าชมยังไม่เคยถูกถาม */
	function needsAnswer(stored) {
		if (!stored) {
			return true;
		}
		return (tools.analytics && !stored.asked.analytics) || (tools.marketing && !stored.asked.marketing);
	}

	function dismissedThisVisit() {
		try {
			return window.sessionStorage.getItem(DISMISS_KEY) === '1';
		} catch (err) {
			return false;
		}
	}

	function rememberDismiss() {
		try {
			window.sessionStorage.setItem(DISMISS_KEY, '1');
		} catch (err) {}
	}

	function forgetDismiss() {
		try {
			window.sessionStorage.removeItem(DISMISS_KEY);
		} catch (err) {}
	}

	/* ลบคุกกี้ตามชื่อ ทั้งโฮสต์ปัจจุบันและโดเมนแม่ทุกชั้น (GA/Pixel มักตั้งไว้ที่ .โดเมนหลัก) */
	function clearCookies(pattern) {
		var parts = window.location.hostname.split('.');
		var domains = [''];
		for (var i = 0; i < parts.length - 1; i++) {
			var d = parts.slice(i).join('.');
			domains.push(d, '.' + d);
		}
		document.cookie.split(';').forEach(function (part) {
			var name = part.split('=')[0].trim();
			if (!name || !pattern.test(name)) {
				return;
			}
			domains.forEach(function (domain) {
				document.cookie = name + '=; max-age=0; path=/' + (domain ? '; domain=' + domain : '');
			});
		});
	}

	/* ---------------- แท็กติดตาม ---------------- */

	function signals(choice) {
		var ads = choice.marketing ? 'granted' : 'denied';
		return {
			analytics_storage: choice.analytics ? 'granted' : 'denied',
			ad_storage: ads,
			ad_user_data: ads,
			ad_personalization: ads
		};
	}

	function loadAnalytics(choice) {
		if (loaded.analytics || !gaId) {
			return;
		}
		loaded.analytics = true;
		window['ga-disable-' + gaId] = false;
		window.dataLayer = window.dataLayer || [];
		if (typeof window.gtag !== 'function') {
			window.gtag = function () {
				window.dataLayer.push(arguments);
			};
		}
		if (!window.fenixConsentDefault) {
			window.gtag('consent', 'default', signals({ analytics: false, marketing: false }));
		}
		window.gtag('consent', 'update', signals(choice));
		window.gtag('js', new Date());
		window.gtag('config', gaId);
		var tag = document.createElement('script');
		tag.async = true;
		tag.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(gaId);
		document.head.appendChild(tag);
	}

	function loadMarketing() {
		if (loaded.marketing || !pixelId) {
			return;
		}
		loaded.marketing = true;
		if (typeof window.fbq !== 'function') {
			var n = function () {
				if (n.callMethod) {
					n.callMethod.apply(n, arguments);
				} else {
					n.queue.push(arguments);
				}
			};
			window.fbq = n;
			if (!window._fbq) {
				window._fbq = n;
			}
			n.push = n;
			n.loaded = true;
			n.version = '2.0';
			n.queue = [];
			var tag = document.createElement('script');
			tag.async = true;
			tag.src = 'https://connect.facebook.net/en_US/fbevents.js';
			document.head.appendChild(tag);
		}
		window.fbq('consent', 'grant');
		window.fbq('init', pixelId);
		window.fbq('track', 'PageView');
	}

	function announce() {
		var detail = { analytics: !!(current && current.analytics), marketing: !!(current && current.marketing) };
		try {
			document.dispatchEvent(new CustomEvent('fenix:consent', { detail: detail }));
		} catch (err) {}
	}

	function apply(choice) {
		var gaWasLoaded = loaded.analytics;
		current = choice;
		if (choice.analytics) {
			loadAnalytics(choice);
		}
		if (choice.marketing) {
			loadMarketing();
		}
		if (gaWasLoaded && typeof window.gtag === 'function') {
			window.gtag('consent', 'update', signals(choice));
		}
		announce();
	}

	function save(choice) {
		choice = {
			analytics: tools.analytics && !!choice.analytics,
			marketing: tools.marketing && !!choice.marketing,
			asked: { analytics: tools.analytics, marketing: tools.marketing }
		};
		var revoke = (loaded.analytics && !choice.analytics) || (loaded.marketing && !choice.marketing);

		write(choice);
		forgetDismiss();

		if (!choice.analytics) {
			if (gaId) {
				window['ga-disable-' + gaId] = true;
			}
			clearCookies(/^(_ga(_.+)?|_gid|_gat(_.+)?)$/);
		}
		if (!choice.marketing) {
			if (loaded.marketing && typeof window.fbq === 'function') {
				window.fbq('consent', 'revoke');
			}
			clearCookies(/^(_fbp|_fbc|_gcl_.+)$/);
		}

		if (revoke) {
			/* แท็กที่โหลดไปแล้วยังค้างในหน้า · แจ้งเลิกใช้แล้วรีโหลดให้หน้าใหม่ไม่มีแท็กนั้นเลย */
			if (loaded.analytics && typeof window.gtag === 'function') {
				window.gtag('consent', 'update', signals(choice));
			}
			current = choice;
			announce();
			window.location.reload();
			return;
		}

		apply(choice);
		hide();
	}

	/* ---------------- การ์ด ---------------- */

	function each(selector, fn) {
		if (!box) {
			return;
		}
		Array.prototype.forEach.call(box.querySelectorAll(selector), fn);
	}

	function view(name) {
		var mode = hasOptional ? 'optional' : 'none';
		box.setAttribute('data-view', name);
		each('[data-show], [data-when]', function (el) {
			var show = el.getAttribute('data-show');
			var when = el.getAttribute('data-when');
			el.hidden = !!((show && show !== name) || (when && when !== mode));
		});
		each('[data-cat]', function (el) {
			var cat = el.getAttribute('data-cat');
			el.hidden = !tools[cat];
			var input = el.querySelector('input');
			if (input) {
				input.checked = !!(current && current[cat]);
			}
		});
		each('details.consent-more', function (el) {
			el.open = false;
		});
	}

	/* มือถือ: ยกการ์ดขึ้นเหนือบาร์เมนูล่าง (วัดจากตำแหน่งจริง รองรับบาร์ทุกแบบ) */
	function placeAboveDock() {
		if (!box || box.hidden) {
			return;
		}
		var offset = 0;
		var dock = document.querySelector('.mobile-app-nav, .dock, [data-dock]');
		if (dock) {
			var style = window.getComputedStyle(dock);
			if (style.display !== 'none' && style.visibility !== 'hidden' && style.position === 'fixed') {
				var rect = dock.getBoundingClientRect();
				if (rect.height > 0 && rect.top > window.innerHeight * 0.5) {
					offset = Math.max(0, Math.round(window.innerHeight - rect.top));
				}
			}
		}
		box.style.setProperty('--consent-dock', offset + 'px');
	}

	function queuePlace() {
		if (frame || !box || box.hidden) {
			return;
		}
		frame = window.requestAnimationFrame(function () {
			frame = 0;
			placeAboveDock();
		});
	}

	function show(name, focus) {
		if (!ensureBox()) {
			return;
		}
		view(name);
		box.hidden = false;
		placeAboveDock();
		if (focus) {
			var title = box.querySelector('.consent-title');
			if (title) {
				title.focus();
			}
		}
	}

	function hide() {
		if (!box) {
			return;
		}
		var focusInside = box.contains(document.activeElement);
		box.hidden = true;
		if (trigger && document.contains(trigger)) {
			trigger.focus();
		} else if (focusInside && document.activeElement && document.activeElement.blur) {
			document.activeElement.blur();
		}
		trigger = null;
	}

	function dismiss() {
		if (needsAnswer(read())) {
			rememberDismiss();
		}
		hide();
	}

	function onBoxClick(e) {
		var btn = e.target && e.target.closest ? e.target.closest('[data-consent]') : null;
		if (!btn || !box.contains(btn)) {
			return;
		}
		var action = btn.getAttribute('data-consent');
		if (action === 'accept') {
			save({ analytics: true, marketing: true });
		} else if (action === 'reject' || action === 'ok') {
			save({ analytics: false, marketing: false });
		} else if (action === 'save') {
			var picked = {};
			each('[data-cat] input', function (input) {
				picked[input.name] = input.checked;
			});
			save(picked);
		} else if (action === 'prefs') {
			view('prefs');
			var title = box.querySelector('.consent-title');
			if (title) {
				title.focus();
			}
			queuePlace();
		} else if (action === 'close') {
			dismiss();
		}
	}

	function ensureBox() {
		if (!box) {
			box = document.getElementById('cookie-settings');
		}
		if (box && !bound) {
			bound = true;
			box.addEventListener('click', onBoxClick);
			box.addEventListener('keydown', function (e) {
				if (e.key === 'Escape' || e.key === 'Esc') {
					dismiss();
				}
			});
		}
		return box;
	}

	function init() {
		if (!ensureBox()) {
			return;
		}

		if ((hasOptional || cfg.force) && needsAnswer(read()) && !dismissedThisVisit()) {
			show('intro', false);
		}

		/* ตัวเปิดการ์ดจากนอกการ์ด (footer, หน้า /go/, เนื้อหาหน้า privacy-policy ฯลฯ) */
		document.addEventListener('click', function (e) {
			var el = e.target && e.target.closest ? e.target.closest(TRIGGERS) : null;
			if (!el || box.contains(el)) {
				return;
			}
			e.preventDefault();
			trigger = el;
			show('prefs', true);
		});

		function fromHash() {
			if (window.location.hash === '#cookie-settings') {
				show('prefs', true);
			}
		}
		fromHash();
		/* เปิดจาก URL ที่มี #cookie-settings: เบราว์เซอร์เลื่อนหา fragment หลัง DOM พร้อมและดึงโฟกัสออก จึงคืนโฟกัสให้หัวการ์ดอีกครั้งตอน load */
		if (window.location.hash === '#cookie-settings' && document.readyState !== 'complete') {
			window.addEventListener('load', function () {
				var title = box.querySelector('.consent-title');
				if (!box.hidden && title && !box.contains(document.activeElement)) {
					title.focus();
				}
			}, { once: true });
		}
		window.addEventListener('hashchange', fromHash);
		window.addEventListener('resize', queuePlace);
		window.addEventListener('orientationchange', queuePlace);
		window.addEventListener('scroll', queuePlace, { passive: true });
	}

	/* ---------------- เริ่มทำงาน ---------------- */

	/* ใช้คำตอบที่เก็บไว้ทันที (ไม่ต้องรอ DOM) ให้แท็กที่ได้รับอนุญาตแล้วโหลดเร็วที่สุด */
	var stored = read();
	if (stored) {
		apply(stored);
	}

	window.fenixConsent = {
		version: version,
		tools: { analytics: tools.analytics, marketing: tools.marketing },
		has: function (cat) {
			if (cat === 'necessary') {
				return true;
			}
			return !!(current && (cat === 'analytics' || cat === 'marketing') && current[cat]);
		},
		get: function () {
			return current ? { analytics: !!current.analytics, marketing: !!current.marketing } : null;
		},
		open: function (name) {
			show(name === 'intro' ? 'intro' : 'prefs', true);
		}
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
