const state = {
	user: null,
	userStatus: null,
};
const getters = {
	// ユーザーデータ
	user: state => {
		return state.user;
	},
	status:state => {
		return {
			user : state.userStatus,
			posts: state.postsStatus,
		};
	},
	friendship:state => {
		return state.user.data.attributes.friendship;
	},
	// TODO rootstateとは
	friendRequestButton: (state, getters, rootState) => {
		// 自分自身の画面の場合はボタンを非表示
		if (rootState.User.user.data.user_id == state.user.data.user_id) {
			return '';
		}else
		if (getters.friendship === null) {
			// まだフレンドリクエストがないとき
			return 'Add Friend';
		} else if (getters.friendship.data.attributes.confirmed_at === null
		&& getters.friendship.data.attributes.friend_id !== rootState.User.user.data.user_id) {
			return  'Pending Friend Request';
		} else if (getters.friendship.data.attributes.confirmed_at !== null) {
			return  '';
		}
		// 承認済みのもの
		return 'Accept'
	},
};
const actions = {
	// ユーザーデータ取得
	fetchUser({ commit, dispatch }, userId) {
		// ステータス呼び出し
		commit('setUserStatus', 'loading');
		axios.get('/api/users/' + userId)
			.then(res => {
				commit('setUser', res.data);
				commit('setUserStatus', 'success');
		    })
		    .catch(error => {
				commit('setUserStatus', 'error');
		    });
	},
	
	// フレンドリクエスト送信
	sendFriendRequest({ commit, getters }, friendId) {
		// 追加ボタンでないときは処理しない
		if (getters.friendRequestButton !== 'Add Friend') return;
		axios.post('/api/friend-request', { 'friend_id': friendId })
			.then(res => {
				commit('setFriendship', res.data);
			})
			.catch(error => {
			})
	},
	// フレンドリクエスト承認
	acceptFriendRequest({ commit, status }, userId) {
		axios.post('/api/friend-request-response',
			{
				'user_id': userId,
				'status' : 1,
			})
			.then(res => {
				commit('setFriendship', res.data);
			})
			.catch(error => {
			})
	},
	// フレンドリクエスト拒否
	ignoreFriendRequest({ commit, status }, userId) {
		axios.delete('/api/friend-request-response/delete',
			{ data: { 'user_id': userId } })
			.then(res => {
				commit('setFriendship', null);
			})
			.catch(error => {
			})
	},
};
const mutations = {
	// ユーザーデータのセット
	setUser(state, user) {
		state.user = user;
	},
    // // 投稿データの取得
	// setPosts(state, posts) {
	// 	state.posts = posts;
	// },
	// フレンドリクエストの内容変更
	setFriendship(state, friendship) {
		state.user.data.attributes.friendship = friendship;
	},

	// 取得できないエラーのセット
	setUserStatus(state, status) {
		state.userStatus = status;
	},
	// setPostsStatus(state, status) {
	// 	state.postsStatus = status;
	// },
	// // ボタン名変更のセット
	// setFriendReqButton(state, text) {
	// 	state.friendRequestButton = text;
	// } // ボタン名の変更はフロントでやってる
};

export default {
	state,getters,actions,mutations,
}