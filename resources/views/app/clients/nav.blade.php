@php
    $isEditors = $client::$isEditors;
@endphp

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link @if(Route::getCurrentRoute()->getName() == 'clients.show' || Route::getCurrentRoute()->getName() == 'editors.show') active @endif" href="{{ route(($isEditors ? 'editors.show' : 'clients.show'), $client->id) }}">{{ ($isEditors ? __('Editor Details') : __('Client Details')) }}</a>
    </li>
    @can('clients_activity')
        <li class="nav-item">
            <a class="nav-link @if(Route::getCurrentRoute()->getName() == 'clients.activity' || Route::getCurrentRoute()->getName() == 'editors.activity') active @endif" href="{{ route(($isEditors ? 'editors.activity' : 'clients.activity'), $client->id) }}">{{__('Activity Log')}}</a>
        </li>
    @endcan
    <div style="margin-left: 30%;width: 67%;position: absolute;" class="page-title text-right">
        <div class="heading">
            @if(!$client->isSuperAdmin())
                @can('clients_edit')
                    <a href="{{ route(($isEditors ? 'editors.edit' : 'clients.edit'), $client->id) }}" class="btn btn-primary btn-round"><i class="fa fa-edit"></i> <span class="d-md-inline d-none">{{__('Edit')}}</span></a>
                @endcan
            @endif

            @if(!$client->isSuperAdmin())
                @can('clients_ban')
                    @if(!$client->banned)
                        <!--a href="{{route('clients.ban', $client->id)}}" class="btn btn-dark btn-round confirmBtn" data-confirm-message="{{__('Are you sure you want to ban this client?')}}"><i class="fa fa-exclamation-triangle"></i> <span class="d-md-inline d-none">{{__('Ban Clients')}}</span></a-->
                    @endif
                @endcan
            @endif

            @if(!$client->isSuperAdmin())
                @can('clients_delete')
                    <form action="{{ route(($isEditors ? 'editors.destroy' : 'clients.destroy'), $client->id) }}" method="POST" class="d-inline">
                        @method('DELETE')
                        @csrf
                        <button type="button" class="btn btn-danger btn-round deleteBtn" data-confirm-message="{{__("Are you sure you want to delete this client?")}}"><i class="fa fa-trash"></i> <span class="d-md-inline d-none">{{__('Delete')}}</span></button>
                    </form>
                @endcan
            @endif

            @can('clients_print')
                <a href="{{ route(($isEditors ? 'editors.print' : 'clients.print'), $client->id) }}" target="__blank" class="btn btn-success btn-round"><i class="metismenu-icon pe-7s-print"></i></a>
            @elsecan('editors_print')
                <a href="{{ route(($isEditors ? 'editors.print' : 'clients.print'), $client->id) }}" target="__blank" class="btn btn-success btn-round"><i class="metismenu-icon pe-7s-print"></i></a>
            @endcan

            <form action="{{ route(($isEditors ? 'editors.email' : 'clients.email'), $client->id) }}" method="POST" class="d-none" id="html">
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
            @can('clients_email')
                <button type="button" class="btn btn-warning btn-round toEmails" data-html="#html">
                    <i class="fa fa-envelope"></i>
                </button>
            @elsecan('editors_email')
                <button type="button" class="btn btn-warning btn-round toEmails" data-html="#html">
                    <i class="fa fa-envelope"></i>
                </button>
            @endcan

            <a href="{{ route(($isEditors ? 'editors.index' : 'clients.index')) }}" class="btn btn-secondary btn-round"><i class="metismenu-icon pe-7s-back"></i> <span class="d-md-inline d-none">{{__('Back To List')}}</span></a>

        </div>
    </div>
</ul>
