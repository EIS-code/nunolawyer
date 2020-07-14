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
                        <!--li class="app-sidebar__heading">Clients</li-->
                        <li>
                            <a href="{{ route('clients.index') }}" class="{{ (request()->is('clients*') ? 'mm-active' : '') }}">
                                <i class="metismenu-icon pe-7s-users"></i>
                                {{ __('Add New client') }}
                            </a>
                        </li>
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
                        <!--li class="app-sidebar__heading">{{ ('Editors') }}</li-->
                        <li>
                            <a href="{{ route('editors.index') }}" class="{{ (request()->is('editors*') ? 'mm-active' : '') }}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                {{ __('View all editors') }}
                            </a>
                        </li>
                        <!--li class="app-sidebar__heading">{{ __('Article / Purpose') }}</li-->
                        <li>
                            <a href="forms-controls.html" class="{{ (request()->is('') ? 'mm-active' : '') }}">
                                <i class="metismenu-icon pe-7s-notebook"></i>
                                {{ __('Article / Purpose') }}
                            </a>
                        </li>
                        <!--li class="app-sidebar__heading">{{ __('POA & agreement') }}</li-->
                        <li>
                            <a href="forms-validation.html" class="{{ (request()->is('') ? 'mm-active' : '') }}">
                                <i class="metismenu-icon pe-7s-note"></i>
                                {{ __('POA & agreement') }}
                            </a>
                        </li>
                        <!--li class="app-sidebar__heading">{{ __('Translate model or documents') }}</li-->
                        <li>
                            <a href="charts-chartjs.html" class="{{ (request()->is('') ? 'mm-active' : '') }}">
                                <i class="metismenu-icon pe-7s-file"></i>
                                {{ __('Translate model or documents') }}
                            </a>
                        </li>
                        <!--li class="app-sidebar__heading">{{ __('Our fee / policy document') }}</li-->
                        <li>
                            <a href="charts-chartjs.html" class="{{ (request()->is('') ? 'mm-active' : '') }}">
                                <i class="metismenu-icon pe-7s-file"></i>
                                {{ __('Our fee / policy document') }}
                            </a>
                        </li>
                        <!--li class="app-sidebar__heading">{{ __('Account') }}</li-->
                        <li>
                            <a href="charts-chartjs.html" class="{{ (request()->is('') ? 'mm-active' : '') }}">
                                <i class="metismenu-icon pe-7s-pen"></i>
                                {{ __('Account') }}
                            </a>
                        </li>
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