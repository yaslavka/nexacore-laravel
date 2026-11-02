const copy = {
  en: {
    title: 'Your project. Ready for the next step.', subtitle: 'Secure payment for your agreed NexaCore services.', reference: 'Enquiry reference', amount: 'Amount due', test: 'Test payment', testNote: 'This is a Payler sandbox payment. Use test card details only. No real funds are transferred.',
    cancelled: 'Payment cancelled', created: 'Ready to pay', pending: 'Awaiting payment confirmation', paid: 'Payment confirmed', authorized: 'Payment authorized; confirmation pending', declined: 'The last payment attempt was declined', refunded: 'Payment refunded', reversed: 'Payment authorization reversed', unknown: 'Payment has not been confirmed', expired: 'This payment link has expired',
    pay: 'Continue to secure payment', refresh: 'Check payment status', checking: 'Checking payment…', consent: 'I accept the public offer and refund policy.', offer: 'Public offer', refunds: 'Refund policy', security: 'Card details are entered only on Payler’s secure payment page. NexaCore does not receive or store them.', contact: 'Contact NexaCore', error: 'We could not check the payment. Please try again.', thanks: 'Thank you. Your payment has been verified with Payler.', due: 'Pay the agreed amount shown below. The currency is fixed for this payment.', testPaid: 'Test payment confirmed', testThanks: 'The sandbox flow completed successfully. This is not a real payment.'
  },
  ru: {
    title: 'Ваш проект. Следующий шаг.', subtitle: 'Безопасная оплата согласованных услуг NexaCore.', reference: 'Номер заявки', amount: 'К оплате', test: 'Тестовый платёж', testNote: 'Это тестовая среда Payler. Используйте только тестовые карты. Реальные деньги не списываются.',
    cancelled: 'Платёж отменён', created: 'Готово к оплате', pending: 'Ожидаем подтверждение оплаты', paid: 'Оплата подтверждена', authorized: 'Средства заблокированы; оплата ещё не подтверждена', declined: 'Последняя попытка оплаты отклонена', refunded: 'Платёж возвращён', reversed: 'Блокировка средств отменена', unknown: 'Оплата не подтверждена', expired: 'Срок действия ссылки истёк',
    pay: 'Перейти к безопасной оплате', refresh: 'Проверить статус', checking: 'Проверяем платёж…', consent: 'Я принимаю публичную оферту и условия возврата.', offer: 'Публичная оферта', refunds: 'Условия возврата', security: 'Реквизиты карты вводятся только на защищённой странице Payler. NexaCore не получает и не хранит их.', contact: 'Связаться с NexaCore', error: 'Не удалось проверить платёж. Попробуйте ещё раз.', thanks: 'Спасибо. Оплата подтверждена через Payler.', due: 'Оплатите согласованную сумму ниже. Валюта этого платежа зафиксирована.', testPaid: 'Тестовая оплата подтверждена', testThanks: 'Тестовый сценарий завершён успешно. Это не настоящий платёж.'
  },
  ky: {
    title: 'Долбооруңуздун кийинки кадамы.', subtitle: 'NexaCore менен макулдашылган кызматтар үчүн коопсуз төлөм.', reference: 'Өтүнмөнүн номери', amount: 'Төлөнө турган сумма', test: 'Сыноо төлөмү', testNote: 'Бул Payler сыноо чөйрөсү. Сыноо карталарын гана колдонуңуз. Чыныгы акча алынбайт.',
    cancelled: 'Төлөм жокко чыгарылды', created: 'Төлөөгө даяр', pending: 'Төлөмдүн ырасталышын күтүүдө', paid: 'Төлөм ырасталды', authorized: 'Каражат бөгөттөлдү; төлөм азырынча ырастала элек', declined: 'Акыркы төлөм аракети четке кагылды', refunded: 'Төлөм кайтарылды', reversed: 'Каражаттын бөгөтү алынды', unknown: 'Төлөм ырастала элек', expired: 'Шилтеменин мөөнөтү бүттү',
    pay: 'Коопсуз төлөмгө өтүү', refresh: 'Төлөмдү текшерүү', checking: 'Төлөм текшерилүүдө…', consent: 'Ачык офертага жана кайтаруу шарттарына макулмун.', offer: 'Ачык оферта', refunds: 'Кайтаруу шарттары', security: 'Картанын маалыматы Payler коопсуз барагында гана киргизилет. NexaCore аны албайт жана сактабайт.', contact: 'NexaCore менен байланышуу', error: 'Төлөмдү текшерүү мүмкүн болгон жок. Кайра аракет кылыңыз.', thanks: 'Рахмат. Төлөм Payler аркылуу ырасталды.', due: 'Төмөндө көрсөтүлгөн макулдашылган сумманы төлөңүз. Бул төлөмдүн валютасы бекитилген.', testPaid: 'Сыноо төлөмү ырасталды', testThanks: 'Сыноо ийгиликтүү аяктады. Бул чыныгы төлөм эмес.'
  }
};
let timer;
let active = 0;
export function stopCheckout() { active++; clearTimeout(timer); }
export async function renderCheckout({ token, main, api, escape, icon, locale, href }) {
  stopCheckout();
  const generation = active;
  const c = copy[locale] || copy.en;
  main.innerHTML = `<section class="container checkout-page"><div class="loading-state" role="status">${escape(c.checking)}</div></section>`;
  let payment = (await api(`/api/payments/${token}`)).payment;
  let error = '';
  let polls = 0;
  async function check() {
    try { payment = (await api(`/api/payments/${token}/status`, { method: 'POST', body: '{}' })).payment; error = ''; }
    catch (e) { error = e.message || c.error; }
    if (generation !== active) return;
    draw();
    if (['pending', 'authorized', 'unknown'].includes(payment.status) && polls < 5) {
      timer = setTimeout(check, [2000, 5000, 10000, 20000, 30000][polls++]);
    }
  }
  function draw() {
    if (generation !== active) return;
    const paid = payment.status === 'paid';
    const closed = ['paid', 'refunded', 'reversed', 'cancelled'].includes(payment.status);
    const amount = new Intl.NumberFormat(locale === 'ru' ? 'ru-RU' : 'en-US', { style: 'currency', currency: payment.currency }).format(payment.amount / 100);
    const label = paid && payment.mode === 'sandbox' ? c.testPaid : c[payment.expired && !paid ? 'expired' : payment.status] || c.unknown;
    main.innerHTML = `<section class="container checkout-page"><div class="checkout-intro"><span class="eyebrow">NEXACORE / ${escape(payment.reference)}</span><h1>${escape(c.title)}</h1><p class="detail-lead">${escape(c.subtitle)}</p><p>${escape(c.security)}</p><a class="text-button" href="${href('/contact')}">${escape(c.contact)} ${icon('arrow')}</a></div><div class="checkout-card">${payment.mode === 'sandbox' ? `<div class="checkout-test"><strong>${escape(c.test)}</strong><p>${escape(c.testNote)}</p></div>` : ''}<span class="checkout-status ${paid ? 'is-paid' : ''}" role="status">${icon(paid ? 'check' : 'lock')} ${escape(label)}</span><h2>${escape(payment.product)}</h2><p class="checkout-reference">${escape(c.reference)}: ${escape(payment.reference)}</p><div class="checkout-total"><span>${escape(c.amount)}</span><strong>${escape(amount)}</strong></div><p>${escape(paid ? payment.mode === 'sandbox' ? c.testThanks : c.thanks : c.due)}</p>${!closed && !payment.expired ? `<form id="checkout-pay"><label class="consent-label"><input type="checkbox" required name="consent"><span>${escape(c.consent)} <a href="${href('/offer')}" target="_blank" rel="noopener">${escape(c.offer)}</a> · <a href="${href('/refunds')}" target="_blank" rel="noopener">${escape(c.refunds)}</a></span></label><button class="button button-navy full-width" type="submit">${escape(c.pay)} ${icon('right')}</button></form>` : ''}<div class="form-error" role="alert">${escape(error)}</div><button class="button button-light full-width" id="checkout-refresh">${escape(c.refresh)}</button></div></section>`;
    main.querySelector('#checkout-refresh').addEventListener('click', async e => { e.currentTarget.disabled = true; clearTimeout(timer); await check(); });
    main.querySelector('#checkout-pay')?.addEventListener('submit', async e => {
      e.preventDefault();
      const button = e.currentTarget.querySelector('button'); button.disabled = true;
      try {
        const result = await api(`/api/payments/${token}/start`, { method: 'POST', body: JSON.stringify({ consent: true }) });
        const target = new URL(result.paymentUrl);
        if (!['https://sandbox.payler.com', 'https://secure.payler.com'].includes(target.origin)) throw new Error(c.error);
        location.assign(target.href);
      } catch (e) { error = e.message || c.error; draw(); }
    });
  }
  draw();
  // A redirect or query parameter is never proof of payment; the gateway
  // (GetStatus) is always re-queried before the state is confirmed. When
  // the user returns from the hosted page, re-check right away.
  const outcome = new URLSearchParams(location.search).get('result');
  if (outcome === 'success' || outcome === 'decline' || payment.status !== 'created') await check();
}
