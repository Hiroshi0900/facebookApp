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
                <div class="col-md-6">
	        		<button value='Login' class="form-control w-10/12 pl-4 h-8 bg-blue-200 rounded-lg 
	        		focus:outline-none focus:shadow-outline text-sm mt-2">
	        	    Login</button>
                </div>
                <div class="form-group row mb-0 mt-2">
                    <div class="col-md-8 offset-md-4">
                        <a class="btn btn-link" href="./password/reset">
                            パスワードを忘れたら
                        </a>
                    </div>
                </div>
	        </div>
        </div>
	</form>
</template>

<script>
    // export default {
	// 	name:'LoginButton',
		
	// 	props:[
	// 		'LoginButton',
	// 	],
	// }
    export default {
        data() {
            return {
				'userId'     :'',
				'password'   :'',
				'disableFlag':false,
            }
        },
        mounted() {
            let d = new Date();
			this.disp = d.getFullYear().toString() + "/" + (d.getMonth()+1).toString() + "/" + d.getDate().toString();
			console.log(this.$refs);
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
		             });
            }
        },
   }
</script>

<style scoped>

</style>