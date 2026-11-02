export const USD_TO_KGS = 87.5;

export const COMPANY = {
  brand: 'NexaCore',
  legalNameEn: 'NexaCore LLC',
  legalNameRu: 'ОсОО «НексаКор»',
  legalNameKy: 'ОсОО «НексаКор»',
  displayName: 'ОсОО «НексаКор» / NexaCore LLC',
  inn: '01910202610058',
  registrationNumber: '335284-3301-ООО',
  okpo: '35354562',
  registrationDate: '19.10.2026',
  phone: '+996 501 750 774',
  email: 'office@nexa-core-technologies.com',
  address: {
    en: 'Kyrgyz Republic, Bishkek, Pervomaisky district, Chingiz Aitmatov Avenue, 126, apartment 68',
    ru: 'Кыргызская Республика, г. Бишкек, Первомайский район, проспект Чынгыза Айтматова, дом 126, квартира 68',
    ky: 'Кыргыз Республикасы, Бишкек ш., Первомай району, Чыңгыз Айтматов проспекти, 126-үй, 68-батир'
  },
  activity: {
    en: 'IT services and business consulting. Services are provided remotely.',
    ru: 'IT-услуги и бизнес-консалтинг. Услуги оказываются дистанционно.',
    ky: 'IT-кызматтар жана бизнес-консалтинг. Кызматтар аралыктан көрсөтүлөт.'
  },
  ownServicesOnly: {
    en: 'NexaCore accepts payment exclusively for its own services.',
    ru: 'NexaCore принимает оплату исключительно за собственные услуги.',
    ky: 'NexaCore төлөмдү өз кызматтары үчүн гана кабыл алат.'
  }
};

export function kgsFromUsd(usd) {
  return Math.round(Number(usd) * USD_TO_KGS);
}

export function formatKgsAmount(usd) {
  return String(kgsFromUsd(usd)).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}

export function formatUsdAmount(usd) {
  return Number(usd).toLocaleString('en-US');
}
