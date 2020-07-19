<template>
    <form @submit.prevent="exec">
        <div class="mt-6">
	        <div class="form-group row">
	        	<div class="form-group row">
                    <div class="col-md-6">
	        			<input id="email" type="email" placeholder="E-mail Address" v-model="userId"
	        			class="form-control w-10/12 pl-4 h-8 bg-gray-200 rounded-lg mt-2
	        			focus:outline-none focus:shadow-outline text-sm" name="email"  required autocomplete="email" autofocus>
        
                    </div>
                </div>
            
                <div class="form-group row">
                    <div class="col-md-6">
	        			<input placeholder='Password' id="password" type="password" v-model="password"
						class="form-control w-10/12 pl-4 h-8 bg-gray-200 rounded-lg 
	        			focus:outline-none focus:shadow-outline text-sm mt-2" name="password" required autocomplete="current-password">
                    </div>
	        	</div>
				<transition name='fade'>
                    <div class="col-md-6" v-if="disableFlag">
	        	    	<div class="pl-4 w-10/12 h-12 text-left m-auto text-red-500">
							<span>
							    E-MailかPasswordが誤っています。入力内容を確認してください。
							</span>
						</div>
                    </div>
				</transition>
                <div class="col-md-6">
	        		<button value='Login' class="form-control w-10/12 pl-4 h-8 rounded-lg bg-gray-500
	        		focus:outline-none focus:shadow-outline text-sm mt-2"
					v-bind:class='{clickButton:buttonLayout}'>
	        	    Login</button>
                </div>
	        </div>
        </div>
	</form>
</template>

<script>
    export default {
        data() {
            return {
				'userId'     :'',
				'password'   :'',
				'disableFlag':false,
            }
        },
        mounted() {

		},
		methods: {
            exec: function () {
                axios.post('/login',{
			        	email   :this.userId,
			        	password:this.password,
			        })
		            .then(res => {
			    		location.href = res.request.responseURL;
		            })
		            .catch(error =>{
						console.log(error);
						this.disableFlag = true;
						this.password    = '';
		            });
            }
		},
        computed: {
			// ログインボタンの状態を監視する
            buttonLayout: function () {
				if(this.userId && this.password){
					return true;
				}else{
					return false;
				}
            }
        }
   }
</script>

<style scoped>
.fade-enter-active , .fade-leave-active{
	transition: opacity .5s;
}
.fade-enter , .fade-leave-to{
	opacity: 0;
}
.clickButton {
    --bg-opacity: 1;
    background-color: #bee3f8;
    background-color: rgba(190, 227, 248, var(--bg-opacity));
}

</style>