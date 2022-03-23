<div class="form-group {!! !$errors->has($label) ?: 'has-error' !!}">

    <label for="{{$id}}" class="col-sm-2 control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="kit">
            <audio controls="">
                <source class="source-address" src="{{$value}}" type="audio/mp3">
            </audio>
            <input type="file" class="choose-audio kit-file" {!! $attributes !!}>
            <input type="hidden" class="kit-data" name="{{$name}}" value="{{$value}}"/>
        </div>

    </div>
</div>
