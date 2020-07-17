@extends('layout')

@section('sub_content')

    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    @if($request->has('new'))
                        <a href="{{route('editors.index')}}" class="breadcrumb-item">{{__('Editors')}}</a>
                        <span class="breadcrumb-item">{{__('New Editors')}}</span>
                    @elseif($request->has('banned'))
                        <a href="{{route('editors.index')}}" class="breadcrumb-item">{{__('Editors')}}</a>
                        <span class="breadcrumb-item">{{__('Banned Editors')}}</span>
                    @else
                        <span class="breadcrumb-item active">{{__('Editors')}}</span>
                    @endif
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
                                    <div class="col-md-6">
                                        <label>{{__('Name, mobile or email')}}</label>
                                        <input type="text" name="s" class="form-control searchInput" placeholder="{{__('Search  by name, mobile or email')}}" @if(!empty($term->get('s'))) value="{{$term->get('s')}}" @endif>
                                    </div>
                                    <div class="col-md-6">
                                         <label>{{__('DOB')}}</label>
                                         <div class="input-group">
                                            <input type="date" name="dob" class="form-control" @if(!empty($term->get('dob'))) value="{{$term->get('dob')}}" @endif>
                                        </div>
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>{{__('Registered From / To')}}</label>
                                        <div class="input-group">
                                            <!--label>
                                                <button type="button" class="btn btn-default">
                                                    {{ __('From') }}
                                                </button>   
                                            </label-->
                                            <input type="date" name="rs" class="form-control" @if(!empty($term->get('rs'))) value="{{$term->get('rs')}}" @endif>
                                            <!--label>
                                                <button type="button" class="btn btn-default">
                                                    {{ __('To') }}
                                                </button>   
                                            </label-->
                                            <input type="date" name="rst" class="form-control" @if(!empty($term->get('rst'))) value="{{$term->get('rst')}}" @endif>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label>{{ __('Purpose / Article') }}</label>

                                        <select class="form-control" name="pur">
                                            <option value="">{{ __('Select') }}</option>

                                            @php
                                                $purposeArticles = $clientModel::getAllClientPurposeArticles();
                                            @endphp

                                            @if (!empty($purposeArticles) && !$purposeArticles->isEmpty())
                                                @foreach ($purposeArticles as $purposeArticle)
                                                    <option value="{{ $purposeArticle['id'] }}" @if ($term->get('pur') && $term->get('pur') == $purposeArticle['id']) selected="true" @endif>{{ $purposeArticle['title'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>{{__('Work Status')}}</label>
                                    </div>
                                    <div class="col-md-12">
                                        <select name="ws[]" multiple="true" class="form-control work_status">
                                            <!--option value="">{{ __('Select') }}</option-->

                                            @foreach ($clientModel::$workStatus as $value => $text)
                                                <option value="{{ $value }}" @php echo (!empty($term->get('ws')) && in_array($value, $term->get('ws')) ? 'selected' : ''); @endphp>{{ $text }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <br />

                                <div class="input-group-append">
                                     @if($isFiltered == true)
                                        <a href="{{route('editors.index')}}" class="btn btn-light">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    @endif
                                    <button class="btn btn-primary">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <!--input type="hidden" name="export" id="export" value="" />
                                    <button class="btn btn-success" onclick="javascript: document.getElementById('export').value = 'export';">
                                        <i class="fa fa-file"></i>
                                    </button>
                                    <input type="submit" name="export" id="export" value="{{ __('Export') }}" class="btn btn-success"/-->
                                    <button type="submit" name="export" value="{{ __('Export') }}" class="btn btn-success">
                                        <i class="fa fa-file"></i>
                                        {{ __('Export') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <div class="page-title pull-right">
                            <div class="heading">
                                @can('editors_create')
                                    <a href="{{route('editors.create')}}" class="btn btn-primary btn-round"><i class="metismenu-icon pe-7s-user"></i> {{__('Add New Editor')}}</a>
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
                                <th width="1"></th>
                                <th width="1"></th>
                                <th width="1"></th>
                                <th>{{ __('Registered Date') }}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{ __('DOB') }}</th>
                                <th>{{ __('Nationality') }}</th>
                                <th>{{ __('Passport/Art.') }}</th>
                                <th>{{__('E-mail')}}</th>
                                <th>{{ __('Contact') }}</th>
                                <th>{{__('Role')}}</th>
                                @can('editors_activity')
                                    <th>{{ __('Log') }}</th>
                                @endcan
                                @can('editors_print')
                                    <th>{{ __('View') }}</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @if($clients->total() == 0)
                                <tr>
                                    <td colspan="12" class="text-center"><mark>{{__('No results found.')}}</mark></td>
                                </tr>
                            @else
                                @foreach($clients as $client)
                                    <tr>
                                        <td width="1">
                                            @can('editors_show')
                                                <a href="{{route('editors.show', $client->id)}}" target="__blank" data-toggle="tooltip" data-placement="top" title="{{__('View Editor')}}">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endcan
                                        </td>
                                        <td width="1">
                                            @can('editors_edit')
                                                <a href="{{route('editors.edit', $client->id)}}" data-toggle="tooltip" data-placement="top" title="{{__('Edit Editor')}}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endcan
                                        </td>
                                        <td width="1">
                                            @if(!$client->isSuperAdmin())
                                                @can('editors_delete')
                                                    <form action="{{ route('editors.destroy', $client->id) }}" method="POST">
                                                        @method('DELETE')
                                                        @csrf
                                                        <a href="#" class="deleteBtn" data-confirm-message="{{__("Are you sure you want to delete this editor?")}}" data-toggle="tooltip" data-placement="top" title="{{__('Delete Editor')}}"><i class="fa fa-trash"></i></a>
                                                    </form>
                                                @endcan
                                            @endif
                                        </td>
                                        <td>{{ $client->registration_date }}</td>
                                        <td>
                                            @if(auth()->user()->can('editors_show'))
                                                <a href="{{route('editors.show', $client->id)}}" target="__blank" >{{ $client->first_name . ' ' . $client->last_name }}</a>
                                            @else
                                                {{ $client->first_name . ' ' . $client->last_name }}
                                            @endif
                                        </td>
                                        <td>{{ $client->dob }}</td>
                                        <td>{{ $client->nationality }}</td>
                                        <td>{{ $client->passport_number . ' / ' . $client->process_address }}</td>
                                        <td>{{$client->email}}</td>
                                        <td>{{$client->contact}}</td>
                                        <td><span class="badge badge-lg badge-secondary text-white">{{@$client->getRoleNames()[0]}}</span></td>
                                        @can('editors_activity')
                                            <td>
                                                <a href="{{ route('editors.activity', $client->id) }}#" target="_blank">{{ __('Log') }}</a>
                                            </td>
                                        @endcan
                                        @can('editors_print')
                                            <td>
                                                <a href="{{ route('editors.print', $client->id) }}" target="_blank">{{ __('Print') }}</a>
                                            </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div class="float-left">
                        @if(!empty($term))
                            {{ $clients->appends($term->all())->links() }}
                        @else
                            {{ $clients->links() }}
                        @endif
                    </div>

                    <div class="float-right text-muted">
                        {{__('Showing')}} {{ $clients->firstItem() }} - {{ $clients->lastItem() }} / {{ $clients->total() }} ({{__('page')}} {{ $clients->currentPage() }} )
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('app.clients.scripts')
@endsection
