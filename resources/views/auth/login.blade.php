<x-guest-layout>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #09090B;
            overflow: hidden;
            height: 100%;
        }

        /* GRID BACKGROUND */
        .grid-bg {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255, 255, 255, .03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .03) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(circle at center, black, transparent 90%);
            -webkit-mask-image: radial-gradient(circle at center, black, transparent 90%);
        }

        /* AURORA EFFECTS */
        .aurora {
            position: absolute;
            width: 700px;
            height: 700px;
            border-radius: 999px;
            filter: blur(150px);
            opacity: .18;
            animation: float 18s ease-in-out infinite;
            pointer-events: none;
        }

        .a1 { top: -250px; left: -200px; background: #6D5DFB; }
        .a2 { bottom: -300px; right: -200px; background: #8B5CF6; animation-delay: 3s; }
        .a3 { top: 30%; left: 35%; background: #ffffff; opacity: .05; animation-delay: 6s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(80px, -50px) scale(1.2); }
        }

        /* GLASSMORPHISM */
        .glass {
            background: rgba(255, 255, 255, .04);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, .08);
            box-shadow: 0 0 40px rgba(0, 0, 0, .35), inset 0 1px rgba(255, 255, 255, .05);
        }

        /* CARD HOVER PARALLAX EFFECTS */
        .login-card {
            transform-style: preserve-3d;
            transition: transform .15s linear, box-shadow .3s ease;
        }

        .login-card:hover {
            box-shadow: 0 40px 120px rgba(0, 0, 0, .45), 0 0 120px rgba(139, 92, 246, .18);
        }

        /* TEXT GRADIENT */
        .hero-gradient {
            background: linear-gradient(90deg, #ffffff, #D8CFFF, #8B5CF6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* FADE ANIMATION */
        .fade { animation: fade .8s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }

        @keyframes fade {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* BLOBS */
        .blob {
            position: absolute;
            width: 14px;
            height: 14px;
            background: white;
            border-radius: 999px;
            opacity: .15;
            animation: bounce 5s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* NOISE BACKGROUND */
        .noise {
            pointer-events: none;
            position: absolute;
            inset: 0;
            opacity: .015;
            background-image: url("https://grainy-gradients.vercel.app/noise.svg");
            mix-blend-mode: soft-light;
        }

        /* CURSOR LIGHT */
        .cursor-light {
            position: fixed;
            top: 0;
            left: 0;
            width: 600px;
            height: 600px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.08), transparent 70%);
            pointer-events: none;
            filter: blur(40px);
            z-index: 10;
            will-change: transform;
        }
    </style>

    <div class="relative h-screen w-screen overflow-hidden select-none">
        <div class="grid-bg"></div>
        <div class="noise"></div>
        <div id="cursorLight" class="cursor-light hidden md:block"></div>
        
        <div class="aurora a1"></div>
        <div class="aurora a2"></div>
        <div class="aurora a3"></div>

        <div class="blob top-40 left-80"></div>
        <div class="blob bottom-48 left-60"></div>
        <div class="blob right-80 top-52"></div>

        <div class="relative z-20 h-full w-full">
            <div class="grid lg:grid-cols-2 h-full">
                
                <div class="hidden lg:flex items-center px-16 xl:px-24">
                    <div class="fade delay-1">
                        <img src="{{ asset('images/VYBE.png') }}" class="h-10 mb-10" alt="Vybe">

                        <h1 class="text-5xl xl:text-6xl font-black text-white leading-[1.1] tracking-tight mb-8">
                            Find your people.<br>
                            Share your <span class="hero-gradient">vibe.</span><br>
                            Build community.
                        </h1>

                        <p class="text-zinc-400 text-lg leading-relaxed max-w-lg mb-12">
                            Vybe is where conversations become communities. Share moments, discover people, and stay connected with what matters.
                        </p>

                        <div class="flex gap-5">
                            <div class="glass rounded-2xl px-6 py-4">
                                <div class="text-3xl font-extrabold text-white">50K+</div>
                                <div class="text-zinc-400 text-sm mt-1">Active Users</div>
                            </div>
                            <div class="glass rounded-2xl px-6 py-4">
                                <div class="text-3xl font-extrabold text-white">120K+</div>
                                <div class="text-zinc-400 text-sm mt-1">Posts Shared</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center px-6 sm:px-12 lg:px-16 xl:px-24">
                    <div class="glass login-card rounded-[32px] w-full max-w-md p-8 sm:p-10 fade delay-2 relative overflow-hidden">
                        
                        <div class="absolute -top-24 -right-24 w-64 h-64 bg-violet-500/10 blur-[100px] rounded-full pointer-events-none"></div>

                        <div class="relative z-10">
                            <h2 class="text-3xl font-bold text-white tracking-tight">Welcome back</h2>
                            <p class="text-zinc-400 mt-2 text-sm sm:text-base">Login to continue your journey on Vybe.</p>

                            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                                @csrf

                                <div class="relative">
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder=" "
                                        class="peer w-full rounded-2xl bg-zinc-900/50 border border-zinc-800 px-5 pt-6 pb-2 text-white outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10 text-sm sm:text-base">
                                    <label for="email" 
                                        class="absolute left-5 top-4 text-zinc-500 transition-all text-sm sm:text-base pointer-events-none
                                        peer-placeholder-shown:text-zinc-500 peer-placeholder-shown:top-4
                                        peer-focus:text-xs peer-focus:top-2 peer-focus:text-violet-400
                                        peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:top-2">
                                        Email Address
                                    </label>
                                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-400"/>
                                </div>

                                <div class="relative">
                                    <input type="password" id="password" name="password" required placeholder=" "
                                        class="peer w-full rounded-2xl bg-zinc-900/50 border border-zinc-800 px-5 pt-6 pb-2 text-white outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10 text-sm sm:text-base">
                                    <label for="password" 
                                        class="absolute left-5 top-4 text-zinc-500 transition-all text-sm sm:text-base pointer-events-none
                                        peer-placeholder-shown:text-zinc-500 peer-placeholder-shown:top-4
                                        peer-focus:text-xs peer-focus:top-2 peer-focus:text-violet-400
                                        peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:top-2">
                                        Password
                                    </label>
                                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-400"/>
                                </div>

                                <div class="flex items-center justify-between text-xs sm:text-sm pt-1">
                                    <label class="flex items-center gap-2 text-zinc-400 cursor-pointer select-none">
                                        <input type="checkbox" name="remember" class="rounded border-zinc-700 bg-zinc-900 text-violet-600 focus:ring-violet-500/20">
                                        Remember me
                                    </label>
                                    @if(Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-violet-400 hover:text-violet-300 transition font-medium">
                                            Forgot Password?
                                        </a>
                                    @endif
                                </div>

                                <button type="submit" class="w-full rounded-2xl py-3.5 font-semibold text-black bg-white hover:bg-zinc-100 transition active:scale-[0.98] mt-2 text-sm sm:text-base shadow-lg shadow-white/5">
                                    Continue
                                </button>

                                <div class="flex items-center gap-4 py-1">
                                    <div class="flex-1 h-px bg-zinc-800/80"></div>
                                    <span class="text-zinc-600 text-xs font-bold tracking-widest">OR</span>
                                    <div class="flex-1 h-px bg-zinc-800/80"></div>
                                </div>

                                <button type="button" class="w-full rounded-2xl border border-zinc-800 bg-zinc-900/80 hover:bg-zinc-800/80 transition py-3.5 text-white font-medium text-sm sm:text-base flex items-center justify-center gap-3 active:scale-[0.98]">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.53 12-4.53z" fill="#EA4335"/>
                                    </svg>
                                    Continue with Google
                                </button>
                            </form>

                            <p class="text-center mt-6 text-xs sm:text-sm text-zinc-500">
                                Don't have an account?
                                <a href="{{ route('register') }}" class="text-violet-400 hover:text-violet-300 transition font-medium">
                                    Create account
                                </a>
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        const cursorLight = document.getElementById('cursorLight');
        const card = document.querySelector('.login-card');

        window.addEventListener('mousemove', (e) => {
            if(cursorLight) {
                const x = e.clientX - 300;
                const y = e.clientY - 300;
                cursorLight.style.transform = `translate3d(${x}px, ${y}px, 0)`;
            }

            if(card && window.innerWidth > 1024) {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left - (rect.width / 2);
                const y = e.clientY - rect.top - (rect.height / 2);
                
                const rotateX = -(y / rect.height) * 6;
                const rotateY = (x / rect.width) * 6;

                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            }
        });

        if(card) {
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
            });
        }
    </script>
</x-guest-layout>