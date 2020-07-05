const state = {
	posts       : null,
	postsStatus : null,
	postMessage : null,
};
const getters = {
	posts: state => {
		return state.posts;
	},
	newsStatus: state => {
		return {
			postsStatus: state.postsStatus,
		}
	},
	postMessage: state => {
		return state.postMessage;
	}

};
const actions = {
	fetchNewsPosts({ commit, state }) {
		commit('setPostsStatus', 'loading');
		axios.get('/api/posts')
		    .then(res => {
				commit('setPosts',res.data);
				commit('setPostsStatus', 'success');
		    })
		    .catch(error =>{
		    	// 例外処理
				commit('setPostsStatus', 'error');
		    });
	},
	// ユーザー投稿データ取得
	fetchUserPosts({ commit, dispatch }, userId) {
		// ステータス呼び出し
		commit('setPostsStatus', 'loading');
		axios.get('/api/users/' + userId)
			.then(res => {
				commit('setUser', res.data);
				commit('setUserStatus', 'success');
		    })
		    .catch(error => {
				commit('setUserStatus', 'error');
		    });

		axios.get('/api/users/' + userId + '/posts')
			.then(res => {
				commit('setPosts', res.data);
				commit('setPostsStatus', 'success');
			})
			.catch(error => {
				commit('setPostsStatus', 'error');
			});
	},
	postMessage({ commit, state }) {
		commit('setPostsStatus', 'loading');
		axios.post('/api/posts', {
			body:state.postMessage,
		})
		    .then(res => {
				commit('pushPost', res.data);
				commit('setPostsStatus', 'success');
				commit('updateMessage', '');
		    })
		    .catch(error =>{
		    });
	},
	// TODO logout検証
	logout({ commit, state }) {
		axios.post('/logout');
		alert('logoutできた');
		location.href = "/login";
	},
	// TODO
	// イイネボタンのアクション
	likePost({ commit, state }, data) {
		axios.post('/api/posts/' + data.postId + '/like')
		    .then(res => {
		    	commit('pushLikes',{ likes:res.data,postKey:data.postKey });
		    })
		    .catch(error =>{
		    });
	},
	// コメント追加アクション
	commentPost({ commit, state }, data) {
		// console.log(data);
		// return;
		axios.post('/api/posts/' + data.postId + '/comment',{body:data.body})
			.then(res => {
				commit('pushComments',{ comments:res.data,postKey:data.postKey });
		    })
			.catch(error => {
				console.log(error);
		    });
	},
};
const mutations = {
	// 投稿データ取得
	setPosts(state,posts) {
		state.posts = posts;
	},
	// ステータス変更
	setPostsStatus(state,status) {
		state.postsStatus = status;
	},
	// 更新メッセージ
	updateMessage(state,message) {
		state.postMessage = message;
	},
	// 投稿データ追加
	pushPost(state,post) {
		state.posts.data.unshift(post);
	},
	// いいねボタン
	pushLikes(state,data) {
		state.posts.data[data.postKey].data.attributes.likes = data.likes;
	},
	// コメント追加
	pushComments(state,data) {
		state.posts.data[data.postKey].data.attributes.comments = data.comments;
	},
};

export default {
    state,getters,actions,mutations
}