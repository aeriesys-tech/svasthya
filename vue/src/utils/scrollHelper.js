// src/utils/scrollHelper.js
export function scrollToTop(delay = 0) {
    setTimeout(() => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }, delay);
}
