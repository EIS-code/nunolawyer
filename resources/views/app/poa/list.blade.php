@extends('layout')

@section('sub_content')

    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <span class="breadcrumb-item active">{{__('POA Agreement')}}</span>
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
                            <label>{{__('Text')}}</label>
                            <div class="input-group bg-light">
                                <input type="text" name="s" class="form-control searchInput" placeholder="{{__('Search text')}}" @if(!empty($term->get('s'))) value="{{$term->get('s')}}" @endif>
                                <div class="input-group-append">
                                     @if($isFiltered == true)
                                        <a href="{{route('poa.index')}}" class="btn btn-light">
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
                                @can('poa_create')
                                    <a href="{{route('poa.create')}}" class="btn btn-primary btn-round"><i class="metismenu-icon pe-7s-user"></i> {{__('Add New')}}</a>
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
                                <!--th width="48%">{{ __('Text') }}</th-->
                                @can('poa_view')
                                    <th width="15%">{{ __('View File') }}</th>
                                @endcan
                                @can('poa_download')
                                    <th width="15%">{{ __('Download File') }}</th>
                                @endcan
                                @can('clients_email')
                                    <th width="10%">{{ __('Emails') }}</th>
                                @elsecan('editors_email')
                                    <th width="10%">{{ __('Emails') }}</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @if($poaAgreements->total() == 0)
                                <tr>
                                    <td colspan="12" class="text-center"><mark>{{__('No results found.')}}</mark></td>
                                </tr>
                            @else
                                @foreach($poaAgreements as $poaAgreement)
                                    <tr>
                                        <td class="d-none">
                                            <form action="{{ route('poa.email', $poaAgreement->id) }}" method="POST" class="d-none" id="html">
                                                @csrf
                                                <div>
                                                    <br />
                                                    <div class="form-group row">
                                                        <div class="col-md-12">
                                                            <label>{{ __('To') }}<span style="color: red;">* </span></label>
                                                            <input type="text" name="emails" class="form-control" />
                                                            <span style="color: red;">(Use comma separator for multiple like : test@gmail.com, test2@gmail.com)</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                        <td width="1">
                                            @can('poa_edit')
                                                <a href="{{route('poa.edit', $poaAgreement->id)}}" data-toggle="tooltip" data-placement="top" title="{{__('Edit')}}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endcan
                                        </td>
                                        <td width="1">
                                            @can('poa_delete')
                                                <form action="{{ route('poa.destroy', $poaAgreement->id) }}" method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                                    <a href="#" class="deleteBtn" data-confirm-message="{{__("Are you sure you want to delete this ?")}}" data-toggle="tooltip" data-placement="top" title="{{__('Delete')}}"><i class="fa fa-trash"></i></a>
                                                </form>
                                            @endcan
                                        </td>
                                        <td>{{ $poaAgreement->title }}</td>
                                        <!-- <td>{!! str_limit($poaAgreement->text, 100, '...') !!}</td> -->
                                        @can('poa_view')
                                            @if (!empty($poaAgreement->file))
                                                <td>
                                                    <a href="{{ $poaAgreement->file }}" target="__blank">{{ __('View') }}</a>
                                                </td>
                                            @else
                                                <td>
                                                    <mark>{{ __('No File') }}</mark>
                                                </td>
                                            @endif
                                        @endcan
                                        @can('poa_download')
                                            <td>
                                                @if (!empty($poaAgreement->file))
                                                    <a href="{{ route('poa.download', $poaAgreement) }}">{{ __('Download') }}</a>
                                                @else
                                                    <mark>{{ __('No File') }}</mark>
                                                @endif
                                            </td>
                                        @endcan
                                        @can('clients_email')
                                            <td>
                                                <button type="button" class="btn btn-warning btn-round toEmails" data-html="#html">
                                                    <i class="fa fa-envelope"></i>
                                                </button>
                                            </td>
                                        @elsecan('editors_email')
                                            <td>
                                                <button type="button" class="btn btn-warning btn-round toEmails" data-html="#html">
                                                    <i class="fa fa-envelope"></i>
                                                </button>
                                            </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div class="float-left">
                        @if(!empty($term))
                            {{ $poaAgreements->appends($term->all())->links() }}
                        @else
                            {{ $poaAgreements->links() }}
                        @endif
                    </div>

                    <div class="float-right text-muted">
                        {{__('Showing')}} {{ $poaAgreements->firstItem() }} - {{ $poaAgreements->lastItem() }} / {{ $poaAgreements->total() }} ({{__('page')}} {{ $poaAgreements->currentPage() }} )
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
