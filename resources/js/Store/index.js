import { createStore } from 'vuex';
import axios from 'axios';

export default createStore({
    state: () => ({
        notifications: []
    }),
    mutations: {
        ADD_NOTIFICATION(state, notification) {
            state.notifications.push(notification);
        },
        SET_NOTIFICATIONS(state, notifications) {
            state.notifications = notifications;
        }
    },
    actions: {
        async fetchNotifications({ commit }) {
            const response = await axios.get('/notifications');
            commit('SET_NOTIFICATIONS', response.data);
        }
    }
});