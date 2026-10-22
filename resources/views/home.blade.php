<!DOCTYPE html>
<html lang="{{ $locale ?? 'sk' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('site.meta.title') }}</title>
    <meta name="description" content="{{ __('site.meta.desc') }}">
    <style>
        :root {
            --bg: #fcfbf8;
            --surface: #ffffff;
            --surface-2: #f1f2ec;
            --wood: #f5efe6;
            --wood-strong: #c9a477;
            --border: #dddfd5;
            --text: #20372d;
            --muted: #5c685f;
            --subtle: #98a2b3;
            --accent: #285c46;
            --accent-strong: #24564a;
            --accent-soft: #e7f1ec;
            --dark: #10241d;
            --ring: rgba(47, 107, 85, 0.35);
            --radius: 16px;
            --shadow-sm: 0 1px 2px rgba(16, 24, 40, 0.06);
            --shadow: 0 1px 3px rgba(16, 24, 40, 0.08), 0 6px 18px rgba(16, 24, 40, 0.06);
            --shadow-lg: 0 24px 60px rgba(16, 36, 29, 0.16);
            --max: 1180px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; scroll-padding-top: 80px; }
        body {
            font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: var(--text);
            background: var(--bg);
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }
        a:focus-visible, button:focus-visible, summary:focus-visible { outline: 3px solid var(--ring); outline-offset: 3px; border-radius: 8px; }
        .container { width: 100%; max-width: var(--max); margin: 0 auto; padding: 0 24px; }
        .icon {
            flex: none;
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--accent);
        }
        h1, h2, h3 { letter-spacing: -0.025em; line-height: 1.15; }
        h2 { font-size: clamp(30px, 3.6vw, 44px); font-weight: 700; }
        .section { padding: 88px 0; }
        .section-head { max-width: 680px; margin-bottom: 56px; }
        .section-head h2 { margin: 10px 0 16px; }
        .section-head p { font-size: 18px; color: var(--muted); }
        .center { margin-left: auto; margin-right: auto; text-align: center; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 22px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 12px;
            border: 1px solid transparent;
            transition: background 0.15s, color 0.15s, border-color 0.15s, transform 0.15s, box-shadow 0.15s;
        }
        .btn .icon { width: 18px; height: 18px; transition: transform 0.15s; }
        .btn:hover .icon { transform: translateX(3px); }
        .btn-primary { color: #fff; background: var(--accent); box-shadow: 0 4px 12px rgba(47, 107, 85, 0.12); }
        .btn-primary:hover { background: var(--accent-strong); transform: translateY(-1px); }
        .btn-ghost { color: var(--text); background: var(--surface); border-color: var(--border); }
        .btn-ghost:hover { border-color: var(--text); }
        .btn-light { color: var(--dark); background: #fff; }
        .btn-light:hover { background: var(--wood); transform: translateY(-1px); }
        .btn-sm { padding: 10px 18px; font-size: 15px; }

        .nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: saturate(180%) blur(12px);
            -webkit-backdrop-filter: saturate(180%) blur(12px);
            border-bottom: 1px solid transparent;
            transition: border-color 0.2s;
        }
        .nav.is-scrolled { border-bottom-color: var(--border); }
        .nav-inner { display: flex; align-items: center; justify-content: space-between; height: 72px; gap: 24px; }
        .brand { display: inline-flex; align-items: center; gap: 10px; font-size: 19px; font-weight: 700; letter-spacing: -0.02em; }
        .brand-mark {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            color: #fff;
            background: var(--accent);
            border-radius: 10px;
        }
        .brand-mark .icon { width: 20px; height: 20px; stroke-width: 2; }
        .nav-links { display: flex; align-items: center; gap: 20px; font-size: 14px; font-weight: 500; color: var(--muted); }
        .nav-links a:hover { color: var(--text); }

        .lang-switch {
            display: inline-flex;
            align-items: center;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 2px;
            gap: 2px;
        }
        .lang-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 700;
            color: var(--muted);
            text-decoration: none;
            border-radius: 999px;
            transition: all 0.15s ease;
        }
        .lang-link:hover { color: var(--text); }
        .lang-link.is-active {
            background: #fff;
            color: var(--text);
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.08);
        }
        .footer-lang {
            margin-top: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        .footer-lang a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }
        .footer-lang a:hover { background: rgba(255, 255, 255, 0.16); }
        .footer-lang a.is-active { background: var(--accent); }

        .hero { position: relative; padding: 72px 0 80px; overflow: hidden; background: linear-gradient(120deg, #fcfbf8 35%, #f0f2e9); }
        .hero::before {
            content: '';
            position: absolute;
            inset: -20% -10% auto auto;
            width: 760px;
            height: 760px;
            background: radial-gradient(circle, rgba(47, 107, 85, 0.12), transparent 65%);
            pointer-events: none;
        }
        .hero-grid { position: relative; display: grid; grid-template-columns: 1.1fr 1fr; align-items: center; gap: 40px; }
        .hero h1 { margin: 22px 0 24px; font-size: clamp(36px, 4.3vw, 56px); font-weight: 650; letter-spacing: -0.045em; line-height: 1.1; }
        .hero h1 em { font-style: normal; color: var(--accent); }
        .hero-lead { max-width: 540px; font-size: 19px; color: var(--muted); }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 34px; }
        .checks { display: flex; flex-wrap: wrap; gap: 10px 22px; margin-top: 30px; list-style: none; font-size: 14px; font-weight: 500; color: var(--muted); }
        .checks li { display: inline-flex; align-items: center; gap: 8px; }
        .checks .icon { width: 18px; height: 18px; color: var(--accent); stroke-width: 2.4; }
        .badge-flag {
            display: inline-block;
            width: 18px;
            height: 12px;
            vertical-align: -1px;
            border-radius: 2px;
            box-shadow: 0 0 0 1px rgba(16, 24, 40, 0.15);
            overflow: hidden;
            flex-shrink: 0;
        }

        .hero-visual {
            position: relative;
            aspect-ratio: 5 / 4;
            background: linear-gradient(160deg, #f7f3ec 0%, #e9efeb 100%);
            border: 1px solid #d8dfd0;
            border-radius: 120px 24px 24px 24px;
            box-shadow: 0 20px 60px rgba(36, 54, 40, 0.08);
            overflow: hidden;
        }
        .hero-photo { min-width: 0; }
        .hero-visual img { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center 48%; }
        .photo-label { position: absolute; inset: auto 0 0; padding: 52px 24px 22px; color: #fff; background: linear-gradient(transparent, rgba(16, 36, 29, 0.88)); }
        .photo-label strong { display: block; font-size: 20px; letter-spacing: -0.02em; }
        .photo-label span { font-size: 13px; color: #e5ece7; }
        .photo-credit { margin-top: 12px; font-size: 12px; color: var(--muted); line-height: 1.6; }
        .photo-credit a { text-decoration: underline; text-underline-offset: 3px; }
        .photo-credit a:hover { color: var(--accent); }

        .stats { border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); background: var(--surface-2); }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); }
        .stat { padding: 34px 24px; }
        .stat + .stat { border-left: 1px solid var(--border); }
        .stat strong { display: block; font-size: clamp(26px, 2.8vw, 36px); font-weight: 800; letter-spacing: -0.03em; color: var(--accent); }
        .stat span { font-size: 15px; color: var(--muted); }
        .source { padding: 0 24px 18px; font-size: 12px; color: var(--subtle); }

        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
        .about-text p { margin-bottom: 18px; font-size: 18px; color: var(--muted); }
        .about-text p strong { color: var(--text); font-weight: 600; }
        .flow { display: flex; flex-direction: column; gap: 14px; list-style: none; }
        .flow li {
            position: relative;
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 20px 22px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }
        .flow li + li::before {
            content: '';
            position: absolute;
            top: -15px;
            left: 49px;
            width: 2px;
            height: 15px;
            background: var(--border);
        }
        .flow-icon {
            display: grid;
            place-items: center;
            flex: none;
            width: 54px;
            height: 54px;
            color: var(--accent);
            background: var(--accent-soft);
            border-radius: 14px;
        }
        .flow h3 { font-size: 17px; }
        .flow p { font-size: 15px; color: var(--muted); }

        .features { background: var(--surface-2); }
        .feature-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
        .feature {
            padding: 26px 24px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        }
        .feature:hover { transform: translateY(-3px); box-shadow: var(--shadow); border-color: transparent; }
        .feature-icon {
            display: grid;
            place-items: center;
            width: 46px;
            height: 46px;
            margin-bottom: 18px;
            color: var(--accent);
            background: var(--accent-soft);
            border-radius: 12px;
        }
        .feature h3 { margin-bottom: 8px; font-size: 18px; }
        .feature p { font-size: 15px; color: var(--muted); }
        .feature .metric { display: inline-block; margin-bottom: 8px; padding: 2px 10px; font-size: 13px; font-weight: 700; color: var(--accent-strong); background: var(--wood); border-radius: 999px; }

        .made { position: relative; color: #e6efe9; background: var(--dark); overflow: hidden; }
        .made::after {
            content: '';
            position: absolute;
            right: -160px;
            top: -160px;
            width: 520px;
            height: 520px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(201, 164, 119, 0.22), transparent 65%);
            pointer-events: none;
        }
        .made-grid { position: relative; z-index: 1; display: grid; grid-template-columns: 0.9fr 1.1fr; gap: 72px; align-items: start; }
        .made .eyebrow { color: var(--wood-strong); }
        .made h2 { margin: 10px 0 18px; color: #fff; }
        .made-lead { font-size: 18px; color: #b8c9c0; }
        .made-lead + .btn { margin-top: 30px; }
        .made-list { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; list-style: none; }
        .made-list li {
            padding: 24px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: var(--radius);
        }
        .made-list .icon { width: 26px; height: 26px; margin-bottom: 14px; color: var(--wood-strong); }
        .made-list h3 { margin-bottom: 6px; font-size: 17px; color: #fff; }
        .made-list p { font-size: 15px; color: #a9bcb2; }

        .steps { display: grid; grid-template-columns: repeat(5, 1fr); gap: 18px; list-style: none; counter-reset: step; }
        .step { position: relative; padding-top: 58px; }
        .step::before {
            counter-increment: step;
            content: counter(step);
            position: absolute;
            top: 0;
            left: 0;
            display: grid;
            place-items: center;
            width: 40px;
            height: 40px;
            font-weight: 700;
            color: #fff;
            background: var(--accent);
            border-radius: 50%;
            box-shadow: 0 0 0 6px var(--accent-soft);
        }
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 58px;
            right: -10px;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-soft), var(--border));
        }
        .step h3 { margin-bottom: 8px; font-size: 18px; }
        .step p { font-size: 15px; color: var(--muted); }
        .step a { color: var(--accent); font-weight: 600; }

        .cta-band {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            align-items: center;
            gap: 48px;
            padding: 56px;
            background: linear-gradient(135deg, var(--wood) 0%, #eaf1ec 100%);
            border-radius: 28px;
        }
        .cta-band h2 { margin: 10px 0 14px; }
        .cta-band p { margin-bottom: 28px; font-size: 18px; color: var(--muted); }
        .cta-band ul { display: flex; flex-wrap: wrap; gap: 8px 20px; margin: -10px 0 28px; list-style: none; font-size: 15px; font-weight: 500; }
        .cta-band li { display: inline-flex; align-items: center; gap: 8px; }
        .cta-band li .icon { width: 18px; height: 18px; color: var(--accent); stroke-width: 2.4; }
        .strip {
            padding: 28px;
            background: #fff;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
        }
        .strip-head { display: flex; justify-content: space-between; margin-bottom: 18px; font-size: 13px; font-weight: 600; color: var(--muted); }
        .strip-row { display: flex; gap: 6px; }
        .strip-row .seg {
            flex: 1;
            display: grid;
            place-items: center;
            aspect-ratio: 3 / 4;
            color: #475467;
            background: var(--surface-2);
            border-radius: 10px;
        }
        .strip-row .seg.is-end { background: var(--accent-soft); color: var(--accent); }
        .strip-row svg { width: 74%; height: auto; overflow: visible; }
        .strip-row .wall { fill: #fff; stroke: currentColor; stroke-width: 2; stroke-linejoin: round; }
        .strip-row .glass { fill: #cfe3f3; stroke: currentColor; stroke-width: 2; }
        .strip-foot { display: flex; justify-content: space-between; align-items: baseline; margin-top: 20px; padding-top: 18px; border-top: 1px solid var(--border); }
        .strip-foot span { font-size: 14px; color: var(--muted); }
        .strip-foot strong { font-size: 24px; letter-spacing: -0.02em; }

        .maker { background: linear-gradient(180deg, var(--wood) 0%, #fff 100%); }
        .maker h2 em { font-style: normal; color: var(--accent); }
        .maker-head { max-width: 760px; margin: 0 auto 44px; text-align: center; }
        .maker-head h2 { margin: 10px 0 18px; }
        .maker-head p { font-size: 19px; color: var(--muted); }
        .quote { max-width: 760px; margin: 0 auto 52px; padding: 30px 34px; background: var(--surface); border-left: 4px solid var(--wood-strong); border-radius: 0 var(--radius) var(--radius) 0; box-shadow: var(--shadow-sm); }
        .quote p { font-size: 20px; font-style: italic; line-height: 1.5; }
        .quote cite { display: block; margin-top: 12px; font-size: 14px; font-style: normal; font-weight: 600; color: var(--muted); }
        .maker-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
        .maker-card { padding: 28px 24px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-sm); }
        .maker-card h3 { margin: 16px 0 8px; font-size: 19px; }
        .maker-card p { font-size: 15px; color: var(--muted); }
        .maker-close { margin-top: 48px; text-align: center; }
        .maker-close p { margin-bottom: 20px; font-size: 22px; font-weight: 600; letter-spacing: -0.02em; }
        @media (max-width: 1080px) { .maker-cards { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 560px) { .maker-cards { grid-template-columns: 1fr; } .quote { padding: 22px; } }

        .type-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
        .type-card { padding: 22px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); transition: transform 0.2s, box-shadow 0.2s; }
        .type-card:hover { transform: translateY(-3px); box-shadow: var(--shadow); }
        .type-art { display: grid; place-items: center; aspect-ratio: 4 / 3; margin-bottom: 18px; color: var(--accent); background: linear-gradient(160deg, #f7f3ec 0%, #e9efeb 100%); border-radius: 12px; }
        .type-art svg { width: 78%; height: auto; overflow: visible; fill: none; stroke: currentColor; stroke-width: 2; stroke-linejoin: round; stroke-linecap: round; }
        .type-art .fill { fill: #fff; }
        .type-art .glass { fill: #cfe3f3; }
        .type-art .ground { stroke: #b7c4bd; }
        .type-card h3 { margin-bottom: 6px; font-size: 18px; }
        .type-card p { font-size: 15px; color: var(--muted); }
        .type-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 10px; margin-top: 36px; padding: 22px 26px; background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius); }
        .type-meta strong { margin-right: 6px; font-size: 14px; letter-spacing: 0.04em; text-transform: uppercase; color: var(--muted); }
        .chip { padding: 6px 14px; font-size: 14px; font-weight: 600; color: var(--accent-strong); background: var(--accent-soft); border-radius: 999px; }
        .type-meta p { flex-basis: 100%; font-size: 14px; color: var(--muted); }

        .carbon { background: var(--surface-2); }
        .carbon-grid { display: grid; grid-template-columns: 0.9fr 1.1fr; gap: 72px; align-items: center; }
        .carbon-text h2 { margin: 10px 0 16px; }
        .carbon-text p { font-size: 18px; color: var(--muted); }
        .chart { padding: 30px; background: var(--surface); border: 1px solid var(--border); border-radius: 20px; box-shadow: var(--shadow); }
        .chart-unit { margin-bottom: 22px; font-size: 13px; font-weight: 600; color: var(--muted); }
        .bar { margin-bottom: 20px; }
        .bar-head { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 8px; font-size: 15px; font-weight: 600; }
        .bar-track { height: 14px; background: var(--surface-2); border-radius: 999px; }
        .bar-fill { height: 100%; border-radius: 999px; background: #98a2b3; }
        .bar-fill.is-mid { background: var(--wood-strong); }
        .bar-fill.is-neg { background: var(--accent); }
        .chart-note { font-size: 12px; color: var(--subtle); }

        .service-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .service { padding: 26px 24px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
        .service h3 { margin: 14px 0 8px; font-size: 18px; }
        .service p { font-size: 15px; color: var(--muted); }
        .paths-title { margin: 56px 0 20px; font-size: 24px; }
        .paths { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
        .path { display: flex; flex-direction: column; align-items: flex-start; gap: 10px; padding: 30px; border-radius: 20px; background: var(--wood); }
        .path.is-alt { background: var(--dark); color: #e6efe9; }
        .path .eyebrow { color: var(--accent-strong); }
        .path.is-alt .eyebrow { color: var(--wood-strong); }
        .path h3 { font-size: 22px; }
        .path.is-alt h3 { color: #fff; }
        .path p { margin-bottom: 8px; font-size: 16px; color: var(--muted); }
        .path.is-alt p { color: #b8c9c0; }

        .faq-grid { display: grid; grid-template-columns: 0.8fr 1.2fr; gap: 72px; align-items: start; }
        .faq-list { border-top: 1px solid var(--border); }
        .faq-list details { border-bottom: 1px solid var(--border); }
        .faq-list summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 22px 0;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            list-style: none;
        }
        .faq-list summary::-webkit-details-marker { display: none; }
        .faq-list summary .icon { width: 20px; height: 20px; color: var(--muted); transition: transform 0.2s; }
        .faq-list details[open] summary .icon { transform: rotate(45deg); color: var(--accent); }
        .faq-list details p { padding: 0 40px 24px 0; color: var(--muted); }

        .footer { padding: 64px 0 40px; color: #a9bcb2; background: var(--dark); }
        .footer-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 48px; padding-bottom: 40px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .footer .brand { color: #fff; }
        .footer p { margin-top: 14px; max-width: 360px; font-size: 15px; }
        .footer h4 { margin-bottom: 14px; font-size: 13px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: #fff; }
        .footer ul { display: flex; flex-direction: column; gap: 10px; list-style: none; font-size: 15px; }
        .footer a:hover { color: #fff; }
        .footer-note { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 12px; padding-top: 28px; font-size: 13px; color: #7f948a; }

        .reveal { opacity: 1; transform: none; }
        .reveal.is-visible { opacity: 1; transform: none; }

        .skip-link { position: fixed; top: -100px; left: 20px; z-index: 100; padding: 12px 20px; background: var(--dark); color: #fff; }
        .skip-link:focus { top: 12px; }
        .mobile-menu { display: none; }
        .offer-section { border-top: 1px solid var(--border); }
        .offer-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .offer-card { display: flex; flex-direction: column; align-items: flex-start; padding: 32px; border: 1px solid var(--border); border-radius: 20px; background: #fff; }
        .offer-card:first-child { background: var(--dark); color: #fff; border-color: var(--dark); }
        .offer-number { font-size: 13px; font-weight: 700; letter-spacing: 0.1em; color: var(--accent); margin-bottom: 28px; }
        .offer-card:first-child .offer-number { color: #c7d8bd; }
        .offer-card h3 { font-size: 26px; margin-bottom: 14px; }
        .offer-card p { max-width: 440px; color: var(--muted); margin-bottom: 28px; }
        .offer-card:first-child p { color: #c6d2ca; }
        .offer-card .btn { margin-top: auto; }
        .scope { margin-top: 32px; padding: 32px; background: var(--wood); border-radius: 20px; }
        .scope h3 { font-size: 24px; margin-bottom: 24px; }
        .scope-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; }
        .scope h4 { margin-bottom: 8px; font-size: 15px; }
        .scope p { font-size: 15px; color: var(--muted); }
        .scope .price-note { margin-top: 24px; padding-top: 20px; border-top: 1px solid #d9d2c5; font-size: 14px; }
        .technical-note { margin-top: 28px; font-size: 14px; color: var(--muted); max-width: 760px; }
        .contact-section { padding: 72px 0; background: var(--wood); border-top: 1px solid var(--border); }
        .contact-grid { display: flex; align-items: center; justify-content: space-between; gap: 40px; }
        .contact-grid h2 { max-width: 640px; margin-bottom: 16px; }
        .contact-grid p { max-width: 640px; color: var(--muted); font-size: 18px; }
        .contact-grid .btn { flex-shrink: 0; }
        .nav-links .btn-primary { color: #fff; }
        .nav-links .btn-primary:hover { color: #fff; }
        #content:focus { outline: none; }

        @media (max-width: 1080px) {
            .nav-links { gap: 12px; }
            .feature-grid, .type-grid { grid-template-columns: repeat(2, 1fr); }
            .service-grid { grid-template-columns: 1fr; }
            .steps { grid-template-columns: 1fr; gap: 0; }
            .step { padding: 0 0 32px 64px; min-height: 40px; }
            .step:not(:last-child)::after { top: 52px; bottom: 6px; left: 19px; right: auto; width: 2px; height: auto; background: var(--border); }
        }
        @media (max-width: 900px) {
            .section { padding: 80px 0; }
            .nav-links { display: none; }
            .nav-inner { gap: 12px; }
            .mobile-menu { display: block; }
            .mobile-menu summary { cursor: pointer; list-style: none; display: flex; align-items: center; gap: 10px; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; font-weight: 600; }
            .mobile-menu summary::-webkit-details-marker { display: none; }
            .mobile-menu[open] summary { background: var(--accent-soft); }
            .mobile-menu nav { position: absolute; left: 16px; right: 16px; top: 80px; display: grid; padding: 16px; gap: 4px; background: var(--bg); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow-lg); max-height: calc(100dvh - 100px); overflow-y: auto; }
            .mobile-menu nav > a { padding: 12px; border-radius: 8px; }
            .mobile-menu nav > a:hover { background: var(--accent-soft); }
            .mobile-menu .lang-switch { justify-self: start; margin: 12px 0 4px; }
            .mobile-menu .lang-link { padding: 10px 14px; }
            .hero { padding: 40px 0 72px; }
            .hero-grid, .about-grid, .made-grid, .cta-band, .faq-grid, .carbon-grid, .paths { grid-template-columns: 1fr; gap: 40px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .stat:nth-child(3) { border-left: none; }
            .stat:nth-child(n + 3) { border-top: 1px solid var(--border); }
            .cta-band { padding: 36px 24px; }
            .footer-grid { grid-template-columns: 1fr; gap: 32px; }
            .contact-grid { flex-direction: column; align-items: flex-start; gap: 24px; }
        }
        @media (max-width: 560px) {
            .container { padding: 0 20px; }
            .feature-grid, .made-list, .type-grid { grid-template-columns: 1fr; }
            .hero-actions .btn { width: 100%; }
            .faq-list summary { font-size: 16px; }
            .offer-grid, .scope-grid { grid-template-columns: 1fr; gap: 20px; }
            .offer-card, .scope { padding: 24px; }
            .hero h1 { font-size: clamp(30px, 8vw, 44px); }
            .hero-lead { font-size: 17px; }
            .section { padding: 56px 0; }
            .section-head { margin-bottom: 28px; }
            .hero-visual { border-top-left-radius: 72px; }
            .photo-label { padding: 40px 18px 16px; }
            .photo-label strong { font-size: 18px; }
            .type-meta { padding: 20px; }
        }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .reveal { opacity: 1; transform: none; }
            *, *::before, *::after { transition-duration: 0.01ms !important; animation-duration: 0.01ms !important; }
        }
    </style>
</head>
<body>
    <a class="skip-link" href="#content">{{ __('site.experience.skip') }}</a>
    <svg width="0" height="0" style="position:absolute" aria-hidden="true">
        <symbol id="flag-sk" viewBox="0 0 900 600">
            <rect width="900" height="200" fill="#ffffff"/>
            <rect y="200" width="900" height="200" fill="#0b4ea2"/>
            <rect y="400" width="900" height="200" fill="#ee1c25"/>
            <g transform="translate(130, 80)">
                <path d="M0 0 h240 v180 c0 150 -50 240 -120 280 c-70 -40 -120 -130 -120 -280 Z" fill="#ffffff"/>
                <path d="M14 14 h212 v166 c0 138 -45 220 -106 258 c-61 -38 -106 -120 -106 -258 Z" fill="#ee1c25"/>
                <path d="M42 340 c26 -46 56 -46 72 -12 c12 -42 38 -42 50 0 c16 -34 46 -34 72 12 c-22 42 -56 74 -94 84 c-38 -10 -72 -42 -100 -84 Z" fill="#0b4ea2"/>
                <rect x="110" y="70" width="20" height="240" fill="#ffffff"/>
                <rect x="75" y="118" width="90" height="18" fill="#ffffff"/>
                <rect x="60" y="176" width="120" height="18" fill="#ffffff"/>
            </g>
        </symbol>
        <symbol id="flag-cz" viewBox="0 0 900 600">
            <rect width="900" height="300" fill="#ffffff"/>
            <rect y="300" width="900" height="300" fill="#d7141a"/>
            <polygon points="0,0 450,300 0,600" fill="#11457e"/>
        </symbol>
        <symbol id="flag-en" viewBox="0 0 60 30">
            <clipPath id="uk-clip"><path d="M0 0v30h60V0z"/></clipPath>
            <clipPath id="uk-diag"><path d="M30 15h30v15zv15H0zH0V0zV0h30z"/></clipPath>
            <g clip-path="url(#uk-clip)">
                <path d="M0 0v30h60V0z" fill="#012169"/>
                <path d="M0 0l60 30m0-30L0 30" stroke="#fff" stroke-width="6"/>
                <path d="M0 0l60 30m0-30L0 30" clip-path="url(#uk-diag)" stroke="#c8102e" stroke-width="4"/>
                <path d="M30 0v30M0 15h60" stroke="#fff" stroke-width="10"/>
                <path d="M30 0v30M0 15h60" stroke="#c8102e" stroke-width="6"/>
            </g>
        </symbol>
        <symbol id="i-house" viewBox="0 0 24 24"><path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M10 20v-5h4v5"/></symbol>
        <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7.5"/></symbol>
        <symbol id="i-rotate" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 4v5h-5"/></symbol>
        <symbol id="i-cube" viewBox="0 0 24 24"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z"/><path d="M4 7.5l8 4.5 8-4.5M12 12v9"/></symbol>
        <symbol id="i-cnc" viewBox="0 0 24 24"><rect x="3" y="15" width="18" height="5" rx="1"/><path d="M8 15V9h8v6"/><path d="M12 9V4"/><path d="M10 4h4"/></symbol>
        <symbol id="i-ruler" viewBox="0 0 24 24"><path d="M3 17L17 3l4 4L7 21z"/><path d="M7 13l2 2M10 10l2 2M13 7l2 2"/></symbol>
        <symbol id="i-thermo" viewBox="0 0 24 24"><path d="M14 14.8V5a2 2 0 1 0-4 0v9.8a4 4 0 1 0 4 0z"/><path d="M12 9v7"/></symbol>
        <symbol id="i-weight" viewBox="0 0 24 24"><path d="M6.5 8h11l2.5 12H4z"/><circle cx="12" cy="5" r="2"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></symbol>
        <symbol id="i-leaf" viewBox="0 0 24 24"><path d="M5 20c0-9 6-15 15-15 0 9-6 15-15 15z"/><path d="M5 20l8-8"/></symbol>
        <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M9 12l2 2 4-4"/></symbol>
        <symbol id="i-grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></symbol>
        <symbol id="i-cycle" viewBox="0 0 24 24"><path d="M20 12a8 8 0 0 1-13.7 5.7"/><path d="M4 12A8 8 0 0 1 17.7 6.3"/><path d="M18 2.5v4h-4"/><path d="M6 21.5v-4h4"/></symbol>
        <symbol id="i-factory" viewBox="0 0 24 24"><path d="M3 21V10l6 4v-4l6 4V5h6v16z"/><path d="M7 17h2M12 17h2M17 17h2"/></symbol>
        <symbol id="i-scan" viewBox="0 0 24 24"><path d="M4 8V5a1 1 0 0 1 1-1h3M16 4h3a1 1 0 0 1 1 1v3M20 16v3a1 1 0 0 1-1 1h-3M8 20H5a1 1 0 0 1-1-1v-3"/><path d="M8 12h8"/></symbol>
        <symbol id="i-truck" viewBox="0 0 24 24"><path d="M2 6h12v10H2z"/><path d="M14 10h4l4 4v2h-8"/><circle cx="6.5" cy="18" r="2"/><circle cx="17.5" cy="18" r="2"/></symbol>
        <symbol id="i-box" viewBox="0 0 24 24"><path d="M3 7l9-4 9 4v10l-9 4-9-4z"/><path d="M3 7l9 4 9-4M12 11v10"/></symbol>
        <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
        <symbol id="i-pencil" viewBox="0 0 24 24"><path d="M4 20l1-4L16 5l3 3L8 19z"/><path d="M14 7l3 3"/></symbol>
        <symbol id="i-people" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3 20c0-3.5 2.7-6 6-6s6 2.5 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M17 14c2.5 0 4 2 4 5"/></symbol>
    </svg>

    <header class="nav" id="nav">
        <div class="container nav-inner">
            <a class="brand" href="{{ ($locale ?? 'sk') === 'sk' ? '/' : '/' . ($locale ?? 'sk') }}" aria-label="{{ __('site.nav.aria_brand') }}">
                <span class="brand-mark"><svg class="icon"><use href="#i-house"/></svg></span>
                WikiDom
            </a>
            <nav class="nav-links" aria-label="{{ __('site.nav.aria_main') }}">
                <a href="#ponuka">{{ __('site.experience.offer') }}</a>
                <a href="#system">{{ __('site.nav.system') }}</a>
                <a href="#postup">{{ __('site.nav.process') }}</a>
                <a href="#kontakt">{{ __('site.footer.col_contact') }}</a>
                <a class="btn btn-primary btn-sm" href="/glb-viewer">{{ __('site.nav.configurator') }}</a>
                <div class="lang-switch" aria-label="Jazyk">
                    <a href="/sk" class="lang-link {{ ($locale ?? 'sk') === 'sk' ? 'is-active' : '' }}" title="Slovenčina">
                        <svg class="badge-flag" aria-hidden="true"><use href="#flag-sk"/></svg> SK
                    </a>
                    <a href="/cs" class="lang-link {{ ($locale ?? 'sk') === 'cs' ? 'is-active' : '' }}" title="Čeština">
                        <svg class="badge-flag" aria-hidden="true"><use href="#flag-cz"/></svg> CZ
                    </a>
                    <a href="/en" class="lang-link {{ ($locale ?? 'sk') === 'en' ? 'is-active' : '' }}" title="English">
                        <svg class="badge-flag" aria-hidden="true"><use href="#flag-en"/></svg> EN
                    </a>
                </div>
            </nav>
            <details class="mobile-menu">
                <summary>{{ __('site.experience.menu') }} <svg class="icon" aria-hidden="true"><use href="#i-grid"/></svg></summary>
                <nav aria-label="{{ __('site.nav.aria_main') }}">
                    <a href="#ponuka">{{ __('site.experience.offer') }}</a>
                    <a href="#system">{{ __('site.nav.system') }}</a>
                    <a href="#postup">{{ __('site.nav.process') }}</a>
                    <a href="#faq">{{ __('site.nav.faq') }}</a>
                    <a href="#kontakt">{{ __('site.footer.col_contact') }}</a>
                    <a class="btn btn-primary" href="/glb-viewer">{{ __('site.nav.configurator') }}</a>
                    <div class="lang-switch" aria-label="Language">
                        @foreach (['sk' => 'SK', 'cs' => 'CZ', 'en' => 'EN'] as $code => $label)
                            <a href="/{{ $code }}" lang="{{ $code }}" class="lang-link {{ ($locale ?? 'sk') === $code ? 'is-active' : '' }}" @if (($locale ?? 'sk') === $code) aria-current="page" @endif>{{ $label }}</a>
                        @endforeach
                    </div>
                </nav>
            </details>
        </div>
    </header>

    <main id="content" tabindex="-1">
        <section class="hero">
            <div class="container hero-grid">
                <div>
                    <span class="eyebrow">{{ __('site.hero.eyebrow') }}</span>
                    <h1>{!! __('site.hero.title_html') !!}</h1>
                    <p class="hero-lead">{{ __('site.hero.lead') }}</p>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="/glb-viewer">{{ __('site.hero.cta_primary') }} <svg class="icon"><use href="#i-arrow"/></svg></a>
                        <a class="btn btn-ghost" href="#ponuka">{{ __('site.hero.cta_secondary') }}</a>
                    </div>
                    <ul class="checks">
                        <li><svg class="icon"><use href="#i-check"/></svg>{{ __('site.hero.check_1') }}</li>
                        <li><svg class="icon"><use href="#i-check"/></svg>{{ __('site.hero.check_2') }}</li>
                        <li><svg class="icon"><use href="#i-check"/></svg>{{ __('site.hero.check_3') }}</li>
                    </ul>
                </div>
                <figure class="hero-photo">
                    <div class="hero-visual">
                        <img src="/images/wikihouse-assembly.jpg" width="1280" height="1280" fetchpriority="high" alt="{{ __('site.hero.photo_alt') }}">
                        <div class="photo-label">
                            <strong>{{ __('site.hero.photo_title') }}</strong>
                            <span>{{ __('site.hero.photo_label') }}</span>
                        </div>
                    </div>
                    <figcaption class="photo-credit">
                        {{ __('site.hero.photo_credit') }}
                        <a href="https://commons.wikimedia.org/wiki/File:Ouisharefest_-_Wikihouse_(8710416975).jpg" target="_blank" rel="noopener">Javier Leiva / Wikimedia Commons</a>
                        · <a href="https://creativecommons.org/licenses/by/2.0/" target="_blank" rel="noopener">CC BY 2.0</a>
                    </figcaption>
                </figure>
            </div>
        </section>

        <section class="section offer-section" id="ponuka">
            <div class="container">
                <div class="section-head">
                    <span class="eyebrow">{{ __('site.experience.offer') }}</span>
                    <h2>{{ __('site.experience.intro') }}</h2>
                    <p>{{ __('site.experience.intro_lead') }}</p>
                </div>
                <div class="offer-grid">
                    <article class="offer-card">
                        <span class="offer-number">01 / {{ __('site.nav.configurator') }}</span>
                        <h3>{{ __('site.experience.offer_1_title') }}</h3>
                        <p>{{ __('site.experience.offer_1_desc') }}</p>
                        <a class="btn btn-light" href="/glb-viewer">{{ __('site.experience.offer_1_cta') }} <svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
                    </article>
                    <article class="offer-card">
                        <span class="offer-number">02 / WikiDom</span>
                        <h3>{{ __('site.experience.offer_2_title') }}</h3>
                        <p>{{ __('site.experience.offer_2_desc') }}</p>
                        <a class="btn btn-ghost" href="#kontakt">{{ __('site.experience.offer_2_cta') }} <svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
                    </article>
                </div>
                <div class="scope">
                    <h3>{{ __('site.experience.scope_title') }}</h3>
                    <div class="scope-grid">
                        <div><h4>{{ __('site.experience.included') }}</h4><p>{{ __('site.experience.included_text') }}</p></div>
                        <div><h4>{{ __('site.experience.separate') }}</h4><p>{{ __('site.experience.separate_text') }}</p></div>
                    </div>
                    <p class="price-note">{{ __('site.experience.price_note') }}</p>
                </div>
            </div>
        </section>

        <section class="section" id="system">
            <div class="container about-grid">
                <div class="about-text reveal">
                    <span class="eyebrow">{{ __('site.system.eyebrow') }}</span>
                    <h2 style="margin: 10px 0 22px;">{{ __('site.system.title') }}</h2>
                    <p>{!! __('site.system.p1') !!}</p>
                    <p>{!! __('site.system.p2') !!}</p>
                    <p>{!! __('site.system.p3') !!}</p>
                </div>
                <ol class="flow reveal" aria-label="{{ __('site.system.flow_label') }}">
                    <li>
                        <span class="flow-icon"><svg class="icon"><use href="#i-cube"/></svg></span>
                        <div><h3>{{ __('site.system.step1_title') }}</h3><p>{{ __('site.system.step1_desc') }}</p></div>
                    </li>
                    <li>
                        <span class="flow-icon"><svg class="icon"><use href="#i-cnc"/></svg></span>
                        <div><h3>{{ __('site.system.step2_title') }}</h3><p>{{ __('site.system.step2_desc') }}</p></div>
                    </li>
                    <li>
                        <span class="flow-icon"><svg class="icon"><use href="#i-house"/></svg></span>
                        <div><h3>{{ __('site.system.step3_title') }}</h3><p>{{ __('site.system.step3_desc') }}</p></div>
                    </li>
                </ol>
            </div>
        </section>

        @if (Lang::hasForLocale('site.types.title'))
        <section class="section" id="typy">
            <div class="container">
                <div class="section-head reveal">
                    <span class="eyebrow">{{ __('site.types.eyebrow') }}</span>
                    <h2>{{ __('site.types.title') }}</h2>
                    <p>{{ __('site.types.lead') }}</p>
                </div>
                <div class="type-grid">
                    <article class="type-card reveal">
                        <div class="type-art" aria-hidden="true">
                            <svg viewBox="0 0 110 90"><path class="ground" d="M6 78h98"/><path class="fill" d="M24 44L55 18l31 26v34H24z"/><rect class="glass" x="46" y="52" width="18" height="14" rx="1.5"/></svg>
                        </div>
                        <h3>{{ __('site.types.t1_title') }}</h3>
                        <p>{{ __('site.types.t1_desc') }}</p>
                    </article>
                    <article class="type-card reveal">
                        <div class="type-art" aria-hidden="true">
                            <svg viewBox="0 0 110 90"><path class="ground" d="M4 78h102"/><path class="fill" d="M10 46l15-16 15 16 15-16 15 16 15-16 15 16v32H10z"/><path d="M40 46v32M70 46v32"/><rect class="glass" x="19" y="56" width="12" height="11" rx="1.5"/><rect class="glass" x="49" y="56" width="12" height="11" rx="1.5"/><rect class="glass" x="79" y="56" width="12" height="11" rx="1.5"/></svg>
                        </div>
                        <h3>{{ __('site.types.t2_title') }}</h3>
                        <p>{{ __('site.types.t2_desc') }}</p>
                    </article>
                    <article class="type-card reveal">
                        <div class="type-art" aria-hidden="true">
                            <svg viewBox="0 0 110 90"><path class="ground" d="M6 78h98"/><rect class="fill" x="28" y="8" width="54" height="70" rx="2"/><rect class="glass" x="36" y="16" width="9" height="9"/><rect class="glass" x="50" y="16" width="9" height="9"/><rect class="glass" x="64" y="16" width="9" height="9"/><rect class="glass" x="36" y="32" width="9" height="9"/><rect class="glass" x="50" y="32" width="9" height="9"/><rect class="glass" x="64" y="32" width="9" height="9"/><rect class="glass" x="36" y="48" width="9" height="9"/><rect class="glass" x="50" y="48" width="9" height="9"/><rect class="glass" x="64" y="48" width="9" height="9"/><rect class="glass" x="50" y="64" width="9" height="14"/></svg>
                        </div>
                        <h3>{{ __('site.types.t3_title') }}</h3>
                        <p>{{ __('site.types.t3_desc') }}</p>
                    </article>
                    <article class="type-card reveal">
                        <div class="type-art" aria-hidden="true">
                            <svg viewBox="0 0 110 90"><path class="ground" d="M4 78h102"/><rect class="fill" x="8" y="26" width="28" height="52"/><rect class="fill" x="74" y="26" width="28" height="52"/><rect x="40" y="26" width="30" height="52" style="fill:#e7f1ec;stroke-dasharray:5 4"/><rect class="glass" x="48" y="36" width="14" height="12" rx="1.5"/><rect class="glass" x="48" y="56" width="14" height="12" rx="1.5"/><rect class="glass" x="16" y="36" width="12" height="10"/><rect class="glass" x="82" y="36" width="12" height="10"/></svg>
                        </div>
                        <h3>{{ __('site.types.t4_title') }}</h3>
                        <p>{{ __('site.types.t4_desc') }}</p>
                    </article>
                </div>
                <div class="type-meta reveal">
                    <strong>{{ __('site.types.use_title') }}</strong>
                    @foreach (range(1, 5) as $i)
                        <span class="chip">{{ __('site.types.use_' . $i) }}</span>
                    @endforeach
                    <strong style="margin-left: 18px;">{{ __('site.types.storeys_title') }}</strong>
                    @foreach (range(1, 4) as $i)
                        <span class="chip">{{ __('site.types.storey_' . $i) }}</span>
                    @endforeach
                    <p>{{ __('site.types.storeys_note') }}</p>
                </div>
            </div>
        </section>
        @endif

        <section class="section features" id="vyhody">
            <div class="container">
                <div class="section-head center reveal">
                    <span class="eyebrow">{{ __('site.benefits.eyebrow') }}</span>
                    <h2>{{ __('site.experience.benefits_title') }}</h2>
                    <p>{{ __('site.experience.benefits_lead') }}</p>
                </div>
                <div class="feature-grid">
                    @foreach (__('site.experience.benefits') as $benefit)
                        <article class="feature">
                            <span class="feature-icon"><svg class="icon" aria-hidden="true"><use href="#{{ ['i-ruler', 'i-cnc', 'i-house', 'i-cycle'][$loop->index] }}"/></svg></span>
                            <h3>{{ $benefit['title'] }}</h3>
                            <p>{{ $benefit['text'] }}</p>
                        </article>
                    @endforeach
                </div>
                <p class="technical-note">{{ __('site.experience.technical_note') }}</p>
            </div>
        </section>

        <section class="section made" id="vyroba">
            <div class="container made-grid">
                <div class="reveal">
                    <span class="eyebrow">{{ __('site.production.eyebrow') }}</span>
                    <h2>{{ __('site.production.title') }}</h2>
                    <p class="made-lead">{{ __('site.production.lead') }}</p>
                    @if (Lang::hasForLocale('site.production.network'))
                        <p class="made-lead" style="margin-top: 14px;">{{ __('site.production.network') }}</p>
                    @endif
                    <a class="btn btn-light" href="/glb-viewer">{{ __('site.production.cta') }} <svg class="icon"><use href="#i-arrow"/></svg></a>
                </div>
                <ul class="made-list">
                    <li class="reveal">
                        <svg class="icon"><use href="#i-factory"/></svg>
                        <h3>{{ __('site.production.item1_title') }}</h3>
                        <p>{{ __('site.production.item1_desc') }}</p>
                    </li>
                    <li class="reveal">
                        <svg class="icon"><use href="#i-scan"/></svg>
                        <h3>{{ __('site.production.item2_title') }}</h3>
                        <p>{{ __('site.production.item2_desc') }}</p>
                    </li>
                    <li class="reveal">
                        <svg class="icon"><use href="#i-truck"/></svg>
                        <h3>{{ __('site.production.item3_title') }}</h3>
                        <p>{{ __('site.production.item3_desc') }}</p>
                    </li>
                    <li class="reveal">
                        <svg class="icon"><use href="#i-box"/></svg>
                        <h3>{{ __('site.production.item4_title') }}</h3>
                        <p>{{ __('site.production.item4_desc') }}</p>
                    </li>
                </ul>
            </div>
        </section>

        @if (Lang::hasForLocale('site.services.title'))
        <section class="section" id="sluzby">
            <div class="container">
                <div class="section-head reveal">
                    <span class="eyebrow">{{ __('site.services.eyebrow') }}</span>
                    <h2>{{ __('site.services.title') }}</h2>
                    <p>{{ __('site.services.lead') }}</p>
                </div>
                <div class="service-grid">
                    <article class="service reveal">
                        <span class="feature-icon"><svg class="icon"><use href="#i-cube"/></svg></span>
                        <h3>{{ __('site.services.sv1_title') }}</h3>
                        <p>{{ __('site.services.sv1_desc') }}</p>
                    </article>
                    <article class="service reveal">
                        <span class="feature-icon"><svg class="icon"><use href="#i-pencil"/></svg></span>
                        <h3>{{ __('site.services.sv2_title') }}</h3>
                        <p>{{ __('site.services.sv2_desc') }}</p>
                    </article>
                    <article class="service reveal">
                        <span class="feature-icon"><svg class="icon"><use href="#i-people"/></svg></span>
                        <h3>{{ __('site.services.sv3_title') }}</h3>
                        <p>{{ __('site.services.sv3_desc') }}</p>
                    </article>
                </div>
            </div>
        </section>
        @endif

        <section class="section" id="postup">
            <div class="container">
                <div class="section-head reveal">
                    <span class="eyebrow">{{ __('site.process.eyebrow') }}</span>
                    <h2>{{ __('site.process.title') }}</h2>
                    <p>{{ __('site.process.lead') }}</p>
                </div>
                <ol class="steps">
                    <li class="step reveal">
                        <h3>{{ __('site.process.s1_title') }}</h3>
                        <p>{!! __('site.process.s1_desc', ['url' => '/glb-viewer']) !!}</p>
                    </li>
                    <li class="step reveal">
                        <h3>{{ __('site.process.s2_title') }}</h3>
                        <p>{{ __('site.process.s2_desc') }}</p>
                    </li>
                    <li class="step reveal">
                        <h3>{{ __('site.process.s3_title') }}</h3>
                        <p>{{ __('site.process.s3_desc') }}</p>
                    </li>
                    <li class="step reveal">
                        <h3>{{ __('site.process.s4_title') }}</h3>
                        <p>{{ __('site.process.s4_desc') }}</p>
                    </li>
                    <li class="step reveal">
                        <h3>{{ __('site.process.s5_title') }}</h3>
                        <p>{{ __('site.process.s5_desc') }}</p>
                    </li>
                </ol>
            </div>
        </section>

        <section class="section" style="padding-top: 0;">
            <div class="container">
                <div class="cta-band reveal">
                    <div>
                        <span class="eyebrow">{{ __('site.cta_band.eyebrow') }}</span>
                        <h2>{{ __('site.cta_band.title') }}</h2>
                        <p>{{ __('site.cta_band.lead') }}</p>
                        <ul>
                            <li><svg class="icon"><use href="#i-check"/></svg>{{ __('site.cta_band.check_1') }}</li>
                            <li><svg class="icon"><use href="#i-check"/></svg>{{ __('site.cta_band.check_2') }}</li>
                            <li><svg class="icon"><use href="#i-check"/></svg>{{ __('site.cta_band.check_3') }}</li>
                        </ul>
                        <a class="btn btn-primary" href="/glb-viewer">{{ __('site.cta_band.button') }} <svg class="icon"><use href="#i-arrow"/></svg></a>
                    </div>
                    <div class="strip" aria-hidden="true">
                        <div class="strip-head"><span>{{ __('site.experience.preview') }}</span><span>WikiDom</span></div>
                        <div class="strip-row">
                            <span class="seg is-end"><svg viewBox="0 0 64 48"><path class="wall" d="M8 44V20L32 5l24 15v24z"/><rect class="glass" x="27" y="17" width="10" height="9" rx="1.5"/></svg></span>
                            <span class="seg"><svg viewBox="0 0 64 48"><rect class="wall" x="6" y="6" width="52" height="36" rx="3"/><rect class="glass" x="21" y="13" width="22" height="18" rx="1.5"/></svg></span>
                            <span class="seg"><svg viewBox="0 0 64 48"><rect class="wall" x="6" y="6" width="52" height="36" rx="3"/><rect class="glass" x="12" y="12" width="40" height="22" rx="1.5"/></svg></span>
                            <span class="seg"><svg viewBox="0 0 64 48"><rect class="wall" x="6" y="6" width="52" height="36" rx="3"/><rect class="glass" x="28" y="11" width="8" height="26" rx="1.5"/></svg></span>
                            <span class="seg"><svg viewBox="0 0 64 48"><rect class="wall" x="6" y="6" width="52" height="36" rx="3"/><rect class="glass" x="21" y="13" width="22" height="18" rx="1.5"/></svg></span>
                            <span class="seg is-end"><svg viewBox="0 0 64 48"><path class="wall" d="M8 44V20L32 5l24 15v24z"/><rect class="glass" x="27" y="17" width="10" height="9" rx="1.5"/></svg></span>
                        </div>
                        <div class="strip-foot"><span>{{ __('site.experience.preview_note') }}</span></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section features" id="faq">
            <div class="container faq-grid">
                <div class="reveal">
                    <span class="eyebrow">{{ __('site.faq.eyebrow') }}</span>
                    <h2 style="margin-top: 10px;">{{ __('site.faq.title') }}</h2>
                </div>
                <div class="faq-list reveal">
                    <details>
                        <summary>{{ __('site.faq.q1') }}<svg class="icon"><use href="#i-plus"/></svg></summary>
                        <p>{{ __('site.faq.a1') }}</p>
                    </details>
                    <details>
                        <summary>{{ __('site.faq.q2') }}<svg class="icon"><use href="#i-plus"/></svg></summary>
                        <p>{{ __('site.faq.a2') }}</p>
                    </details>
                    <details>
                        <summary>{{ __('site.faq.q3') }}<svg class="icon"><use href="#i-plus"/></svg></summary>
                        <p>{{ __('site.faq.a3') }}</p>
                    </details>
                    <details>
                        <summary>{{ __('site.faq.q4') }}<svg class="icon"><use href="#i-plus"/></svg></summary>
                        <p>{{ __('site.faq.a4') }}</p>
                    </details>
                    <details>
                        <summary>{{ __('site.faq.q5') }}<svg class="icon"><use href="#i-plus"/></svg></summary>
                        <p>{{ __('site.faq.a5') }}</p>
                    </details>
                    @if (Lang::hasForLocale('site.faq.q6'))
                        @foreach ([6, 7] as $n)
                            <details>
                                <summary>{{ __('site.faq.q' . $n) }}<svg class="icon"><use href="#i-plus"/></svg></summary>
                                <p>{{ __('site.faq.a' . $n) }}</p>
                            </details>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>
        <section class="contact-section" id="kontakt">
            <div class="container contact-grid">
                <div>
                    <h2>{{ __('site.experience.contact') }}</h2>
                    <p>{{ __('site.experience.contact_lead') }}</p>
                </div>
                <a class="btn btn-primary" href="mailto:{{ __('site.footer.email') }}">{{ __('site.experience.contact_cta') }} <svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <a class="brand" href="{{ ($locale ?? 'sk') === 'sk' ? '/' : '/' . ($locale ?? 'sk') }}"><span class="brand-mark"><svg class="icon"><use href="#i-house"/></svg></span>WikiDom</a>
                    <p>{{ __('site.footer.brand_desc') }}</p>
                    <div class="footer-lang" aria-label="Jazyk">
                        <a href="/sk" class="{{ ($locale ?? 'sk') === 'sk' ? 'is-active' : '' }}"><svg class="badge-flag" aria-hidden="true"><use href="#flag-sk"/></svg> Slovenčina</a>
                        <a href="/cs" class="{{ ($locale ?? 'sk') === 'cs' ? 'is-active' : '' }}"><svg class="badge-flag" aria-hidden="true"><use href="#flag-cz"/></svg> Čeština</a>
                        <a href="/en" class="{{ ($locale ?? 'sk') === 'en' ? 'is-active' : '' }}"><svg class="badge-flag" aria-hidden="true"><use href="#flag-en"/></svg> English</a>
                    </div>
                </div>
                <div>
                    <h4>{{ __('site.footer.col_page') }}</h4>
                    <ul>
                        <li><a href="#system">{{ __('site.nav.system') }}</a></li>
                        <li><a href="#vyhody">{{ __('site.nav.benefits') }}</a></li>
                        <li><a href="#postup">{{ __('site.nav.process') }}</a></li>
                        <li><a href="/glb-viewer">{{ __('site.nav.configurator') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4>{{ __('site.footer.col_contact') }}</h4>
                    <ul>
                        <li><a href="mailto:{{ __('site.footer.email') }}">{{ __('site.footer.email') }}</a></li>
                        <li>{{ __('site.footer.country') }}</li>
                        <li><a href="https://www.wikihouse.cc/" target="_blank" rel="noopener">wikihouse.cc</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-note">
                <span>© <span id="year">2026</span> WikiDom</span>
                <span>{{ __('site.footer.note') }}</span>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();

        const nav = document.getElementById('nav');
        const onScroll = () => nav.classList.toggle('is-scrolled', window.scrollY > 8);
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        const mobileMenu = document.querySelector('.mobile-menu');
        mobileMenu.addEventListener('click', (event) => {
            if (event.target.closest('a')) mobileMenu.open = false;
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && mobileMenu.open) {
                mobileMenu.open = false;
                mobileMenu.querySelector('summary').focus();
            }
        });

        if ('IntersectionObserver' in window) {
            const revealer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('is-visible');
                    revealer.unobserve(entry.target);
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
            document.querySelectorAll('.reveal').forEach((el) => revealer.observe(el));
        } else {
            document.querySelectorAll('.reveal').forEach((el) => el.classList.add('is-visible'));
        }
    </script>
</body>
</html>
