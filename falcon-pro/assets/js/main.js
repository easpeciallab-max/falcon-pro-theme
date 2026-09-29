/**
 * FALCON PRO EA · theme scripts (main.js)
 * - โมดูลอื่น (assets/js/<module>.js) โหลดหลังไฟล์นี้ · ใช้ window.fenix.observe / window.fenix.reduced ได้
 * - การขอความยินยอมคุกกี้ + โหลด GA/Pixel อยู่ใน assets/js/consent.js (ไม่ใช่ไฟล์นี้)
 */
(function () {
	'use strict';

	var mq = function (q) {
		return window.matchMedia ? window.matchMedia(q) : { matches: false };
	};
	var reduced = mq('(prefers-reduced-motion: reduce)').matches;
	var cfg = window.fenixChrome || {};

	/* observe(el, cb) · เรียก cb(el) ครั้งเดียวเมื่อเลื่อนมาถึง · ไม่มี IntersectionObserver หรือลดการเคลื่อนไหว = เรียกทันที */
	var observers = {};
	function observe(el, cb, opts) {
		opts = opts || {};
		if (!el || typeof cb !== 'function') {
			return;
		}
		if (!('IntersectionObserver' in window) || reduced) {
			cb(el);
			return;
		}
		var threshold = typeof opts.threshold === 'number' ? opts.threshold : 0.12;
		var key = String(threshold);
		if (!observers[key]) {
			observers[key] = new IntersectionObserver(function (entries, io) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}
					var task = entry.target.fenixTask;
					io.unobserve(entry.target);
					entry.target.fenixTask = null;
					if (typeof task === 'function') {
						task(entry.target);
					}
				});
			}, { threshold: threshold, rootMargin: '0px 0px -40px 0px' });
		}
		el.fenixTask = cb;
		observers[key].observe(el);
	}
	window.fenix = window.fenix || {};
	window.fenix.reduced = reduced;
	window.fenix.observe = observe;

	/* Header scrolled state */
	var header = document.querySelector('.site-header');
	function onScroll() {
		if (header) {
			header.classList.toggle('scrolled', window.scrollY > 10);
		}
	}
	onScroll();
	window.addEventListener('scroll', onScroll, { passive: true });

	/* เมนูหลัก · หัวข้อที่มีเมนูย่อยเปิด/ปิดเมนูย่อย (ทั้ง dropdown เดสก์ท็อปและในลิ้นชักมือถือ) */
	function setParent(li, open) {
		li.classList.toggle('is-open', open);
		var link = li.querySelector(':scope > a');
		if (link) {
			link.setAttribute('aria-expanded', open ? 'true' : 'false');
		}
	}
	function closeParents(except) {
		document.querySelectorAll('.nav-list .menu-item-has-children.is-open').forEach(function (li) {
			if (li !== except) {
				setParent(li, false);
			}
		});
	}

	document.querySelectorAll('.nav-list .menu-item-has-children > a').forEach(function (link) {
		link.setAttribute('aria-haspopup', 'true');
		link.setAttribute('aria-expanded', 'false');
		link.addEventListener('click', function (e) {
			e.preventDefault();
			var li = link.parentNode;
			var willOpen = !li.classList.contains('is-open');
			closeParents(li);
			setParent(li, willOpen);
		});
	});

	document.addEventListener('click', function (e) {
		if (e.target.closest && e.target.closest('.nav-list .menu-item-has-children')) {
			return;
		}
		if (document.body.classList.contains('nav-open')) {
			return; // ในลิ้นชัก เมนูย่อยที่เปิดไว้ค้างได้จนกว่าจะปิดลิ้นชัก
		}
		closeParents(null);
	});

	/* ลิ้นชักเมนูมือถือ (≤960px) · ลิงก์ปกติปิดลิ้นชัก · หัวข้อที่มีเมนูย่อยแค่เปิด/ปิดเมนูย่อย · Esc ปิดแล้วคืนโฟกัสให้ปุ่ม */
	var toggle = document.querySelector('.nav-toggle');
	var nav = document.getElementById('site-nav');
	var drawerMq = mq('(max-width: 960px)');

	function setDrawer(open, refocus) {
		document.body.classList.toggle('nav-open', open);
		toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		if (open) {
			/* เปิดกลุ่มเมนูของหน้าปัจจุบันไว้ให้เห็นทันที */
			nav.querySelectorAll('.nav-list > .menu-item-has-children').forEach(function (li) {
				if (li.classList.contains('current-menu-ancestor') || li.classList.contains('current-menu-parent')) {
					setParent(li, true);
				}
			});
		} else {
			closeParents(null);
			if (refocus) {
				toggle.focus();
			}
		}
	}

	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			setDrawer(!document.body.classList.contains('nav-open'), false);
		});

		nav.addEventListener('click', function (e) {
			var link = e.target.closest ? e.target.closest('a') : null;
			if (!link || !document.body.classList.contains('nav-open')) {
				return;
			}
			var li = link.parentNode;
			if (li && li.classList && li.classList.contains('menu-item-has-children')) {
				return;
			}
			setDrawer(false, false);
		});

		document.addEventListener('keydown', function (e) {
			if (e.key !== 'Escape') {
				return;
			}
			if (document.body.classList.contains('nav-open')) {
				setDrawer(false, true);
				return;
			}
			var open = document.querySelector('.nav-list .menu-item-has-children.is-open > a');
			closeParents(null);
			if (open) {
				open.focus();
			}
		});

		/* ขยายหน้าต่างพ้นโหมดลิ้นชักขณะเปิดอยู่ → ปิดลิ้นชัก */
		var onDrawerMq = function () {
			if (!drawerMq.matches && document.body.classList.contains('nav-open')) {
				setDrawer(false, false);
			}
		};
		if (drawerMq.addEventListener) {
			drawerMq.addEventListener('change', onDrawerMq);
		} else if (drawerMq.addListener) {
			drawerMq.addListener(onDrawerMq);
		}
	}

	/* บาร์ล่างมือถือ · PHP ส่ง is-active / aria-current / --dock-x มากับ HTML แล้ว (ปิด JS ก็ถูก)
	   บล็อกนี้เพิ่มแค่ลูกเล่น: แรงกด, ไฟวิ่งไปช่องที่กด, ย่อบาร์ตอนเลื่อนลง (ย่อ ไม่ซ่อน) · เว้นที่ท้ายหน้าด้วย div.dock-spacer */
	var dock = document.querySelector('.mobile-app-nav.dock');

	if (dock) {
		var dockKeys = Array.prototype.slice.call(dock.querySelectorAll('.dock-key'));
		var dockSeats = Math.max(1, dockKeys.length);
		var dockNarrow = mq('(max-width: 760px)');
		var dockWait = 0;
		var dockLastY = Math.max(0, window.pageYOffset || 0);
		var dockDrift = 0;

		var dockLight = function (seat) {
			if (seat < 0 || seat >= dockSeats) {
				return;
			}
			dock.style.setProperty('--dock-x', ((seat + 0.5) * (100 / dockSeats)).toFixed(3) + '%');
			dock.classList.add('is-lit');
		};

		var dockRest = function () {
			window.clearTimeout(dockWait);
			dock.classList.remove('is-going');
			dockKeys.forEach(function (key) {
				key.classList.remove('is-going');
				key.classList.remove('is-press');
			});
		};

		/* กันเหนียว: PHP หาช่องของหน้านี้ไม่เจอ (เช่นปลั๊กอินเปลี่ยน URL) → หาด้วย path */
		if (!dock.querySelector('.dock-key.is-active')) {
			var here = window.location.pathname.replace(/\/+$/, '') || '/';
			var best = -1;
			var pick = null;
			var pickSeat = -1;
			dockKeys.forEach(function (key, seat) {
				if (key.classList.contains('is-action')) {
					return;
				}
				var path;
				try {
					path = new URL(key.href, window.location.href).pathname.replace(/\/+$/, '') || '/';
				} catch (err) {
					return;
				}
				var hit = path === '/' ? here === '/' : (here === path || here.indexOf(path + '/') === 0);
				if (hit && path.length > best) {
					best = path.length;
					pick = key;
					pickSeat = seat;
				}
			});
			if (pick) {
				pick.classList.add('is-active');
				pick.setAttribute('aria-current', 'page');
				dockLight(pickSeat);
			}
		}

		dockKeys.forEach(function (key) {
			key.addEventListener('pointerdown', function () {
				key.classList.add('is-press');
			});
			['pointerup', 'pointercancel', 'pointerleave', 'blur'].forEach(function (evt) {
				key.addEventListener(evt, function () {
					key.classList.remove('is-press');
				});
			});
		});

		dock.addEventListener('pointerdown', function () {
			dock.classList.remove('is-slim');
			dockDrift = 0;
		});

		dock.addEventListener('click', function (e) {
			var key = e.target && e.target.closest ? e.target.closest('.dock-key') : null;
			if (!key || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button > 0) {
				return;
			}
			if (key.target === '_blank') {
				return;
			}
			var href = key.getAttribute('href') || '';
			if (href === '' || href.charAt(0) === '#' || key.href === window.location.href) {
				return;
			}
			var seat = parseInt(key.getAttribute('data-slot'), 10);
			if (!isNaN(seat)) {
				dockLight(seat);
			}
			key.classList.add('is-going');
			dock.classList.add('is-going');
			window.clearTimeout(dockWait);
			dockWait = window.setTimeout(dockRest, 8000);
		});

		window.addEventListener('pageshow', dockRest);

		var dockScroll = function () {
			if (!dockNarrow.matches || reduced) {
				return;
			}
			var y = Math.max(0, window.pageYOffset || 0);
			var step = y - dockLastY;
			dockLastY = y;
			if (Math.abs(step) < 2) {
				return;
			}
			var toEnd = document.documentElement.scrollHeight - window.innerHeight - y;
			if (y < 220 || toEnd < 140) {
				dockDrift = 0;
				dock.classList.remove('is-slim');
				return;
			}
			dockDrift = (dockDrift > 0) === (step > 0) ? dockDrift + step : step;
			if (dockDrift > 56) {
				dockDrift = 0;
				dock.classList.add('is-slim');
			} else if (dockDrift < -18) {
				dockDrift = 0;
				dock.classList.remove('is-slim');
			}
		};
		window.addEventListener('scroll', dockScroll, { passive: true });

		var dockRecalibrate = function () {
			dockLastY = Math.max(0, window.pageYOffset || 0);
			dockDrift = 0;
			if (!dockNarrow.matches || document.documentElement.scrollHeight - window.innerHeight < 260) {
				dock.classList.remove('is-slim');
			}
		};
		window.addEventListener('resize', dockRecalibrate, { passive: true });
		window.addEventListener('orientationchange', dockRecalibrate);
		if (dockNarrow.addEventListener) {
			dockNarrow.addEventListener('change', dockRecalibrate);
		} else if (dockNarrow.addListener) {
			dockNarrow.addListener(dockRecalibrate);
		}
	}

	/* Reveal on scroll · .reveal (เลื่อนขึ้นจาง) และ .watch (สถานะอย่างเดียว) ได้คลาส .in ครั้งเดียว */
	document.querySelectorAll('.reveal, .watch').forEach(function (el) {
		observe(el, function (target) {
			target.classList.add('in');
		});
	});

	/* Related posts rail · arrow scroll + show controls only when overflowing */
	document.querySelectorAll('.related-section').forEach(function (section) {
		var rail = section.querySelector('.related-rail');
		if (!rail) {
			return;
		}

		function updateControls() {
			var scrollable = rail.scrollWidth > rail.clientWidth + 4;
			section.classList.toggle('is-scrollable', scrollable);
		}

		section.querySelectorAll('.rail-btn').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var dir = parseInt(btn.getAttribute('data-dir'), 10) || 1;
				var card = rail.querySelector('.post-card');
				var step = card ? card.offsetWidth + 18 : rail.clientWidth * 0.8;
				rail.scrollBy({ left: dir * step, behavior: reduced ? 'auto' : 'smooth' });
			});
		});

		updateControls();
		window.addEventListener('resize', updateControls);
	});

	/* Share · copy link to clipboard */
	document.querySelectorAll('.share-copy').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var url = btn.getAttribute('data-url') || window.location.href;
			var done = function () {
				btn.classList.add('is-copied');
				setTimeout(function () {
					btn.classList.remove('is-copied');
				}, 1600);
			};
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(url).then(done).catch(function () {});
			} else {
				var ta = document.createElement('textarea');
				ta.value = url;
				ta.setAttribute('readonly', '');
				ta.style.position = 'absolute';
				ta.style.left = '-9999px';
				document.body.appendChild(ta);
				ta.select();
				try {
					document.execCommand('copy');
					done();
				} catch (e) {}
				document.body.removeChild(ta);
			}
		});
	});

	/* Reading progress bar */
	var progress = document.querySelector('.reading-progress span');
	var progressArticle = document.querySelector('.single-article');
	if (progress && progressArticle) {
		var onProgress = function () {
			var total = progressArticle.offsetHeight - window.innerHeight;
			var scrolled = Math.min(Math.max(-progressArticle.getBoundingClientRect().top, 0), Math.max(total, 0));
			progress.style.width = (total > 0 ? (scrolled / total) * 100 : 0) + '%';
		};
		window.addEventListener('scroll', onProgress, { passive: true });
		window.addEventListener('resize', onProgress);
		onProgress();
	}

	/* Load more posts (AJAX) */
	var loadMoreBtn = document.querySelector('.load-more-btn');
	if (loadMoreBtn && window.fenixLoadMore) {
		var loadMoreLabel = loadMoreBtn.textContent.trim();
		var loadingText = loadMoreBtn.getAttribute('data-loading') || cfg.loadingText || '…';
		loadMoreBtn.addEventListener('click', function () {
			var page = parseInt(loadMoreBtn.getAttribute('data-page'), 10) || 1;
			var max = parseInt(loadMoreBtn.getAttribute('data-max'), 10) || 1;
			var next = page + 1;
			if (loadMoreBtn.classList.contains('is-loading') || next > max) {
				return;
			}
			loadMoreBtn.classList.add('is-loading');
			loadMoreBtn.textContent = loadingText;

			var data = new FormData();
			data.append('action', 'fenix_load_more');
			data.append('nonce', window.fenixLoadMore.nonce);
			data.append('page', next);
			data.append('query', loadMoreBtn.getAttribute('data-query') || '');

			fetch(window.fenixLoadMore.ajaxUrl, {
				method: 'POST',
				body: data,
				credentials: 'same-origin'
			})
				.then(function (res) { return res.text(); })
				.then(function (html) {
					var grid = document.querySelector('.posts-grid');
					if (grid && html.trim()) {
						grid.insertAdjacentHTML('beforeend', html);
					}
					loadMoreBtn.setAttribute('data-page', String(next));
					loadMoreBtn.classList.remove('is-loading');
					loadMoreBtn.textContent = loadMoreLabel;
					if (next >= max) {
						var wrap = loadMoreBtn.closest('.load-more');
						if (wrap) {
							wrap.parentNode.removeChild(wrap);
						}
					}
				})
				.catch(function () {
					loadMoreBtn.classList.remove('is-loading');
					loadMoreBtn.textContent = loadMoreLabel;
				});
		});
	}

	/* นับคลิก LINE · แยก LINE OA (line_click) กับ OpenChat (openchat_click)
	   ส่งเฉพาะเมื่อ gtag / fbq ถูกโหลดแล้ว (หลังผู้ใช้ยินยอม ผ่าน consent.js) · Meta Contact เฉพาะ LINE OA
	   ตำแหน่งปุ่มอ่านจาก data-line-pos ถ้าไม่มีใช้ id ของกล่องที่ใกล้ที่สุด · data-line-pkg = ชื่อแพ็กเกจ (ถ้ามี) */
	document.addEventListener('click', function (e) {
		var link = e.target && e.target.closest ? e.target.closest('a[href]') : null;
		if (!link) {
			return;
		}
		var url;
		try {
			url = new URL(link.getAttribute('href') || '', window.location.href);
		} catch (err) {
			return;
		}
		var host = url.hostname.replace(/^www\./, '').toLowerCase();
		var isLine = host === 'line.me' || host === 'lin.ee' || host === 'liff.line.me' || host === 'page.line.me' || url.protocol === 'line:';
		if (!isLine) {
			return;
		}
		var isOpenChat = (host === 'line.me' && /^\/(?:R\/)?ti\/g2\//i.test(url.pathname)) || /openchat/i.test(link.getAttribute('data-line-pos') || '');
		var copy = link.cloneNode(true);
		Array.prototype.forEach.call(copy.querySelectorAll('.sr-only'), function (node) {
			node.parentNode.removeChild(node);
		});
		var box = link.parentNode && link.parentNode.closest ? link.parentNode.closest('[id]') : null;
		var payload = {
			link_url: url.href,
			link_text: (link.getAttribute('aria-label') || copy.textContent || 'LINE').replace(/\s+/g, ' ').trim(),
			line_pos: link.getAttribute('data-line-pos') || (box ? box.id : ''),
			page_path: window.location.pathname,
			page_title: document.title
		};
		if (link.getAttribute('data-line-pkg')) {
			payload.package_name = link.getAttribute('data-line-pkg');
		}
		/* เช็กความยินยอม ณ ตอนคลิก (consent.js โหลดหลังไฟล์นี้) · กันปลั๊กอินอื่นที่ประกาศ gtag/fbq ไว้ก่อนผู้ใช้ยินยอม */
		var consent = window.fenixConsent && typeof window.fenixConsent.has === 'function' ? window.fenixConsent : null;
		if (typeof window.gtag === 'function' && (!consent || consent.has('analytics'))) {
			window.gtag('event', isOpenChat ? 'openchat_click' : 'line_click', payload);
		}
		if (!isOpenChat && typeof window.fbq === 'function' && (!consent || consent.has('marketing'))) {
			window.fbq('track', 'Contact', { content_name: payload.line_pos || 'line' });
		}
	});

	/* สารบัญ (หน้าคู่มือ) · ไฮไลต์หัวข้อที่กำลังอ่าน */
	var tocLinks = document.querySelectorAll('.longform-toc a[href^="#"]');
	if (tocLinks.length && 'IntersectionObserver' in window) {
		var tocMap = {};
		tocLinks.forEach(function (a) { tocMap[decodeURIComponent(a.getAttribute('href').slice(1))] = a; });
		var tocObs = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				tocLinks.forEach(function (a) { a.classList.remove('is-active'); });
				var link = tocMap[entry.target.id];
				if (link) link.classList.add('is-active');
			});
		}, { rootMargin: '-20% 0px -70% 0px' });
		Object.keys(tocMap).forEach(function (id) {
			var h = document.getElementById(id);
			if (h) tocObs.observe(h);
		});
	}

	/* เครื่องคำนวณ [falcon_calc] · Lot size + Drawdown recovery */
	var num = function (el) {
		var v = parseFloat(el && el.value);
		return isFinite(v) ? v : 0;
	};
	var fmt = function (v, d) {
		return v.toLocaleString('th-TH', { minimumFractionDigits: d, maximumFractionDigits: d });
	};
	document.querySelectorAll('.calc[data-calc="lot"]').forEach(function (box) {
		var q = function (k) { return box.querySelector('[data-in="' + k + '"]'); };
		var preset = q('preset');
		var run = function () {
			var money = num(q('balance')) * num(q('risk')) / 100;
			var perLot = num(q('sl')) * num(q('pipval'));
			var step = num(q('step'));
			if (!(step > 0)) step = 0.01;
			var lot = perLot > 0 ? Math.floor((money / perLot) / step + 1e-9) * step : 0;
			var decimals = Math.max(0, Math.min(3, Math.ceil(-Math.log10(step))));
			box.querySelector('[data-out="money"]').textContent = fmt(money, 2) + ' USD';
			box.querySelector('[data-out="lot"]').textContent = perLot > 0 ? fmt(lot, decimals) + ' lot' : '–';
			box.classList.toggle('is-under', perLot > 0 && lot < step);
		};
		if (preset) {
			preset.addEventListener('change', function () {
				if (preset.value !== 'custom') q('pipval').value = preset.value;
				run();
			});
		}
		box.addEventListener('input', run);
		run();
	});
	document.querySelectorAll('.calc[data-calc="drawdown"]').forEach(function (box) {
		var q = function (k) { return box.querySelector('[data-in="' + k + '"]'); };
		var run = function () {
			var dd = Math.min(99, Math.max(0, num(q('dd')))) / 100;
			var bal = num(q('balance'));
			var gain = dd < 1 ? dd / (1 - dd) * 100 : 0;
			box.querySelector('[data-out="gain"]').textContent = fmt(gain, 1) + ' %';
			box.querySelector('[data-out="left"]').textContent = bal > 0 ? fmt(bal * (1 - dd), 2) + ' USD' : '–';
			var bar = box.querySelector('[data-out="bar"]');
			if (bar) bar.style.width = Math.min(100, gain / 2) + '%';
		};
		box.addEventListener('input', run);
		run();
	});

	/* Tabs (role=tablist) · คลิก/ลูกศรซ้ายขวาเพื่อสลับ */
	document.querySelectorAll('[data-tabs]').forEach(function (wrap) {
		var tabs = Array.prototype.slice.call(wrap.querySelectorAll('[role="tab"]'));
		var select = function (tab, focus) {
			tabs.forEach(function (t) {
				var on = t === tab;
				t.setAttribute('aria-selected', on ? 'true' : 'false');
				t.tabIndex = on ? 0 : -1;
				var panel = document.getElementById(t.getAttribute('aria-controls'));
				if (panel) panel.hidden = !on;
			});
			if (focus) tab.focus();
		};
		tabs.forEach(function (tab, i) {
			tab.addEventListener('click', function () { select(tab, false); });
			tab.addEventListener('keydown', function (e) {
				if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
				e.preventDefault();
				var next = tabs[(i + (e.key === 'ArrowRight' ? 1 : tabs.length - 1)) % tabs.length];
				select(next, true);
			});
		});
	});

	/* Lightbox · a.lightbox (จัดกลุ่มด้วย data-group) */
	var lbLinks = document.querySelectorAll('a.lightbox');
	if (lbLinks.length) {
		var arrow = '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
		var lb = document.createElement('div');
		lb.className = 'lb';
		lb.setAttribute('role', 'dialog');
		lb.setAttribute('aria-modal', 'true');
		lb.setAttribute('aria-label', 'ภาพขนาดใหญ่');
		lb.innerHTML = '<figure><img alt=""><figcaption></figcaption></figure>' +
			'<button type="button" class="lb-close" aria-label="ปิด"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg></button>' +
			'<button type="button" class="lb-prev" aria-label="ภาพก่อนหน้า">' + arrow + '</button>' +
			'<button type="button" class="lb-next" aria-label="ภาพถัดไป">' + arrow + '</button>';
		document.body.appendChild(lb);
		var lbImg = lb.querySelector('img');
		var lbCap = lb.querySelector('figcaption');
		var group = [];
		var idx = 0;
		var lastFocus = null;
		var show = function () {
			var a = group[idx];
			lbImg.src = a.getAttribute('href');
			var img = a.querySelector('img');
			lbImg.alt = img ? img.alt : '';
			lbCap.textContent = a.getAttribute('data-caption') || '';
			var multi = group.length > 1;
			lb.querySelector('.lb-prev').hidden = !multi;
			lb.querySelector('.lb-next').hidden = !multi;
		};
		var close = function () {
			lb.classList.remove('is-open');
			document.body.style.overflow = '';
			if (lastFocus) lastFocus.focus();
		};
		lbLinks.forEach(function (a) {
			a.addEventListener('click', function (e) {
				e.preventDefault();
				var g = a.getAttribute('data-group');
				group = g ? Array.prototype.slice.call(document.querySelectorAll('a.lightbox[data-group="' + g + '"]')) : [a];
				idx = Math.max(0, group.indexOf(a));
				lastFocus = a;
				show();
				lb.classList.add('is-open');
				document.body.style.overflow = 'hidden';
				// รอให้ visibility เปลี่ยนก่อน แล้วค่อยย้ายโฟกัส (ปุ่มที่ยัง hidden รับโฟกัสไม่ได้)
				setTimeout(function () { lb.querySelector('.lb-close').focus(); }, 30);
			});
		});
		lb.addEventListener('click', function (e) {
			if (e.target === lb) close();
		});
		lb.querySelector('.lb-close').addEventListener('click', close);
		lb.querySelector('.lb-prev').addEventListener('click', function () { idx = (idx + group.length - 1) % group.length; show(); });
		lb.querySelector('.lb-next').addEventListener('click', function () { idx = (idx + 1) % group.length; show(); });
		document.addEventListener('keydown', function (e) {
			if (!lb.classList.contains('is-open')) return;
			if (e.key === 'Escape') close();
			if (e.key === 'Tab') {
				var btns = Array.prototype.filter.call(lb.querySelectorAll('button'), function (b) { return !b.hidden; });
				var at = btns.indexOf(document.activeElement);
				e.preventDefault();
				var nextBtn = btns[(at + (e.shiftKey ? btns.length - 1 : 1) + btns.length) % btns.length];
				if (nextBtn) nextBtn.focus();
			}
			if (e.key === 'ArrowRight' && group.length > 1) { idx = (idx + 1) % group.length; show(); }
			if (e.key === 'ArrowLeft' && group.length > 1) { idx = (idx + group.length - 1) % group.length; show(); }
		});
	}

	/* Footer · นาฬิกาเวลาไทย (นาทีละครั้ง) · ไม่มี JS = ซ่อนทั้งแถว (CSS) */
	var clockEl = document.querySelector('[data-clock-out]');
	if (clockEl) {
		try {
			var clockFmt = new Intl.DateTimeFormat('th-TH', { timeZone: clockEl.getAttribute('data-tz') || 'Asia/Bangkok', hour: '2-digit', minute: '2-digit', hour12: false });
			var tick = function () {
				var now = new Date();
				clockEl.textContent = clockFmt.format(now);
				clockEl.setAttribute('datetime', now.toISOString());
			};
			tick();
			setTimeout(function () {
				tick();
				setInterval(tick, 60000);
			}, 60000 - (Date.now() % 60000));
		} catch (e) {}
	}
})();
