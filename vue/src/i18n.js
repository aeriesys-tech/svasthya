import { createI18n } from 'vue-i18n';
import en from './locales/en.json';
import kn from './locales/kn.json';

const messages = { en, kn };

const i18n = createI18n({
    locale: 'en',         // default
    fallbackLocale: 'en',
    messages,
    globalInjection: true
});

export default i18n;
