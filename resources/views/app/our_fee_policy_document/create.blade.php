@extends('layout')

@section('sub_content')

    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{ route('dashboard') }}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <a href="{{ route('our_fee_policy_document.index') }}" class="breadcrumb-item">
                        {{__('Our Fee Policy Document')}}
                    </a>
                    <span class="breadcrumb-item active">{{__('Add New')}}</span>
                </div>
            </div>
        </div>
    </div>
	
    <div class="content">
        <div class="card bg-white">
            <div class="card-body">
                <form method="POST" action="{{ route('our_fee_policy_document.store') }}">
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label>{{ __('Title') }}</label>
                            <input id="title" type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ old('title') }}" required autofocus>

                            @if ($errors->has('title'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <label>{{ __('Text') }}</label>
                            <textarea id="text" type="text" class="form-control{{ $errors->has('text') ? ' is-invalid' : '' }}" name="text" required autofocus>{{ old('text') }}</textarea>

                            @if ($errors->has('text'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('text') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="metismenu-icon pe-7s-diskette"></i> {{ __('Create') }}
                            </button>
                            <a href="{{route('our_fee_policy_document.index')}}" class="btn btn-danger">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('app.scripts.scripts')
@endsection
