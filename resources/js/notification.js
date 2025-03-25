document.addEventListener('DOMContentLoaded', function() {
    const notificationIcon = document.getElementById('notificationIcon');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationCount = document.getElementById('notificationCount');
    const notificationsList = document.getElementById('notifications-list');
    const markAllRead = document.getElementById('markAllRead');
    
    // Créer l'élément audio pour la notification sonore
   // const notificationSound = new Audio('/path/to/notification-sound.mp3');
    // Note: Remplacez '/path/to/notification-sound.mp3' par le chemin vers votre fichier son
    
    let notifications = JSON.parse(localStorage.getItem('notifications')) || [];
    let unreadCount = notifications.filter(n => !n.read).length;
    
    // Mark all notifications as read
    markAllRead.addEventListener('click', function(e) {
        e.preventDefault();
        notifications.forEach(notification => {
            notification.read = true;
        });
        unreadCount = 0;
        saveNotifications();
        updateNotificationUI();
    });

    // Format date for display
    function formatDate(date) {
        return new Date(date).toLocaleTimeString('fr-FR', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Sauvegarder les notifications dans localStorage
    function saveNotifications() {
        localStorage.setItem('notifications', JSON.stringify(notifications));
    }



    // Update notification UI
    function updateNotificationUI() {
        notificationCount.textContent = unreadCount;
        notificationCount.style.display = unreadCount > 0 ? 'flex' : 'none';

        notificationsList.innerHTML = '';

        if (notifications.length === 0) {
            notificationsList.innerHTML = '<div class="no-notifications px-16 py-12">Aucune notification</div>';
            return;
        }

        notifications.forEach((notification, index) => {
            const notificationItem = document.createElement('div');
            notificationItem.className = `notification-item ${notification.read ? '' : 'unread'}`;

            const notificationContent = document.createElement('div');
            notificationContent.innerHTML = `
                <strong>New Ordre</strong>
                <div>Order ID #${notification.orderId}</div>
                <div>Customer : ${notification.customerName}</div>
                <div>Phone : ${notification.phoneNumber}</div>
             
                <div class="notification-time">${formatDate(notification.time)}</div>
            `;

            notificationItem.appendChild(notificationContent);

            // Mark as read on click
            notificationItem.addEventListener('click', function() {
                if (!notification.read) {
                    notification.read = true;
                    unreadCount--;
                    saveNotifications();
                    updateNotificationUI();
                }

                // Redirect to order detail page
                window.location.href = `/admin/orders/view/${notification.orId}`;
            });

            notificationsList.appendChild(notificationItem);
        });
    }

    // Listen for Pusher events
    window.Echo.channel('orders')
        .listen('.OrderCreated', (e) => {
            console.log('Notification reçue:', e);

        // Son simple généré par le navigateur (beep)
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.type = 'ding-dong'; // son sinusoïdal (plus doux)
        oscillator.frequency.value = 500 ; // fréquence en Hz
        gainNode.gain.value = 0.1; // volume faible pour ne pas surprendre
        
        oscillator.start();
        
        // Arrêter après 0.3 secondes
        setTimeout(function() {
            oscillator.stop();
        }, 1000);
            const newNotification = {
                orderId: e.order_id,
                orId: e.id,
                customerName: e.customer_name,
                phoneNumber: e.phone_number,
                //ariverdfrom : e.arrived_from,
                message: e.message,
                time: new Date(),
                read: false
            };

            // Jouer le son lorsqu'une nouvelle notification arrive
           // playNotificationSound();

            notifications.unshift(newNotification);
            unreadCount++;

            if (notifications.length > 20) {
                if (!notifications[notifications.length - 1].read) {
                    unreadCount--;
                }
                notifications.pop();
            }

            saveNotifications();
            updateNotificationUI();
        });

    // Initialize UI
    updateNotificationUI();
});