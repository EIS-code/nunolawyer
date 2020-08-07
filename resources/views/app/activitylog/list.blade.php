@extends('layout')

@section('sub_content')

    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <span class="breadcrumb-item active">{{__('Activity Log')}}</span>
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
                <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                        <thead>
                            <tr>
                                <th width="1"></th>
                                <th width="1"></th>
                                <th>{{__('Client')}}</th>
                                <th>{{__('Date Time')}}</th>
                                <th>{{__('Event')}}</th>
                                <th>{{__('IP Address')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $isHave = false;
                            @endphp

                            @if($audits->total() > 0)
                                @foreach($audits as $audit)
                                    @if (!empty($audit->getModified()))
                                        @if (count($audit->getModified()) == 1 && !empty($audit->getModified()['registration_date']))
                                            @if (strtotime(date('Y-m-d', strtotime($audit->getModified()['registration_date']['old']))) == strtotime(date('Y-m-d', strtotime($audit->getModified()['registration_date']['new']))))
                                                @continue
                                            @endif
                                        @endif

                                        @if (count($audit->getModified()) == 1 && !empty($audit->getModified()['date']))
                                            @if (strtotime(date('Y-m-d', strtotime($audit->getModified()['date']['old']))) == strtotime(date('Y-m-d', strtotime($audit->getModified()['date']['new']))))
                                                @continue
                                            @endif
                                        @endif
                                    @endif

                                    @php
                                        $isHave = true;
                                    @endphp

                                    <tr>
                                        <td width="1">
                                            @can('activitylog_show')
                                                <a href="{{route('activitylog.show', $audit->id)}}" data-toggle="tooltip" data-placement="top" title="{{__('Activity Details')}}">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endcan
                                        </td>
                                        <td width="1">
                                            @can('activitylog_delete')
                                                <form action="{{ route('activitylog.destroy', $audit->id) }}" method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                                    <a href="#" class="deleteBtn" data-confirm-message="{{__("Are you sure you want to delete this activity?")}}" data-toggle="tooltip" data-placement="top" title="{{__('Delete Activity')}}"><i class="fa fa-trash"></i></a>
                                                </form>
                                            @endcan
                                        </td>
                                        <td>
                                            @if(isset($audit->user))
                                                @if(auth()->user()->can('clients_show'))
                                                    <a href="{{route('clients.own.activity', $audit->user->id)}}" data-toggle="tooltip" data-placement="top" title="{{__('Go to client\'s activity log')}}">{{$audit->user->first_name . ' ' . $audit->user->last_name}}</a>
                                                @else
                                                    {{ $audit->user->first_name . ' ' . $audit->user->last_name }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{$audit->created_at}}</td>
                                        <td>{{@$audit['event_message']}}</td>
                                        <td>{{$audit->ip_address}}</td>
                                    </tr>
                                @endforeach
                            @else
                                @php
                                    $isHave = true;
                                @endphp
                                <tr>
                                    <td colspan="6" class="text-center">{{__('No results found.')}}</td>
                                </tr>
                            @endif

                            @if (!$isHave)
                                <tr>
                                    <td colspan="6" class="text-center">{{__('No results found.')}}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    <div class="float-left">{{ $audits->links() }}</div>

                    <div class="float-right text-muted">
                        {{__('Showing')}} {{ $audits->firstItem() }} - {{ $audits->lastItem() }} / {{ $audits->total() }} ({{__('page')}} {{ $audits->currentPage() }} )
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
