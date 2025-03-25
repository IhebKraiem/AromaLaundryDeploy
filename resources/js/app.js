import './bootstrap';
import './notification';
window.Echo.channel('orders')
    .listen('OrderCreated', (event) => {
        console.log(event.message); // Affiche le message dans la console
        alert(event.message); // Affiche une alerte avec le message
    });