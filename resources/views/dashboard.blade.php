@extends('layout')

@section('sub_content')
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
                        <div class="widget-numbers text-white"><span>{{ __('1896') }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-arielle-smile">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">{{ __('Profits') }}</div>
                        <div class="widget-subheading">{{ __('Total Clients Profit') }}</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-white"><span>{{ __('$ 568') }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-grow-early">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">{{ __('Followers') }}</div>
                        <div class="widget-subheading">{{ __('People Interested') }}</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-white"><span>{{ __('46%') }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-xl-none d-lg-block col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-premium-dark">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">{{ __('Products Sold') }}</div>
                        <div class="widget-subheading">{{ __('Revenue streams') }}</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-warning"><span>{{ __('$14M') }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection