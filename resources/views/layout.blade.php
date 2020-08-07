@extends('layouts.app')

@section('content')
        <div class="app-sidebar sidebar-shadow">
            <div class="app-header__logo">
                <div class="logo-src"></div>
                <div class="header__pane ml-auto">
                    <div>
                        <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="app-header__mobile-menu">
                <div>
                    <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="app-header__menu">
                <span>
                    <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                        <span class="btn-icon-wrapper">
                            <i class="fa fa-ellipsis-v fa-w-6"></i>
                        </span>
                    </button>
                </span>
            </div>
            <div class="scrollbar-sidebar">
                <div class="app-sidebar__inner">
                    <ul class="vertical-nav-menu">
                        <li class="app-sidebar__heading"></li>
                        <li>
                            <a href="{{ route('dashboard') }}" class="{{ (request()->is('/') ? 'mm-active' : '') }}">
                                <i class="metismenu-icon pe-7s-refresh-2"></i>
                                {{ __('Dashboards') }}
                            </a>
                        </li>
                        @can('clients_access')
                            <!--li class="app-sidebar__heading">Clients</li-->
                            <li>
                                <a href="{{ route('clients.index') }}" class="{{ (request()->is('clients*') && !request()->is('clients/view') ? 'mm-active' : '') }}">
                                    <i class="metismenu-icon pe-7s-users"></i>
                                    {{ __('Clients') }}
                                </a>
                            </li>
                        @endcan
                        @can('clients_access')
                            <!--li class="app-sidebar__heading">Clients</li-->
                            <li>
                                <a href="{{ route('clients.view') }}" class="{{ (request()->is('clients/view') ? 'mm-active' : '') }}">
                                    <i class="metismenu-icon pe-7s-users"></i>
                                    {{ __('View all clients') }}
                                </a>
                            </li>
                        @endcan
                        <!--li>
                            <a href="#">
                                <i class="metismenu-icon pe-7s-albums"></i>
                                {{ __('View all clients') }}
                            </a>
                        </li>
                        <li  >
                            <a href="tables-regular.html">
                                <i class="metismenu-icon pe-7s-search"></i>
                                {{ __('Search clients') }}
                            </a>
                        </li-->
                        @can('editors_access')
                            <!--li class="app-sidebar__heading">{{ ('Editors') }}</li-->
                            <li>
                                <a href="{{ route('editors.index') }}" class="{{ (request()->is('editors*') ? 'mm-active' : '') }}">
                                    <i class="metismenu-icon pe-7s-display2"></i>
                                    {{ __('View all editors') }}
                                </a>
                            </li>
                        @endcan
                        @can('article_purpose_access')
                            <!--li class="app-sidebar__heading">{{ __('Article / Purpose') }}</li-->
                            <li>
                                <a href="{{ route('article_purpose.index') }}" class="{{ (request()->is('article_purpose*') ? 'mm-active' : '') }}">
                                    <i class="metismenu-icon pe-7s-notebook"></i>
                                    {{ __('Article / Purpose') }}
                                </a>
                            </li>
                        @endcan
                        @can('poa_access')
                            <!--li class="app-sidebar__heading">{{ __('POA & agreement') }}</li-->
                            <li>
                                <a href="{{ route('poa.index') }}" class="{{ (request()->is('poa*') ? 'mm-active' : '') }}">
                                    <i class="metismenu-icon pe-7s-note"></i>
                                    {{ __('POA & agreement') }}
                                </a>
                            </li>
                        @endcan
                        @can('translate_model_document_access')
                            <!--li class="app-sidebar__heading">{{ __('Translate model or documents') }}</li-->
                            <li>
                                <a href="{{ route('translate_model_document.index') }}" class="{{ (request()->is('translate_model_document*') ? 'mm-active' : '') }}">
                                    <i class="metismenu-icon pe-7s-file"></i>
                                    {{ __('Translate model or documents') }}
                                </a>
                            </li>
                        @endcan
                        @can('our_fee_policy_document_access')
                            <!--li class="app-sidebar__heading">{{ __('Our fee / policy document') }}</li-->
                            <li>
                                <a href="{{ route('our_fee_policy_document.index') }}" class="{{ (request()->is('our_fee_policy_document*') ? 'mm-active' : '') }}">
                                    <i class="metismenu-icon pe-7s-wallet"></i>
                                    {{ __('Our fee / policy document') }}
                                </a>
                            </li>
                        @endcan
                        @can('terms_and_conditions_access')
                            <!--li class="app-sidebar__heading">{{ __('Our fee / policy document') }}</li-->
                            <li>
                                <a href="{{ route('terms_and_conditions.index') }}" class="{{ (request()->is('terms_and_conditions*') ? 'mm-active' : '') }}">
                                    <i class="metismenu-icon pe-7s-info"></i>
                                    {{ __('Terms and conditions') }}
                                </a>
                            </li>
                        @endcan
                        @can('account_access')
                            <!--li class="app-sidebar__heading">{{ __('Account') }}</li-->
                            <li>
                                <a href="{{ route('account.index') }}" class="{{ (request()->is('account*') ? 'mm-active' : '') }}">
                                    <i class="metismenu-icon pe-7s-pen"></i>
                                    {{ __('Account') }}
                                </a>
                            </li>
                        @endcan
                        @can('follow_up_access')
                            <li>
                                <a href="{{ route('follow_up.index') }}" class="{{ (request()->is('follow_up*') ? 'mm-active' : '') }}">
                                    <i class="metismenu-icon pe-7s-note"></i>
                                    {{ __('Follow Up') }}
                                </a>
                            </li>
                        @endcan
                        @can('activitylog_access')
                            <li>
                                <a href="{{ route('activitylog.index') }}" class="{{ (request()->is('activitylog*') ? 'mm-active' : '') }}">
                                    <i class="metismenu-icon pe-7s-pen"></i>
                                    {{ __('Activity Log') }}
                                </a>
                            </li>
                        @endcan
                        @can('roles_access')
                            <li>
                                <a href="{{ route('roles.index') }}" class="{{ (request()->is('roles*') ? 'mm-active' : '') }}">
                                    <i class="metismenu-icon pe-7s-lock"></i> 
                                    {{__('Roles')}}
                                </a>
                            </li>
                        @endcan

                        @can('permissions_access')  
                            <li>
                                <a href="{{ route('permissions.index') }}" class="{{ (request()->is('permissions*') ? 'mm-active' : '') }}">
                                    <i class="metismenu-icon pe-7s-settings"></i> 
                                    {{__('Permissions')}}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </div>
        </div>
        <div class="app-main__outer">
            <div class="app-main__inner">
                @yield('sub_content')
            </div>
        </div>
    </div>
</div>

@endsection