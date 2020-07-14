@extends('layout')

@section('sub_content')

    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <span class="breadcrumb-item active">{{__('Permissions')}}</span>
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
                                        <a href="{{route('permissions.index')}}" class="btn btn-light">
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
                                @can('permissions_create')
                                    <a href="{{route('permissions.create')}}" class="btn btn-primary btn-round float-right"><i class="metismenu-icon pe-7s-settings"></i> {{__('Add New Permission')}}</a>
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
                                <th>{{__('Display Name')}}</th>
                                <th>{{__('Group')}}</th>
                                <th>{{__('Group Slug')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($permissions->total() == 0)
                                <tr>
                                    <td colspan="7">{{__('No results found.')}}</td>
                                </tr>
                            @else
                                @foreach($permissions as $permission)
                                    <tr>
                                        <td width="1">
                                            @can('permissions_show')
                                                <a href="{{route('permissions.show', $permission->id)}}" data-toggle="tooltip" data-placement="top" title="{{__('View Permission')}}">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endcan
                                        </td>
                                        <td width="1">
                                            @can('permissions_edit')
                                                <a href="{{route('permissions.edit', $permission->id)}}" data-toggle="tooltip" data-placement="top" title="{{__('Edit Permission')}}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td width="1">
                                            @can('permissions_delete')
                                                <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                                    <a href="#" class="deleteBtn" data-confirm-message="{{__("Are you sure you want to delete this permission?")}}" data-toggle="tooltip" data-placement="top" title="{{__('Delete Permission')}}"><i class="fa fa-trash"></i></a>
                                                </form>
                                            @endcan
                                        </td>
                                        <td>
                                            @if(auth()->user()->can('permissions_show'))
                                                <a href="{{route('permissions.show', $permission->id)}}">{{$permission->name}}</a>
                                            @else
                                                {{$permission->name}}
                                            @endif
                                        </td>
                                        <td>{{$permission->display_name}}</td>
                                        <td>{{$permission->group_name}}</td>
                                        <td>{{$permission->group_slug}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div class="float-left">
                        @if(!empty($term))
                            {{ $permissions->appends(['s' => $term])->links() }}
                        @else
                            {{ $permissions->links() }}
                        @endif
                    </div>

                    <div class="float-right text-muted">
                        {{__('Showing')}} {{ $permissions->firstItem() }} - {{ $permissions->lastItem() }} / {{ $permissions->total() }} ({{__('page')}} {{ $permissions->currentPage() }} )
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
