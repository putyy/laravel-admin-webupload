<div class="form-group {!! !$errors->has($label) ?: 'has-error' !!}">

    <label for="{{$id}}" class="col-sm-2 control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="kit">
            <video controls="" class="kit-video">
                <source class="source-address" src="" type="audio/mp3">
            </video>
            <input type="file" class="choose-video kit-file" {!! $attributes !!}>
            <input type="hidden" class="kit-data" name="{{$name}}" value="{{$value}}"/>
        </div>

    </div>
</div>