@extends('layout')

@section('sub_content')
    <div class="page-header">
        <div class="breadcrumb-line">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{route('dashboard')}}" class="breadcrumb-item"><i class="metismenu-icon pe-7s-home" style="margin-top: 3px;"></i>&nbsp;{{__('Dashboard')}}</a>
                    <a href="{{ route('follow_up.index') }}" class="breadcrumb-item">{{ __('Follow Ups') }}</a>
                    <span class="breadcrumb-item active">{{ __('Follow Up Editors') }}</span>
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

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                        <thead>
                            <th width="1%"></th>
                            <th>{{ __('Assigned Date') }}</th>
                            <th>{{ __('Assigned By') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('DOB') }}</th>
                            <th>{{ __('Nationality') }}</th>
                            <th>{{ __('Purpose/Art.') }}</th>
                            <th>{{ __('Contact') }}</th>
                            <th>{{ __('Email') }}</th>
                        </thead>

                        <tbody>
                            @if (empty($followUps) || $followUps->count() == 0)
                                <tr>
                                    <td colspan="12" class="text-center"><mark>{{__('No results found.')}}</mark></td>
                                </tr>
                            @else
                                @foreach ($followUps as $followUp)
                                    <tr>
                                        <td>
                                            <a target="__blank" href="{{ route('editors.edit', $followUp->client_id)}}" data-toggle="tooltip" data-placement="top" title="{{__('View Editor')}}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>

                                        <td>
                                            {{ $followUp->created_at }}
                                        </td>

                                        <td>
                                            {{ $clientModel::getClientNames($followUp->follow_from) }}
                                        </td>

                                        <td>
                                            {{ $followUp->first_name . ' ' . $followUp->last_name }}
                                        </td>

                                        <td>
                                            {{ $followUp->dob }}
                                        </td>

                                        <td>
                                            {{ $followUp->nationality }}
                                        </td>

                                        <td>
                                            @php
                                                $titles = [];
                                                $followUp->clientPurposeArticles->map(function($data) use(&$titles) {
                                                    $titles[] = $data->purposeArticle->title;
                                                });
                                            @endphp
                                            {{ implode(', ', $titles) }}
                                        </td>

                                        <td>
                                            {{ $followUp->contact }}
                                        </td>

                                        <td>
                                            {{ $followUp->email }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
