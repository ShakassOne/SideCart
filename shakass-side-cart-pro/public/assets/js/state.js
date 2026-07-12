(function () {
	window.SSCState = {
		cart: null,
		listeners: [],

		set(cart) {
			this.cart = cart;
			this.listeners.forEach((listener) => listener(cart));
			document.dispatchEvent(new CustomEvent('ssc:cart-updated', { detail: cart }));
		},

		on(listener) {
			this.listeners.push(listener);
		},
	};
})();
