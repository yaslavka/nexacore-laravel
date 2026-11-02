import { COMPANY, USD_TO_KGS } from './company.js';

export const LEGAL_SLUGS = ['privacy', 'offer', 'payment', 'orders', 'refunds', 'remote', 'company'];

const rate = `1 USD = ${String(USD_TO_KGS).replace('.', ',')} KGS`;

function entityBlock(lang) {
  const address = COMPANY.address[lang];
  if (lang === 'ru') {
    return [
      `${COMPANY.displayName}.`,
      `Юридический адрес: ${address}.`,
      `ИНН: ${COMPANY.inn}.`,
      `Регистрационный номер: ${COMPANY.registrationNumber}.`,
      `ОКПО: ${COMPANY.okpo}.`,
      `Дата регистрации: ${COMPANY.registrationDate}.`,
      `Телефон: ${COMPANY.phone}.`,
      `Электронная почта: ${COMPANY.email}.`
    ].join(' ');
  }
  if (lang === 'ky') {
    return [
      `${COMPANY.displayName}.`,
      `Юридикалык дареги: ${address}.`,
      `ИНН: ${COMPANY.inn}.`,
      `Каттоо номери: ${COMPANY.registrationNumber}.`,
      `ОКПО: ${COMPANY.okpo}.`,
      `Катталган күнү: ${COMPANY.registrationDate}.`,
      `Телефон: ${COMPANY.phone}.`,
      `Электрондук почта: ${COMPANY.email}.`
    ].join(' ');
  }
  return [
    `${COMPANY.displayName}.`,
    `Legal address: ${address}.`,
    `TIN: ${COMPANY.inn}.`,
    `Registration number: ${COMPANY.registrationNumber}.`,
    `OKPO: ${COMPANY.okpo}.`,
    `Date of registration: ${COMPANY.registrationDate}.`,
    `Phone: ${COMPANY.phone}.`,
    `Email: ${COMPANY.email}.`
  ].join(' ');
}

const docs = {
  privacy: {
    en: {
      title: 'Privacy policy',
      sections: [
        { h: '1. Data controller', p: entityBlock('en') },
        { h: '2. What this policy covers', p: 'This policy describes how NexaCore LLC processes personal data of website visitors and clients who send an enquiry or pay for NexaCore’s own remote IT and business consulting services.' },
        { h: '3. Data we collect', p: 'The enquiry form collects your name, email address, company name (optional), project description and selected service. The server stores an enquiry reference, creation time and your consent. Server logs may include IP address, date and requested page for security and abuse prevention. The website does not collect payment card numbers, CVV codes or card expiry dates.' },
        { h: '4. Why we process data', p: 'We process data to review your enquiry, contact you about the project, prepare a proposal, perform a contract, issue invoices, process payments through the acquiring bank, handle refunds and meet legal accounting duties of the Kyrgyz Republic.' },
        { h: '5. Legal basis', p: 'Processing is based on your consent given in the form, on steps needed to conclude and perform a contract, and on legal obligations applicable to NexaCore LLC.' },
        { h: '6. Storage and access', p: 'Enquiries are stored on the company’s protected server. Access is limited to authorised staff with password-protected accounts. Data are kept for as long as needed to handle the enquiry, perform the contract, keep accounting records and resolve disputes, then deleted or anonymised unless a longer period is required by law.' },
        { h: '7. Recipients', p: 'We do not sell personal data. Data may be disclosed to the acquiring bank and payment systems (Visa, Mastercard and other systems approved by the bank) solely to process a payment or refund; to a hosting or email provider acting on our instructions; and to public authorities when required by the law of the Kyrgyz Republic.' },
        { h: '8. Cookies and analytics', p: 'The website uses a strictly necessary session cookie for staff access to the dashboard. Language and motion preferences may be stored in your browser. No advertising or third-party analytics cookies are used.' },
        { h: '9. Your rights', p: `You may request access, correction or deletion of your personal data, withdraw consent for future processing, and lodge a complaint with the competent authority of the Kyrgyz Republic. Write to ${COMPANY.email} or call ${COMPANY.phone}.` },
        { h: '10. Changes', p: 'The current version of this policy is published on this page. Material changes will be posted here before they take effect.' }
      ]
    },
    ru: {
      title: 'Политика конфиденциальности',
      sections: [
        { h: '1. Оператор данных', p: entityBlock('ru') },
        { h: '2. На что распространяется политика', p: 'Политика описывает, как ОсОО «НексаКор» обрабатывает персональные данные посетителей сайта и клиентов, которые оставляют заявку или оплачивают собственные дистанционные IT-услуги и бизнес-консалтинг NexaCore.' },
        { h: '3. Какие данные мы собираем', p: 'Форма заявки собирает имя, адрес электронной почты, название компании (по желанию), описание проекта и выбранную услугу. Сервер сохраняет номер обращения, время создания и факт согласия. В журналах сервера могут храниться IP-адрес, дата и запрошенная страница для защиты от злоупотреблений. Сайт не собирает номера карт, коды CVV и сроки действия карт.' },
        { h: '4. Для чего обрабатываются данные', p: 'Данные нужны, чтобы рассмотреть заявку, связаться по проекту, подготовить предложение, заключить и исполнить договор, выставить счёт, провести оплату через банк-эквайер, оформить возврат и выполнить требования бухгалтерского учёта Кыргызской Республики.' },
        { h: '5. Правовые основания', p: 'Обработка ведётся на основании согласия, данного в форме, действий, необходимых для заключения и исполнения договора, а также обязанностей, установленных законом для ОсОО «НексаКор».' },
        { h: '6. Хранение и доступ', p: 'Заявки хранятся на защищённом сервере компании. Доступ имеют только уполномоченные сотрудники с парольной защитой. Данные хранятся столько, сколько нужно для обработки заявки, исполнения договора, ведения учёта и разрешения споров, затем удаляются или обезличиваются, если более длительный срок не требуется законом.' },
        { h: '7. Получатели', p: 'Мы не продаём персональные данные. Данные могут передаваться банку-эквайеру и платёжным системам (Visa, Mastercard и иным системам, одобренным банком) только для проведения оплаты или возврата; хостинг- или почтовому провайдеру по нашему поручению; государственным органам, если это требует закон Кыргызской Республики.' },
        { h: '8. Файлы cookie и аналитика', p: 'Сайт использует строго необходимую сессионную cookie для входа сотрудников в панель. Предпочтения языка и анимации могут сохраняться в браузере. Рекламные и сторонние аналитические cookie не используются.' },
        { h: '9. Ваши права', p: `Вы можете запросить доступ, исправление или удаление персональных данных, отозвать согласие на будущую обработку и обратиться с жалобой в компетентный орган Кыргызской Республики. Пишите на ${COMPANY.email} или звоните ${COMPANY.phone}.` },
        { h: '10. Изменения', p: 'Актуальная редакция политики опубликована на этой странице. Существенные изменения размещаются здесь до вступления в силу.' }
      ]
    },
    ky: {
      title: 'Купуялык саясаты',
      sections: [
        { h: '1. Маалымат оператору', p: entityBlock('ky') },
        { h: '2. Саясат эмнени камтыйт', p: 'Бул саясат ОсОО «НексаКор» сайттын конокторунун жана NexaCore’дун өз аралыктан IT-кызматтары менен бизнес-консалтинги үчүн арыз калтырган же төлөгөн кардарлардын жеке маалыматын кантип иштетерин сүрөттөйт.' },
        { h: '3. Кандай маалыматты чогултабыз', p: 'Арыз формасы атыңызды, электрондук почтаңызды, компаниянын аталышын (каалоо боюнча), долбоордун сүрөттөмөсүн жана тандалган кызматты чогултат. Сервер кайрылуу номерин, түзүлгөн убакытты жана макулдукту сактайт. Кыянаттыктан коргоо үчүн сервер журналдарында IP-дарек, күн жана суралган барак болушу мүмкүн. Сайт карта номерлерин, CVV коддорун жана картанын жарактуулук мөөнөтүн чогултпайт.' },
        { h: '4. Маалымат эмне үчүн иштетилет', p: 'Маалымат арызды кароо, долбоор боюнча байланышуу, сунуш даярдоо, келишим түзүү жана аткаруу, эсеп коюу, эквайер банк аркылуу төлөм өткөрүү, кайтаруу тариздөө жана Кыргыз Республикасынын бухгалтердик эсеп талаптарын аткаруу үчүн керек.' },
        { h: '5. Укуктук негиз', p: 'Иштетүү формадагы макулдукка, келишим түзүү жана аткаруу үчүн зарыл аракеттерге, ошондой эле ОсОО «НексаКор» үчүн мыйзамда белгиленген милдеттерге негизделет.' },
        { h: '6. Сактоо жана кирүү', p: 'Арыздар компаниянын корголгон серверинде сакталат. Кирүү сырсөз менен корголгон ыйгарым укуктуу кызматкерлерге гана берилет. Маалымат арызды кароо, келишимди аткаруу, эсеп жүргүзүү жана талаштарды чечүү үчүн керек болгон мөөнөткө сакталат, андан кийин мыйзам узак мөөнөт талап кылбаса жок кылынат же анонимдештирилет.' },
        { h: '7. Алуучулар', p: 'Биз жеке маалыматты сатпайбыз. Маалымат эквайер банкка жана төлөм системаларына (Visa, Mastercard жана банк жактырган башка системалар) төлөм же кайтаруу үчүн гана; биздин тапшырма боюнча хостинг же почта провайдерине; Кыргыз Республикасынын мыйзамы талап кылса мамлекеттик органдарга берилиши мүмкүн.' },
        { h: '8. Cookie жана аналитика', p: 'Сайт кызматкерлердин панелге кириши үчүн гана зарыл сессиялык cookie колдонот. Тил жана анимация тандоосу браузерде сакталышы мүмкүн. Жарнамалык жана үчүнчү жактын аналитикалык cookie’лери колдонулбайт.' },
        { h: '9. Сиздин укуктарыңыз', p: `Сиз жеке маалыматыңызга кирүүнү, оңдоону же жок кылууну сурай аласыз, келечектеги иштетүүгө макулдукту кайтарып аласыз жана Кыргыз Республикасынын компетенттүү органына даттана аласыз. ${COMPANY.email} дарегине жазыңыз же ${COMPANY.phone} номерине чалыңыз.` },
        { h: '10. Өзгөртүүлөр', p: 'Саясаттын учурдагы редакциясы ушул баракта жарыяланган. Маанилүү өзгөртүүлөр күчүнө киргенге чейин ушул жерге жайгаштырылат.' }
      ]
    }
  },
  offer: {
    en: {
      title: 'Public offer',
      sections: [
        { h: '1. Offeror', p: entityBlock('en') },
        { h: '2. Subject', p: 'This document is a public offer by NexaCore LLC to conclude a contract for remote IT services and business consulting. NexaCore accepts payment exclusively for its own services. The company does not sell third-party goods and does not provide physical delivery.' },
        { h: '3. Services', p: 'The catalogue on this website lists the areas of work, indicative scope and starting prices. Final scope, deliverables, timelines and price are agreed in writing (proposal, invoice or contract) before work starts.' },
        { h: '4. Prices', p: `Prices are shown in US dollars and in Kyrgyz som at the calculation rate ${rate}. Example: 100 USD = 8 750 KGS. The amount payable for an agreed service is the KGS amount calculated at this rate from the agreed USD price, unless the invoice states a different KGS amount.` },
        { h: '5. Acceptance', p: 'The offer is accepted when the client pays the invoice, confirms the proposal in writing, or otherwise expressly agrees to the stated terms. Sending an enquiry through the website form is not acceptance and does not create a contract.' },
        { h: '6. Performance', p: 'Services are performed remotely. Results are handed over electronically (email, file exchange or other agreed channel). There is no shipment of physical goods.' },
        { h: '7. Payment', p: 'Payment is made by bank transfer or by Visa/Mastercard through the acquiring bank after an invoice is issued. Card details are entered on the bank’s payment page, not on this website.' },
        { h: '8. Cancellation and refunds', p: 'The procedure is set out in the pages “Orders and cancellation” and “Refunds”. Statutory rights of the client under the law of the Kyrgyz Republic remain in force.' },
        { h: '9. Governing law', p: 'The contract is governed by the law of the Kyrgyz Republic. Disputes are considered at the location of NexaCore LLC in Bishkek, unless a mandatory rule provides otherwise.' },
        { h: '10. Contact', p: `Questions about this offer: ${COMPANY.email}, ${COMPANY.phone}.` }
      ]
    },
    ru: {
      title: 'Публичная оферта',
      sections: [
        { h: '1. Оферент', p: entityBlock('ru') },
        { h: '2. Предмет', p: 'Настоящий документ является публичной офертой ОсОО «НексаКор» заключить договор на дистанционные IT-услуги и бизнес-консалтинг. NexaCore принимает оплату исключительно за собственные услуги. Компания не продаёт чужие товары и не осуществляет физическую доставку.' },
        { h: '3. Услуги', p: 'Каталог на сайте описывает направления работ, ориентировочный состав и стартовые цены. Итоговый объём, результат, сроки и стоимость согласовываются письменно (предложение, счёт или договор) до начала работ.' },
        { h: '4. Цены', p: `Цены указаны в долларах США и в кыргызских сомах по расчётному курсу ${rate}. Пример: 100 USD = 8 750 KGS. К оплате по согласованной услуге принимается сумма в сомах, рассчитанная по этому курсу от согласованной цены в USD, если в счёте не указана иная сумма в KGS.` },
        { h: '5. Акцепт', p: 'Оферта считается принятой, когда клиент оплачивает счёт, письменно подтверждает предложение либо иным явным способом соглашается с указанными условиями. Отправка заявки через форму сайта акцептом не является и договор не создаёт.' },
        { h: '6. Исполнение', p: 'Услуги оказываются дистанционно. Результат передаётся электронно (почта, обмен файлами или иной согласованный канал). Физической отгрузки товаров нет.' },
        { h: '7. Оплата', p: 'Оплата производится банковским переводом или картами Visa/Mastercard через банк-эквайер после выставления счёта. Реквизиты карты вводятся на странице банка, а не на этом сайте.' },
        { h: '8. Отмена и возврат', p: 'Порядок описан на страницах «Оформление и отмена заказа» и «Возврат средств». Законные права клиента по законодательству Кыргызской Республики сохраняются.' },
        { h: '9. Применимое право', p: 'Договор регулируется правом Кыргызской Республики. Споры рассматриваются по месту нахождения ОсОО «НексаКор» в г. Бишкек, если императивная норма не устанавливает иное.' },
        { h: '10. Контакты', p: `Вопросы по оферте: ${COMPANY.email}, ${COMPANY.phone}.` }
      ]
    },
    ky: {
      title: 'Коомдук оферта',
      sections: [
        { h: '1. Оферент', p: entityBlock('ky') },
        { h: '2. Предмет', p: 'Бул документ ОсОО «НексаКор» тарабынан аралыктан IT-кызматтар жана бизнес-консалтинг боюнча келишим түзүүгө коомдук оферта болуп саналат. NexaCore төлөмдү өз кызматтары үчүн гана кабыл алат. Компания бөтөн товарларды сатпайт жана физикалык жеткирүү кылбайт.' },
        { h: '3. Кызматтар', p: 'Сайттагы каталог иш багыттарын, болжолдуу курамды жана баштапкы бааларды сүрөттөйт. Акыркы көлөм, жыйынтык, мөөнөт жана баа иш башталганга чейин жазуу жүзүндө (сунуш, эсеп же келишим) макулдашылат.' },
        { h: '4. Баалар', p: `Баалар АКШ долларында жана кыргыз сомунда ${rate} эсептик курсу боюнча көрсөтүлгөн. Мисал: 100 USD = 8 750 KGS. Макулдашылган кызмат үчүн төлөнгөн сумма — эсепте башка KGS суммасы жазылбаса — макулдашылган USD баасынан ушул курс боюнча эсептелген сом суммасы.` },
        { h: '5. Акцепт', p: 'Оферта кардар эсепти төлөгөндө, сунушту жазуу жүзүндө ырастаганда же көрсөтүлгөн шарттарга башка ачык жол менен макул болгондо кабыл алынган деп эсептелет. Сайт формасы аркылуу арыз жөнөтүү акцепт эмес жана келишим түзбөйт.' },
        { h: '6. Аткаруу', p: 'Кызматтар аралыктан көрсөтүлөт. Жыйынтык электрондук түрдө берилет (почта, файл алмашуу же башка макулдашылган канал). Физикалык товар жөнөтүү жок.' },
        { h: '7. Төлөм', p: 'Төлөм эсеп коюлгандан кийин банк которуусу же Visa/Mastercard карталары менен эквайер банк аркылуу жүргүзүлөт. Карта реквизиттери ушул сайтта эмес, банктын барагында киргизилет.' },
        { h: '8. Жокко чыгаруу жана кайтаруу', p: 'Тартиби «Заказ тариздөө жана жокко чыгаруу» жана «Акчаны кайтаруу» барактарында жазылган. Кыргыз Республикасынын мыйзамы боюнча кардардын укуктары сакталат.' },
        { h: '9. Колдонулуучу укук', p: 'Келишим Кыргыз Республикасынын укугу менен жөнгө салынат. Талаштар башка императивдүү ченем каралбаса, Бишкектеги ОсОО «НексаКор» турган жер боюнча каралат.' },
        { h: '10. Байланыш', p: `Оферта боюнча суроолор: ${COMPANY.email}, ${COMPANY.phone}.` }
      ]
    }
  },
  payment: {
    en: {
      title: 'Card payment terms',
      sections: [
        { h: '1. Merchant', p: entityBlock('en') },
        { h: '2. What you pay for', p: 'NexaCore accepts payment exclusively for its own services: remote IT services and business consulting listed in the catalogue and agreed in the invoice. The company does not accept payment for third-party goods or services.' },
        { h: '3. Cards and systems', p: 'When internet acquiring is connected, payment is accepted with Visa and Mastercard. Space is reserved on the website for other payment systems approved by the acquiring bank. The set of enabled systems is shown next to the payment marks in the footer.' },
        { h: '4. Currency', p: `Catalogue prices are shown in USD and KGS at ${rate} (100 USD = 8 750 KGS). Card payments are processed in Kyrgyz som. The charge amount is the KGS figure stated on the invoice.` },
        { h: '5. How payment is made', p: 'After scope and price are agreed, NexaCore issues an invoice or a payment link. You are redirected to the bank’s secure page to enter card details. This website does not collect, process or store card numbers, CVV codes or expiry dates. The bank may use 3-D Secure or another authentication method required by the card scheme.' },
        { h: '6. Confirmation', p: 'A successful payment is confirmed by the bank. We record the payment against your invoice and continue the agreed work. An enquiry submitted on this website is not a payment.' },
        { h: '7. Failed or pending payments', p: 'If the bank declines the payment, no charge is completed and the order stays unpaid. You may try again or choose another agreed method. Pending authorisations are handled by the bank according to its rules.' },
        { h: '8. Refunds', p: 'Refunds, where due, are returned to the same card used for payment, as described on the Refunds page.' },
        { h: '9. Support', p: `Payment questions: ${COMPANY.email}, ${COMPANY.phone}. For a charge that already appears on your card statement, also contact your issuing bank.` }
      ]
    },
    ru: {
      title: 'Условия оплаты банковскими картами Visa и Mastercard',
      sections: [
        { h: '1. Получатель платежа', p: entityBlock('ru') },
        { h: '2. За что принимается оплата', p: 'NexaCore принимает оплату исключительно за собственные услуги: дистанционные IT-услуги и бизнес-консалтинг, указанные в каталоге и согласованные в счёте. Компания не принимает оплату за чужие товары или услуги.' },
        { h: '3. Карты и платёжные системы', p: 'После подключения интернет-эквайринга оплата принимается картами Visa и Mastercard. На сайте предусмотрено место для других платёжных систем, одобренных банком-эквайером. Подключённые системы отображаются рядом со знаками оплаты в подвале сайта.' },
        { h: '4. Валюта', p: `Цены в каталоге указаны в USD и KGS по курсу ${rate} (100 USD = 8 750 KGS). Оплата картой проводится в кыргызских сомах. К списанию принимается сумма в KGS, указанная в счёте.` },
        { h: '5. Как проходит оплата', p: 'После согласования объёма и цены NexaCore выставляет счёт или платёжную ссылку. Реквизиты карты вводятся на защищённой странице банка. Этот сайт не собирает, не обрабатывает и не хранит номера карт, коды CVV и сроки действия. Банк может запросить 3-D Secure или иную проверку, требуемую платёжной системой.' },
        { h: '6. Подтверждение', p: 'Успешная оплата подтверждается банком. Мы фиксируем платёж по счёту и продолжаем согласованные работы. Заявка на сайте оплатой не является.' },
        { h: '7. Отклонённый или незавершённый платёж', p: 'Если банк отклоняет операцию, списание не завершается, заказ остаётся неоплаченным. Можно повторить попытку или выбрать другой согласованный способ. Авторизации в ожидании обрабатывает банк по своим правилам.' },
        { h: '8. Возврат', p: 'Возврат, если он положен, направляется на ту же карту, с которой была оплата, в порядке страницы «Возврат средств».' },
        { h: '9. Поддержка', p: `Вопросы по оплате: ${COMPANY.email}, ${COMPANY.phone}. Если списание уже отразилось в выписке по карте, также обратитесь в банк-эмитент.` }
      ]
    },
    ky: {
      title: 'Visa жана Mastercard карталары менен төлөө шарттары',
      sections: [
        { h: '1. Төлөм алуучу', p: entityBlock('ky') },
        { h: '2. Төлөм эмне үчүн кабыл алынат', p: 'NexaCore төлөмдү өз кызматтары үчүн гана кабыл алат: каталогдо көрсөтүлгөн жана эсепте макулдашылган аралыктан IT-кызматтар жана бизнес-консалтинг. Компания бөтөн товар же кызмат үчүн төлөм албайт.' },
        { h: '3. Карталар жана төлөм системалары', p: 'Интернет-эквайринг туташкандан кийин төлөм Visa жана Mastercard карталары менен кабыл алынат. Сайтта эквайер банк жактырган башка төлөм системалары үчүн орун каралган. Туташкан системалар сайттын астындагы төлөм белгилеринин жанында көрсөтүлөт.' },
        { h: '4. Валюта', p: `Каталогдогу баалар USD жана KGS түрүндө ${rate} курсу боюнча көрсөтүлгөн (100 USD = 8 750 KGS). Карта менен төлөм кыргыз сомунда өтөт. Эсепте жазылган KGS суммасы алынат.` },
        { h: '5. Төлөм кандай өтөт', p: 'Көлөм жана баа макулдашылгандан кийин NexaCore эсеп же төлөм шилтемесин берет. Карта реквизиттери банктын корголгон барагында киргизилет. Бул сайт карта номерлерин, CVV коддорун жана жарактуулук мөөнөтүн чогултпайт, иштетпейт жана сактабайт. Банк 3-D Secure же төлөм системасы талап кылган башка текшерүүнү сурашы мүмкүн.' },
        { h: '6. Ырастоо', p: 'Ийгиликтүү төлөмдү банк ырастайт. Биз төлөмдү эсепке байлап, макулдашылган ишти улантабыз. Сайттагы арыз төлөм эмес.' },
        { h: '7. Четке кагылган же бүтпөгөн төлөм', p: 'Банк операцияны четке какса, алуу бүтпөйт, заказ төлөнбөй калат. Кайра аракет кылсаңыз же башка макулдашылган ыкманы тандасаңыз болот. Күтүүдөгү авторизацияларды банк өз эрежелери боюнча иштетет.' },
        { h: '8. Кайтаруу', p: 'Кайтаруу тиешелүү болсо, «Акчаны кайтаруу» барагындагы тартип боюнча төлөнгөн ошол эле картага жөнөтүлөт.' },
        { h: '9. Колдоо', p: `Төлөм боюнча суроолор: ${COMPANY.email}, ${COMPANY.phone}. Эгер алуу карта көчүрмөсүндө көрүнсө, эмитент банкка да кайрылыңыз.` }
      ]
    }
  },
  orders: {
    en: {
      title: 'Order placement and cancellation',
      sections: [
        { h: '1. How to place an order', p: 'Choose a service in the catalogue or describe your task on the contact page. Submit the enquiry form. We review the request, clarify the scope and send a proposal with price, timeline and deliverables. An order is placed when you accept the proposal and pay the invoice or otherwise confirm in writing.' },
        { h: '2. What the website form does', p: 'The form creates an enquiry with a unique reference. It is not a paid order, not an acceptance of the public offer and not a card payment.' },
        { h: '3. Agreement before work', p: 'Work starts only after the scope, price in USD and KGS, timeline and deliverables are agreed. Remote performance and electronic handover are part of the agreement.' },
        { h: '4. Cancellation before payment', p: 'You may cancel an enquiry at any time before payment by writing to the company email and quoting the enquiry reference. No fee is charged.' },
        { h: '5. Cancellation after payment, before work starts', p: 'If you cancel in writing after payment and before we start the agreed work, the paid amount is refunded in full according to the Refunds page.' },
        { h: '6. Cancellation after work has started', p: 'If work has started, we agree the completed portion in writing. The completed portion is payable; the unused remainder is refunded. If a fixed-price stage has been fully delivered, that stage is not refunded.' },
        { h: '7. Cancellation by NexaCore', p: 'If we cannot perform for reasons beyond the agreed scope or for legal reasons, we notify you and refund the unused prepaid amount.' },
        { h: '8. How to send a cancellation', p: `Write to ${COMPANY.email} from the address used in the enquiry, or call ${COMPANY.phone}. Include the enquiry or invoice reference.` }
      ]
    },
    ru: {
      title: 'Порядок оформления и отмены заказа',
      sections: [
        { h: '1. Как оформить заказ', p: 'Выберите услугу в каталоге или опишите задачу на странице контактов. Отправьте форму заявки. Мы изучаем обращение, уточняем объём и направляем предложение со стоимостью, сроками и составом работ. Заказ считается оформленным, когда вы принимаете предложение и оплачиваете счёт либо письменно подтверждаете условия.' },
        { h: '2. Что делает форма на сайте', p: 'Форма создаёт заявку с уникальным номером. Это не оплаченный заказ, не акцепт публичной оферты и не оплата картой.' },
        { h: '3. Согласование до начала работ', p: 'Работы начинаются только после согласования объёма, цены в USD и KGS, сроков и результата. Дистанционное исполнение и электронная передача результата входят в договорённость.' },
        { h: '4. Отмена до оплаты', p: 'Заявку можно отменить в любое время до оплаты, написав на корпоративную почту и указав номер обращения. Плата за отмену не взимается.' },
        { h: '5. Отмена после оплаты, до начала работ', p: 'Если вы письменно отменяете заказ после оплаты, но до начала согласованных работ, уплаченная сумма возвращается полностью в порядке страницы «Возврат средств».' },
        { h: '6. Отмена после начала работ', p: 'Если работы уже начаты, письменно фиксируем выполненную часть. Она подлежит оплате; неиспользованный остаток возвращается. Полностью сданный этап с фиксированной ценой возврату не подлежит.' },
        { h: '7. Отмена со стороны NexaCore', p: 'Если исполнить заказ невозможно по причинам вне согласованного объёма или по закону, мы уведомляем вас и возвращаем неиспользованную предоплату.' },
        { h: '8. Как направить отмену', p: `Напишите на ${COMPANY.email} с адреса, указанного в заявке, или позвоните ${COMPANY.phone}. Укажите номер заявки или счёта.` }
      ]
    },
    ky: {
      title: 'Заказ тариздөө жана жокко чыгаруу тартиби',
      sections: [
        { h: '1. Заказ кандай таризделет', p: 'Каталогдон кызматты тандаңыз же байланыш барагында милдетти сүрөттөңүз. Арыз формасын жөнөтүңүз. Биз кайрылууну карап, көлөмдү тактап, баасы, мөөнөтү жана курамы бар сунуш жиберебиз. Заказ сиз сунушту кабыл алып, эсепти төлөгөндө же шарттарды жазуу жүзүндө ырастаганда таризделген деп эсептелет.' },
        { h: '2. Сайттагы форма эмне кылат', p: 'Форма уникалдуу номери бар арыз түзөт. Бул төлөнгөн заказ, коомдук офертанын акцепти же карта менен төлөм эмес.' },
        { h: '3. Иш башталганга чейинки макулдашуу', p: 'Иш көлөм, USD жана KGS баасы, мөөнөт жана жыйынтык макулдашылгандан кийин гана башталат. Аралыктан аткаруу жана жыйынтыкты электрондук өткөрүп берүү макулдашууга кирет.' },
        { h: '4. Төлөмгө чейин жокко чыгаруу', p: 'Арызды төлөмгө чейин каалаган учурда корпоративдик почтага жазып, кайрылуу номерин көрсөтүү менен жокко чыгарсаңыз болот. Жокко чыгаруу үчүн акы алынбайт.' },
        { h: '5. Төлөмдөн кийин, иш башталганга чейин жокко чыгаруу', p: 'Төлөмдөн кийин, бирок макулдашылган иш башталганга чейин жазуу жүзүндө жокко чыгарсаңыз, төлөнгөн сумма «Акчаны кайтаруу» барагындагы тартип боюнча толук кайтарылат.' },
        { h: '6. Иш башталгандан кийин жокко чыгаруу', p: 'Иш башталса, аткарылган бөлүктү жазуу жүзүндө белгилейбиз. Ал төлөнөт; колдонулбаган калдык кайтарылат. Белгиленген баасы бар толук тапшырылган этап кайтарылбайт.' },
        { h: '7. NexaCore тарабынан жокко чыгаруу', p: 'Заказды макулдашылган көлөмдөн тышкары себептер же мыйзам боюнча аткаруу мүмкүн болбосо, сизге кабарлап, колдонулбаган алдын ала төлөмдү кайтарабыз.' },
        { h: '8. Жокко чыгарууну кантип жөнөтүү', p: `Арыздагы даректен ${COMPANY.email} дарегине жазыңыз же ${COMPANY.phone} номерине чалыңыз. Арыз же эсеп номерин көрсөтүңүз.` }
      ]
    }
  },
  refunds: {
    en: {
      title: 'Refund terms',
      sections: [
        { h: '1. When a refund is due', p: 'A full refund is due if you cancel in writing after payment and before the agreed work starts, if the service was not provided, or if a payment was taken in error. A partial refund is due if work has started and the unused remainder is agreed in writing. NexaCore accepts payment exclusively for its own services; amounts collected in error for anything else are returned in full.' },
        { h: '2. When a refund is not due', p: 'A refund is not due for a stage that has been fully delivered and accepted, for third-party costs already incurred at your request (licences, advertising spend, paid APIs, hosting), or if you provided incorrect data that made performance impossible after work had reasonably started.' },
        { h: '3. How to request a refund', p: `Write to ${COMPANY.email} from the email used for the order, or call ${COMPANY.phone}. State the enquiry or invoice reference, the payment date and the reason. We confirm receipt within two business days.` },
        { h: '4. Review period', p: 'We review the request within ten business days of receiving complete information and send a written decision.' },
        { h: '5. How money is returned', p: 'Card payments are refunded to the same Visa or Mastercard used for the original payment. Bank transfers are returned to the same account. We do not refund in cash.' },
        { h: '6. Timing of the credit', p: 'After we instruct the refund, the acquiring bank and the card issuer usually post the amount within fourteen business days. The exact timing depends on the bank and the payment system.' },
        { h: '7. Currency', p: `Refunds of card payments are made in KGS for the amount originally charged, or the unused part of that amount. Display prices on the website use ${rate}.` },
        { h: '8. Contact', p: `Refund support: ${COMPANY.email}, ${COMPANY.phone}.` }
      ]
    },
    ru: {
      title: 'Условия и сроки возврата денежных средств',
      sections: [
        { h: '1. Когда возврат положен', p: 'Полный возврат положен, если вы письменно отменили заказ после оплаты и до начала согласованных работ, если услуга не оказана либо если списание произошло по ошибке. Частичный возврат положен, если работы начаты и неиспользованный остаток согласован письменно. NexaCore принимает оплату исключительно за собственные услуги; суммы, ошибочно принятые за что-либо иное, возвращаются полностью.' },
        { h: '2. Когда возврат не положен', p: 'Возврат не положен за полностью сданный и принятый этап, за уже понесённые по вашей просьбе расходы третьих лиц (лицензии, рекламный бюджет, платные API, хостинг), а также если из-за неверных данных с вашей стороны исполнение стало невозможным после того, как работы обоснованно начались.' },
        { h: '3. Как запросить возврат', p: `Напишите на ${COMPANY.email} с почты, указанной в заказе, или позвоните ${COMPANY.phone}. Укажите номер заявки или счёта, дату оплаты и причину. Получение запроса подтверждаем в течение двух рабочих дней.` },
        { h: '4. Срок рассмотрения', p: 'Запрос рассматриваем в течение десяти рабочих дней с момента получения полных сведений и направляем письменное решение.' },
        { h: '5. Как возвращаются деньги', p: 'Оплата картой возвращается на ту же карту Visa или Mastercard, с которой было списание. Банковский перевод возвращается на тот же счёт. Наличными возврат не производится.' },
        { h: '6. Срок зачисления', p: 'После нашего поручения банку-эквайеру сумма обычно отражается у банка-эмитента в течение четырнадцати рабочих дней. Точный срок зависит от банка и платёжной системы.' },
        { h: '7. Валюта', p: `Возврат по картам выполняется в KGS в размере фактически списанной суммы или её неиспользованной части. Цены на сайте показаны по курсу ${rate}.` },
        { h: '8. Контакты', p: `По вопросам возврата: ${COMPANY.email}, ${COMPANY.phone}.` }
      ]
    },
    ky: {
      title: 'Акчаны кайтаруу шарттары жана мөөнөттөрү',
      sections: [
        { h: '1. Кайтаруу качан тиешелүү', p: 'Төлөмдөн кийин жана макулдашылган иш башталганга чейин жазуу жүзүндө жокко чыгарсаңыз, кызмат көрсөтүлбөсө же алуу ката менен болсо, толук кайтаруу тиешелүү. Иш башталып, колдонулбаган калдык жазуу жүзүндө макулдашылса, жарым-жартылай кайтаруу тиешелүү. NexaCore төлөмдү өз кызматтары үчүн гана кабыл алат; башка нерсе үчүн ката менен алынган сумма толук кайтарылат.' },
        { h: '2. Кайтаруу качан тиешелүү эмес', p: 'Толук тапшырылган жана кабыл алынган этап, сиздин өтүнүчүңүз боюнча буга чейин кеткен үчүнчү жактын чыгымдары (лицензиялар, жарнама бюджети, акылуу API, хостинг), ошондой эле сиздин туура эмес маалыматыңыздан улам иш негиздүү башталгандан кийин аткаруу мүмкүн болбой калса, кайтаруу тиешелүү эмес.' },
        { h: '3. Кайтарууну кантип суроо', p: `Заказдагы почтадан ${COMPANY.email} дарегине жазыңыз же ${COMPANY.phone} номерине чалыңыз. Арыз же эсеп номерин, төлөм күнүн жана себебин көрсөтүңүз. Сурамдын алынганын эки жумуш күнү ичинде ырастайбыз.` },
        { h: '4. Кароо мөөнөтү', p: 'Толук маалымат алынгандан кийин сурамды он жумуш күнү ичинде карап, жазуу жүзүндө чечим жиберебиз.' },
        { h: '5. Акча кандай кайтарылат', p: 'Карта менен төлөм баштапкы Visa же Mastercard картасына кайтарылат. Банк которуусу ошол эле эсепке кайтарылат. Накталай кайтаруу жок.' },
        { h: '6. Чегерилүү мөөнөтү', p: 'Биз эквайер банкка тапшырма бергенден кийин сумма адатта он төрт жумуш күнү ичинде эмитент банкта көрүнөт. Так мөөнөт банкка жана төлөм системасына жараша болот.' },
        { h: '7. Валюта', p: `Карта боюнча кайтаруу иш жүзүндө алынган KGS суммасында же анын колдонулбаган бөлүгүндө жасалат. Сайттагы баалар ${rate} курсу боюнча көрсөтүлгөн.` },
        { h: '8. Байланыш', p: `Кайтаруу боюнча: ${COMPANY.email}, ${COMPANY.phone}.` }
      ]
    }
  },
  remote: {
    en: {
      title: 'Remote performance of services',
      sections: [
        { h: '1. Nature of the services', p: 'NexaCore LLC provides IT services and business consulting. All services listed on this website are performed remotely. There is no physical delivery of goods, no shipping, no pickup point and no courier handover.' },
        { h: '2. How results are delivered', p: 'Deliverables are transferred electronically: email, a file-exchange link, a repository or another channel named in the proposal. Access credentials, reports, source materials and recommendations are provided in digital form.' },
        { h: '3. Place of performance', p: 'The contractor performs the work without visiting the client’s premises unless a separate written agreement says otherwise. Meetings are held by video, phone or email.' },
        { h: '4. Client cooperation', p: 'Remote performance requires timely access to the information, systems and people named in the brief. Delays in providing access may move the timeline by the same number of business days.' },
        { h: '5. No goods in transit', p: 'Because nothing is shipped, delivery tracking, transport risk and customs rules for goods do not apply. Acceptance is confirmed in writing or by using the delivered materials without reasoned objection within the period stated in the proposal, or five business days if no period is stated.' },
        { h: '6. Payment for own services only', p: COMPANY.ownServicesOnly.en + ' Payment is not taken for third-party physical goods.' }
      ]
    },
    ru: {
      title: 'Порядок дистанционного оказания услуг',
      sections: [
        { h: '1. Характер услуг', p: 'ОсОО «НексаКор» оказывает IT-услуги и бизнес-консалтинг. Все услуги, указанные на сайте, выполняются дистанционно. Физической доставки товаров, отгрузки, пункта выдачи и передачи курьером нет.' },
        { h: '2. Как передаётся результат', p: 'Результат передаётся электронно: почта, ссылка для обмена файлами, репозиторий или иной канал, названный в предложении. Доступы, отчёты, исходные материалы и рекомендации предоставляются в цифровом виде.' },
        { h: '3. Место исполнения', p: 'Исполнитель выполняет работу без выезда на территорию клиента, если иное не согласовано отдельно письменно. Встречи проводятся по видеосвязи, телефону или почте.' },
        { h: '4. Содействие клиента', p: 'Для дистанционной работы нужен своевременный доступ к сведениям, системам и людям, указанным в задании. Задержка доступа сдвигает срок на такое же число рабочих дней.' },
        { h: '5. Нет товара в пути', p: 'Поскольку ничего не отгружается, правила отслеживания доставки, транспортного риска и таможенного оформления товаров не применяются. Приёмка подтверждается письменно либо использованием переданных материалов без мотивированных замечаний в срок, указанный в предложении, а если срок не указан — в течение пяти рабочих дней.' },
        { h: '6. Оплата только за собственные услуги', p: COMPANY.ownServicesOnly.ru + ' Оплата за чужие физические товары не принимается.' }
      ]
    },
    ky: {
      title: 'Кызматтарды аралыктан көрсөтүү тартиби',
      sections: [
        { h: '1. Кызматтардын мүнөзү', p: 'ОсОО «НексаКор» IT-кызматтарды жана бизнес-консалтингди көрсөтөт. Сайттагы бардык кызматтар аралыктан аткарылат. Физикалык товар жеткирүү, жөнөтүү, берүү пункту жана курьер аркылуу өткөрүү жок.' },
        { h: '2. Жыйынтык кандай берилет', p: 'Жыйынтык электрондук түрдө берилет: почта, файл алмашуу шилтемеси, репозиторий же сунушта аталган башка канал. Кирүүлөр, отчеттор, баштапкы материалдар жана сунуштар санарип түрүндө берилет.' },
        { h: '3. Аткаруу жери', p: 'Аткаруучу өзүнчө жазуу жүзүндө макулдашылбаса, кардардын аймагына барбастан иштейт. Жолугушуулар видео, телефон же почта аркылуу өтөт.' },
        { h: '4. Кардардын көмөгү', p: 'Аралыктан иштөө үчүн тапшырмада аталган маалыматка, системаларга жана адамдарга өз убагында кирүү керек. Кирүү кечиктирилсе, мөөнөт ошончо жумуш күнүнө жылат.' },
        { h: '5. Жолдо товар жок', p: 'Эч нерсе жөнөтүлбөгөндүктөн, жеткирүүнү көзөмөлдөө, транспорт тобокелдиги жана товардын бажы эрежелери колдонулбайт. Кабыл алуу жазуу жүзүндө же берилген материалдарды сунуштагы мөөнөттө жүйөлүү эскертүүсүз колдонуу менен ырасталат; мөөнөт жазылбаса — беш жумуш күнү ичинде.' },
        { h: '6. Төлөм өз кызматтары үчүн гана', p: COMPANY.ownServicesOnly.ky + ' Бөтөн физикалык товар үчүн төлөм алынбайт.' }
      ]
    }
  },
  company: {
    en: {
      title: 'Company details',
      sections: [
        { h: 'Legal name', p: COMPANY.displayName },
        { h: 'Activity', p: COMPANY.activity.en },
        { h: 'Remote services', p: 'Services are provided remotely. There is no physical delivery of goods.' },
        { h: 'Payment', p: COMPANY.ownServicesOnly.en },
        { h: 'Legal address', p: COMPANY.address.en },
        { h: 'TIN', p: COMPANY.inn },
        { h: 'Registration number', p: COMPANY.registrationNumber },
        { h: 'OKPO', p: COMPANY.okpo },
        { h: 'Date of registration', p: COMPANY.registrationDate },
        { h: 'Phone', p: COMPANY.phone },
        { h: 'Corporate email', p: COMPANY.email }
      ]
    },
    ru: {
      title: 'Реквизиты компании',
      sections: [
        { h: 'Наименование', p: COMPANY.displayName },
        { h: 'Деятельность', p: COMPANY.activity.ru },
        { h: 'Дистанционное оказание', p: 'Услуги оказываются дистанционно. Физической доставки товаров нет.' },
        { h: 'Оплата', p: COMPANY.ownServicesOnly.ru },
        { h: 'Юридический адрес', p: COMPANY.address.ru },
        { h: 'ИНН', p: COMPANY.inn },
        { h: 'Регистрационный номер', p: COMPANY.registrationNumber },
        { h: 'ОКПО', p: COMPANY.okpo },
        { h: 'Дата регистрации', p: COMPANY.registrationDate },
        { h: 'Телефон', p: COMPANY.phone },
        { h: 'Корпоративная почта', p: COMPANY.email }
      ]
    },
    ky: {
      title: 'Компаниянын реквизиттери',
      sections: [
        { h: 'Аталышы', p: COMPANY.displayName },
        { h: 'Ишмердүүлүк', p: COMPANY.activity.ky },
        { h: 'Аралыктан көрсөтүү', p: 'Кызматтар аралыктан көрсөтүлөт. Физикалык товар жеткирүү жок.' },
        { h: 'Төлөм', p: COMPANY.ownServicesOnly.ky },
        { h: 'Юридикалык дарек', p: COMPANY.address.ky },
        { h: 'ИНН', p: COMPANY.inn },
        { h: 'Каттоо номери', p: COMPANY.registrationNumber },
        { h: 'ОКПО', p: COMPANY.okpo },
        { h: 'Катталган күнү', p: COMPANY.registrationDate },
        { h: 'Телефон', p: COMPANY.phone },
        { h: 'Корпоративдик почта', p: COMPANY.email }
      ]
    }
  }
};

export function legalDoc(slug, lang) {
  const key = slug === 'terms' ? 'offer' : slug;
  const pack = docs[key];
  if (!pack) return null;
  return pack[lang] || pack.en;
}
