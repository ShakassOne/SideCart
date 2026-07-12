(function () {
	window.SSCRewards = {
		init() {
			SSCState.on((cart) => this.render(cart.rewards || {}));
		},

		render(reward) {
			const section = document.querySelector('[data-ssc-progress]');
			if (!section) {
				return;
			}

			section.hidden = !reward.enabled;
			const bar = section.querySelector('[data-ssc-progress-fill]');
			const message = section.querySelector('[data-ssc-progress-message]');
			if (bar) {
				bar.style.width = (reward.percent || 0) + '%';
			}
			if (message) {
				message.innerHTML = reward.message || '';
			}
		},
	};
})();
