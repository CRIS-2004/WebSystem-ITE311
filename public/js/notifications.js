$(document).ready(function() {
    // Function to update notifications
    function updateNotifications() {
        const baseUrl = window.location.origin;
        const url = baseUrl + '/ITE311-PARCON/notifications';
        
        console.log('Fetching notifications from:', url);
        
        $.get(url, function(response) {
            console.log('Notifications response:', response);
            
            if (response && response.success) {
                const $badge = $('.notification-badge');
                const $container = $('.notifications-container');
                
                // Update badge
                if (response.unreadCount > 0) {
                    $badge.text(response.unreadCount).show();
                } else {
                    $badge.hide();
                }
                
                // Update notifications container
                if (response.notifications && response.notifications.length > 0) {
                    let html = '';
                    response.notifications.forEach(function(notification) {
                        const isUnread = notification.is_read === 0 || notification.is_read === '0';
                        const alertClass = isUnread ? 'alert-info' : 'alert-light';
                        
                        html += `
                            <div class="alert ${alertClass} p-2 mb-2 notification-item" data-id="${notification.id}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="me-2 notification-message">${notification.message}</div>
                                    ${isUnread ? 
                                        '<button class="btn btn-sm btn-outline-secondary mark-as-read">Mark as Read</button>' : 
                                        '<small class="text-muted">Read</small>'}
                                </div>
                                <div class="small text-muted notification-time">
                                    ${new Date(notification.created_at).toLocaleString()}
                                </div>
                            </div>
                        `;
                    });
                    $container.html(html);
                } else {
                    $container.html('<div class="text-center p-3 text-muted">No notifications</div>');
                }
            }
        }).fail(function(xhr, status, error) {
            console.error('Error fetching notifications:', status, error);
            console.log('Response text:', xhr.responseText);
        });
    }

    // Toggle dropdown
    $(document).on('click', '.notification-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.notification-dropdown').toggleClass('show');
        updateNotifications();
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.notification-container').length) {
            $('.notification-dropdown').removeClass('show');
        }
    });

    // Mark as read functionality
    $(document).on('click', '.mark-as-read', function() {
        const $notification = $(this).closest('.notification-item');
        const notificationId = $notification.data('id');
        const baseUrl = window.location.origin;
        
        $.post(baseUrl + '/ITE311-PARCON/notifications/mark_read/' + notificationId, function(response) {
            if (response && response.success) {
                // Update UI
                $notification
                    .removeClass('alert-info')
                    .addClass('alert-light')
                    .find('.mark-as-read')
                    .replaceWith('<small class="text-muted">Read</small>');
                
                // Update badge count
                const $badge = $('.notification-badge');
                const newCount = parseInt($badge.text()) - 1;
                if (newCount > 0) {
                    $badge.text(newCount);
                } else {
                    $badge.hide();
                }
            }
        });
    });

    // Initial load
    updateNotifications();
    
    // Refresh every 30 seconds
    setInterval(updateNotifications, 5000);
});