<x-layout>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden px-4">

        <!-- Floating background blobs -->
        <div
            class="absolute top-20 left-20 w-72 h-72 bg-india-saffron/10 rounded-full mix-blend-screen blur-3xl opacity-50 animate-blob">
        </div>
        <div
            class="absolute top-20 right-20 w-72 h-72 bg-india-blue/20 rounded-full mix-blend-screen blur-3xl opacity-50 animate-blob animation-delay-2000">
        </div>
        <div
            class="absolute -bottom-8 left-1/2 w-72 h-72 bg-india-green/10 rounded-full mix-blend-screen blur-3xl opacity-50 animate-blob animation-delay-4000">
        </div>

        <!-- Main Card -->
        <div class="glass p-1 rounded-3xl w-full max-w-5xl relative z-10">
            <div
                class="bg-slate-900/60 backdrop-blur-xl rounded-[20px] shadow-2xl border border-white/5 grid grid-cols-1 md:grid-cols-2 overflow-hidden">

                <!-- LEFT : Login Form -->
                <div class="p-10 flex flex-col justify-center">
                    <div class="mb-10">
                        <div
                            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-india-saffron via-white to-india-green flex items-center justify-center font-bold text-2xl shadow-lg mb-4 border border-white/10 p-[1px]">
                            <div
                                class="w-full h-full rounded-2xl bg-slate-900 flex items-center justify-center text-white">
                                E
                            </div>
                        </div>

                        <h2 class="text-3xl font-bold text-white tracking-tight">
                            Welcome Back
                        </h2>
                        <p class="text-slate-400 mt-2">
                            Sign into eOffice Workspace
                        </p>
                    </div>

                    <form action="{{ route('login') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Username -->
                        <div class="space-y-2">
                            <label for="username" class="text-sm font-medium text-slate-300 ml-1">
                                Username
                            </label>
                            <input id="username" name="username" type="text" required autocomplete="username"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-india-saffron/40 focus:border-india-saffron/40 transition-all shadow-inner"
                                placeholder="Enter your username">
                            @error('username')
                                <p class="text-sm text-red-400 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="space-y-2">
                            <label for="password" class="text-sm font-medium text-slate-300 ml-1">
                                Password
                            </label>
                            <input id="password" name="password" type="password" required
                                autocomplete="current-password"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-india-saffron/40 focus:border-india-saffron/40 transition-all shadow-inner"
                                placeholder="••••••••">
                        </div>

                        <!-- Remember + Forgot -->
                        <div class="flex items-center justify-between">


                            <a href="#"
                                class="text-sm font-medium text-india-saffron hover:text-orange-300 transition-colors">
                                Forgot password ? Please Reset It Through CIS
                            </a>
                        </div>

                        <!-- Submit -->
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-india-saffron to-india-green text-white font-bold py-3.5 rounded-xl shadow-lg shadow-india-saffron/10 hover:shadow-india-saffron/20 transform hover:-translate-y-0.5 transition-all duration-200 border border-white/5">
                            Sign In
                        </button>
                    </form>
                </div>

                <!-- RIGHT : Image / Visual -->
                <div
                    class="hidden md:flex items-center justify-center relative bg-gradient-to-br from-india-saffron/20 via-slate-900 to-india-green/20 p-10">

                    <!-- Replace image path as needed -->
                    <img src="/images/login-illustration.svg" alt="eOffice Workspace" class="max-w-sm opacity-90">

                    <!-- Optional overlay text -->
                    <div class="absolute bottom-10 left-10 right-10 text-white">
                        <h3 class="text-2xl font-bold leading-tight mb-2">
                            One workspace.<br>All your workflows.
                        </h3>
                        <p class="text-slate-300 text-sm">
                            Documents, approvals, and internal operations — unified.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layout>