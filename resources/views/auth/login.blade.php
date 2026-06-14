<x-guest-layout>
    @php
        $storeName = settings('store_name', 'Simple Store');
        $storeSlogan = settings('store_slogan', __('auth.login.brand_slogan'));
        $logoPath = settings('logo');
    @endphp

    <div class="auth-shell">
        <aside class="auth-story" aria-label="{{ __('auth.login.brand_panel_label') }}">
            <div class="auth-story__glow auth-story__glow--one"></div>
            <div class="auth-story__glow auth-story__glow--two"></div>

            <a href="{{ route('home') }}" class="auth-brand" aria-label="{{ __('auth.login.back_to_store', ['store' => $storeName]) }}">
                @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                    <span class="auth-brand__logo auth-brand__logo--image">
                        <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $storeName }}">
                    </span>
                @else
                    <span class="auth-brand__logo" aria-hidden="true">
                        <i class="fas fa-basket-shopping"></i>
                    </span>
                @endif
                <span>
                    <strong>{{ $storeName }}</strong>
                    <small>{{ $storeSlogan }}</small>
                </span>
            </a>

            <div class="auth-story__content">
                <span class="auth-eyebrow">
                    <i class="fas fa-sparkles" aria-hidden="true"></i>
                    {{ __('auth.login.member_space') }}
                </span>
                <h1>{{ __('auth.login.welcome_title') }}</h1>
                <p>{{ __('auth.login.welcome_description') }}</p>

                <ul class="auth-benefits">
                    <li>
                        <span><i class="fas fa-box-open" aria-hidden="true"></i></span>
                        <div>
                            <strong>{{ __('auth.login.benefits.orders_title') }}</strong>
                            <small>{{ __('auth.login.benefits.orders_description') }}</small>
                        </div>
                    </li>
                    <li>
                        <span><i class="fas fa-bolt" aria-hidden="true"></i></span>
                        <div>
                            <strong>{{ __('auth.login.benefits.checkout_title') }}</strong>
                            <small>{{ __('auth.login.benefits.checkout_description') }}</small>
                        </div>
                    </li>
                    <li>
                        <span><i class="fas fa-heart" aria-hidden="true"></i></span>
                        <div>
                            <strong>{{ __('auth.login.benefits.experience_title') }}</strong>
                            <small>{{ __('auth.login.benefits.experience_description') }}</small>
                        </div>
                    </li>
                </ul>
            </div>

            <a href="{{ route('home') }}" class="auth-store-link">
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                {{ __('auth.login.continue_shopping') }}
            </a>
        </aside>

        <main class="auth-form-panel">
            <div class="auth-mobile-brand">
                <a href="{{ route('home') }}" aria-label="{{ __('auth.login.back_to_store', ['store' => $storeName]) }}">
                    @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                        <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $storeName }}">
                    @else
                        <span><i class="fas fa-basket-shopping" aria-hidden="true"></i></span>
                        <strong>{{ $storeName }}</strong>
                    @endif
                </a>
            </div>

            <div class="auth-form-wrap">
                <div class="auth-heading">
                    <span class="auth-heading__icon" aria-hidden="true"><i class="fas fa-user"></i></span>
                    <h2>{{ __('auth.login.title') }}</h2>
                    <p>{{ __('auth.login.subtitle') }}</p>
                </div>

                <x-auth-session-status class="auth-status" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <div class="auth-field">
                        <label for="email">{{ __('auth.login.email') }}</label>
                        <div class="auth-input-wrap @error('email') auth-input-wrap--error @enderror">
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                            <input id="email"
                                   name="email"
                                   type="email"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus
                                   autocomplete="email"
                                   inputmode="email"
                                   placeholder="{{ __('auth.login.email_placeholder') }}">
                        </div>
                        @error('email')
                            <p class="auth-error"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="auth-field">
                        <div class="auth-label-row">
                            <label for="password">{{ __('auth.login.password') }}</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">{{ __('auth.login.forgot_password') }}</a>
                            @endif
                        </div>
                        <div class="auth-input-wrap @error('password') auth-input-wrap--error @enderror">
                            <i class="fas fa-lock" aria-hidden="true"></i>
                            <input id="password"
                                   name="password"
                                   type="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="{{ __('auth.login.password_placeholder') }}">
                            <button type="button"
                                    class="auth-password-toggle"
                                    data-password-toggle
                                    aria-label="{{ __('auth.login.show_password') }}"
                                    aria-pressed="false">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="auth-error"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="auth-remember" for="remember_me">
                        <input id="remember_me" name="remember" type="checkbox">
                        <span aria-hidden="true"><i class="fas fa-check"></i></span>
                        {{ __('auth.login.remember_me') }}
                    </label>

                    <button type="submit" class="auth-submit">
                        <span>{{ __('auth.login.submit') }}</span>
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>

                <div class="auth-divider"><span>{{ __('auth.login.new_customer') }}</span></div>

                <a href="{{ route('register') }}" class="auth-register">
                    <i class="fas fa-user-plus" aria-hidden="true"></i>
                    {{ __('auth.login.create_account') }}
                </a>

                <p class="auth-security">
                    <i class="fas fa-shield-halved" aria-hidden="true"></i>
                    <span><strong>{{ __('auth.login.secure_title') }}</strong> {{ __('auth.login.secure_description') }}</span>
                </p>
            </div>
        </main>
    </div>

    <script>
        document.querySelector('[data-password-toggle]')?.addEventListener('click', function () {
            const password = document.getElementById('password');
            const isVisible = password.type === 'text';

            password.type = isVisible ? 'password' : 'text';
            this.setAttribute('aria-pressed', String(!isVisible));
            this.setAttribute('aria-label', isVisible
                ? @js(__('auth.login.show_password'))
                : @js(__('auth.login.hide_password')));
            this.querySelector('i').className = isVisible ? 'fas fa-eye' : 'fas fa-eye-slash';
        });
    </script>
</x-guest-layout>
