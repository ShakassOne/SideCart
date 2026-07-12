(function () {
	window.SSCApi = {
		request(path, options = {}) {
			const controller = new AbortController();
			const headers = Object.assign(
				{
					'Content-Type': 'application/json',
					'X-WP-Nonce': sscConfig.nonce,
				},
				options.headers || {}
			);

			return fetch(sscConfig.restUrl + path, Object.assign({}, options, { signal: controller.signal, headers, credentials: 'same-origin', cache: 'no-store' }))
				.then(async (response) => {
					const data = await response.json().catch(() => ({ message: sscConfig.i18n.error }));
					if (!response.ok) {
						throw data;
					}
					return data;
				});
		},

		cart() {
			return this.request('cart');
		},

		quantity(key, quantity) {
			return this.request('cart/item', {
				method: 'POST',
				body: JSON.stringify({ key, quantity: parseInt(quantity, 10) || 0 }),
			});
		},

		remove(key) {
			return this.request('cart/item?key=' + encodeURIComponent(key), { method: 'DELETE' });
		},

		coupon(code) {
			return this.request('cart/coupon', {
				method: 'POST',
				body: JSON.stringify({ code }),
			});
		},

		removeCoupon(code) {
			return this.request('cart/coupon?code=' + encodeURIComponent(code), { method: 'DELETE' });
		},
	};
})();
