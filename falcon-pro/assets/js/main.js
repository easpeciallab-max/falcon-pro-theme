/**
 * FALCON PRO EA · theme scripts
 */
(function () {
	'use strict';

	/* Header scrolled state */
	var header = document.querySelector('.site-header');
	function onScroll() {
		if (header) {
			header.classList.toggle('scrolled', window.scrollY > 10);
		}
	}
	onScroll();
	window.addEventListener('scroll', onScroll, { passive: true });

	/* Mobile navigation */
	var toggle = document.querySelector('.nav-toggle');
	var nav = document.getElementById('site-nav');

	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			var open = document.body.classList.toggle('nav-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		});

		nav.addEventListener('click', function (e) {
			if (e.target.closest('a')) {
				document.body.classList.remove('nav-open');
				toggle.setAttribute('aria-expanded', 'false');
			}
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && document.body.classList.contains('nav-open')) {
				document.body.classList.remove('nav-open');
				toggle.setAttribute('aria-expanded', 'false');
				toggle.focus();
			}
		});
	}

	/* Parent menu items with a submenu toggle the dropdown instead of navigating */
	var parentLinks = document.querySelectorAll('.nav-list .menu-item-has-children > a');
	parentLinks.forEach(function (link) {
		link.setAttribute('aria-haspopup', 'true');
		link.setAttribute('aria-expanded', 'false');
		link.addEventListener('click', function (e) {
			e.preventDefault();
			var li = link.parentNode;
			var willOpen = !li.classList.contains('is-open');

			var siblings = li.parentNode.querySelectorAll('.menu-item-has-children.is-open');
			siblings.forEach(function (other) {
				if (other !== li) {
					other.classList.remove('is-open');
					var otherLink = other.querySelector(':scope > a');
					if (otherLink) {
						otherLink.setAttribute('aria-expanded', 'false');
					}
				}
			});

			li.classList.toggle('is-open', willOpen);
			link.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
		});
	});

	document.addEventListener('click', function (e) {
		if (e.target.closest('.nav-list .menu-item-has-children')) {
			return;
		}
		document.querySelectorAll('.nav-list .menu-item-has-children.is-open').forEach(function (li) {
			li.classList.remove('is-open');
			var openLink = li.querySelector(':scope > a');
			if (openLink) {
				openLink.setAttribute('aria-expanded', 'false');
			}
		});
	});

	/* Mobile app-style bottom navigation */
	var mobileNav = document.querySelector('.mobile-app-nav');
	if (mobileNav) {
		document.body.classList.add('has-mobile-app-nav');

		var currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
		var activeItem = null;
		var activeLength = 0;
		var mobileItems = mobileNav.querySelectorAll('.mobile-app-nav-item');

		mobileItems.forEach(function (item) {
			var itemUrl = new URL(item.href, window.location.origin);
			var itemPath = itemUrl.pathname.replace(/\/+$/, '') || '/';
			var isMatch = itemPath === '/' ? currentPath === '/' : (currentPath === itemPath || currentPath.indexOf(itemPath + '/') === 0);

			if (!item.classList.contains('is-action') && isMatch && itemPath.length >= activeLength) {
				activeItem = item;
				activeLength = itemPath.length;
			}

			item.addEventListener('pointerdown', function () {
				item.classList.add('is-pressing');
			});

			item.addEventListener('pointerup', function () {
				item.classList.remove('is-pressing');
			});

			item.addEventListener('pointerleave', function () {
				item.classList.remove('is-pressing');
			});
		});

		if (activeItem) {
			activeItem.classList.add('is-active');
		}
	}

	/* Reveal on scroll */
	var items = document.querySelectorAll('.reveal');
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if ('IntersectionObserver' in window && !reduced) {
		var io = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('in');
						io.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
		);
		items.forEach(function (el) {
			io.observe(el);
		});
	} else {
		items.forEach(function (el) {
			el.classList.add('in');
		});
	}

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
				rail.scrollBy({ left: dir * step, behavior: 'smooth' });
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
		loadMoreBtn.addEventListener('click', function () {
			var page = parseInt(loadMoreBtn.getAttribute('data-page'), 10) || 1;
			var max = parseInt(loadMoreBtn.getAttribute('data-max'), 10) || 1;
			var next = page + 1;
			if (loadMoreBtn.classList.contains('is-loading') || next > max) {
				return;
			}
			loadMoreBtn.classList.add('is-loading');
			loadMoreBtn.textContent = 'กำลังโหลด…';

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

	/* Cookie consent + gated tracking (โหลด GA/Pixel เฉพาะหลังยอมรับ; ผู้ที่เคยยอมรับโหลดต่อแม้ปิดแบนเนอร์) */
	var trackingCfg = window.fenixTracking || {};
	var cookieBar = document.querySelector('.cookie-consent');

	if (trackingCfg.ga || trackingCfg.pixel || cookieBar) {
		var getConsent = function () {
			var m = document.cookie.match(/(?:^|;\s*)fenix_consent=([^;]+)/);
			return m ? m[1] : '';
		};
		var setConsent = function (value) {
			var d = new Date();
			d.setTime(d.getTime() + 365 * 24 * 60 * 60 * 1000);
			document.cookie = 'fenix_consent=' + value + '; expires=' + d.toUTCString() + '; path=/; SameSite=Lax';
		};
		var trackersLoaded = false;
		var loadTrackers = function () {
			if (trackersLoaded) return;
			trackersLoaded = true;
			if (trackingCfg.ga) {
				window['ga-disable-' + trackingCfg.ga] = false;
				var g = document.createElement('script');
				g.async = true;
				g.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(trackingCfg.ga);
				document.head.appendChild(g);
				window.dataLayer = window.dataLayer || [];
				window.gtag = function () { window.dataLayer.push(arguments); };
				window.gtag('js', new Date());
				window.gtag('config', trackingCfg.ga);
			}
			if (trackingCfg.pixel) {
				!function (f, b, e, v, n, t, s) {
					if (f.fbq) return;
					n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); };
					if (!f._fbq) f._fbq = n;
					n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = [];
					t = b.createElement(e); t.async = !0; t.src = v;
					s = b.getElementsByTagName(e)[0];
					if (s && s.parentNode) { s.parentNode.insertBefore(t, s); } else { (b.head || b.documentElement).appendChild(t); }
				}(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
				window.fbq('init', trackingCfg.pixel);
				window.fbq('track', 'PageView');
			}
		};

		if (getConsent() === 'accepted') {
			loadTrackers();
		}

		if (cookieBar) {
			var sessionDismissed = function () {
				try { return sessionStorage.getItem('fenixConsentDismissed') === '1'; } catch (e) { return false; }
			};
			var setSessionDismissed = function () {
				try { sessionStorage.setItem('fenixConsentDismissed', '1'); } catch (e) {}
			};

			var consent = getConsent();
			if (consent !== 'accepted' && consent !== 'declined' && !sessionDismissed()) {
				cookieBar.classList.add('is-visible');
			}

			var acceptBtn = cookieBar.querySelector('.cookie-accept');
			var declineBtn = cookieBar.querySelector('.cookie-decline');
			var closeBtn = cookieBar.querySelector('.cookie-consent-close');
			if (acceptBtn) {
				acceptBtn.addEventListener('click', function () {
					setConsent('accepted');
					cookieBar.classList.remove('is-visible');
					loadTrackers();
				});
			}
			if (declineBtn) {
				declineBtn.addEventListener('click', function () {
					setConsent('declined');
					cookieBar.classList.remove('is-visible');
					// เคยยอมรับแล้วเปลี่ยนใจ (ผ่านปุ่ม "ตั้งค่าคุกกี้") → หยุดส่งข้อมูลทันทีในหน้านี้
					if (trackersLoaded) {
						if (trackingCfg.ga) window['ga-disable-' + trackingCfg.ga] = true;
						if (typeof window.fbq === 'function') window.fbq('consent', 'revoke');
					}
				});
			}
			if (closeBtn) {
				closeBtn.addEventListener('click', function () {
					setSessionDismissed();
					cookieBar.classList.remove('is-visible');
				});
			}
		}
	}

	/* ปุ่ม "ตั้งค่าคุกกี้" ใน footer · เปิดแบนเนอร์ความยินยอมอีกครั้ง */
	document.querySelectorAll('.cookie-reopen').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var bar = document.querySelector('.cookie-consent');
			if (bar) bar.classList.add('is-visible');
		});
	});

	/* LINE click tracking · ส่ง event แยกตามตำแหน่งปุ่ม (data-line-pos) เฉพาะเมื่อโหลด GA/Pixel แล้ว (ผ่าน consent) */
	document.addEventListener('click', function (e) {
		var link = e.target.closest ? e.target.closest('a[href]') : null;
		if (!link) return;
		var href = link.getAttribute('href') || '';
		var isLine = /(^|\/\/)(lin\.ee|line\.me|page\.line\.me)\//i.test(href);
		if (!isLine && !link.hasAttribute('data-line-pos')) return;
		var pos = link.getAttribute('data-line-pos') || (link.closest('[id]') ? link.closest('[id]').id : 'unknown');
		if (typeof window.gtag === 'function') {
			window.gtag('event', 'line_click', { line_pos: pos, page_path: location.pathname });
		}
		if (typeof window.fbq === 'function') {
			window.fbq('track', 'Contact', { content_name: pos });
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
})();
