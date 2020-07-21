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
                    <div class="page-title-subheading">
                        {{ __('This is an analytical dashboard.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xl-4">
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
        </div>
        <div class="col-md-6 col-xl-4">
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
        </div>
        <div class="col-md-6 col-xl-4">
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
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-premium-dark">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">{{ __('POA') }}</div>
                        <div class="widget-subheading">{{ __('Total Active Client Count') }}</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-white"><span>{{ $totalPoaAgreement }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
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
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-vicious-stance">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">{{ __('Our fee / policy document') }}</div>
                        <div class="widget-subheading">{{ __('Total Active Client Count') }}</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-white"><span>{{ $totalOurFeePolicyDocument }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection