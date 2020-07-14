@extends('layout')

@section('sub_content')

    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <a href="{{route('roles.index')}}" class="breadcrumb-item">{{__('Roles')}}</a>
                    <span class="breadcrumb-item active">{{__('Role Details')}}</span>
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
                    <div class="col-md-12">
                        <div class="page-title pull-right">
                            <div class="heading">
                                @can('roles_edit')
                                    <a href="{{route('roles.edit', $role->id)}}" class="btn btn-primary btn-round"><i class="fa fa-edit"></i> <span class="d-md-inline d-none">{{__('Edit')}}</span></a>
                                @endcan

                                @if($role->id !== 1)
                                    @can('roles_delete')
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                            @method('DELETE')
                                            @csrf
                                            <button type="button" class="btn btn-danger btn-round deleteBtn" data-confirm-message="{{__('Are you sure you want to delete this role?')}}"><i class="fa fa-trash"></i> <span class="d-md-inline d-none">{{__('Delete')}}</span></button>
                                        </form>
                                    @endcan
                                @endif

                                <a href="{{route('roles.index')}}" class="btn btn-secondary btn-round"><i class="metismenu-icon pe-7s-back"></i> <span class="d-md-inline d-none">{{__('Back To List')}}</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-2">{{ __('Name') }}</div>
                    <div class="col-md-8">
                        {{ $role->name }}
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-2">{{ __('Permissions') }}</div>
                    <div class="col-md-8">
                        <table class="table table-striped table-bordered permissions_table">
                            @foreach($groups as $group)
                                <tr>
                                    <td>
                                        <h6 class="mb-2 font-weight-bold">{{$group['name']}}</h6>
                                        <div>
                                            @foreach($group['permissions'] as $perm)
                                                <label class="mr-4">
                                                    @if($role->hasPermissionTo($perm['id'])) 
                                                        <i class="metismenu-icon pe-7s-plus" style="color: green;"></i>
                                                    @else
                                                        <i class="metismenu-icon pe-7s-close-circle" style="color: red;"></i>
                                                    @endif
                                                    {{$perm['display_name'] !== null ? $perm['display_name'] : $perm['name']}}
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
