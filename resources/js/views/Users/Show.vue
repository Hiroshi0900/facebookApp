<template>
    <div class="flex flex-col items-center" v-if="status.user === 'success' && user">
		<!-- 画像スペース -->
		<div class='relative mb-8'>
			<div class="w-100 h-64 overflow-hidden z-10">
				<Uploadablemage  
				    image-width='1200' 
					image-height='500' 
					location='cover'
					classes='object-cover w-full'
					alt='user background image'
    				:user-image='user.data.attributes.cover_image'/>
			</div>
			<!-- ユーザー情報 -->
			<div class="absolute flex items-center bottom-0 left-0 ml-12 -mb-8 z-20">
				<!-- ユーザーアイコン -->
				<div class="w-32">
				<Uploadablemage  
				    image-width='750' 
					image-height='750' 
					location='profile'
					classes='object-hover w-32 h-32 border-4 border-gray-200 rounded-full shadow-lg'
					alt='user profile image'
    				:user-image='user.data.attributes.profile_image'/>
					<!-- <img src='../../../../public/images/sakechu.jpg' alt=""
					class="object-hover w-32 h-32 border-4 border-gray-200 rounded-full shadow-lg"> -->
				</div>
				<!-- ユーザー名 -->
				<p class="text-2xl text-gray-100 ml-4">{{ user.data.attributes.name }}</p>
			</div>
			<!-- フレンド追加ボタン -->
			<div class='absolute flex items-center bottom-0 right-0 mr-12 mb-4 z-20'>
				<button v-if="friendRequestButton && friendRequestButton !== 'Accept'"
				    class='py-1 px-3 bg-gray-400 rounded'
				    @click="$store.dispatch('sendFriendRequest',$route.params.userId)">
					{{ friendRequestButton }}
				</button>
				<button v-if="friendRequestButton && friendRequestButton == 'Accept'"
				    class='mr-4 py-1 px-3 bg-blue-400 rounded'
				    @click="$store.dispatch('acceptFriendRequest',$route.params.userId)">
					Accept
				</button>
				<button v-if="friendRequestButton && friendRequestButton == 'Accept'"
				    class='py-1 px-3 bg-gray-400 rounded'
				    @click="$store.dispatch('ignoreFriendRequest',$route.params.userId)">
					Ignore
				</button>
			</div>
		</div>
		<!-- 自分自身の投稿内容 -->
		<div v-if='status.posts === "loading"'>Loading...</div>
		<!-- データが一軒もない場合は空メッセージを表示する -->
		<div v-else-if="posts.data.length < 1 ">データがありません。</div>
		<Post v-else v-for='(post,postKey) in posts.data' :key='postKey' :post='post' />

	</div>
</template>

<script>
	import Post from '../../components/Post';
	import Uploadablemage from '../../components/Uploadablemage';
	import { mapGetters } from 'vuex';

    export default {
		name:'Show',
		
		data: () => {
			return {
				// posts       : null, //ポストデータ取得
				// postLoading : true, //ポストロードフラグ
			}
		},
		// 外部のコンポーネントを呼ぶ時はcomponentsに記載する
		components:{
			Post,
			Uploadablemage,
		},

		mounted(){
			// Vuexで実装
			this.$store.dispatch('fetchUser',this.$route.params.userId);

			// ユーザーの投稿データの取得をVuexに変更
			this.$store.dispatch('fetchUserPosts',this.$route.params.userId);
		},
		computed:{
			...mapGetters({
				user:'user',
				posts:'posts',
			    status:'status',
				friendRequestButton:'friendRequestButton',
			}),
		}
    }
</script>

<style scoped>

</style>