<x-guest-layout>
    @php
        $storeName = settings('store_name', 'Simple Store');
        $storeSlogan = settings('store_slogan', __('auth.login.brand_slogan'));
        $logoPath = settings('logo');
    @endphp

    <div class="auth-shell auth-shell--register">
        <aside class="auth-story" aria-label="{{ __('auth.register.brand_panel_label') }}">
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
                    <i class="fas fa-gift" aria-hidden="true"></i>
                    {{ __('auth.register.member_space') }}
                </span>
                <h1>{{ __('auth.register.welcome_title') }}</h1>
                <p>{{ __('auth.register.welcome_description') }}</p>

                <ul class="auth-benefits">
                    <li>
                        <span><i class="fas fa-route" aria-hidden="true"></i></span>
                        <div>
                            <strong>{{ __('auth.register.benefits.tracking_title') }}</strong>
                            <small>{{ __('auth.register.benefits.tracking_description') }}</small>
                        </div>
                    </li>
                    <li>
                        <span><i class="fas fa-clock-rotate-left" aria-hidden="true"></i></span>
                        <div>
                            <strong>{{ __('auth.register.benefits.history_title') }}</strong>
                            <small>{{ __('auth.register.benefits.history_description') }}</small>
                        </div>
                    </li>
                    <li>
                        <span><i class="fas fa-tags" aria-hidden="true"></i></span>
                        <div>
                            <strong>{{ __('auth.register.benefits.offers_title') }}</strong>
                            <small>{{ __('auth.register.benefits.offers_description') }}</small>
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
                    <span class="auth-heading__icon" aria-hidden="true"><i class="fas fa-user-plus"></i></span>
                    <h2>{{ __('auth.register.title') }}</h2>
                    <p>{{ __('auth.register.subtitle') }}</p>
                </div>

                <x-auth-session-status class="auth-status" :status="session('status')" />

                <form method="POST" action="{{ route('register') }}" class="auth-form">
                    @csrf

                    <div class="auth-field">
                        <label for="name">{{ __('auth.register.name') }}</label>
                        <div class="auth-input-wrap @error('name') auth-input-wrap--error @enderror">
                            <i class="fas fa-user" aria-hidden="true"></i>
                            <input id="name"
                                   name="name"
                                   type="text"
                                   value="{{ old('name') }}"
                                   required
                                   autofocus
                                   autocomplete="name"
                                   placeholder="{{ __('auth.register.name_placeholder') }}">
                        </div>
                        @error('name')
                            <p class="auth-error"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="auth-field">
                        <label for="email">{{ __('auth.register.email') }}</label>
                        <div class="auth-input-wrap @error('email') auth-input-wrap--error @enderror">
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                            <input id="email"
                                   name="email"
                                   type="email"
                                   value="{{ old('email') }}"
                                   required
                                   autocomplete="email"
                                   inputmode="email"
                                   placeholder="{{ __('auth.register.email_placeholder') }}">
                        </div>
                        @error('email')
                            <p class="auth-error"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="auth-fields-grid">
                        <div class="auth-field">
                            <label for="password">{{ __('auth.register.password') }}</label>
                            <div class="auth-input-wrap @error('password') auth-input-wrap--error @enderror">
                                <i class="fas fa-lock" aria-hidden="true"></i>
                                <input id="password"
                                       name="password"
                                       type="password"
                                       required
                                       autocomplete="new-password"
                                       placeholder="{{ __('auth.register.password_placeholder') }}">
                                <button type="button"
                                        class="auth-password-toggle"
                                        data-password-toggle="password"
                                        aria-label="{{ __('auth.login.show_password') }}"
                                        aria-pressed="false">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="auth-error"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="auth-field">
                            <label for="password_confirmation">{{ __('auth.register.password_confirmation') }}</label>
                            <div class="auth-input-wrap">
                                <i class="fas fa-shield-halved" aria-hidden="true"></i>
                                <input id="password_confirmation"
                                       name="password_confirmation"
                                       type="password"
                                       required
                                       autocomplete="new-password"
                                       placeholder="{{ __('auth.register.confirmation_placeholder') }}">
                                <button type="button"
                                        class="auth-password-toggle"
                                        data-password-toggle="password_confirmation"
                                        aria-label="{{ __('auth.login.show_password') }}"
                                        aria-pressed="false">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <p class="auth-password-hint">
                        <i class="fas fa-circle-info" aria-hidden="true"></i>
                        {{ __('auth.register.password_hint') }}
                    </p>

                    <label class="auth-terms" for="terms">
                        <input id="terms" name="terms" type="checkbox" required>
                        <span class="auth-terms__check" aria-hidden="true"><i class="fas fa-check"></i></span>
                        <span>{!! __('auth.register.terms') !!}</span>
                    </label>

                    <button type="submit" class="auth-submit">
                        <span>{{ __('auth.register.submit') }}</span>
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>

                <div class="auth-divider"><span>{{ __('auth.register.already_registered') }}</span></div>

                <a href="{{ route('login') }}" class="auth-register">
                    <i class="fas fa-right-to-bracket" aria-hidden="true"></i>
                    {{ __('auth.register.login') }}
                </a>

                <p class="auth-security">
                    <i class="fas fa-shield-halved" aria-hidden="true"></i>
                    <span><strong>{{ __('auth.register.secure_title') }}</strong> {{ __('auth.register.secure_description') }}</span>
                </p>
            </div>
        </main>
    </div>

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', function () {
                const password = document.getElementById(this.dataset.passwordToggle);
                const isVisible = password.type === 'text';

                password.type = isVisible ? 'password' : 'text';
                this.setAttribute('aria-pressed', String(!isVisible));
                this.setAttribute('aria-label', isVisible
                    ? @js(__('auth.login.show_password'))
                    : @js(__('auth.login.hide_password')));
                this.querySelector('i').className = isVisible ? 'fas fa-eye' : 'fas fa-eye-slash';
            });
        });
    </script>
</x-guest-layout>
