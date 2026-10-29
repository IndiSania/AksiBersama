document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('.nav-toggle');
    const nav = document.querySelector('.main-nav');
    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            const expanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!expanded));
            nav.classList.toggle('nav-open', !expanded);
        });
    }

    document.querySelectorAll('[data-table-search]').forEach((input) => {
        const table = document.querySelector(input.dataset.tableSearch);
        if (!table) return;
        input.addEventListener('input', () => {
            const query = input.value.trim().toLocaleLowerCase('id');
            table.querySelectorAll('tbody tr').forEach((row) => {
                row.hidden = !row.textContent.toLocaleLowerCase('id').includes(query);
            });
        });
    });

    document.querySelectorAll('a[href^="#"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            const target = document.querySelector(link.getAttribute('href'));
            if (target) {
                event.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
