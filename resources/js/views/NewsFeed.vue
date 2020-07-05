<template>
    <div class="flex flex-col items-center py-4">
		<NewPost />
		<p v-if='newsStatus.newsStatus == "loading"'>Loading...</p>
		<Post v-else v-for='(post,postKey) in posts.data' :key='postKey' :post='post' />
	</div>
</template>

<script>
    // 外部コンポーネント取得
    import NewPost from '../components/NewPost';
	import Post from '../components/Post';
	import { mapGetters} from 'vuex';

    export default {
		name:'NewsFeed',
		
		components:{
			NewPost,
			Post,
		},
		// フェッチして取得するデータの定義をしておく（多分）
		// data: () => {
		// 	return {
		// 		posts  :[],
		// 		loading:true,//マウントが走る前は表示処理をさせない
		// 	}
		// },

		// HTTPリクエストをマウントする
		mounted(){
			// vuexのアクションをディスパッチする（メソッドを呼び出す）
			this.$store.dispatch('fetchNewsPosts');
		},
		computed:{
			...mapGetters({
				posts:'posts',
				newsStatus:'newsStatus',
			}),
		},
    }
</script>

<style scoped>

</style>