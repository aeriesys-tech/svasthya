export const TOAST_SUCCESS = {
    type: 'success',
    dangerouslyHTMLString: true,
    autoClose: 1500,
    toastClassName: 'toast-black-bg',
    bodyClassName: 'toast-light-text',
    toastStyle: {
        fontSize: '14px',
    },
};

export const TOAST_ERROR = {
    type: 'error',
    dangerouslyHTMLString: true,
    autoClose: 1500,
    toastClassName: 'toast-black-bg',
    bodyClassName: 'toast-light-text',
    toastStyle: {
        fontSize: '14px',
    },
};

export const themeInitializer = () => {
    if (typeof setActivelink === 'function') setActivelink();
    if (typeof fixedHeaderSpace === 'function') fixedHeaderSpace();
    // if (typeof autoThemeMode === 'function') autoThemeMode();
    if (typeof featherjs === 'function') featherjs();
    if (typeof coverimg === 'function') coverimg();
    if (typeof dontclosedd === 'function') dontclosedd();
    if (typeof checkstrength === 'function') checkstrength();
    if (typeof bstooltip === 'function') bstooltip();
    if (typeof swipernavpagination === 'function') swipernavpagination();
    if (typeof PageLoaderHide === 'function') PageLoaderHide();
}