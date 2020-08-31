@extends('layout')

@section('sub_content')

    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <span class="breadcrumb-item active">{{__('Translate Model Document')}}</span>
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
                                        <a href="{{route('translate_model_document.index')}}" class="btn btn-light">
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
                                @can('translate_model_document_create')
                                    <a href="{{route('translate_model_document.create')}}" class="btn btn-primary btn-round"><i class="metismenu-icon pe-7s-user"></i> {{__('Add New')}}</a>
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
                                <th width="40%">{{ __('Title') }}</th>
                                <th width="20%">{{ __('View Details') }}</th>
                                @can('translate_model_document_show_file')
                                    <th width="10%">{{ __('View File') }}</th>
                                @endcan
                                @can('translate_model_document_show_client')
                                    <th width="10%">{{ __('View Clients') }}</th>
                                @endcan
                                @can('translate_model_document_download')
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
                            @if($translateModelDocuments->total() == 0)
                                <tr>
                                    <td colspan="12" class="text-center"><mark>{{__('No results found.')}}</mark></td>
                                </tr>
                            @else
                                @foreach($translateModelDocuments as $translateModelDocument)
                                    <tr>
                                        <td class="d-none">
                                            <form action="{{ route('translate_model_document.email', $translateModelDocument->id) }}" method="POST" class="d-none" id="html">
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
                                            @can('translate_model_document_edit')
                                                <a href="{{route('translate_model_document.edit', $translateModelDocument->id)}}" data-toggle="tooltip" data-placement="top" title="{{__('Edit')}}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endcan
                                        </td>
                                        <td width="1">
                                            @can('translate_model_document_delete')
                                                <form action="{{ route('translate_model_document.destroy', $translateModelDocument->id) }}" method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                                    <a href="#" class="deleteBtn" data-confirm-message="{{__("Are you sure you want to delete this ?")}}" data-toggle="tooltip" data-placement="top" title="{{__('Delete')}}"><i class="fa fa-trash"></i></a>
                                                </form>
                                            @endcan
                                        </td>
                                        <td>{{ $translateModelDocument->title }}</td>
                                        <!-- <td>{!! str_limit($translateModelDocument->text, 100, '...') !!}</td> -->
                                        <td>
                                            <div class="d-none" id="view-details-{{ $translateModelDocument->id }}">
                                                <div class="content">
                                                <div class="row">
                                                    <label class="h6"><u>{{ __('Title') }}</u></label>
                                                    <div class="col-md-12">
                                                        {{ $translateModelDocument->title }}<br /><br />
                                                    </div>

                                                    <label class="h6"><u>{{ __('Text') }}</u></label>
                                                    <div class="col-md-12 texts">
                                                        {!! $translateModelDocument->text !!}
                                                        @if (empty($translateModelDocument->text))
                                                            {{ __('-') }}
                                                        @endif
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                            <a href="javascript:void(0);" class="view-details" data-id="{{ $translateModelDocument->id }}" data-url="{{ route('translate_model_document.edit', $translateModelDocument->id) }}">{{ __('View') }}</a>
                                        </td>
                                        @can('translate_model_document_show_file')
                                            @if (!empty($translateModelDocument->file))
                                                <td>
                                                    <a href="{{ $translateModelDocument->file }}" target="__blank">{{ __('View') }}</a>
                                                </td>
                                            @else
                                                <td>
                                                    <mark>{{ __('No File') }}</mark>
                                                </td>
                                            @endif
                                        @endcan
                                        @can('translate_model_document_show_client')
                                            <td>
                                                @if (!empty($translateModelDocument->client_id))
                                                    <a href="{{ route('clients.edit', $translateModelDocument->client_id) }}" target="__blank">{{ __('View') }}</a>
                                                @else
                                                    <mark>{{ __('No Clients') }}</mark>
                                                @endif
                                            </td>
                                        @endcan
                                        @can('translate_model_document_download')
                                            <td>
                                                @if (!empty($translateModelDocument->file))
                                                    <a href="{{ route('translate_model_document.download', $translateModelDocument) }}">{{ __('Download') }}</a>
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
                            {{ $translateModelDocuments->appends($term->all())->links() }}
                        @else
                            {{ $translateModelDocuments->links() }}
                        @endif
                    </div>

                    <div class="float-right text-muted">
                        {{__('Showing')}} {{ $translateModelDocuments->firstItem() }} - {{ $translateModelDocuments->lastItem() }} / {{ $translateModelDocuments->total() }} ({{__('page')}} {{ $translateModelDocuments->currentPage() }} )
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
