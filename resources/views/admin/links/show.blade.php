@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.links.title') }}
    </div>

    <div class="card-body">
        @if(session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.links.fields.id') }}
                        </th>
                        <td>
                            {{ $links->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.links.fields.ticket_id') }}
                        </th>
                        <td>
                            {{ $links->tickets->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.links.fields.ticket_name') }}
                        </th>
                        <td>
                            {{ $links->tickets->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.links.fields.user') }}
                        </th>
                        <td>
                            {{ $links->tickets->user->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.links.fields.link') }}
                        </th>
                        <th>
                            {{ $links->payment_url }}
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
        <a class="btn btn-default my-2" href="{{ route('admin.links.index') }}">
            {{ trans('global.back_to_list') }}
        </a>

{{--        <a href="{{ route('admin.links.edit', $links->id) }}" class="btn btn-primary">--}}
{{--            @lang('global.edit') @lang('cruds.links.title_singular')--}}
{{--        </a>--}}

        <nav class="mb-3">
            <div class="nav nav-tabs">

            </div>
        </nav>
    </div>
</div>
@endsection
