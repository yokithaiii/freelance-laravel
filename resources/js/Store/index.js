import { createStore } from 'vuex';
import axios from 'axios';

export default createStore({
    state: () => ({
        notifications: [],
        accountType: localStorage.getItem('accountType') || 'customer'
    }),
    mutations: {
        ADD_NOTIFICATION(state, notification) {
            state.notifications.push(notification);
        },
        SET_NOTIFICATIONS(state, notifications) {
            state.notifications = notifications;
        },
        SET_ACCOUNT_TYPE(state, type) {
            state.accountType = type;
            localStorage.setItem('accountType', type);
        }
    },
    actions: {
        async fetchNotifications({ commit }) {
            const response = await axios.get('/notifications');
            commit('SET_NOTIFICATIONS', response.data);
        },
        async switchAccountType({ commit }, type) {
            commit('SET_ACCOUNT_TYPE', type);
        }
    },
    getters: {
        accountType: state => state.accountType
    }
});