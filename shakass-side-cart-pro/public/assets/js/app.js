(function () {
	function refreshAndMaybeOpen(open) {
		return window.ShakassSideCart.refresh().then(() => {
			if (open && sscConfig.openAfterAdd) {
				SSCDrawer.open();
			}
		});
	}

	function init() {
		SSCDrawer.init();
		SSCCartItems.init();
		if (window.SSCCoupons) {
			SSCCoupons.init();
		}
		if (window.SSCRewards) {
			SSCRewards.init();
		}
		if (window.SSCRecommendations) {
			SSCRecommendations.init();
		}

		window.ShakassSideCart = {
			open: () => SSCDrawer.open(),
			close: () => SSCDrawer.close(),
			toggle: () => SSCDrawer.toggle(),
			refresh: () => SSCApi.cart().then((cart) => SSCState.set(cart)),
		};

		window.ShakassSideCart.refresh();

		document.body.addEventListener('added_to_cart', () => refreshAndMaybeOpen(true));
		document.addEventListener('ssc:item-added', () => refreshAndMaybeOpen(true));

		if (window.jQuery) {
			window.jQuery(document.body).on('added_to_cart wc_fragments_refreshed', () => refreshAndMaybeOpen(true));
		}

		document.addEventListener('ssc:error', (event) => {
			const live = document.querySelector('[data-ssc-live]');
			if (live) {
				live.textContent = event.detail && event.detail.message ? event.detail.message : sscConfig.i18n.error;
			}
		});

		document.dispatchEvent(new CustomEvent('ssc:initialized'));
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
