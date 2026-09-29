/**
 * FALCON PRO EA · หน้าแรก (home.js)
 * โหลดหลัง main.js ทุกหน้า แต่ทำงานเฉพาะเมื่อมี main.home-v3 (front-page.php)
 *
 * - หน้าจอบันทึก EA (บท 03): พิมพ์ทีละบรรทัดเมื่อเห็นอย่างน้อย 40% · เล่นสูงสุด data-plays รอบ (ค่าเริ่มต้น 2)
 *   จองความสูงของข้อความเต็มก่อนพิมพ์ (ไม่มี layout shift) · แท็บถูกซ่อน = หยุดและคืนข้อความเต็ม
 *   ตัดคำไทยเป็น grapheme (Intl.Segmenter) สระ/วรรณยุกต์จึงไม่โผล่ครึ่งตัว
 * - เส้นขีดฆ่าบท 02: ใส่ .is-on เมื่อรายการเลื่อนเข้ามาในจอ
 * - รางเลขบท / แถบความคืบหน้า: เลขบทปัจจุบัน + aria-current · ตั้ง --p เมื่อเบราว์เซอร์ไม่มี scroll timeline
 * - FAQ: เปิดได้ทีละข้อในเบราว์เซอร์ที่ยังไม่รองรับ <details name>
 *
 * ลดการเคลื่อนไหว (prefers-reduced-motion) = ทุกอย่างอยู่สถานะจบทันที
 * ไม่มี JS / ไฟล์นี้โหลดไม่ขึ้น = CSS แสดงสถานะจบ (สถานะก่อนแอนิเมชันทั้งหมดอยู่ใต้ .hm-js ที่ไฟล์นี้ใส่ให้)
 */
(function () {
	'use strict';

	var root = document.querySelector('.home-v3');
	if (!root) {
		return;
	}

	var F = window.fenix || {};
	var mq = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;
	var reduced = typeof F.reduced === 'boolean' ? F.reduced : !!(mq && mq.matches);
	var hasIO = 'IntersectionObserver' in window;
	var each = function (list, fn) {
		Array.prototype.forEach.call(list || [], fn);
	};
	var raf = window.requestAnimationFrame
		? function (fn) { return window.requestAnimationFrame(fn); }
		: function (fn) { return window.setTimeout(function () { fn(Date.now()); }, 16); };

	root.classList.add('hm-js');

	/* เรียก cb ทุกครั้งที่ el เข้ามาในจอตามเงื่อนไข · คืน { stop } */
	function watch(el, opts, cb) {
		opts = opts || {};
		var min = typeof opts.threshold === 'number' ? opts.threshold : 0;
		if (!hasIO) {
			cb();
			return { stop: function () {} };
		}
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting && entry.intersectionRatio >= min - 0.001) {
					cb();
				}
			});
		}, { threshold: min, rootMargin: opts.rootMargin || '0px' });
		io.observe(el);
		return {
			stop: function () {
				io.disconnect();
			}
		};
	}

	/* ---------- บท 02 · เส้นขีดฆ่าหัวข้อปัญหา ---------- */
	var diag = root.querySelector('[data-hm-strike]');
	if (diag) {
		if (reduced || !hasIO) {
			diag.classList.add('is-on');
		} else {
			var diagWatch = watch(diag, { threshold: 0, rootMargin: '0px 0px -28% 0px' }, function () {
				diag.classList.add('is-on');
				diagWatch.stop();
			});
		}
	}

	/* ---------- บท 03 · หน้าจอบันทึก EA (พิมพ์ทีละบรรทัด) ---------- */
	var segmenter = null;
	try {
		segmenter = new Intl.Segmenter('th', { granularity: 'grapheme' });
	} catch (e) {
		segmenter = null;
	}
	function graphemes(str) {
		return segmenter ? Array.from(segmenter.segment(str), function (s) { return s.segment; }) : Array.from(str);
	}

	function typewrite(out, lines, cps, pause, onDone) {
		var stopped = false;
		var li = 0;
		var ci = 0;
		var chars = graphemes(lines[0].t);
		var el = null;
		var budget = 0;
		var last = 0;
		var waitUntil = 0;

		function open(i) {
			if (i) {
				out.appendChild(document.createTextNode('\n'));
			}
			el = document.createElement('span');
			el.className = 'hm-log-line' + (lines[i].c ? ' ' + lines[i].c : '');
			out.appendChild(el);
		}

		out.textContent = '';
		out.classList.add('is-typing');
		open(0);

		function frame(now) {
			if (stopped) {
				return;
			}
			budget += last ? Math.min(now - last, 250) * cps / 1000 : 0;
			last = now;
			if (now >= waitUntil) {
				var moved = false;
				while (budget >= 1 && ci < chars.length) {
					ci += 1;
					budget -= 1;
					moved = true;
				}
				if (moved) {
					el.textContent = chars.slice(0, ci).join('');
				}
				if (ci >= chars.length) {
					li += 1;
					if (li >= lines.length) {
						out.classList.remove('is-typing');
						if (onDone) {
							onDone();
						}
						return;
					}
					chars = graphemes(lines[li].t);
					ci = 0;
					budget = 0;
					waitUntil = now + pause;
					open(li);
				}
			}
			raf(frame);
		}
		raf(frame);

		return {
			stop: function () {
				stopped = true;
				out.classList.remove('is-typing');
			}
		};
	}

	var log = root.querySelector('[data-hm-log]');
	var logOut = log ? log.querySelector('[data-hm-log-out]') : null;
	var logLines = [];
	if (log) {
		try {
			logLines = JSON.parse(log.getAttribute('data-lines') || '[]');
		} catch (e) {
			logLines = [];
		}
		logLines = (Array.isArray(logLines) ? logLines : []).filter(function (l) {
			return l && typeof l.t === 'string' && l.t !== '';
		});
	}
	if (logOut && logLines.length && !reduced && hasIO) {
		var fullHTML = logOut.innerHTML;
		var plays = 0;
		var maxPlays = parseInt(log.getAttribute('data-plays'), 10) || 2;
		var typing = null;
		var restore = function () {
			if (typing) {
				typing.stop();
				typing = null;
			}
			logOut.innerHTML = fullHTML;
		};
		var logWatch = watch(log, { threshold: 0.4 }, function () {
			if (plays >= maxPlays) {
				return;
			}
			plays += 1;
			if (plays >= maxPlays) {
				logWatch.stop();
			}
			/* จองความสูงของข้อความเต็มก่อนเริ่มพิมพ์ (วัดใหม่ทุกรอบ รองรับการหมุนจอ/ปรับขนาด) */
			restore();
			logOut.style.minHeight = '';
			logOut.style.minHeight = logOut.offsetHeight + 'px';
			typing = typewrite(logOut, logLines, 36, 320, function () {
				typing = null;
				/* พิมพ์ครบแล้ว = ข้อความเต็มอยู่ในกล่อง ปล่อยความสูงคืน (รองรับการปรับขนาดจอภายหลัง) */
				logOut.style.minHeight = '';
			});
		});
		document.addEventListener('visibilitychange', function () {
			if (document.hidden && typing) {
				restore();
				logOut.style.minHeight = '';
			}
		});
	}

	/* ---------- รางเลขบท · --p (ความคืบหน้าการเลื่อน) เมื่อไม่มี CSS scroll timeline หรือผู้ใช้ลดการเคลื่อนไหว ---------- */
	var html = document.documentElement;
	var hasRail = root.querySelector('.hm-rail, .hm-progress');
	var hasTimeline = !!(window.CSS && CSS.supports && CSS.supports('animation-timeline: scroll()'));
	if (hasRail && (!hasTimeline || reduced)) {
		var pQueued = false;
		var setP = function () {
			pQueued = false;
			var max = html.scrollHeight - window.innerHeight;
			var y = window.pageYOffset || html.scrollTop || 0;
			html.style.setProperty('--p', max > 0 ? String(Math.min(1, Math.max(0, y / max))) : '0');
		};
		var queueP = function () {
			if (!pQueued) {
				pQueued = true;
				raf(setP);
			}
		};
		window.addEventListener('scroll', queueP, { passive: true });
		window.addEventListener('resize', queueP);
		queueP();
	}

	/* ---------- รางเลขบท · เลขบทปัจจุบัน + aria-current ---------- */
	var railN = root.querySelector('[data-rail-n]');
	var railLinks = root.querySelectorAll('[data-rail-link]');
	var chapters = root.querySelectorAll('section[data-chapter]');
	if (railN && chapters.length && hasIO) {
		var current = railN.textContent;
		var setCurrent = function (n) {
			if (n === current) {
				return;
			}
			current = n;
			railN.textContent = n;
			if (!reduced) {
				railN.removeAttribute('data-tick');
				raf(function () {
					railN.setAttribute('data-tick', '');
				});
			}
			each(railLinks, function (a) {
				if (a.getAttribute('data-rail-link') === n) {
					a.setAttribute('aria-current', 'true');
				} else {
					a.removeAttribute('aria-current');
				}
			});
		};
		var chapterIO = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					setCurrent(entry.target.getAttribute('data-chapter') || '00');
				}
			});
		}, { rootMargin: '-45% 0px -45% 0px', threshold: 0 });
		each(chapters, function (section) {
			chapterIO.observe(section);
		});
	}

	/* ---------- FAQ · เปิดทีละข้อ (สำรองสำหรับเบราว์เซอร์ที่ยังไม่รองรับ <details name>) ---------- */
	var questions = root.querySelectorAll('details.hm-q[name]');
	if (questions.length && typeof window.HTMLDetailsElement !== 'undefined' && !('name' in window.HTMLDetailsElement.prototype)) {
		each(questions, function (item) {
			item.addEventListener('toggle', function () {
				if (!item.open) {
					return;
				}
				each(questions, function (other) {
					if (other !== item && other.open && other.getAttribute('name') === item.getAttribute('name')) {
						other.open = false;
					}
				});
			});
		});
	}
})();
