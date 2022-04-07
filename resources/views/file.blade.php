<div class="form-group {!! !$errors->has($label) ?: 'has-error' !!}">
    <label for="{{$id}}" class="col-sm-2 control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')
        <div class="kit-file">
            @switch($uploadType)
                @case(1)
                    <img src="{{$value ?: '/vendor/putyy/laravel-admin-webupload/upload-default.png'}}">
                    @break
                @case(2)
                    <audio controls="">
                        <source src="{{$value ?? ''}}" type="audio/mp3"/>
                    </audio>
                    @break
                @case(3)
                    <video controls="">
                        <source src="{{$value ?? ''}}" type="audio/mp4"/>
                    </video>
                    @break
                @case(4)
                    <div class="file-name">{{$value ?? ''}}</div>
                    @break
            @endswitch()
        </div>
        <input type="hidden" class="kit-data" name="{{$name}}" value="{{$value ?? ''}}"/>
        <input type="file" size="1" class="select-file {{$uploadType==1 ? 'choose-image': ''}}" data-type="{{$uploadType}}" {!! $attributes !!}>
    </div>
</div>
