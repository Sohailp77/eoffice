{{--same as login page but with different title and form action--}}

<x-layout>

    <div class="bg-brand-canvas min-h-screen flex items-center justify-center p-4 md:p-6 font-sans">
        
        <div class="bg-white dark:bg-gray-900 w-full max-w-[1100px] h-auto md:h-[750px] rounded-[40px] shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] p-3 md:p-4 flex overflow-hidden relative">

            <div class="hidden md:flex w-5/12 bg-brand-dark rounded-[32px] flex-col relative overflow-hidden text-white">
                
                <div class="absolute inset-0 noise-bg mix-blend-overlay opacity-30"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-brand-primary/80 to-brand-dark opacity-90"></div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-brand-light opacity-20 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>

                <div class="relative z-10 flex flex-col h-full p-10 lg:p-12 justify-between">
                    <div class="mt-8 relative">
                        <div class="w-24 h-1.5 bg-brand-light rounded-full absolute top-[11.5rem] left-0 -z-10 transform -rotate-2 opacity-80"></div>
                        
                        <h1 class="text-4xl lg:text-[42px] font-bold leading-[1.15] mb-6 tracking-tight">
                            Reset Your Password<br>
                            Secure Your Account<br>
                            Regain Access with Ease.
                        </h1>
                        <p class="text-brand-accent text-sm leading-relaxed max-w-xs font-medium opacity-90">
                            Streamline your administrative workflows and boost productivity with our centralized dashboard.
                        </p>
                    </div>

                    <!-- <div class="flex-1 flex items-end justify-center -mx-12 -mb-12">
                        <img 
                            src="{{ asset('images/838.jpg') }}" 
                            alt="Office Team 3D" 
                            class="object-cover h-[400px] w-full mix-blend-normal"
                            style="mask-image: linear-gradient(to top, transparent 0%, black 100%); -webkit-mask-image: linear-gradient(to top, transparent 5%, black 80%);"
                        >
                    </div> -->
                </div>
            </div>

            <div class="w-full md:w-7/12 flex flex-col justify-center items-center py-12 px-6 md:px-16 lg:px-24">

                <div class="w-full max-w-md">
                    <div class="flex items-center justify-center gap-3 mb-10">
                        <div class="w-12 h-12 bg-brand-primary rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-primary/20 transform rotate-3 border border-brand-light/30">
                            <span class="font-bold text-2xl">EO</span>
                        </div>
                        <span class="text-2xl font-bold text-brand-dark dark:text-brand-light tracking-tight">eOffice</span>
                    </div>

                    <div class="text-center mb-10">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-brand-light mb-2">Reset Your Password</h2>
                        <p class="text-gray-400 text-sm">Please enter your new password below</p>
                    </div>

                    <x-ui.error />
                    <x-ui.success />

                    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-500 ml-3 uppercase tracking-wider">Username</label>
                            <input 
                                type="text" 
                                name="username" 
                                placeholder="Enter your username" 
                                required 
                                autofocus
                                class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-light/50 focus:border-brand-primary transition-all shadow-sm"
                            >
                        </div>

                        <div class="space-y-1" x-data="{ show: false }">
                            <label class="text-xs font-semibold text-gray-500 ml-3 uppercase tracking-wider">Password</label>
                            <div class="relative">
                                <input 
                                    :type="show ? 'text' : 'password'" 
                                    name="password" 
                                    placeholder="••••••••" 
                                    required
                                    autocomplete="current-password"
                                    class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-light/50 focus:border-brand-primary transition-all shadow-sm"
                                >
                                
                                <button 
                                    type="button" 
                                    @click="show = !show"
                                    class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-primary focus:outline-none transition-colors"
                                >
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                    <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>



                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="text-xs font-semibold text-gray-500 ml-3 uppercase tracking-wider">Confirm Password</label>
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                placeholder="••••••••" 
                                required
                                autocomplete="new-password"
                                class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-light/50 focus:border-brand-primary transition-all shadow-sm"
                            >
                        </div>

                        <div class="flex justify-end pt-1">
                            {{-- <a href="#" class="text-xs font-semibold text-gray-400 hover:text-brand-primary transition-colors">
                                Forgot password?
                            </a> --}}
                        </div>

                        <button type="submit" class="w-full py-4 bg-brand-dark text-white rounded-2xl font-bold text-sm shadow-lg shadow-brand-dark/30 hover:bg-brand-primary hover:shadow-brand-primary/40 transition-all transform hover:-translate-y-0.5 active:translate-y-0 border border-transparent">
                            Reset Password
                        </button>
                    </form>

                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-100"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span class="px-4 bg-white dark:bg-gray-900 text-gray-400 font-medium">System Access</span>
                        </div>
                    </div>

                    <div class="mt-2 text-center">
                        <p class="text-sm text-gray-400">
                            Remembered your password?
                            <a href="{{ route('login') }}" class="text-brand-primary font-bold hover:text-brand-dark hover:underline transition-all">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
