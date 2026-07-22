(function () {
   const breakpoint = globalThis.matchMedia('(max-width: 991px)');
   const body = document.body;
   const burger = document.querySelector('.osn-burger');
   const menu = document.getElementById('osn-mobile-menu');

   if (!body || !burger || !menu) {
      return;
   }

   const parentItems = Array.from(menu.querySelectorAll('.menu-item-has-children'));
   const menuNav = menu.querySelector('.osn-mobile-menu__nav');
   const menuSocial = menu.querySelector('.osn-mobile-menu__social');
   const languageTrigger = menu.querySelector('.osn-mobile-menu__language-trigger');
   const languageTriggerFlag = menu.querySelector('.osn-mobile-menu__language-trigger-flag');
   const languageTriggerLabel = menu.querySelector('.osn-mobile-menu__language-trigger-label');
   const languagePanel = menu.querySelector('.osn-mobile-menu__languages');
   const languageList = menu.querySelector('.osn-mobile-menu__languages-list');
   const languageBack = menu.querySelector('.osn-mobile-menu__languages-back');
   const defaultLanguageCode = 'sr';
   const languages = {
      sr: 'Srpski',
      hr: 'Hrvatski',
      en: 'English',
      fr: 'Français',
      de: 'Deutsch',
      hu: 'Magyar',
      it: 'Italiano',
      mk: 'Makedonski',
      sl: 'Slovenščina',
   };
   let languageOptions = [];

   function closeSubmenus() {
      parentItems.forEach((item) => {
         const toggle = item.querySelector(':scope > .osn-mobile-menu__submenu-toggle');
         const submenu = item.querySelector(':scope > .sub-menu');

         item.classList.remove('is-open');
         if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
         }
         if (submenu) {
            submenu.hidden = true;
         }
      });
   }

   function setLanguageViewOpen(isOpen) {
      if (!languagePanel || !languageTrigger) {
         return;
      }

      menu.classList.toggle('is-language-view', isOpen);
      languagePanel.hidden = !isOpen;
      languageTrigger.hidden = languageOptions.length === 0;
      languageTrigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

      if (menuNav) {
         menuNav.hidden = isOpen;
      }
      if (menuSocial) {
         menuSocial.hidden = isOpen;
      }
      if (isOpen) {
         closeSubmenus();
      }
   }

   function setMenuOpen(isOpen) {
      body.classList.toggle('osn-menu-open', isOpen);
      burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      menu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');

      if (isOpen) {
         initLanguageOptions();
      } else {
         closeSubmenus();
         setLanguageViewOpen(false);
      }
   }

   function isPlaceholderLink(anchor) {
      const href = (anchor.getAttribute('href') || '').trim();
      return href === '' || href === '#';
   }

   function normalizeLanguageCode(value) {
      if (!value) {
         return '';
      }

      let normalized = '';
      String(value).split('|').pop().split('/').forEach((part) => {
         if (part) {
            normalized = part;
         }
      });

      return normalized.toLowerCase();
   }

   function getCookieValue(name) {
      const cookie = document.cookie
         .split('; ')
         .find((row) => row.startsWith(`${name}=`));

      return cookie ? decodeURIComponent(cookie.split('=').slice(1).join('=')) : '';
   }

   function getCurrentLanguageCode() {
      const cookieCode = normalizeLanguageCode(getCookieValue('googtrans'));
      return cookieCode || normalizeLanguageCode(document.documentElement.lang) || defaultLanguageCode;
   }

   function getThemeUri() {
      return languageTrigger ? languageTrigger.dataset.themeUri || '' : '';
   }

   function getFlagUrl(code) {
      const themeUri = getThemeUri().replace(/\/$/, '');
      return `${themeUri}/assets/images/flags/${code}.svg`;
   }

   function getCleanLanguageLabel(element) {
      return (
         element.getAttribute('title') ||
         element.getAttribute('aria-label') ||
         element.textContent ||
         ''
      ).replace(/\s+/g, ' ').trim();
   }

   function getElementLanguageCode(element) {
      if (!element) {
         return '';
      }

      if (element instanceof HTMLElement) {
         const dataCode = element.dataset.gtLang || element.dataset.lang;

         if (dataCode) {
            return normalizeLanguageCode(dataCode);
         }
      }

      const directCode = element.getAttribute('lang') || element.value;
      if (directCode) {
         return normalizeLanguageCode(directCode);
      }

      const onClick = element.getAttribute('onclick') || '';
      const gtranslateMatch = onClick.match(/doGTranslate\(['"]([^'"]+)['"]\)/);

      if (gtranslateMatch) {
         return normalizeLanguageCode(gtranslateMatch[1]);
      }

      if (element instanceof HTMLAnchorElement && element.href) {
         const url = new URL(element.href, globalThis.location.href);
         return normalizeLanguageCode(url.searchParams.get('lang') || url.searchParams.get('language'));
      }

      return '';
   }

   function codesMatch(firstCode, secondCode) {
      if (!firstCode || !secondCode) {
         return false;
      }

      return firstCode === secondCode || firstCode.split('-')[0] === secondCode.split('-')[0];
   }

   function createFlag(code, className) {
      const image = document.createElement('img');
      image.className = className;
      image.src = getFlagUrl(code);
      image.alt = '';
      image.loading = 'lazy';
      image.setAttribute('aria-hidden', 'true');
      return image;
   }

   function getGTranslateLinks() {
      const wrapper = document.querySelector('.gtranslate_wrapper');

      if (!wrapper) {
         return [];
      }

      const links = Array.from(wrapper.querySelectorAll('a'));
      const selects = Array.from(wrapper.querySelectorAll('select option')).map((option) => option);
      return links.length ? links : selects;
   }

   function getAvailableLanguageOptions() {
      const seen = new Set();

      return getGTranslateLinks()
         .map((element) => {
            const code = getElementLanguageCode(element);

            return {
               code,
               element,
               label: languages[code] || getCleanLanguageLabel(element),
            };
         })
         .filter((option) => {
            if (!option.code || !languages[option.code] || seen.has(option.code)) {
               return false;
            }

            seen.add(option.code);
            return true;
         });
   }

   function updateLanguageTrigger(currentCode) {
      if (!languageTrigger || !languageTriggerFlag || !languageTriggerLabel) {
         return;
      }

      const code = languages[currentCode] ? currentCode : defaultLanguageCode;
      languageTriggerFlag.textContent = '';
      languageTriggerFlag.append(createFlag(code, 'osn-mobile-menu__language-trigger-img'));
      languageTriggerLabel.textContent = languages[code];
   }

   function renderLanguageOptions() {
      if (!languageList || !languageTrigger) {
         return;
      }

      const currentCode = getCurrentLanguageCode();
      languageList.textContent = '';

      languageOptions.forEach((option) => {
         const item = document.createElement('li');
         const button = document.createElement('button');
         const flag = document.createElement('span');
         const label = document.createElement('span');
         const check = document.createElement('span');
         const isCurrent = codesMatch(option.code, currentCode);

         button.type = 'button';
         button.className = 'osn-mobile-menu__language-option';
         button.dataset.languageCode = option.code;
         button.setAttribute('aria-current', isCurrent ? 'true' : 'false');
         flag.className = 'osn-mobile-menu__language-flag';
         label.className = 'osn-mobile-menu__language-label';
         check.className = 'osn-mobile-menu__language-check';
         check.setAttribute('aria-hidden', 'true');
         flag.append(createFlag(option.code, 'osn-mobile-menu__language-img'));
         label.textContent = option.label;
         button.append(flag, label, check);
         item.append(button);
         languageList.append(item);
      });

      languageTrigger.hidden = languageOptions.length === 0;
      updateLanguageTrigger(currentCode);
   }

   function initLanguageOptions() {
      const options = getAvailableLanguageOptions();

      if (!options.length) {
         return false;
      }

      languageOptions = options;
      renderLanguageOptions();
      return true;
   }

   function setGTranslateCookie(code) {
      const value = `/auto/${code}`;
      const maxAge = 60 * 60 * 24 * 365;
      document.cookie = `googtrans=${value}; path=/; max-age=${maxAge}`;
   }

   function selectLanguage(code) {
      const option = languageOptions.find((item) => item.code === code);

      if (!option) {
         return;
      }

      setGTranslateCookie(code);

      if (option.element instanceof HTMLOptionElement) {
         const select = option.element.closest('select');
         if (select) {
            select.value = option.element.value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
         }
      } else if (option.element instanceof HTMLElement) {
         option.element.click();
      } else {
         globalThis.location.reload();
      }

      setMenuOpen(false);
   }

   parentItems.forEach((item) => {
      const link = item.querySelector(':scope > a');
      const submenu = item.querySelector(':scope > .sub-menu');

      if (!link || !submenu) {
         return;
      }

      const toggle = document.createElement('button');
      toggle.type = 'button';
      toggle.className = 'osn-mobile-menu__submenu-toggle';
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', link.textContent.trim());

      submenu.hidden = true;
      link.after(toggle);

      function toggleSubmenu() {
         const willOpen = !item.classList.contains('is-open');

         closeSubmenus();
         item.classList.toggle('is-open', willOpen);
         toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
         submenu.hidden = !willOpen;
      }

      toggle.addEventListener('click', toggleSubmenu);

      // Menu items without a real destination should expand their submenu
      // instead of navigating or closing the menu.
      if (isPlaceholderLink(link)) {
         link.setAttribute('role', 'button');
         link.addEventListener('click', (event) => {
            event.preventDefault();
            toggleSubmenu();
         });
      }
   });

   burger.addEventListener('click', () => {
      setMenuOpen(!body.classList.contains('osn-menu-open'));
   });

   if (languageTrigger) {
      languageTrigger.addEventListener('click', () => {
         initLanguageOptions();
         setLanguageViewOpen(true);
      });
   }

   if (languageBack) {
      languageBack.addEventListener('click', () => {
         setLanguageViewOpen(false);
      });
   }

   if (languageList) {
      languageList.addEventListener('click', (event) => {
         const button = event.target instanceof Element
            ? event.target.closest('.osn-mobile-menu__language-option')
            : null;

         if (button instanceof HTMLButtonElement) {
            selectLanguage(button.dataset.languageCode || '');
         }
      });
   }

   menu.addEventListener('click', (event) => {
      const target = event.target;

      if (target instanceof HTMLAnchorElement && !isPlaceholderLink(target)) {
         setMenuOpen(false);
      }
   });

   if (!initLanguageOptions()) {
      setTimeout(initLanguageOptions, 500);
      setTimeout(initLanguageOptions, 1500);
   }

   breakpoint.addEventListener('change', (event) => {
      if (!event.matches) {
         setMenuOpen(false);
      }
   });
}());
