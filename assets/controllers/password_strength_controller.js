import { Controller } from '@hotwired/stimulus';

const CRITERIA = {
    length: (value) => value.length >= 12,
    lowercase: (value) => /[a-z]/.test(value),
    uppercase: (value) => /[A-Z]/.test(value),
    number: (value) => /[0-9]/.test(value),
    symbol: (value) => /[^a-zA-Z0-9]/.test(value),
};

const LEVELS = [
    { min: 1, width: '20%', color: '#fb7185' },
    { min: 3, width: '60%', color: '#fb923c' },
    { min: 4, width: '80%', color: '#E7FF8D' },
    { min: 5, width: '100%', color: '#D3FF2E' },
];

export default class extends Controller {
    static targets = ['input', 'bar', 'label', 'requirement'];
    static values = { labels: Array };

    connect() {
        this.update();
    }

    update() {
        const value = this.inputTarget.value;
        let metCount = 0;

        this.requirementTargets.forEach((el) => {
            const met = CRITERIA[el.dataset.requirement](value);

            el.classList.toggle('text-cream', met);
            el.classList.toggle('text-taupe', !met);

            const dot = el.querySelector('[data-password-strength-dot]');
            if (dot) {
                dot.classList.toggle('bg-lime', met);
                dot.classList.toggle('bg-taupe/30', !met);
                dot.classList.toggle('scale-125', met);
                dot.classList.toggle('scale-100', !met);
            }

            if (met) {
                metCount += 1;
            }
        });

        const level = value.length === 0 ? null : [...LEVELS].reverse().find((candidate) => metCount >= candidate.min);

        if (this.hasBarTarget) {
            this.barTarget.style.width = level ? level.width : '0%';
            this.barTarget.style.backgroundColor = level ? level.color : 'transparent';
        }

        if (this.hasLabelTarget) {
            const levelIndex = level ? LEVELS.indexOf(level) : -1;
            this.labelTarget.textContent = levelIndex >= 0 ? (this.labelsValue[levelIndex] ?? '') : '';
            this.labelTarget.style.color = level ? level.color : '';
        }
    }
}
