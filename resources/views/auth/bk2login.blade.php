@extends('layouts.app')

@section('content')
<div class="text-center">
    <div class="row justify-content-center border-solid border-2 border-gray-400 rounded-lg bg-white" style='width:400px;margin:auto;'>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mt-6">
                            <div class="form-group row">
                                {{-- <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label> --}}
                                <div class="col-md-6">
						    		<input id="email" type="email" placeholder="E-mail Address" 
						    		class="form-control w-10/12 pl-4 h-8 bg-gray-200 rounded-lg mt-2
						    		focus:outline-none focus:shadow-outline text-sm @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
    
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
    
                            <div class="form-group row">
                                {{-- <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label> --}}
    
                                <div class="col-md-6">
									<input placeholder='Password' id="password" type="password" class="form-control w-10/12 pl-4 h-8 bg-gray-200 rounded-lg 
						    		focus:outline-none focus:shadow-outline text-sm mt-2
									@error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
    
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
							</div>
							<div class="form-group row">
                                <div class="col-md-6">
									<button value='Login' class="form-control w-10/12 pl-4 h-8 bg-blue-200 rounded-lg 
						    		focus:outline-none focus:shadow-outline text-sm mt-2">
								    Login</button>
                                </div>
						    </div>
					    </div>

                        <div class="form-group row mt-2">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0 mt-2">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
	</div>
</div>
@endsection
<script>

	// alert(11);
</script>