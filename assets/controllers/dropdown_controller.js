import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.onClickOutside = this.onClickOutside.bind(this);
        document.addEventListener('click', this.onClickOutside);
    }

    disconnect() {
        document.removeEventListener('click', this.onClickOutside);
    }

    onClickOutside(event) {
        if (!this.element.contains(event.target)) {
            this.element.open = false;
        }
    }

    close() {
        this.element.open = false;
    }
}
