import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js";
import { onMessage } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js";

const firebaseConfig = {
  apiKey: "AIzaSyDbQno1WG8etWRyLSZt1k9MqF2-Xq6316k",
  projectId: "laravel-test-9c8c2",
  messagingSenderId: "658864327741",
  appId: "1:658864327741:web:18a53eba7c7ccd7ae5ae54",
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

async function registerAdminNotification() {
  try {
    const permission = await Notification.requestPermission();
    if (permission === 'granted') {
      const token = await getToken(messaging, {
        vapidKey: 'BAkg2zJwGwqO8LPZ52fk6XqUFsVYSXkdvh_iJNOpmZf3GIVgUzfEtQUpqZZEBEwTl_z6FhFQdUsD65_ZcZ36rGM'
      });

      if (token) {
        fetch('/api/save-fcm-token', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
          },
          body: JSON.stringify({ token: token })
        });
        console.log('Admin FCM Token Registered');
      }
    }
  } catch (error) {
    console.error('FCM Error:', error);
  }
}

registerAdminNotification();



onMessage(messaging, (payload) => {
  console.log('Message received in foreground: ', payload);
  alert(`${payload.notification.title}: ${payload.notification.body}`);
});
