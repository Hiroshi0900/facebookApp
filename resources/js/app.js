import Vue from 'vue';
import router from './router';
import App from './components/App';
import lg from './components/LoginButton';
import store from './store';

require('./bootstrap');

window.Vue = require('vue');

// Vue.component('LoginButton', require(LoginButton));
Vue.component('lg', require('./components/LoginButton.vue').default);
// Vue.component('rp', require('./components/ResetPassword.vue').default);

const app = new Vue({
	// エントリーポイント
	el: '#app',
	// コンポーネント
	components: {
		App,
		// LoginButton,
	},
	// Vueルーター
	router,

	// Vuex
	store,
});