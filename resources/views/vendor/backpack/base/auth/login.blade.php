@extends(backpack_view('layouts.plain'))

@section('content')
    <div class="row custom-row justify-content-space-between login-page animated fadeIn">
        <div class="col-md-6 welcome-banner d-flex flex-column justify-content-center align-items-center padding-y-100">
            <!-- <h1 class="text-center brand">CloudCareVital</h1> -->
            <div class="overlay"></div>
            <div class="surface">
            <img src="../../../../../img/i-alerto-logo.png" class="brand-logo" alt="i-alert-logo">
            {{-- <p class="sub-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Expedita nesciunt debitis, consequatur dolores repellendus molestiae inventore dolorem eius officiis, nam vel atque culpa quae itaque delectus unde dignissimos voluptatibus natus?</p>
            <ul class="feature-list">
              <li class="feature-item">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
              <li class="feature-item">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
              <li class="feature-item">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
            </ul> --}}
            </div>
        </div>
        <div class="col-md-6 d-flex justify-content-center flex-column align-items-center padding-y-100">
            <!-- <h3 class="text-center mb-4">{{ trans('backpack::base.login') }}</h3> -->
            <img src="../../../../../img/i-alerto-logo.png" style="margin-bottom: -68px;" class="brand-logo" alt="i-alert-logo">
            <div class="card">
                <div class="card-body">
                    <form class="col-md-12 p-t-10" role="form" method="POST" action="{{ route('backpack.auth.login') }}">
                        {!! csrf_field() !!}

                        <div class="form-group">
                            <label class="control-label" for="{{ $username }}">{{ config('backpack.base.authentication_column_name') }}</label>

                            <div>
                                <input type="text" class="form-control{{ $errors->has($username) ? ' is-invalid' : '' }}" name="{{ $username }}" value="{{ old($username) }}" id="{{ $username }}">

                                @if ($errors->has($username))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first($username) }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="password">{{ trans('backpack::base.password') }}</label>

                            <div>
                                <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="password">

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> {{ trans('backpack::base.remember_me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div>
                                <button type="submit" class="btn btn-danger btn-block">
                                    {{ trans('backpack::base.login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if (backpack_users_have_email())
                <div class="text-center"><a href="{{ route('backpack.auth.password.reset') }}">{{ trans('backpack::base.forgot_your_password') }}</a></div>
            @endif
            @if (config('backpack.base.registration_open'))
                <div class="text-center"><a href="{{ route('backpack.auth.register') }}">{{ trans('backpack::base.register') }}</a></div>
            @endif
            @if (config('backpack.base.show_powered_by') || config('backpack.base.developer_link'))
                <div class="text-muted ml-auto mr-auto footer-text">
                @if (config('backpack.base.developer_link') && config('backpack.base.developer_name'))
                {{ trans('backpack::base.handcrafted_by') }} <a target="_blank" href="{{ config('backpack.base.developer_link') }}">{{ config('backpack.base.developer_name') }}</a>.
                @endif
                @if (config('backpack.base.show_powered_by'))
                {{ trans('backpack::base.powered_by') }} <a target="_blank" href="http://backpackforlaravel.com?ref=panel_footer_link">Backpack for Laravel</a>.
                @endif
                </div>
            @endif
        </div>
    </div>
@endsection
