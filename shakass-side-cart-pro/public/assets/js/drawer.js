(function () {
	window.SSCDrawer = {
		root: null,
		drawer: null,
		lastFocus: null,

		init() {
			this.root = document.querySelector('[data-ssc-root]');
			if (!this.root) {
				return;
			}

			this.drawer = this.root.querySelector('.ssc-drawer');

			document.addEventListener('click', (event) => {
				if (event.target.closest('[data-ssc-open], .ssc-open-cart')) {
					this.open(event);
				}
				if (event.target.closest('[data-ssc-close]')) {
					this.close();
				}
			});

			document.addEventListener('keydown', (event) => {
				if (event.key === 'Escape' && !this.root.hidden) {
					this.close();
				}
				if (event.key === 'Tab' && !this.root.hidden) {
					this.trap(event);
				}
			});

			document.addEventListener('ssc:open', () => this.open());
			document.addEventListener('ssc:close', () => this.close());
		},

		open(event) {
			document.dispatchEvent(new CustomEvent('ssc:before-open'));
			this.lastFocus = event && event.target ? event.target : document.activeElement;
			this.root.hidden = false;
			document.documentElement.classList.add('ssc-is-open');
			window.setTimeout(() => this.root.classList.add('is-open'), 10);
			this.drawer.focus({ preventScroll: true });
			document.dispatchEvent(new CustomEvent('ssc:opened'));
		},

		close() {
			document.dispatchEvent(new CustomEvent('ssc:before-close'));
			this.root.classList.remove('is-open');
			document.documentElement.classList.remove('ssc-is-open');
			window.setTimeout(() => {
				this.root.hidden = true;
				if (this.lastFocus && this.lastFocus.focus) {
					this.lastFocus.focus({ preventScroll: true });
				}
				document.dispatchEvent(new CustomEvent('ssc:closed'));
			}, 220);
		},

		toggle() {
			if (this.root.hidden) {
				this.open();
			} else {
				this.close();
			}
		},

		trap(event) {
			const focusable = Array.from(this.drawer.querySelectorAll('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])')).filter((element) => !element.disabled && element.offsetParent !== null);
			if (!focusable.length) {
				return;
			}

			const first = focusable[0];
			const last = focusable[focusable.length - 1];
			if (event.shiftKey && document.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		},
	};
})();
