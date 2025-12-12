console.log('Notifications script loaded');

$(document).ready(function() {
    // Get base URL from meta tag or fallback to window.location.origin
    const baseUrl = $('meta[name="base-url"]').attr('content') || window.location.origin;
    
    // Initialize Bootstrap dropdown
    var notificationDropdown = new bootstrap.Dropdown(document.getElementById('notificationDropdown'));
    
    // Function to update notifications
    function updateNotifications() {
        const url = baseUrl + '/notifications';
        
        console.log('Fetching notifications from:', url);
        
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
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
            },
            error: function(xhr, status, error) {
                console.error('Error fetching notifications:', status, error);
                console.log('Response text:', xhr.responseText);
            }
        });
    }

    // Toggle dropdown and update notifications
    $(document).on('click', '.notification-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        updateNotifications();
    });

    // Mark as read functionality
    $(document).on('click', '.mark-as-read', function() {
        const $notification = $(this).closest('.notification-item');
        const notificationId = $notification.data('id');
        
        $.ajax({
            url: baseUrl + '/notifications/mark_read/' + notificationId,
            method: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
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
            },
            error: function(xhr, status, error) {
                console.error('Error marking notification as read:', status, error);
                console.log('Response text:', xhr.responseText);
            }
        });
    });

    // Mark all as read
    $(document).on('click', '.mark-all-read', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: baseUrl + '/notifications/mark_all_read',
            method: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response && response.success) {
                    // Update all notifications to appear as read
                    $('.notification-item').removeClass('alert-info').addClass('alert-light')
                        .find('.mark-as-read')
                        .replaceWith('<small class="text-muted">Read</small>');
                    
                    // Hide the badge
                    $('.notification-badge').hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error marking all as read:', status, error);
                console.log('Response text:', xhr.responseText);
            }
        });
    });

    // Initial load
    updateNotifications();
    
    // Refresh every 30 seconds
    setInterval(updateNotifications, 30000);
});