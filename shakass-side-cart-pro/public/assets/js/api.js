(function () {
	window.SSCApi = {
		controller: null,

		request(path, options = {}) {
			if (this.controller) {
				this.controller.abort();
			}

			this.controller = new AbortController();
			const headers = Object.assign(
				{
					'Content-Type': 'application/json',
					'X-WP-Nonce': sscConfig.nonce,
				},
				options.headers || {}
			);

			return fetch(sscConfig.restUrl + path, Object.assign({}, options, { signal: this.controller.signal, headers }))
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
	};
})();
