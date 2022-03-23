<div class="form-group {!! !$errors->has($label) ?: 'has-error' !!}">

    <label for="{{$id}}" class="col-sm-2 control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="kit">
            <img class="kit-image source-address" src="{{$value ?: '/vendor/putyy/laravel-admin-webupload/upload-default.png'}}">
            <input type="file" class="choose-image kit-file" {!! $attributes !!}>
            <input type="hidden" class="kit-data" name="{{$name}}"
                   value="{{$value ?? ''}}"/>
        </div>

    </div>
</div>
