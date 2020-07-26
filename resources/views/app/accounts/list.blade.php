@extends('layout')

@section('sub_content')

    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <span class="breadcrumb-item active">{{__('Accounts')}}</span>
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

        <div class="card bg-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form class="w-100">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>{{__('Date From / To')}}</label>
                                        <div class="input-group">
                                            <!--label>
                                                <button type="button" class="btn btn-default">
                                                    {{ __('From') }}
                                                </button>   
                                            </label-->
                                            <input type="date" name="dt" class="form-control" @if(!empty($term->get('dt'))) value="{{$term->get('dt')}}" @endif>
                                            <!--label>
                                                <button type="button" class="btn btn-default">
                                                    {{ __('To') }}
                                                </button>   
                                            </label-->
                                            <input type="date" name="dtt" class="form-control" @if(!empty($term->get('dtt'))) value="{{$term->get('dtt')}}" @endif>
                                        </div>
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>{{__('Name')}}</label>
                                        <input type="text" name="s" class="form-control searchInput" placeholder="{{__('Search  by name, mobile or email')}}" @if(!empty($term->get('s'))) value="{{$term->get('s')}}" @endif>
                                    </div>
                                    <div class="col-md-6">
                                        <label>{{__('Role')}}</label>
                                        <select name="role" class="form-control">
                                            <option value="">{{ __('Select') }}</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}" {{ ($term->get('role') == $role->name ? 'selected' : '') }}>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <br />

                                <div class="input-group-append">
                                     @if($isFiltered == true)
                                        <a href="{{route('account.index')}}" class="btn btn-light">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    @endif
                                    <button class="btn btn-primary">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <button type="submit" name="export" value="{{ __('Export') }}" class="btn btn-success">
                                        <i class="fa fa-file"></i>
                                        {{ __('Export') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-white mt-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                        <thead>
                            <tr>
                                <th>{{ __('Editor ID') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Client') }}</th>
                                <th>{{ __('Received Amt') }}</th>
                                <th>{{ __('Purpose') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($accounts->total() == 0)
                                <tr>
                                    <td colspan="12" class="text-center"><mark>{{__('No results found.')}}</mark></td>
                                </tr>
                            @else
                                @foreach($accounts as $account)
                                    <tr>
                                        <td>{{ $account->created_by }}</td>
                                        <td>{{ $account->date }}</td>
                                        <td>{{ $account->client_id }}</td>
                                        <td>{{ $account->received_amount }}</td>
                                        <td>{{ $account->purpose_article_id }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div class="float-left">
                        @if(!empty($term))
                            {{ $accounts->appends($term->all())->links() }}
                        @else
                            {{ $accounts->links() }}
                        @endif
                    </div>

                    <div class="float-right text-muted">
                        {{__('Showing')}} {{ $accounts->firstItem() }} - {{ $accounts->lastItem() }} / {{ $accounts->total() }} ({{__('page')}} {{ $accounts->currentPage() }} )
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
