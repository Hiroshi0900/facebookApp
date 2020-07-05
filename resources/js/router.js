import Vue from 'vue';
import VueRouter from 'vue-router';
// import start from './views/Start';
import NewsFeed from './views/NewsFeed';
import UserShow from './views/Users/Show';

Vue.use(VueRouter);

export default new VueRouter({
	mode: 'history',
	
	routes: [
		// 投稿データのAPI
		{
			path: '/',
			name: 'hoge',
			component: NewsFeed,
			// タイトル追加
			meta: {
				title:'News',
			},
		},
		// ユーザーデータのAPI
		{
			path: '/users/:userId',
			name: 'user.show',
			component: UserShow,
			// タイトル追加
			meta: {
				title:'Profile',
			},
		}

	]
});