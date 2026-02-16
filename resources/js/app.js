import './bootstrap';

import { registerSW } from 'virtual:pwa-register';

if ('serviceWorker' in navigator) {
  registerSW({
    immediate: true,
    onNeedRefresh() {
      console.log('محتوى جديد متاح، يرجى التحديث.');
    },
    onOfflineReady() {
      console.log('التطبيق جاهز للعمل بدون إنترنت.');
    },
  });
}
