@extends('layout')

@section('sub_content')
    <div>
        @if (session('success'))
            <div class="content alert alert-success" role="alert">
                {{ session('success') }}
            </div>
            <br />
        @endif

        @if (session('error'))
            <div class="content alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
            <br />
        @endif
    </div>
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-car icon-gradient bg-mean-fruit">
                    </i>
                </div>
                <div>
                    {{ __('Analytics Dashboard') }}
                    <!--div class="page-title-subheading">
                        {{ __('This is an analytical dashboard.') }}
                    </div-->
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin: o auto;">
        <div class="col-md-1"></div>
        <div class="col-md-2 text-center">
            <a href="@can('clients_create') {{ route('clients.create') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div style="flex-direction: column;" class="card mb-3 widget-content bg-white-bloom">
                    <div class="widget-content text-black">
                        <div class="row">
                            <div class="col-md-12">
                                <i class="metismenu-icon pe-7s-users" style="font-size: 50px;"></i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{ __('Add New client') }}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2 text-center">
            <a href="@can('clients_access') {{ route('clients.view') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div style="flex-direction: column;" class="card mb-3 widget-content bg-white-bloom">
                    <div class="widget-content text-black">
                        <div class="row">
                            <div class="col-md-12">
                                <i class="metismenu-icon pe-7s-search" style="font-size: 50px;"></i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{ __('Search all clients') }}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2 text-center">
            <a href="@can('clients_access') {{ route('clients.index') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div style="flex-direction: column;" class="card mb-3 widget-content bg-white-bloom">
                    <div class="widget-content text-black">
                        <div class="row">
                            <div class="col-md-12">
                                <i class="fa fa-users" style="font-size: 50px;"></i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{ __('View all clients') }}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2 text-center">
            <a href="@can('editors_create') {{ route('editors.create') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div style="flex-direction: column;" class="card mb-3 widget-content bg-white-bloom">
                    <div class="widget-content text-black">
                        <div class="row">
                            <div class="col-md-12">
                                <i class="metismenu-icon pe-7s-users" style="font-size: 50px;"></i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{ __('Add New editor') }}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2 text-center">
            <a href="@can('editors_access') {{ route('editors.index') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div style="flex-direction: column;" class="card mb-3 widget-content bg-white-bloom">
                    <div class="widget-content text-black">
                        <div class="row">
                            <div class="col-md-12">
                                <i class="fa fa-users" style="font-size: 50px;"></i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{ __('View all editors') }}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-2 text-center">
            <a href="@can('article_purpose_access') {{ route('article_purpose.index') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div style="flex-direction: column;" class="card mb-3 widget-content bg-white-bloom">
                    <div class="widget-content text-black">
                        <div class="row">
                            <div class="col-md-12">
                                <i class="metismenu-icon pe-7s-notebook" style="font-size: 50px;"></i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{ __('Article / Purpose') }}<br />&nbsp;
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2 text-center">
            <a href="@can('poa_access') {{ route('poa.index') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div style="flex-direction: column;" class="card mb-3 widget-content bg-white-bloom">
                    <div class="widget-content text-black">
                        <div class="row">
                            <div class="col-md-12">
                                <i class="metismenu-icon pe-7s-note" style="font-size: 50px;"></i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{ __('POA & agreement') }}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2 text-center">
            <a href="@can('translate_model_document_access') {{ route('translate_model_document.index') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div style="flex-direction: column;" class="card mb-3 widget-content bg-white-bloom">
                    <div class="widget-content text-black">
                        <div class="row">
                            <div class="col-md-12">
                                <i class="metismenu-icon pe-7s-note" style="font-size: 50px;"></i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{ __('Translate model or documents') }}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2 text-center">
            <a href="@can('our_fee_policy_document_access') {{ route('our_fee_policy_document.index') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div style="flex-direction: column;" class="card mb-3 widget-content bg-white-bloom">
                    <div class="widget-content text-black">
                        <div class="row">
                            <div class="col-md-12">
                                <i class="metismenu-icon pe-7s-info" style="font-size: 50px;"></i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{ __('Our fee / policy document') }}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2 text-center">
            <a href="@can('account_access') {{ route('account.index') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div style="flex-direction: column;" class="card mb-3 widget-content bg-white-bloom">
                    <div class="widget-content text-black">
                        <div class="row">
                            <div class="col-md-12">
                                <i class="metismenu-icon pe-7s-pen" style="font-size: 50px;"></i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{ __('Account') }}<br />&nbsp;
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xl-4">
            <a href="@can('clients_access') {{ route('clients.index') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div class="card mb-3 widget-content bg-midnight-bloom">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">{{ __('Total Clients') }}</div>
                            <!--div class="widget-subheading"></div-->
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span>{{ $totalClients }}</span></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="@can('editors_access') {{ route('editors.index') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div class="card mb-3 widget-content bg-arielle-smile">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">{{ __('Total Editors') }}</div>
                            <!--div class="widget-subheading">{{ __('Total Clients Profit') }}</div-->
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span>{{ $totalEditors }}</span></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="@can('translate_model_document_access') {{ route('translate_model_document.index') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div class="card mb-3 widget-content bg-grow-early">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">{{ __('Translate Model Document') }}</div>
                            <div class="widget-subheading">{{ __('Total Active Client Count') }}</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span>{{ $totalTranslateModelDocuments }}</span></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xl-4">
            <a href="@can('poa_access') {{ route('poa.index') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div class="card mb-3 widget-content bg-premium-dark">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">{{ __('POA') }}</div>
                            <!--div class="widget-subheading">{{ __('Total Active Client Count') }}</div-->
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span>{{ $totalPoaAgreement }}</span></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="@can('article_purpose_access') {{ route('article_purpose.index') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div class="card mb-3 widget-content bg-midnight-bloom">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">{{ __('Article / Purpose') }}</div>
                            <div class="widget-subheading">{{ __('Total Active Client Count') }}</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span>{{ $totalPurposeArticle }}</span></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="@can('our_fee_policy_document_access') {{ route('our_fee_policy_document.index') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div class="card mb-3 widget-content bg-vicious-stance">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">{{ __('Our fee / policy document') }}</div>
                            <!--div class="widget-subheading">{{ __('Total Active Client Count') }}</div-->
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span>{{ $totalOurFeePolicyDocument }}</span></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a href="@can('terms_and_conditions_access') {{ route('terms_and_conditions.index') }} @else javascript:void(0) @endcan" style="text-decoration: none;">
                <div class="card mb-3 widget-content bg-arielle-smile">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">{{ __('Terms and Conditions') }}</div>
                            <div class="widget-subheading">{{ __('Total Terms and Conditions') }}</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span>{{ $totalTermsAndCondition }}</span></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection