import "../scss/styles.scss";

document.addEventListener('DOMContentLoaded', () => {

    const noticeEl = document.querySelector<HTMLDivElement>('.wp-block-digitfab-cookie-notice');
    const hideEls = document.querySelectorAll('.df-hide-cookie-notice');

    if (!noticeEl || !hideEls.length) return;

    hideEls.forEach(e => {
        e.addEventListener('click', e => {
            const date = new Date(Date.now());
            date.setFullYear(date.getFullYear() + 1);

            document.cookie = "df-hide-cookie-notice=1; path=/; expires=" + date.toUTCString();
            noticeEl.style.display = 'none';
        })
    })
});
