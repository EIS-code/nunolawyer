@extends('layout')

@section('sub_content')

    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <a href="{{route('activitylog.index')}}" class="breadcrumb-item">{{__('Activity Log')}}</a>
                    <span class="breadcrumb-item active">{{__('Activity Details')}}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="card bg-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="page-title pull-right">
                            <div class="heading">
                                @can('activitylog_delete')
                                    <form action="{{ route('activitylog.destroy', $audit->id) }}" method="POST" class="d-inline">
                                        @method('DELETE')
                                        @csrf
                                        <button type="button" class="btn btn-danger deleteBtn" data-confirm-message="{{__("Are you sure you want to delete this activity?")}}"><i class="fa fa-trash"></i> <span class="d-md-inline d-none">Delete</span></button>
                                    </form>
                                @endcan

                                <a href="{{route('activitylog.index')}}" class="btn btn-secondary"><i class="metismenu-icon pe-7s-back"></i> <span class="d-md-inline d-none">Back To List</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-2">{{ __('Client') }}</div>
                    <div class="col-md-8">
                        @if(isset($audit->user))
                            @if(auth()->user()->can('clients_show'))
                                <a href="{{route('clients.activity', $audit->user->id)}}">{{ $audit->user->first_name . ' ' . $audit->user->last_name }}</a>
                            @else
                                {{ $audit->user->first_name . ' ' . $audit->user->last_name }}
                            @endif
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-2">{{ __('Date Time') }}</div>
                    <div class="col-md-8">
                        {{$audit->created_at}}
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-2">{{ __('Event') }}</div>
                    <div class="col-md-8">
                        {{@$audit['event_message']}}
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-2">{{ __('Changed Values (OLD)') }}</div>
                    <div class="col-md-8">
                        @if (!empty($audit->old_values))
                            @foreach ($audit->old_values as $key => $oldValue)

                                @if ($key == 'registration_date' || $key == 'date')
                                    @php $oldValue = date('Y-m-d', strtotime($oldValue)); @endphp
                                @endif
                                @if ($key == 'work_status')
                                    @php $oldValue = App\Client::$workStatus[$oldValue]; @endphp
                                @endif

                                {{ ucfirst(str_ireplace("_", " ", $key)) . ': ' . $oldValue }}
                                <br />
                            @endforeach
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-2">{{ __('New Values') }}</div>
                    <div class="col-md-8">
                        @if (!empty($audit->new_values))
                            @foreach ($audit->new_values as $key => $newValue)
                                @if ($key == 'work_status')
                                    @php $newValue = App\Client::$workStatus[$newValue]; @endphp
                                @endif

                                {{ ucfirst(str_ireplace("_", " ", $key)) . ': ' . $newValue }}
                                <br />
                            @endforeach
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-2">{{ __('IP Address') }}</div>
                    <div class="col-md-8">
                        {{$audit->ip_address}}
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-2">{{ __('User Agent') }}</div>
                    <div class="col-md-8">
                        {{$audit->user_agent}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
