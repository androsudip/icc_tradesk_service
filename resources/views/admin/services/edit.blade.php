@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.service.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.services.update", [$service->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('cruds.services.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($service) ? $service->name : '') }}" required>
                @if($errors->has('name'))
                    <em class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.services.fields.name_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('color') ? 'has-error' : '' }}">
                <label for="description">{{ trans('cruds.services.fields.description') }}</label>
                <textarea id="description" name="description" class="form-control ">{{ old('description', isset($service) ? $service->description : '') }}</textarea>
                @if($errors->has('description'))
                    <em class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.services.fields.description_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('service_type') ? 'has-error' : '' }}">
                <label for="service_type">{{ trans('cruds.services.fields.service_type') }}*</label>
                <input type="text" id="service_type" name="service_type" class="form-control" value="{{ old('service_type', isset($service) ? $service->service_type : '') }}" required>
                @if($errors->has('service_type'))
                    <em class="invalid-feedback">
                        {{ $errors->first('service_type') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.services.fields.service_type_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('attachments') ? 'has-error' : '' }}">
                <label for="attachments">{{ trans('cruds.services.fields.attachments') }}</label>
                <div class="needsclick dropzone" id="attachments-dropzone">

                </div>
                @if($errors->has('attachments'))
                    <em class="invalid-feedback">
                        {{ $errors->first('attachments') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.services.fields.attachments_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('category_id') ? 'has-error' : '' }}">
                <label for="category_id">{{ trans('cruds.services.fields.category_name') }}*</label>
                <select name="category_id" id="category_id" class="form-control select2" required>
                    <option value="" selected>Select Category</option>
                    @foreach($categories as $id => $category)
                        <option value="{{ $category->id }}" {{ (old('category_id') == $category->id || $service->category_id == $category->id ) ? 'selected' : '' }}>{{ $category->name .' - '. $category->id }}</option>
                    @endforeach
                </select>
                @if($errors->has('category_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('category_id') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.services.fields.category_name_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('cost') ? 'has-error' : '' }}">
                <label for="cost">{{ trans('cruds.services.fields.cost') }}*</label>
                <input type="number" id="cost" name="cost" class="form-control" value="{{ old('cost', isset($service) ? $service->cost : '') }}" required>
                @if($errors->has('cost'))
                    <em class="invalid-feedback">
                        {{ $errors->first('cost') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.services.fields.cost_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('contact_info') ? 'has-error' : '' }}">
                <label for="contact_info">{{ trans('cruds.services.fields.contact_info') }}*</label>
                <textarea id="contact_info" name="contact_info" class="form-control ">{{ old('description', isset($service) ? $service->contact_info : '') }}</textarea>
                @if($errors->has('contact_info'))
                    <em class="invalid-feedback">
                        {{ $errors->first('contact_info') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.services.fields.contact_info_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                <label for="status">{{ trans('cruds.services.fields.status') }}*</label>
                <select name="status" id="status" class="form-control select2" required>
                    <option value=""  selected>Select Status</option>
                    <option value="1" {{ isset($service->status) && (old('category_id') == $service->status) ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ isset($service->status) && (old('category_id') == $service->status) ? 'selected' : '' }}>In Active</option>
                </select>
                @if($errors->has('status'))
                    <em class="invalid-feedback">
                        {{ $errors->first('status') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.services.fields.status_helper') }}
                </p>
            </div>
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>


    </div>
</div>
@endsection
@section('scripts')
<script>
    var uploadedAttachmentsMap = {}
    Dropzone.options.attachmentsDropzone = {
        url: '{{ route('admin.services.storeMedia') }}',
        maxFilesize: 2, // MB
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        params: {
            size: 2
        },
        success: function (file, response) {
            $('form').append('<input type="hidden" name="attachments[]" value="' + response.name + '">')
            uploadedAttachmentsMap[file.name] = response.name
        },
        removedfile: function (file) {
            file.previewElement.remove()
            var name = ''
            if (typeof file.file_name !== 'undefined') {
                name = file.file_name
            } else {
                name = uploadedAttachmentsMap[file.name]
            }
            $('form').find('input[name="attachments[]"][value="' + name + '"]').remove()
        },
        init: function () {
            @if(isset($service) && $service->attachments)
            var files =
                {!! json_encode($service->attachments) !!}
                for (var i in files) {
                var file = files[i]
                this.options.addedfile.call(this, file)
                file.previewElement.classList.add('dz-complete')
                $('form').append('<input type="hidden" name="attachments[]" value="' + file.file_name + '">')
            }
            @endif
        },
        error: function (file, response) {
            if ($.type(response) === 'string') {
                var message = response //dropzone sends it's own error messages in string
            } else {
                var message = response.errors.file
            }
            file.previewElement.classList.add('dz-error')
            _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
            _results = []
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                node = _ref[_i]
                _results.push(node.textContent = message)
            }

            return _results
        }
    }
</script>
@endsection
