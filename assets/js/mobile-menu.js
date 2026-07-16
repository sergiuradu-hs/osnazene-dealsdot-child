(function () {
   const breakpoint = globalThis.matchMedia('(max-width: 1024px)');
   const body = document.body;
   const burger = document.querySelector('.osn-burger');
   const menu = document.getElementById('osn-mobile-menu');

   if (!body || !burger || !menu) {
      return;
   }

   const parentItems = Array.from(menu.querySelectorAll('.menu-item-has-children'));

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

   function setMenuOpen(isOpen) {
      body.classList.toggle('osn-menu-open', isOpen);
      burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      menu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');

      if (!isOpen) {
         closeSubmenus();
      }
   }

   function isPlaceholderLink(anchor) {
      const href = (anchor.getAttribute('href') || '').trim();
      return href === '' || href === '#';
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

   menu.addEventListener('click', (event) => {
      const target = event.target;

      if (target instanceof HTMLAnchorElement && !isPlaceholderLink(target)) {
         setMenuOpen(false);
      }
   });

   breakpoint.addEventListener('change', (event) => {
      if (!event.matches) {
         setMenuOpen(false);
      }
   });
}());
