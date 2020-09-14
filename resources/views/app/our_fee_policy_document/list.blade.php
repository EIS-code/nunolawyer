@extends('layout')

@section('sub_content')

    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <span class="breadcrumb-item active">{{__('Our Fee Policy Document')}}</span>
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
                    <div class="col-md-4">
                        <form class="w-100">
                            <label>{{__('Title')}}</label>
                            <div class="input-group bg-light">
                                <input type="text" name="s" class="form-control searchInput" placeholder="{{__('Search title')}}" @if(!empty($term->get('s'))) value="{{$term->get('s')}}" @endif>
                                <div class="input-group-append">
                                     @if($isFiltered == true)
                                        <a href="{{route('our_fee_policy_document.index')}}" class="btn btn-light">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    @endif
                                    <button class="btn btn-primary">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <div class="page-title pull-right">
                            <label>&nbsp;</label>
                            <div class="heading">
                                @can('our_fee_policy_document_create')
                                    <a href="{{route('our_fee_policy_document.create')}}" class="btn btn-primary btn-round"><i class="metismenu-icon pe-7s-user"></i> {{__('Add New')}}</a>
                                @endcan
                            </div>
                        </div>
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
                                <th width="1%"></th>
                                <th width="1%"></th>
                                <th width="20%">{{ __('Title') }}</th>
                                <th width="10%">{{ __('View Details') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($ourFeePolicyDocuments->total() == 0)
                                <tr>
                                    <td colspan="12" class="text-center"><mark>{{__('No results found.')}}</mark></td>
                                </tr>
                            @else
                                @foreach($ourFeePolicyDocuments as $ourFeePolicyDocument)
                                    <tr>
                                        <td width="1">
                                            @can('our_fee_policy_document_edit')
                                                <a href="{{route('our_fee_policy_document.edit', $ourFeePolicyDocument->id)}}" data-toggle="tooltip" data-placement="top" title="{{__('Edit')}}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endcan
                                        </td>
                                        <td width="1">
                                            @can('our_fee_policy_document_delete')
                                                <form action="{{ route('our_fee_policy_document.destroy', $ourFeePolicyDocument->id) }}" method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                                    <a href="#" class="deleteBtn" data-confirm-message="{{__("Are you sure you want to delete this ?")}}" data-toggle="tooltip" data-placement="top" title="{{__('Delete')}}"><i class="fa fa-trash"></i></a>
                                                </form>
                                            @endcan
                                        </td>
                                        <td>{{ $ourFeePolicyDocument->title }}</td>
                                        <!-- <td>{!! str_limit($ourFeePolicyDocument->text, 100, '...') !!}</td> -->
                                        <td>
                                            <div class="d-none" id="view-details-{{ $ourFeePolicyDocument->id }}">
                                                <div class="content">
                                                <div class="row">
                                                    <label class="h6"><u>{{ __('Title') }}</u></label>
                                                    <div class="col-md-12">
                                                        {{ $ourFeePolicyDocument->title }}<br /><br />
                                                    </div>

                                                    <label class="h6"><u>{{ __('Text') }}</u></label>
                                                    <div class="col-md-12 texts">
                                                        {!! $ourFeePolicyDocument->text !!}
                                                        @if (empty($ourFeePolicyDocument->text))
                                                            {{ __('-') }}
                                                        @endif
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                            <a href="javascript:void(0);" class="view-details" @can('our_fee_policy_document_edit') data-is-edit="true" @else data-is-edit="false" @endcan data-id="{{ $ourFeePolicyDocument->id }}" data-url="{{ route('our_fee_policy_document.edit', $ourFeePolicyDocument->id) }}">{{ __('View') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div class="float-left">
                        @if(!empty($term))
                            {{ $ourFeePolicyDocuments->appends($term->all())->links() }}
                        @else
                            {{ $ourFeePolicyDocuments->links() }}
                        @endif
                    </div>

                    <div class="float-right text-muted">
                        {{__('Showing')}} {{ $ourFeePolicyDocuments->firstItem() }} - {{ $ourFeePolicyDocuments->lastItem() }} / {{ $ourFeePolicyDocuments->total() }} ({{__('page')}} {{ $ourFeePolicyDocuments->currentPage() }} )
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
