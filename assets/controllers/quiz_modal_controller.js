import { Controller } from '@hotwired/stimulus';

const FOCUSABLE_SELECTOR = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

export default class extends Controller {
    static targets = ['panel', 'dialog'];

    connect() {
        this.onKeydown = this.onKeydown.bind(this);
    }

    open() {
        this.lastFocused = document.activeElement;

        this.panelTarget.classList.remove('hidden');
        this.panelTarget.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        this.dialogTarget.setAttribute('role', 'dialog');
        this.dialogTarget.setAttribute('aria-modal', 'true');
        document.addEventListener('keydown', this.onKeydown);

        requestAnimationFrame(() => {
            this.panelTarget.classList.remove('opacity-0');
            this.dialogTarget.classList.remove('translate-y-4');
            this.dialogTarget.focus();
        });
    }

    close() {
        this.panelTarget.classList.add('opacity-0');
        this.dialogTarget.classList.add('translate-y-4');
        document.body.classList.remove('overflow-hidden');

        this.dialogTarget.removeAttribute('role');
        this.dialogTarget.removeAttribute('aria-modal');
        document.removeEventListener('keydown', this.onKeydown);

        window.setTimeout(() => {
            this.panelTarget.classList.add('hidden');
            this.panelTarget.classList.remove('flex');
        }, 300);

        if (this.lastFocused) {
            this.lastFocused.focus();
        }
    }

    onKeydown(event) {
        if (event.key === 'Escape') {
            this.close();

            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        const focusable = this.dialogTarget.querySelectorAll(FOCUSABLE_SELECTOR);
        if (focusable.length === 0) {
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
    }

    stopPropagation(event) {
        event.stopPropagation();
    }
}
