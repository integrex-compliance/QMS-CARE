// Keep financial inputs editable while preventing accidental native stepping.
// Delegation also protects payment rows and invoice modals added after page load.
(function () {
    'use strict';

    function isNumberInput(element) {
        return element && element.tagName === 'INPUT' && element.type === 'number';
    }

    document.addEventListener('wheel', function (event) {
        if (isNumberInput(event.target) && document.activeElement === event.target) {
            event.preventDefault();
        }
    }, { capture: true, passive: false });

    document.addEventListener('keydown', function (event) {
        if (isNumberInput(event.target) && (event.key === 'ArrowUp' || event.key === 'ArrowDown')) {
            event.preventDefault();
        }
    }, true);
}());
