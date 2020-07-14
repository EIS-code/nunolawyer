@extends('layout')

@section('sub_content')

    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <span class="breadcrumb-item active">{{__('Roles')}}</span>
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
                            <div class="input-group bg-light">
                                <input type="text" name="s" class="form-control searchInput" placeholder="{{__('Search')}}" @if(!empty($term)) value="{{$term}}" @endif>
                                <div class="input-group-append">
                                     @if(!empty($term))
                                        <a href="{{route('roles.index')}}" class="btn btn-light">
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
                            <div class="heading">
                                @can('roles_create')
                                    <a href="{{route('roles.create')}}" class="btn btn-primary btn-round float-right"><i class="metismenu-icon pe-7s-lock"></i> {{__('Add New Role')}}</a>
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
                                <th>{{__('Name')}}</th>
                                <th>{{__('Users')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($roles->total() == 0)
                                <tr>
                                    <td colspan="5">{{__('No results found.')}}</td>
                                </tr>
                            @else
                                @foreach($roles as $role)
                                    <tr>
                                        <td width="1">
                                            @can('roles_show')
                                                <a href="{{route('roles.show', $role->id)}}" data-toggle="tooltip" data-placement="top" title="{{__('View Role')}}">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endcan
                                        </td>
                                        <td width="1">
                                            @can('roles_edit')
                                                <a href="{{route('roles.edit', $role->id)}}" data-toggle="tooltip" data-placement="top" title="{{__('Edit Role')}}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endcan
                                        </td>
                                        <td width="1">
                                            @if($role->id !== 1)
                                                @can('roles_delete')
                                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                                                        @method('DELETE')
                                                        @csrf
                                                        <a href="#" class="deleteBtn" data-confirm-message="{{__("Are you sure you want to delete this role?")}}"  data-toggle="tooltip" data-placement="top" title="{{__('Delete Role')}}">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </form>
                                                @endcan
                                            @endif
                                        </td>
                                        <td>
                                            @if(auth()->user()->can('roles_show'))
                                                <a href="{{route('roles.show', $role->id)}}">{{$role->name}}</a>
                                            @else
                                                {{$role->name}}
                                            @endif
                                         </td>
                                         <td>{{count($role->users)}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div class="float-left">
                        @if(!empty($term))
                            {{ $roles->appends(['s' => $term])->links() }}
                        @else
                            {{ $roles->links() }}
                        @endif
                    </div>

                    <div class="float-right text-muted">
                        {{__('Showing')}} {{ $roles->firstItem() }} - {{ $roles->lastItem() }} / {{ $roles->total() }} ({{__('page')}} {{ $roles->currentPage() }} )
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
