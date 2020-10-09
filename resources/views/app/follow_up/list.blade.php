@extends('layout')

@section('sub_content')

    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <span class="breadcrumb-item active">{{__('Follow Ups')}}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="card bg-white mt-3">
            <div class="card-body">
                @if (!empty($followUps) && !$followUps->isEmpty())

                    @php $inc = 12; $i = 0; $count = $followUps->count(); @endphp

                    @foreach ($followUps as $followUp)
                        @if ($inc % 12 == 0)
                            <div class="row">
                        @endif

                            <a href="{{ route('follow_up.show', $followUp->id) }}" target="__blank">
                                <div class="col-md-2">
                                    <div class="folder">
                                        <span>
                                            {{ $followUp->first_name . ' ' . $followUp->last_name }}
                                        </span>
                                    </div>
                                </div>
                            </a>

                        @php $inc++; @endphp

                        @if ($inc % 12 == 0 || ($i == ($count - 1)))
                            </div>
                        @endif

                        @php $i++; @endphp
                    @endforeach
                @else
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <mark>{{ __('No record found!') }}</mark>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
