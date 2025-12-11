<template>
    <!-- notification -->
    <div class="offcanvas offcanvas-end shadow border-0 maxwidth-300" tabindex="-1" id="view-notification" data-bs-scroll="true" data-bs-backdrop="false">
        <div class="offcanvas-header border-bottom">
            <div class="flex-grow-1">
                <h6 class="mb-0">Notifications</h6>
                <p class="text-secondary">{{ notifications.length }} new update{{ notifications.length !== 1 ? 's' : '' }}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="card bg-none mb-2" v-for="(notification, index) in  notifications" :key="index">
                <div class="card-body">
                    <div class="row gx-3">
                        <div class="col">
                            <p class="small mb-2" @click="handleUserTap(notification)" role="button">{{ notification.message }}</p>
                            <div class="row gx-3 gx-lg-4 align-items-center">
                                <div class="col">
                                    <p class="text-secondary small">{{ moment(notification.timestamp).format('D MMM, h:mm a') }}</p>
                                </div>
                                <div class="col-auto">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-square btn-link theme-red" @click="markAsRead(index)"><i class="bi bi-check2-circle fs-5"></i> Clear</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted, watch, inject } from 'vue';
    import { getEcho, isEchoReady } from '@/utils/echoManager';
    import { Notification } from '@/models/notification';
    import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config';
    import { notifications } from '@/stores/notificationStore';
    import { useRouter } from 'vue-router';
    import moment from 'moment';
    import axios from 'axios';
    
    const API_URL = import.meta.env.VITE_API_URL;
    const isNotificationsLoading = ref(false);
    const showToast = inject('showToast')
    const router = useRouter();
    const notificationAudio = new Audio('/sounds/notification-1.wav')

    /**
     * Loaders
     */

    onMounted(() => {
        // loadUnreadNotifications();

        watch(isEchoReady, (ready) => {
            if (ready) {
                const echo = getEcho();
                if (!echo) return;

                echo.private(`notifications.${sessionStorage.getItem('user-id')}`)
                    .listen('.event.created', (e) => {
                        notifications.value.unshift(Notification.fromRealtime(e));
                        playNotificationSound();
                    })
                    .listen('.event.published', (e) => {
                        notifications.value.unshift(Notification.fromRealtime(e));
                        playNotificationSound();
                    })
                    
            }
        }, { immediate: true });
    });

    const loadUnreadNotifications = () => {
        axios.get(`${API_URL}/`, {
            params: {
                user_id: sessionStorage.getItem('user-id')
            },
            headers: {
                Authorization: 'Bearer ' + sessionStorage.getItem('')
            },
            meta: { loadingRef: isNotificationsLoading }
        }).then((response) => {
            const rows = response.data.notifications || [];
            rows.forEach(row => {
                notifications.value.unshift(Notification.fromPersisted(row));
            });
        })
        .catch((error) => {
            console.log(error);
            handleApiError(error);
        })
    }

    /**
     * Operations
     */

    const markAsRead = (index) => {
        const notification = notifications.value[index];

        axios.post(`${API_URL}/`, {
                _method: 'PUT'
            }, {
                headers: {
                    Authorization: 'Bearer ' + sessionStorage.getItem('')
                },
                meta: { loadingRef: isNotificationsLoading }
            }).then((response) => {
                notifications.value.splice(index, 1);
            }).catch((error) => {
                handleApiError(error);
            })
    }

    const handleUserTap = (item) => {
        const offcanvasEl = document.getElementById('view-notification');
        if (!offcanvasEl) return;
        const offcanvas = window.bootstrap.Offcanvas.getInstance(offcanvasEl) || new window.bootstrap.Offcanvas(offcanvasEl);
        offcanvas.hide();

        switch(item.type) {
            case '' :
                router.push({ path: `` });
                break;
            case '' :
                router.push({ path: `` });
                break;
            case '' :
                router.push({ path: `` });
                break;
            case 'default':
                break;
        }
    }

    /**
     * Helpers
     */
    const playNotificationSound = () => {
        notificationAudio.currentTime = 0;
        notificationAudio.play().catch(err => {
            console.warn('Notification-audio failed:', err);
        });
    }

    const handleApiError = (error) => {
        const response = error?.response;       
        const message = response?.data?.message || "Something went wrong.";
        showToast?.(message, TOAST_ERROR);
    };
</script>