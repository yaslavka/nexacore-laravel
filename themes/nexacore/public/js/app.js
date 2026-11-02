import { initMotion, refreshMotion } from './motion.js';
import { renderCheckout, stopCheckout } from './checkout.js?v=20261102-payler';
import { COMPANY, formatKgsAmount, formatUsdAmount } from './company.js';
import { LEGAL_SLUGS, legalDoc } from './legal.js';
import { LOCALES, LOCALE_META, companyAddress, detectLocale, getLocale, href, localizePath, packageLabel, parseRoute, pluralServices, setLocale, t } from './i18n.js';

const $ = (s, root = document) => root.querySelector(s);
const escape = (v = '') => String(v).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const paths = {
 arrow:'M7 17 17 7M7 7h10v10', right:'M4 12h16m-6-6 6 6-6 6', chevron:'m9 5 7 7-7 7', down:'m6 9 6 6 6-6', close:'m6 6 12 12M6 18 18 6', menu:'M4 6h16M4 12h16M4 18h16', search:'M21 21l-5-5M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0', check:'m5 12 4 4L19 6', code:'m8 7-5 5 5 5m8-10 5 5-5 5m-3-14-2 18', cloud:'M7 18a5 5 0 1 1 .5-10A7 7 0 0 1 21 11a4 4 0 0 1-1 7H7', ai:'M9 3v3m6-3v3M9 18v3m6-3v3M3 9h3m-3 6h3m12-6h3m-3 6h3M6 6h12v12H6zM10 10h4v4h-4z', consult:'M4 4h16v12H9l-5 4V4zm4 4h8m-8 4h5', business:'M3 7h18v14H3zM8 7V3h8v4M3 12c5 4 13 4 18 0M10 12h4', chart:'M4 3v17h17M8 15l4-5 4 2 5-7', project:'M3 4h7v6H3zm11 10h7v6h-7zM6 10v7h8M14 4h7v6h-7z', engineering:'M12 3v3m0 12v3M3 12h3m12 0h3M6 6l2 2m8 8 2 2M6 18l2-2m8-8 2-2M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0', marketing:'m3 10 16-6v16L3 14v-4zm4 5 2 6h4l-2-5M22 9v6', education:'m2 9 10-6 10 6-10 6L2 9zm4 3v6c4 3 8 3 12 0v-6m4-3v9', booking:'M3 5h18v16H3zM7 3v4m10-4v4M3 10h18m-14 5h3m4 0h3m-10 3h3', license:'m12 2 8 4v6c0 5-8 10-8 10S4 17 4 12V6l8-4zm-4 10 3 3 5-6', globe:'M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0M3 12h18M12 3c5 5 5 13 0 18-5-5-5-13 0-18', clock:'M12 7v5l3 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0', mail:'M3 5h18v14H3zM3 5l9 8 9-8', layers:'m12 3 10 6-10 6L2 9l10-6zm-10 12 10 6 10-6M2 12l10 6 10-6', plus:'M12 5v14M5 12h14', lock:'M5 10h14v11H5zM8 10V6a4 4 0 0 1 8 0v4M12 14v3'
};
const icon = (name, cls = '') => `<svg class="icon ${cls}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="${paths[name] || paths.layers}"/></svg>`;
const logo = () => `<a href="${href('/')}" class="brand" aria-label="${escape(t('homeAria'))}"><span class="brand-mark"><i></i><i></i><i></i></span>NexaCore</a>`;
const monthlyPeriod = period => period && /month|мес|ай/i.test(period);
function money(price, period = '') {
  if (price == null) return `<span class="price-stack"><span class="price-usd">${escape(t('customQuote'))}</span></span>`;
  const suffix = monthlyPeriod(period) ? ` ${t('perMonth')}` : '';
  return `<span class="price-stack"><span class="price-usd">${escape(t('from'))} $${formatUsdAmount(price)}${escape(suffix)}</span><span class="price-kgs">${formatKgsAmount(price)} KGS${escape(suffix)}</span></span>`;
}
const main = $('#main');
let categories = [], allServices = [], settings = {}, adminAccess = {}, activeAdminTab = 'orders', toastTimer;

async function api(path, options = {}) {
  const response = await fetch(path, { ...options, headers: { 'Content-Type':'application/json', ...options.headers } });
  const result = await response.json();
  if (!response.ok) throw new Error(result.error || result.message || t('requestFailed'));
  return result;
}
function notify(text) { const el = $('#toast'); el.textContent = text; el.classList.add('show'); clearTimeout(toastTimer); toastTimer = setTimeout(() => el.classList.remove('show'), 4500); }
function currentAppPath() { return parseRoute(location.pathname).path; }
function languageSwitcher() {
  const current = getLocale();
  return `<div class="language-switcher">
    <button type="button" class="language-toggle" aria-expanded="false" aria-haspopup="listbox" aria-label="${escape(t('language'))}">${icon('globe')} <span>${LOCALE_META[current].short}</span></button>
    <div class="language-menu" hidden role="listbox" aria-label="${escape(t('language'))}">
      ${LOCALES.map(code => `<a href="${localizePath(currentAppPath(), code)}${location.search}" hreflang="${code}" lang="${code}" role="option" aria-selected="${code===current}" data-locale="${code}">${LOCALE_META[code].label}</a>`).join('')}
      <p class="language-hint">${escape(t('otherLanguages'))}</p>
    </div>
  </div>`;
}
function bindLanguageSwitcher() {
  const root = $('.language-switcher');
  if (!root) return;
  const button = $('.language-toggle', root);
  const menu = $('.language-menu', root);
  button.addEventListener('click', () => {
    const open = menu.hidden;
    menu.hidden = !open;
    button.setAttribute('aria-expanded', String(open));
  });
}
function paymentMarks() {
  const ASSET_BASE = (document.body?.dataset.themeBase ?? '/themes/nexacore/');
  return `<div class="payment-marks" role="group" aria-label="${escape(t('paymentMethods'))}">
    <img src="${ASSET_BASE}assets/payments/visa.svg" alt="Visa" width="72" height="48">
    <img src="${ASSET_BASE}assets/payments/mastercard.svg" alt="Mastercard" width="72" height="48">
    <span class="payment-marks-slot">${escape(t('otherPaymentSystems'))}</span>
  </div>`;
}
function companyRequisites() {
  return `<dl class="company-requisites">
    <div><dt>${escape(t('legalName'))}</dt><dd>${escape(COMPANY.displayName)}</dd></div>
    <div><dt>${escape(t('legalAddress'))}</dt><dd>${escape(companyAddress())}</dd></div>
    <div><dt>${escape(t('inn'))}</dt><dd>${escape(COMPANY.inn)}</dd></div>
    <div><dt>${escape(t('regNumber'))}</dt><dd>${escape(COMPANY.registrationNumber)}</dd></div>
    <div><dt>${escape(t('okpo'))}</dt><dd>${escape(COMPANY.okpo)}</dd></div>
    <div><dt>${escape(t('regDate'))}</dt><dd>${escape(COMPANY.registrationDate)}</dd></div>
    <div><dt>${escape(t('phone'))}</dt><dd><a href="tel:${COMPANY.phone.replace(/[^\d+]/g,'')}">${escape(COMPANY.phone)}</a></dd></div>
    <div><dt>${escape(t('email'))}</dt><dd><a href="mailto:${escape(COMPANY.email)}">${escape(COMPANY.email)}</a></dd></div>
  </dl>
  <p class="own-services">${escape(COMPANY.ownServicesOnly[getLocale()])}</p>`;
}
function renderHeader() {
  const route = currentAppPath();
  const isAdmin = route === '/admin';
  document.body.classList.toggle('admin-route', isAdmin);
  const skip = $('.skip-link');
  if (skip) skip.textContent = t('skip');
  if (isAdmin) {
    $('#header').innerHTML = `<div class="admin-header-inner">${logo()}<span class="workspace-label">Workspace</span><a href="/" class="text-button admin-website-link">View website ${icon('arrow')}</a></div>`;
    return;
  }
  $('#header').innerHTML = `<div class="header-inner container">${logo()}<nav class="desktop-nav" aria-label="${escape(t('navServices'))}"><a href="${href('/services')}" ${route.includes('service') ? 'aria-current="page"' : ''}>${escape(t('navServices'))} ${icon('down')}</a><a href="${href('/?section=approach')}">${escape(t('navApproach'))}</a><a href="${href('/?section=about')}">${escape(t('navAbout'))}</a><a href="${href('/contact')}">${escape(t('navContact'))}</a></nav><div class="header-actions">${languageSwitcher()}<button class="motion-control icon-button" type="button" data-motion-toggle aria-label="${escape(t('pauseMotion'))}" aria-pressed="false" title="${escape(t('pauseMotion'))}"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M9 5v14M15 5v14"/></svg></button><a class="button button-small button-navy" href="${href('/contact')}" aria-label="${escape(t('ctaProject'))}"><span class="header-cta-label">${escape(t('ctaProject'))}</span><span class="header-cta-short" aria-hidden="true">${escape(t('ctaTalk'))}</span> ${icon('arrow')}</a><button class="menu-toggle icon-button" aria-label="${escape(t('openMenu'))}" aria-expanded="false">${icon('menu')}</button></div></div><nav class="mobile-nav container" aria-label="${escape(t('openMenu'))}" hidden><a href="${href('/services')}">${escape(t('allServices'))}</a><a href="${href('/?section=approach')}">${escape(t('navApproach'))}</a><a href="${href('/?section=about')}">${escape(t('navAbout'))}</a><a href="${href('/contact')}">${escape(t('ctaProject'))}</a></nav>`;
  $('.menu-toggle').addEventListener('click', e => { const btn=e.currentTarget, nav=$('.mobile-nav'); nav.hidden=!nav.hidden; btn.setAttribute('aria-expanded',String(!nav.hidden)); btn.setAttribute('aria-label',nav.hidden?t('openMenu'):t('closeMenu')); btn.innerHTML=icon(nav.hidden?'menu':'close'); });
  bindLanguageSwitcher();
}
function renderFooter() {
  const year = new Date().getFullYear();
  $('#footer').innerHTML = `<div class="container footer-top"><div>${logo()}<p>${escape(settings.tagline || t('footerTagline')).replaceAll('\n','<br>')}</p><p class="own-services">${escape(COMPANY.ownServicesOnly[getLocale()])}</p></div><div><span class="footer-label">${escape(t('expertise'))}</span><a href="${href('/services/software')}">${escape(t('softwareDev'))}</a><a href="${href('/services/ai')}">${escape(t('artificialIntelligence'))}</a><a href="${href('/services/business')}">${escape(t('businessConsulting'))}</a></div><div><span class="footer-label">NEXACORE</span><a href="${href('/company')}">${escape(t('company'))}</a><a href="${href('/?section=about')}">${escape(t('navAbout'))}</a><a href="${href('/?section=approach')}">${escape(t('howWeWork'))}</a><a href="${href('/contact')}">${escape(t('talkToUs'))} ${icon('arrow')}</a></div><div class="footer-note"><span class="availability"><i></i> ${escape(t('openToProjects'))}</span><p>${escape(t('footerInvite')).replaceAll('\n','<br>')}</p></div></div>
  <div class="container footer-legal"><div class="footer-legal-links"><a href="${href('/privacy')}">${escape(t('privacy'))}</a><a href="${href('/offer')}">${escape(t('offer'))}</a><a href="${href('/payment')}">${escape(t('payment'))}</a><a href="${href('/orders')}">${escape(t('orders'))}</a><a href="${href('/refunds')}">${escape(t('refunds'))}</a><a href="${href('/remote')}">${escape(t('remote'))}</a></div>${paymentMarks()}</div>
  <div class="container footer-bottom"><span>© ${year} ${escape(COMPANY.displayName)}</span><a href="${href('/company')}">${escape(t('inn'))} ${escape(COMPANY.inn)}</a><a href="${href('/admin')}" class="admin-link" aria-label="${escape(t('dashboard'))}">${icon('lock')}</a></div>`;
}
function coreArt() {
 return `<div class="core-art" role="img" aria-label="${escape(t('coreAria'))}"><div class="art-grid"></div><div class="orbit orbit-one"></div><div class="orbit orbit-two"></div><div class="orbit orbit-three"></div><div class="core-shadow"></div><div class="core-platform platform-back"></div><div class="core-platform platform-middle"></div><div class="core-platform platform-front"></div><div class="core-block"><span class="core-n">N<span>↗</span></span><small>NEXACORE</small></div><div class="art-tag tag-ai"><span class="art-tag-icon">${icon('ai')}</span><div><b>${escape(t('tagAi'))}</b><small>${escape(t('tagAiSub'))}</small></div><i class="status-dot"></i></div><div class="art-tag tag-code"><span class="art-tag-icon">${icon('code')}</span><div><b>${escape(t('tagCode'))}</b><small>${escape(t('tagCodeSub'))}</small></div><i class="status-dot"></i></div><div class="art-tag tag-growth"><span class="art-tag-icon">${icon('chart')}</span><div><b>${escape(t('tagGrowth'))}</b><small>${escape(t('tagGrowthSub'))}</small></div><i class="status-dot"></i></div><span class="art-coordinate coordinate-a">${escape(t('coordA'))}</span><span class="art-coordinate coordinate-b">${escape(t('coordB'))}</span><span class="orbit-node node-a"></span><span class="orbit-node node-b"></span><span class="orbit-node node-c"></span></div>`;
}
const cardIcons = ['code','cloud','ai','consult','business','chart','project','engineering','marketing','education','booking','license'];
function categoryCard(cat, index) {
  const prices = cat.services.map(s=>s.price).filter(v=>v!=null);
  const min = prices.length ? Math.min(...prices) : null;
  const minPeriod = cat.services.find(s=>s.price===min)?.period || '';
 return `<a class="category-card ${index === 2 ? 'featured-card' : ''}" href="${href(`/services/${cat.id}`)}"><div class="card-top"><span class="category-icon">${icon(cardIcons[index])}</span><span class="card-index">${String(index+1).padStart(2,'0')} /</span></div><h3>${escape(cat.shortTitle || cat.title)}</h3><p>${escape(cat.description)}</p><div class="card-bottom"><span>${cat.services.length} ${pluralServices(cat.services.length)} <i>·</i> ${money(min,minPeriod)}</span><span class="card-arrow">${icon('arrow')}</span></div></a>`;
}
function renderHome() {
 document.title=t('homeTitle');
 document.querySelector('meta[name="description"]')?.setAttribute('content', t('homeDescription'));
 main.innerHTML = `<section class="hero"><div class="container hero-grid"><div class="hero-copy"><span class="eyebrow"><span class="tiny-cross">✳</span> ${escape(t('heroEyebrow'))}</span><h1>${t('heroTitle')}</h1><p class="hero-description">${escape(t('heroLead'))}</p><div class="hero-buttons"><a href="${href('/services')}" class="button button-navy">${escape(t('exploreSolutions'))} ${icon('arrow')}</a><a href="${href('/contact')}" class="text-button">${escape(t('talkToUs'))} ${icon('right')}</a></div><div class="hero-footnote"><span class="mini-squares"><i></i><i></i><i></i></span>${escape(t('heroFootnote'))}</div></div>${coreArt()}</div><div class="container hero-summary"><div><strong>12<span>↗</span></strong><span>${t('summaryAreas')}</span></div><div><strong>01<span>—</span></strong><span>${t('summaryPartner')}</span></div><div class="summary-last"><span class="summary-line"></span><span>${t('summaryThink')}</span></div><a href="${href('/?section=directions')}" class="scroll-cue" aria-label="${escape(t('exploreExpertise'))}">${icon('down')}</a></div></section>
 <section class="directions section-pad" id="directions"><div class="container"><div class="section-heading"><div><span class="eyebrow">${escape(t('expertiseEyebrow'))}</span><h2>${t('expertiseTitle')}</h2></div><div class="section-heading-aside"><p>${t('expertiseAside')}</p><a href="${href('/services')}" class="text-button">${escape(t('allServicesPricing'))} ${icon('arrow')}</a></div></div><div class="category-grid">${categories.map(categoryCard).join('')}</div><div class="catalogue-footnote"><span>${escape(t('notSure'))}</span><a href="${href('/contact')}">${escape(t('findStart'))} ${icon('right')}</a></div></div></section>
 <section class="feature-section" id="about"><div class="container feature-grid"><div class="feature-visual"><span class="eyebrow">${escape(t('aboutEyebrowVisual'))}</span><div class="system-diagram"><span class="system-circle circle-outer"></span><span class="system-circle circle-inner"></span><div class="system-center">${icon('layers')}<strong>${escape(t('yourGoal'))}</strong></div><span class="system-pill pill-one">${escape(t('pillTech'))}</span><span class="system-pill pill-two">${escape(t('pillStrategy'))}</span><span class="system-pill pill-three">${escape(t('pillPeople'))}</span><i class="diagram-dot dot-one"></i><i class="diagram-dot dot-two"></i><i class="diagram-dot dot-three"></i></div><div class="visual-caption"><span class="status-dot"></span>${escape(t('visualCaption'))}</div></div><div class="feature-copy"><span class="eyebrow">${escape(t('whyEyebrow'))}</span><h2>${t('whyTitle')}</h2><p>${escape(t('whyLead'))}</p><p class="company-facts">${escape(t('companyFacts'))} ${escape(COMPANY.ownServicesOnly[getLocale()])}</p><div class="feature-list"><div><span>01</span><div><h3>${escape(t('why1Title'))}</h3><p>${escape(t('why1Text'))}</p></div></div><div><span>02</span><div><h3>${escape(t('why2Title'))}</h3><p>${escape(t('why2Text'))}</p></div></div><div><span>03</span><div><h3>${escape(t('why3Title'))}</h3><p>${escape(t('why3Text'))}</p></div></div></div><a href="${href('/company')}" class="text-button">${escape(t('company'))} ${icon('arrow')}</a></div></div></section>
 <section class="process section-pad" id="approach"><div class="container"><div class="section-heading"><div><span class="eyebrow">${escape(t('processEyebrow'))}</span><h2>${t('processTitle')}</h2></div><p class="section-heading-aside">${t('processAside')}</p></div><div class="steps">${[[t('step1Title'),t('step1Text')],[t('step2Title'),t('step2Text')],[t('step3Title'),t('step3Text')],[t('step4Title'),t('step4Text')]].map((s,i)=>`<article class="step"><div class="step-line"><span>0${i+1}</span>${icon(i===3?'check':'right')}</div><h3>${escape(s[0])}</h3><p>${escape(s[1])}</p></article>`).join('')}</div></div></section>
 <section class="faq-section section-pad"><div class="container faq-grid"><div><span class="eyebrow">${escape(t('faqEyebrow'))}</span><h2>${t('faqTitle')}</h2><p>${t('faqMore')}<br><a href="${href('/contact')}">${escape(t('getInTouch'))} ${icon('arrow')}</a></p></div><div class="faq-list">${[['faq1q','faq1a'],['faq2q','faq2a'],['faq3q','faq3a'],['faq4q','faq4a'],['faq5q','faq5a'],['faq6q','faq6a']].map(([q,a])=>`<details><summary>${escape(t(q))}<span>${icon('plus')}</span></summary><p>${escape(t(a))}</p></details>`).join('')}</div></div></section>${cta()}`;
}
function cta() { return `<section class="cta-section"><div class="container cta-inner"><div><span class="eyebrow"><span class="status-dot"></span> ${escape(t('ctaEyebrow'))}</span><h2>${t('ctaTitle')}</h2><p>${escape(t('ctaLead'))}</p></div><a class="button button-white" href="${href('/contact')}">${escape(t('ctaProject'))} ${icon('arrow')}</a><span class="cta-orbit" aria-hidden="true"></span></div></section>`; }
function crumbs(items) { return `<nav class="breadcrumbs" aria-label="Breadcrumbs"><a href="${href('/')}">${escape(t('breadcrumbHome'))}</a>${items.map(([label,link])=>`${icon('chevron')}${link?`<a href="${href(link)}">${escape(label)}</a>`:`<span aria-current="page">${escape(label)}</span>`}`).join('')}</nav>`; }
function renderCatalog(categoryId) {
 const category = categories.find(c=>c.id===categoryId);
 if (categoryId && !category) return notFound();
 document.title=`${category?.title || t('allServices')} — NexaCore`;
 main.innerHTML=`<section class="page-intro container">${crumbs([[category?.shortTitle || t('allServices')]])}<span class="eyebrow">${escape(t('catalogueEyebrow'))}</span><div class="catalogue-heading"><h1>${escape(category?.shortTitle || t('catalogueTitle'))}<span class="blue-word">.</span></h1><p>${escape(category?.description || t('catalogueLead'))}</p></div></section><section class="catalogue-section container"><div class="catalogue-toolbar"><div class="search-field">${icon('search')}<input id="service-search" type="search" placeholder="${escape(t('searchPlaceholder'))}" aria-label="${escape(t('searchAria'))}"><kbd>/</kbd></div><span id="result-count"></span></div><div class="catalogue-layout"><aside class="category-sidebar" aria-label="${escape(t('allExpertise'))}"><a href="${href('/services')}" class="${!category?'active':''}">${escape(t('allExpertise'))} <span>${allServices.length}</span></a>${categories.map((c,i)=>`<a href="${href(`/services/${c.id}`)}" class="${c===category?'active':''}">${icon(cardIcons[i])}<span>${escape(c.shortTitle || c.title)}</span><small>${c.services.length}</small></a>`).join('')}</aside><div><div id="service-results" class="service-results"></div><p class="price-disclaimer">${escape(t('priceDisclaimer'))}</p></div></div></section>${cta()}`;
 const update = () => {
  const query=$('#service-search').value.trim().toLocaleLowerCase(getLocale());
  const list=(category?allServices.filter(s=>s.categoryId===category.id):allServices).filter(s=>`${s.title} ${s.description} ${s.categoryTitle}`.toLocaleLowerCase(getLocale()).includes(query));
  $('#result-count').textContent=`${list.length} ${pluralServices(list.length)}`;
  $('#service-results').innerHTML=list.length?list.map(serviceRow).join(''):`<div class="empty-state">${icon('search')}<h2>${escape(t('noServices'))}</h2><p>${escape(t('noServicesLead'))}</p><button class="button button-light" id="reset-search">${escape(t('clearSearch'))}</button></div>`;
  $('#reset-search')?.addEventListener('click',()=>{$('#service-search').value='';update();$('#service-search').focus();});
 };
 $('#service-search').addEventListener('input',update);$('#result-count').setAttribute('aria-live','polite');$('.category-sidebar a.active')?.setAttribute('aria-current','page');update();
}
function serviceRow(service) {
 return `<a class="service-row" href="${href(`/service/${service.id}`)}"><div><span class="service-category">${escape(service.categoryTitle)}</span><h2>${escape(service.title)}</h2><p>${escape(service.description)}</p><span class="service-duration">${icon('clock')} ${escape(service.duration)}</span></div><div class="service-price">${money(service.price,service.period)}<span>${escape(t('viewDetails'))} ${icon('arrow')}</span></div></a>`;
}
function renderService(id) {
 const s=allServices.find(x=>x.id===id);if(!s)return notFound();
 document.title=`${s.title} — NexaCore`;
 main.innerHTML=`<div class="container service-page">${crumbs([[t('navServices'),'/services'],[s.categoryTitle,`/services/${s.categoryId}`],[s.title]])}<div class="service-detail-grid"><article><span class="eyebrow">${escape(s.categoryTitle)}</span><h1>${escape(s.title)}<span class="blue-word">.</span></h1><p class="detail-lead">${escape(s.description)}</p><div class="detail-meta"><span>${icon('clock')} ${escape(s.duration)}</span><span>${icon('globe')} ${escape(t('remoteCollab'))}</span></div><h2>${escape(t('included'))}</h2><ul class="deliverables">${s.deliverables.map(d=>`<li>${icon('check')}<span>${escape(d)}</span></li>`).join('')}</ul><div class="detail-note">${icon('consult')}<p>${escape(t('detailNote'))}</p></div><h2>${escape(t('youReceive'))}</h2><p>${escape(t('youReceiveText'))}</p></article><aside class="order-panel"><span class="eyebrow">${escape(t('firstStep'))}</span><h2>${escape(t('findFit'))}</h2><div class="package-options">${(s.packages||[{name:'Basic',description:'',price:s.price},{name:'Custom',description:'',price:null}]).map((p,i)=>`<label class="package-option"><input type="radio" name="package" value="${escape(p.name)}" ${i===0?'checked':''}><span><strong>${escape(packageLabel(p.name))}</strong><small>${escape(p.description)}</small></span><b>${money(p.price,s.period)}</b></label>`).join('')}</div><button class="button button-navy full-width" data-order="${escape(s.id)}">${escape(t('enquire'))} ${icon('arrow')}</button><p class="panel-caption">${t('noPaymentYet')}</p><div class="panel-bottom">${icon('license')}<span>${t('indicativePanel')}</span></div></aside></div></div>${cta()}`;
}
function orderForm(service, inDialog=false) {
 return `<form class="enquiry-form" data-service="${escape(service?.id || 'consultation')}"><div class="form-row"><label>${escape(t('yourName'))} <span>*</span><input name="name" autocomplete="name" placeholder="${escape(t('namePlaceholder'))}" required minlength="2" maxlength="100"></label><label>${escape(t('emailAddress'))} <span>*</span><input type="email" name="email" autocomplete="email" placeholder="${escape(t('emailPlaceholder'))}" required maxlength="254"></label></div><label>${escape(t('companyLabel'))}<input name="company" autocomplete="organization" placeholder="${escape(t('companyPlaceholder'))}" maxlength="200"></label>${!service?`<label>${escape(t('serviceInterest'))}<select name="direction"><option value="consultation">${escape(t('helpChoose'))}</option>${categories.map(c=>`<optgroup label="${escape(c.shortTitle||c.title)}">${c.services.map(s=>`<option value="${escape(s.id)}">${escape(s.title)}</option>`).join('')}</optgroup>`).join('')}</select></label>`:''}<label>${escape(t('projectLabel'))} ${!service?'<span>*</span>':''}<textarea name="message" rows="${inDialog?3:4}" placeholder="${escape(t('projectPlaceholder'))}" maxlength="5000" ${!service?'required minlength="10"':''}></textarea></label><label class="consent-label"><input name="consent" type="checkbox" required><span>${escape(t('consent'))} <a href="${href('/privacy')}" target="_blank" rel="noopener">${escape(t('privacy'))}</a>.</span></label><div class="form-error" role="alert"></div><button type="submit" class="button button-navy full-width">${escape(service?t('sendEnquiry'):t('startConversation'))} ${icon('arrow')}</button><p class="form-hint">${escape(t('formHint'))}</p></form>`;
}
function renderContact() {
 document.title=t('contactTitle');
 main.innerHTML=`<section class="container contact-page">${crumbs([[t('ctaProject')]])}<div class="contact-grid"><div><span class="eyebrow">${escape(t('contactEyebrow'))}</span><h1>${t('contactH1')}</h1><p class="detail-lead">${escape(t('contactLead'))}</p><div class="contact-expectations"><h2>${escape(t('whatNext'))}</h2>${[['01',t('next1')],['02',t('next2')],['03',t('next3')]].map(([n,label])=>`<div><span>${n}</span>${escape(label)}</div>`).join('')}</div><div class="contact-mini">${icon('globe')}<span>${t('workRemotely')}</span></div><div class="company-contacts">${companyRequisites()}${paymentMarks()}</div></div><div class="contact-form-card"><div class="form-card-heading"><span class="status-dot"></span> ${escape(t('tellProject'))}</div>${orderForm()}</div></div></section>`;
 bindForms();
}
function openOrder(id) {
 const s=allServices.find(x=>x.id===id);if(!s)return;
 const dialog=$('#order-dialog');
 const selected=$('input[name="package"]:checked')?.value || 'Basic';
 const selectedPackage=s.packages?.find(p=>p.name===selected);
 dialog.innerHTML=`<button class="dialog-close icon-button" aria-label="${escape(t('closeDialog'))}">${icon('close')}</button><span class="eyebrow">${escape(t('getStarted'))}</span><h2 id="order-title">${escape(s.title)}</h2><p class="dialog-subtitle">${escape(packageLabel(selected))} · ${money(selectedPackage ? selectedPackage.price : s.price,s.period)}</p>${orderForm(s,true)}`;
 dialog.dataset.package=selected;
 $('.dialog-close',dialog).addEventListener('click',()=>dialog.close());
 dialog.showModal();bindForms(dialog);
}
function bindForms(root=document) {
 root.querySelectorAll('.enquiry-form').forEach(form=>form.addEventListener('submit',async event=>{
  event.preventDefault();const button=$('button[type="submit"]',form), error=$('.form-error',form);error.textContent='';
  if(!form.reportValidity())return;
  button.disabled=true;button.textContent=t('saving');
  const values=Object.fromEntries(new FormData(form));
  try {
   const {order}=await api('/api/orders',{method:'POST',body:JSON.stringify({name:values.name,email:values.email,company:values.company,message:values.message,consent:values.consent==='on',serviceId:values.direction||form.dataset.service,packageName:root===$('#order-dialog')?root.dataset.package:''})});
   const card=form.closest('.contact-form-card')||form;
   card.innerHTML=`<div class="success-state"><span class="success-icon">${icon('check')}</span><span class="eyebrow">${escape(t('successEyebrow'))}</span><h2>${escape(t('enquiryReceived'))}</h2><p>${escape(t('yourReference'))}</p><strong class="order-reference">${escape(order.reference||order.id)}</strong><p>${escape(t('successLead'))}</p><a href="${href('/services')}" class="button button-navy" data-close-dialog>${escape(t('backToServices'))} ${icon('arrow')}</a></div>`;
  } catch(e) {error.textContent=e.message;button.disabled=false;button.innerHTML=`${escape(t('tryAgain'))} ${icon('arrow')}`;}
 }));
}
function renderLegal(kind) {
 const slug = kind === 'terms' ? 'offer' : kind;
 const doc = legalDoc(slug, getLocale());
 if (!doc) return notFound();
 document.title=`${doc.title} — NexaCore`;
 main.innerHTML=`<article class="container legal-page">${crumbs([[doc.title]])}<span class="eyebrow">${escape(t('legalInfo'))}</span><h1>${escape(doc.title)}</h1>${slug==='company'?'':`<p class="legal-entity">${escape(COMPANY.displayName)}</p>`}${doc.sections.map(section=>`<h2>${escape(section.h)}</h2><p>${escape(section.p)}</p>`).join('')}${paymentMarks()}<a href="${href('/contact')}" class="text-button">${escape(t('getInTouch'))} ${icon('arrow')}</a></article>`;
}
async function renderAdmin() {
 document.title='Dashboard — NexaCore';
 main.innerHTML=`<section class="admin-page"><div id="admin-content" class="loading-state">Checking access…</div></section>`;
 try { const session=await api('/api/admin/session'); if(session.authenticated)await renderDashboard();else renderLogin(); } catch(e){$('#admin-content').className='admin-access-error';$('#admin-content').innerHTML=`<div class="error-state">${escape(e.message)}</div>`;}
}
function invoiceControls(order,invoice,billing) {
 if(invoice)return `<div class="invoice-actions">${invoice.verificationError?`<small role="alert">Verification needs attention: ${escape(invoice.verificationError)}</small>`:''}<small>${invoice.mode==='sandbox'?'TEST · ':''}${escape(invoice.status)} · ${escape((invoice.amount/100).toFixed(2))} ${escape(invoice.currency)}</small><a class="button button-light button-small" href="${escape(invoice.checkoutPath)}" target="_blank" rel="noopener">Open payment link ${icon('arrow')}</a>${invoice.canReissue&&adminAccess.canManageSettings&&order.status!=='cancelled'?`<button class="button button-light button-small" data-create-invoice="${escape(order.id)}">Renew payment link ${icon('plus')}</button>`:''}</div>`;
 if(!billing.configured)return '<small>Payments not connected</small>';
 return `<small>No payment requested</small>${adminAccess.canManageSettings&&order.status!=='cancelled'?`<div class="invoice-actions"><button class="button button-light button-small" data-create-invoice="${escape(order.id)}">${billing.mode==='sandbox'?'Create test payment':'Create payment link'} ${icon('plus')}</button></div>`:''}`;
}
function openInvoice(order,billing) {
 const dialog=$('#order-dialog');
 dialog.innerHTML=`<button class="dialog-close icon-button" data-close-dialog aria-label="Close">${icon('close')}</button><span class="eyebrow">${billing.mode==='sandbox'?'PAYLER SANDBOX':'PAYMENT LINK'}</span><h2 id="order-title">Request an agreed payment</h2><p>${escape(order.reference)} · ${escape(order.serviceTitle)}</p><form id="invoice-form"><label>Agreed amount (${escape(billing.currency)})<input name="amount" type="number" min="0.01" max="1000000" step="0.01" inputmode="decimal" required autocomplete="off"></label><p class="form-hint">${billing.mode==='sandbox'?'Test cards only. No real funds are transferred.':'Use the amount agreed with the client. This link does not charge automatically.'} The currency is ${escape(billing.currency)}; catalogue prices are indicative.</p><label class="consent-label"><input type="checkbox" required><span>I have checked the amount and currency.</span></label><div class="form-error" role="alert"></div><button class="button button-navy full-width" type="submit">Create payment link ${icon('right')}</button></form>`;
 dialog.showModal();
 $('#invoice-form').addEventListener('submit',async e=>{
  e.preventDefault();const form=e.currentTarget,button=$('button',form);button.disabled=true;
  try {
   const {payment}=await api(`/api/admin/orders/${encodeURIComponent(order.id)}/payment`,{method:'POST',body:JSON.stringify({amount:new FormData(form).get('amount')})});
   const address=new URL(payment.checkoutPath,location.origin).href;
   form.innerHTML=`<div class="payment-link-result"><strong>Payment link ready</strong><label>Share with your client<input id="invoice-link" readonly value="${escape(address)}"></label><button type="button" id="copy-invoice" class="button button-navy">Copy link ${icon('check')}</button><a class="text-button" href="${escape(payment.checkoutPath)}" target="_blank" rel="noopener">Open payment page ${icon('arrow')}</a><p class="form-hint">This link expires in 24 hours.${billing.mode==='sandbox'?' Sandbox only; use test card details.':''}</p></div>`;
   $('#copy-invoice').addEventListener('click',async()=>{try{await navigator.clipboard.writeText(address);notify('Payment link copied');}catch{$('#invoice-link').select();notify('Select and copy the link');}});
   await renderDashboard();
  }catch(err){$('.form-error',form).textContent=err.message;button.disabled=false;}
 });
}
function renderLogin() {
 $('#admin-content').className='admin-login-layout';
 $('#admin-content').innerHTML=`<div class="login-card"><span class="category-icon">${icon('lock')}</span><h1>Team sign in</h1><p>Manage your services and enquiries.</p><form id="admin-login"><label>Password<input type="password" name="password" autocomplete="current-password" required></label><div class="form-error" role="alert"></div><button class="button button-navy full-width" type="submit">Sign in ${icon('right')}</button></form><p class="form-hint">Use your team's workspace password.</p></div><p class="admin-login-caption">NexaCore workspace · Team access</p>`;
 $('#admin-login').addEventListener('submit',async e=>{e.preventDefault();const form=e.currentTarget,btn=$('button',form);btn.disabled=true;try{await api('/api/admin/login',{method:'POST',body:JSON.stringify({password:new FormData(form).get('password')})});activeAdminTab='orders';await renderDashboard();}catch(err){$('.form-error',form).textContent=err.message;btn.disabled=false;}});
}
async function renderDashboard() {
 adminAccess=await api('/api/admin/access');
 const host=$('#admin-content');
 host.className='admin-workspace';
 const tabs=[['orders','Enquiries','mail'],['services','Services and pricing','layers'],['categories','Categories','project'],...(adminAccess.canManageSettings?[['settings','Settings','engineering']]:[]),['audit','Activity','clock']];
 host.innerHTML=`<aside class="admin-sidebar"><span class="admin-nav-label">WORKSPACE</span><nav class="admin-tabs" aria-label="Workspace navigation">${tabs.map(([id,label,glyph])=>`<button type="button" data-admin-tab="${id}" ${activeAdminTab===id?'class="active" aria-current="page"':''}>${icon(glyph)}<span>${label}</span></button>`).join('')}</nav><div class="admin-sidebar-bottom"><span class="admin-role">${icon('lock')} ${escape(adminAccess.role || 'Team')} access</span><button id="admin-logout" class="text-button">Sign out ${icon('right')}</button></div></aside><div class="admin-main"><div id="admin-view" class="loading-state">Loading…</div></div>`;
 host.querySelectorAll('[data-admin-tab]').forEach(btn=>btn.addEventListener('click',async()=>{activeAdminTab=btn.dataset.adminTab;try{await renderDashboard();$('[data-admin-tab][aria-current="page"]')?.focus({preventScroll:true});}catch(e){notify(e.message);}}));
 $('#admin-logout').addEventListener('click',async()=>{try{await api('/api/admin/logout',{method:'POST',body:'{}'});renderLogin();}catch(e){notify(e.message);}});
 try {
  const view=$('#admin-view');
  if(activeAdminTab==='orders') {
   const [{orders},billing]=await Promise.all([api('/api/admin/orders'),api('/api/admin/payments')]);
   const invoices=new Map([...billing.payments].reverse().map(payment=>[payment.orderId,payment]));
   view.className='';view.innerHTML=`<div class="admin-section-heading"><div><h1>Incoming enquiries <span class="count-badge">${orders.length}</span></h1><p>${billing.configured?`Payler ${escape(billing.mode)} · ${escape(billing.currency)}. Create payment links only for agreed amounts.`:'Card payments are not configured. Website enquiries are saved here.'}</p></div><div class="admin-downloads"><a class="button button-light button-small" href="/api/admin/export">Export CSV ${icon('arrow')}</a><a class="text-button" href="/api/admin/backup">Download backup ${icon('arrow')}</a></div></div>${orders.length?`<div class="table-scroll"><table><thead><tr><th>Enquiry</th><th>Client</th><th>Service and project</th><th>Status</th></tr></thead><tbody>${orders.map(o=>`<tr><td><strong>${escape(o.reference||o.id)}</strong><small>${new Date(o.createdAt).toLocaleDateString('en-US')}</small></td><td><strong>${escape(o.name)}</strong><a href="mailto:${escape(o.email)}">${escape(o.email)}</a><small>${escape(o.company)}</small></td><td><strong>${escape(o.serviceTitle||allServices.find(s=>s.id===o.serviceId)?.title||'Project enquiry')}</strong><p>${escape(o.message)}</p></td><td><select aria-label="Status ${escape(o.reference||o.id)}" data-order-status="${escape(o.id)}">${[['new','New'],['in_progress','In progress'],['completed','Completed'],['cancelled','Cancelled']].map(([v,label])=>`<option value="${v}" ${o.status===v?'selected':''}>${label}</option>`).join('')}</select>${invoiceControls(o,invoices.get(o.id),billing)}</td></tr>`).join('')}</tbody></table></div>`:`<div class="empty-state">${icon('mail')}<h2>Your enquiries will appear here</h2><p>Enquiries submitted through the form are stored on the server.</p></div>`}`;
   view.querySelectorAll('[data-create-invoice]').forEach(button=>button.addEventListener('click',()=>openInvoice(orders.find(order=>order.id===button.dataset.createInvoice),billing)));
   view.querySelectorAll('[data-order-status]').forEach(sel=>sel.addEventListener('change',async()=>{sel.disabled=true;try{await api(`/api/admin/orders/${encodeURIComponent(sel.dataset.orderStatus)}`,{method:'PATCH',body:JSON.stringify({status:sel.value})});notify('Status saved');}catch(e){notify(e.message);await renderDashboard();}finally{sel.disabled=false;}}));
  } else if(activeAdminTab==='services') {
   view.className='';view.innerHTML=`<div class="admin-section-heading"><div><h1>Service catalogue</h1><p>Changes are saved on the server and displayed in the public catalogue. Public pages also show Kyrgyz and Russian catalogue text from the published translation files.</p></div></div><label class="admin-service-picker">Choose a service<select id="edit-service-select">${categories.map(c=>`<optgroup label="${escape(c.title)}">${c.services.map(s=>`<option value="${escape(s.id)}">${escape(s.title)}</option>`).join('')}</optgroup>`).join('')}</select></label><div id="service-editor"></div>`;
   $('#edit-service-select').addEventListener('change',e=>renderServiceEditor(e.target.value));renderServiceEditor(allServices[0].id);
  } else if(activeAdminTab==='categories') {
   await renderCategoryAdmin(view);
  } else if(activeAdminTab==='settings') {
   await renderSettingsAdmin(view);
  } else {
   const result=await api('/api/admin/audit');const events=result.events||[];
   view.className='';view.innerHTML=`<h1>Activity log</h1><div class="audit-list">${events.length?events.map(ev=>`<div><time>${escape(new Date(ev.at||ev.createdAt||ev.timestamp).toLocaleString('en-US'))}</time><strong>${escape(ev.action)}</strong><span>${escape(ev.targetId||ev.target||'')}</span></div>`).join(''):'<p>No activity yet.</p>'}</div>`;
  }
  if(!adminAccess.canEditOrders && activeAdminTab==='orders')view.querySelectorAll('select').forEach(el=>el.disabled=true);
  if(!adminAccess.canEditCatalog && ['services','categories'].includes(activeAdminTab))view.querySelectorAll('form input,form textarea,form button').forEach(el=>el.disabled=true);
  if(!adminAccess.canExport)view.querySelector('a[href="/api/admin/export"]')?.remove();
  if(!adminAccess.canBackup)view.querySelector('a[href="/api/admin/backup"]')?.remove();
 }catch(e){$('#admin-view').innerHTML=`<div class="error-state">${escape(e.message)}</div>`;}
}
function renderServiceEditor(id) {
 const s=allServices.find(x=>x.id===id);if(!s)return;
 $('#service-editor').innerHTML=`<form id="service-editor-form" class="editor-form"><label>Title<input name="title" value="${escape(s.title)}" required maxlength="200"></label><div class="form-row"><label>Starting price, USD<input name="price" type="number" min="0" max="10000000" step="0.01" value="${s.price??''}"></label><label>Estimated timeline<input name="duration" value="${escape(s.duration)}" required maxlength="200"></label></div><label>Billing period (leave blank for a one-time service)<input name="period" value="${escape(s.period)}" maxlength="100" placeholder="For example: /month"></label><label>Description<textarea name="description" rows="3" required maxlength="3000">${escape(s.description)}</textarea></label><label>Deliverables — one item per line<textarea name="deliverables" rows="5" required>${escape(s.deliverables.join('\n'))}</textarea></label><fieldset class="package-editor"><legend>Options and packages</legend><div id="package-editor-list">${s.packages.map(packageEditor).join('')}</div><button type="button" class="text-button" id="add-package">${icon('plus')} Add package</button></fieldset><div class="form-error" role="alert"></div><button class="button button-navy" type="submit">Save changes ${icon('check')}</button></form>`;
 $('#service-editor-form input[name="price"]').addEventListener('change',e=>{const first=$('[data-package-price]');if(first)first.value=e.target.value;});
 $('#add-package').addEventListener('click',()=>{if(document.querySelectorAll('.package-edit-row').length>=10)return notify('A maximum of 10 packages is allowed');$('#package-editor-list').insertAdjacentHTML('beforeend',packageEditor({name:'',description:'',price:null}));});
 $('#package-editor-list').addEventListener('click',e=>{if(e.target.closest('[data-remove-package]')){if(document.querySelectorAll('.package-edit-row').length<=1)return notify('Keep at least one package');e.target.closest('.package-edit-row').remove();}});
 if(!adminAccess.canEditCatalog)$('#service-editor-form').querySelectorAll('input,textarea,button').forEach(el=>el.disabled=true);
 $('#service-editor-form').addEventListener('submit',async e=>{e.preventDefault();const f=e.currentTarget,v=Object.fromEntries(new FormData(f)),btn=$('button[type="submit"]',f);btn.disabled=true;try{await api(`/api/admin/services/${encodeURIComponent(id)}`,{method:'PUT',body:JSON.stringify({...v,price:v.price===''?null:Number(v.price),deliverables:v.deliverables.split('\n').map(v=>v.trim()).filter(Boolean),packages:readPackages(f)})});await loadCatalog(true);notify('Service updated');}catch(err){$('.form-error',f).textContent=err.message;}finally{btn.disabled=false;}});
}
function packageEditor(p) {
 return `<div class="package-edit-row"><div class="form-row"><label>Package name<input data-package-name value="${escape(p.name)}" required maxlength="160"></label><label>Price, USD (leave blank for a custom quote)<input type="number" min="0" step="0.01" data-package-price value="${p.price??''}"></label></div><label>Package description<textarea data-package-description rows="2" required maxlength="3000">${escape(p.description)}</textarea></label><button class="text-button remove-package" type="button" data-remove-package>Remove package ${icon('close')}</button></div>`;
}
function readPackages(form) {
 return [...form.querySelectorAll('.package-edit-row')].map(row=>({name:$('[data-package-name]',row).value.trim(),description:$('[data-package-description]',row).value.trim(),price:$('[data-package-price]',row).value===''?null:Number($('[data-package-price]',row).value)}));
}
async function loadSettings(){settings=await api('/api/settings');}
async function renderSettingsAdmin(view) {
 const values=await api('/api/admin/settings');view.className='';
 view.innerHTML=`<div class="admin-section-heading"><div><h1>Contact details and documents</h1><p>Public company details, prices in KGS and acquiring documents are published from the website copy in English, Russian and Kyrgyz. Empty contact fields fall back to the registered company details.</p></div></div><form class="editor-form" id="settings-form"><label>Company name<input name="companyName" value="${escape(values.companyName)}" required maxlength="200"></label><label>Brand tagline<input name="tagline" value="${escape(values.tagline)}" maxlength="500" placeholder="Technology. Strategy. Growth."></label><div class="form-row"><label>Contact email<input type="email" name="email" value="${escape(values.email)}" maxlength="254"></label><label>Phone<input type="tel" name="phone" value="${escape(values.phone)}" maxlength="100"></label></div><label>Address<input name="address" value="${escape(values.address)}" maxlength="1000"></label><label>Legal entity and registration details<textarea name="legalEntity" rows="3" maxlength="1000">${escape(values.legalEntity)}</textarea></label><label>Website language<select name="locale"><option value="en">English (admin)</option></select></label><p class="editor-explanation">The public website is available in English, Russian and Kyrgyz. Visitors choose the language in the header. Admin stays in English.</p><label>Privacy policy notes<textarea name="privacyText" rows="7" maxlength="12000" placeholder="Internal notes. Public privacy, offer, payment, refund and remote-service pages are published separately in three languages.">${escape(values.privacyText)}</textarea></label><label>Terms notes<textarea name="termsText" rows="7" maxlength="12000" placeholder="Internal notes. The public offer is published separately.">${escape(values.termsText)}</textarea></label><div class="form-error" role="alert"></div><button class="button button-navy" type="submit">Save settings ${icon('check')}</button><div class="access-info"><h3>Team access</h3><p>Owners manage the entire website. Editors update services, categories and enquiry statuses. Viewers have read-only access. Role passwords are defined in the server configuration.</p></div></form>`;
 $('#settings-form').addEventListener('submit',async e=>{e.preventDefault();const form=e.currentTarget,btn=$('button[type="submit"]',form);btn.disabled=true;try{await api('/api/admin/settings',{method:'PUT',body:JSON.stringify(Object.fromEntries(new FormData(form)))});await loadSettings();renderFooter();notify('Settings saved');}catch(err){$('.form-error',form).textContent=err.message;}finally{btn.disabled=false;}});
}
async function renderCategoryAdmin(view) {
 view.className='';view.innerHTML=`<div class="admin-section-heading"><div><h1>Categories</h1><p>Card titles and descriptions used on the homepage and in the catalogue.</p></div></div><label class="admin-service-picker">Choose a category<select id="category-edit-select">${categories.map(c=>`<option value="${escape(c.id)}">${escape(c.title)}</option>`).join('')}</select></label><div id="category-editor"></div>`;
 const edit=id=>{
  const c=categories.find(c=>c.id===id);
  $('#category-editor').innerHTML=`<form class="editor-form" id="category-form"><label>Full title<input name="title" value="${escape(c.title)}" required maxlength="200"></label><label>Short card title<input name="shortTitle" value="${escape(c.shortTitle)}" required maxlength="100"></label><label>Description<textarea name="description" rows="4" required maxlength="3000">${escape(c.description)}</textarea></label><div class="form-error" role="alert"></div><button class="button button-navy" type="submit">Save category ${icon('check')}</button></form>`;
  const form=$('#category-form');if(!adminAccess.canEditCatalog)form.querySelectorAll('input,textarea,button').forEach(el=>el.disabled=true);
  form.addEventListener('submit',async e=>{e.preventDefault();const btn=$('button',form);btn.disabled=true;try{await api(`/api/admin/categories/${encodeURIComponent(id)}`,{method:'PUT',body:JSON.stringify(Object.fromEntries(new FormData(form)))});await loadCatalog(true);notify('Category saved');}catch(err){$('.form-error',form).textContent=err.message;}finally{btn.disabled=false;}});
 };
 $('#category-edit-select').addEventListener('change',e=>edit(e.target.value));edit(categories[0].id);
}
function notFound(){document.title=t('notFoundTitle');main.innerHTML=`<div class="container empty-state not-found"><span class="eyebrow">${escape(t('notFoundEyebrow'))}</span><h1>${t('notFoundLead')}</h1><a href="${href('/services')}" class="button button-navy">${escape(t('exploreSolutions'))} ${icon('arrow')}</a></div>`;}
let catalogLang = '';
async function loadCatalog(force = false){
  const lang = currentAppPath() === '/admin' ? 'en' : getLocale();
  if (!force && lang === catalogLang && categories.length) return;
  const data = await api(`/api/catalog?lang=${encodeURIComponent(lang)}`);
  categories = data.categories;
  allServices = categories.flatMap(c => c.services.map(s => ({ ...s, categoryId: c.id, categoryTitle: c.shortTitle || c.title })));
  catalogLang = lang;
}
function syncLocaleFromLocation() {
  const { locale: pathLocale, path } = parseRoute(location.pathname);
  const next = pathLocale !== 'en' || /^\/(ru|ky)(\/|$)/.test(location.pathname) ? pathLocale : detectLocale(location.pathname);
  setLocale(next);
  if (next !== 'en' && pathLocale === 'en' && path !== '/admin') {
    history.replaceState(null, '', localizePath(path, next) + location.search);
  }
}
async function switchLocale(next) {
  if (next === getLocale()) return;
  const { path } = parseRoute(location.pathname);
  setLocale(next);
  history.pushState(null, '', localizePath(path, next) + location.search);
  await loadCatalog();
  await route();
}
async function route() {
 stopCheckout();
 $('#order-dialog').close();
 syncLocaleFromLocation();
 await loadCatalog();
 renderHeader();
 renderFooter();
 const { path } = parseRoute(location.pathname);
 const section=new URLSearchParams(location.search).get('section');
 if(path==='/')renderHome();
 else if(path==='/services')renderCatalog();
 else if(path.startsWith('/services/'))renderCatalog(decodeURIComponent(path.split('/')[2]));
 else if(path.startsWith('/service/'))renderService(decodeURIComponent(path.split('/')[2]));
 else if(path==='/contact')renderContact();
 else if(path==='/privacy'||path==='/terms'||LEGAL_SLUGS.includes(path.slice(1)))renderLegal(path.slice(1));
 else if(path==='/admin')await renderAdmin();
 else if(/^\/checkout\/[a-f0-9]{64}$/.test(path))await renderCheckout({token:path.split('/')[2],main,api,escape,icon,locale:getLocale(),href});
 else notFound();
 refreshMotion();
 if(section){requestAnimationFrame(()=>document.getElementById(section)?.scrollIntoView({behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'instant':'smooth'}));}else window.scrollTo(0,0);
}
document.addEventListener('click',e=>{
  const lang=e.target.closest('[data-locale]');
  if(lang){e.preventDefault();switchLocale(lang.dataset.locale).catch(err=>notify(err.message));return;}
  if(!e.target.closest('.language-switcher')){const menu=$('.language-menu');const toggle=$('.language-toggle');if(menu&&!menu.hidden){menu.hidden=true;toggle?.setAttribute('aria-expanded','false');}}
  if(e.target.closest('.skip-link')){e.preventDefault();main.focus();main.scrollIntoView();return;}
  const order=e.target.closest('[data-order]');if(order)openOrder(order.dataset.order);
  if(e.target.closest('[data-close-dialog]'))$('#order-dialog').close();
  const a=e.target.closest('a');
  if(a&&!a.target&&!a.hasAttribute('download')&&!e.ctrlKey&&!e.metaKey&&!e.shiftKey&&!e.altKey&&e.button===0){
    const url=new URL(a.href);
    if(url.origin===location.origin&&!url.pathname.startsWith('/api/')&&a.getAttribute('href').startsWith('/')){
      e.preventDefault();history.pushState(null,'',url.pathname+url.search);main.focus({preventScroll:true});route().catch(err=>notify(err.message));
    }
  }
});
$('#order-dialog').addEventListener('click',e=>{if(e.target===$('#order-dialog')){const r=e.target.getBoundingClientRect();if(e.clientX<r.left||e.clientX>r.right||e.clientY<r.top||e.clientY>r.bottom)e.target.close();}});
document.addEventListener('keydown',e=>{if(e.key==='/'&&!['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName)&&$('#service-search')){e.preventDefault();$('#service-search').focus();}});
window.addEventListener('popstate',()=>{main.focus({preventScroll:true});route().catch(e=>notify(e.message));});
if(location.hash.startsWith('#/'))history.replaceState(null,'',location.hash.slice(1));
async function boot(){
  syncLocaleFromLocation();
  renderHeader();
  renderFooter();
  try{
    await Promise.all([loadCatalog(),loadSettings()]);
    renderFooter();
    await route();
  }catch(e){
    main.innerHTML=`<div class="container empty-state"><h1>${escape(t('loadError'))}</h1><p>${escape(e.message)}</p><button class="button button-navy" id="retry-load">${escape(t('tryAgain'))}</button></div>`;
    $('#retry-load').addEventListener('click',boot);
  }
}
initMotion();
boot();
