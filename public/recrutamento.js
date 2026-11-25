const all = document.querySelector('[data-all]');
const rows = [...document.querySelectorAll('[form="bulk"][type="checkbox"]')];
const bulk = document.querySelector('[data-bulk]');
function updateSelection() {
    const selected = rows.filter(row => row.checked).length;
    if (!all || !bulk) return;
    all.checked = selected > 0 && selected === rows.length;
    all.indeterminate = selected > 0 && selected < rows.length;
    bulk.disabled = selected === 0;
}
all?.addEventListener('change', () => {
    rows.forEach(row => row.checked = all.checked);
    updateSelection();
});
rows.forEach(row => row.addEventListener('change', updateSelection));
updateSelection();
document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', event => {
        if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
});
