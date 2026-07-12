(function () {
	function createCard(item) {
		const card = document.createElement('a');
		card.className = 'ssc-recommendation-card';
		card.href = item.permalink || '#';
		const image = document.createElement('span');
		image.className = 'ssc-recommendation-card__image';
		image.innerHTML = item.image || '';
		const name = document.createElement('strong');
		name.textContent = item.name || '';
		const price = document.createElement('span');
		price.innerHTML = item.price || '';
		card.append(image, name, price);
		return card;
	}

	window.SSCRecommendations = {
		init() {
			SSCState.on((cart) => this.render(cart.recommendations || []));
		},

		render(items) {
			const list = document.querySelector('[data-ssc-recommendations]');
			if (!list) {
				return;
			}
			list.replaceChildren();
			items.forEach((item) => list.appendChild(createCard(item)));
			list.closest('[data-ssc-section]').hidden = !items.length;
		},
	};
})();
