(function () {
	const text = (value) => String(value === null || value === undefined ? '' : value);

	const trustedHtml = (value) => text(value);

	function createElement(tag, className, content) {
		const element = document.createElement(tag);
		if (className) {
			element.className = className;
		}
		if (content !== undefined) {
			element.textContent = content;
		}
		return element;
	}

	window.SSCCartItems = {
		timers: {},

		init() {
			SSCState.on((cart) => this.render(cart));

			document.addEventListener('click', (event) => {
				const button = event.target.closest('[data-ssc-qty], [data-ssc-remove], [data-ssc-mockup-toggle]');
				if (!button) {
					return;
				}

				const item = button.closest('[data-ssc-item]');
				if (!item) {
					return;
				}

				if (button.hasAttribute('data-ssc-remove')) {
					this.remove(item.dataset.key);
					return;
				}

				if (button.hasAttribute('data-ssc-mockup-toggle')) {
					this.toggleMockup(item, button);
					return;
				}

				const input = item.querySelector('[data-ssc-quantity]');
				const current = parseInt(input.value || '0', 10);
				const delta = button.dataset.sscQty === 'plus' ? 1 : -1;
				const next = Math.max(0, current + delta);
				input.value = next;
				this.queue(item.dataset.key, next);
			});

			document.addEventListener('change', (event) => {
				if (!event.target.matches('[data-ssc-quantity]')) {
					return;
				}

				const item = event.target.closest('[data-ssc-item]');
				if (item) {
					this.queue(item.dataset.key, event.target.value);
				}
			});
		},

		toggleMockup(item, button) {
			const isBack = item.classList.toggle('is-showing-back');
			button.setAttribute('aria-pressed', String(isBack));
			button.setAttribute('aria-label', isBack ? sscConfig.i18n.showFront : sscConfig.i18n.showBack);
		},

		queue(key, quantity) {
			clearTimeout(this.timers[key]);
			this.timers[key] = setTimeout(() => {
				delete this.timers[key];
				this.update(key, quantity);
			}, sscConfig.debounce || 450);
		},

		setBusy(key, isBusy) {
			const escapeSelector = window.CSS && CSS.escape ? CSS.escape : (value) => String(value).replace(/["\\]/g, '\\$&');
			const selector = key ? `[data-ssc-item][data-key="${escapeSelector(key)}"] button, [data-ssc-item][data-key="${escapeSelector(key)}"] input` : '[data-ssc-item] button, [data-ssc-item] input';
			document.querySelectorAll(selector).forEach((control) => {
				control.disabled = isBusy;
			});
		},

		update(key, quantity) {
			this.setBusy(key, true);
			document.dispatchEvent(new CustomEvent('ssc:cart-updating'));
			SSCApi.quantity(key, quantity)
				.then((cart) => {
					SSCState.set(cart);
					document.dispatchEvent(new CustomEvent('ssc:quantity-changed', { detail: { key, quantity } }));
				})
				.catch((error) => document.dispatchEvent(new CustomEvent('ssc:error', { detail: error })))
				.finally(() => this.setBusy(key, false));
		},

		remove(key) {
			this.setBusy(key, true);
			document.dispatchEvent(new CustomEvent('ssc:cart-updating'));
			SSCApi.remove(key)
				.then((cart) => {
					SSCState.set(cart);
					document.dispatchEvent(new CustomEvent('ssc:item-removed', { detail: { key } }));
				})
				.catch((error) => document.dispatchEvent(new CustomEvent('ssc:error', { detail: error })))
				.finally(() => this.setBusy(key, false));
		},

		render(cart) {
			const list = document.querySelector('[data-ssc-items]');
			const empty = document.querySelector('[data-ssc-empty]');
			const totals = document.querySelector('[data-ssc-totals]');

			if (!list) {
				return;
			}

			list.replaceChildren();

			(cart.items || []).forEach((item) => {
				list.appendChild(this.renderItem(item));
			});

			if (empty) {
				empty.hidden = (cart.items || []).length > 0;
			}

			if (totals) {
				totals.replaceChildren(this.renderTotalRow(sscConfig.i18n.subtotal, cart.subtotal_html), this.renderTotalRow(sscConfig.i18n.total, cart.total_html));
			}

			document.querySelectorAll('[data-ssc-count]').forEach((element) => {
				element.textContent = cart.count || 0;
			});
			document.querySelectorAll('[data-ssc-total]').forEach((element) => {
				element.innerHTML = trustedHtml(cart.total_html || '');
			});
			document.querySelectorAll('[data-ssc-cart-link]').forEach((element) => {
				element.href = cart.cart_url || '#';
			});
			document.querySelectorAll('[data-ssc-checkout-link]').forEach((element) => {
				element.href = cart.checkout_url || '#';
			});
		},

		renderItem(item) {
			const article = createElement('article', 'ssc-item');
			article.dataset.sscItem = '';
			article.dataset.key = item.key;

			const imageLink = createElement('a', 'ssc-item__image');
			imageLink.href = item.permalink || '#';
			if (item.mockups && item.mockups.recto && item.mockups.verso) {
				imageLink.classList.add('ssc-item__image--mockups');
				const front = createElement('img', 'ssc-item__mockup ssc-item__mockup--front');
				front.src = item.mockups.recto;
				front.alt = item.name;
				front.loading = 'lazy';
				const back = createElement('img', 'ssc-item__mockup ssc-item__mockup--back');
				back.src = item.mockups.verso;
				back.alt = '';
				back.loading = 'lazy';
				imageLink.append(front, back);
			} else {
				imageLink.innerHTML = trustedHtml(item.image);
			}

			const content = createElement('div', 'ssc-item__content');
			const name = createElement('a', 'ssc-item__name', item.name);
			name.href = item.permalink || '#';
			const price = createElement('div', 'ssc-item__price');
			price.innerHTML = trustedHtml(item.price);

			const quantity = createElement('div', 'ssc-qty');
			const minus = createElement('button', '', '−');
			minus.type = 'button';
			minus.dataset.sscQty = 'minus';
			minus.setAttribute('aria-label', sscConfig.i18n.decrease);

			const input = createElement('input');
			input.type = 'number';
			input.min = item.min || 0;
			if (item.max && item.max > 0) {
				input.max = item.max;
			}
			input.value = item.quantity;
			input.dataset.sscQuantity = '';

			const plus = createElement('button', '', '+');
			plus.type = 'button';
			plus.dataset.sscQty = 'plus';
			plus.setAttribute('aria-label', sscConfig.i18n.increase);

			quantity.append(minus, input, plus);
			content.append(name, price, quantity);

			const lineTotal = createElement('div', 'ssc-item__total');
			lineTotal.innerHTML = trustedHtml(item.line_total);

			const remove = createElement('button', 'ssc-remove', '×');
			remove.type = 'button';
			remove.dataset.sscRemove = '';
			remove.setAttribute('aria-label', sscConfig.i18n.remove);

			article.append(imageLink, content, lineTotal, remove);
			if (item.mockups && item.mockups.recto && item.mockups.verso) {
				const toggle = createElement('button', 'ssc-mockup-toggle');
				toggle.type = 'button';
				toggle.dataset.sscMockupToggle = '';
				toggle.setAttribute('aria-label', sscConfig.i18n.showBack);
				toggle.setAttribute('aria-pressed', 'false');
				toggle.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M7 7h9.5l-2.5-2.5M17 17H7.5L10 19.5M18 7a7 7 0 0 1 0 10M6 17a7 7 0 0 1 0-10"/></svg>';
				article.append(toggle);
			}
			return article;
		},

		renderTotalRow(label, value) {
			const row = createElement('div');
			row.append(createElement('span', '', label));
			const amount = createElement('strong');
			amount.innerHTML = trustedHtml(value || '');
			row.append(amount);
			return row;
		},
	};
})();
