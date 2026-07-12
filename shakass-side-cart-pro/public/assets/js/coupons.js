(function () {
	window.SSCCoupons = {
		init() {
			SSCState.on((cart) => this.render(cart.coupons || []));
			document.addEventListener('submit', (event) => {
				const form = event.target.closest('[data-ssc-coupon-form]');
				if (!form) {
					return;
				}

				event.preventDefault();
				const input = form.querySelector('[name="ssc_coupon_code"]');
				this.apply(input ? input.value : '');
			});

			document.addEventListener('click', (event) => {
				const button = event.target.closest('[data-ssc-remove-coupon]');
				if (button) {
					this.remove(button.dataset.sscRemoveCoupon);
				}
			});
		},

		render(coupons) {
			const list = document.querySelector('[data-ssc-coupon-list]');
			if (!list) {
				return;
			}
			list.replaceChildren();
			coupons.forEach((coupon) => {
				const row = document.createElement('div');
				row.className = 'ssc-coupon-pill';
				const label = document.createElement('span');
				label.textContent = coupon.label || coupon.code;
				const button = document.createElement('button');
				button.type = 'button';
				button.dataset.sscRemoveCoupon = coupon.code;
				button.setAttribute('aria-label', sscConfig.i18n.removeCoupon);
				button.textContent = '×';
				row.append(label, button);
				list.appendChild(row);
			});
		},

		apply(code) {
			document.dispatchEvent(new CustomEvent('ssc:cart-updating'));
			SSCApi.coupon(code)
				.then((cart) => {
					SSCState.set(cart);
					document.dispatchEvent(new CustomEvent('ssc:coupon-applied', { detail: { code } }));
				})
				.catch((error) => document.dispatchEvent(new CustomEvent('ssc:error', { detail: error })));
		},

		remove(code) {
			document.dispatchEvent(new CustomEvent('ssc:cart-updating'));
			SSCApi.removeCoupon(code)
				.then((cart) => {
					SSCState.set(cart);
					document.dispatchEvent(new CustomEvent('ssc:coupon-removed', { detail: { code } }));
				})
				.catch((error) => document.dispatchEvent(new CustomEvent('ssc:error', { detail: error })));
		},
	};
})();
