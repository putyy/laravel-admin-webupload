<div class="form-group {!! !$errors->has($label) ?: 'has-error' !!}">
    <label for="{{$id}}" class="col-sm-2 control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')
        <div class="kit-file">
            @foreach($files as $vv)
                <div class="kit-item" data-other="{{$vv['other'] ?? ''}}" data-index="">
                    <div class="option-icon">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                        <span class="glyphicon glyphicon-chevron-right"></span>
                        <span class="glyphicon glyphicon-remove"></span>
                    </div>
                    @switch($uploadType)
                        @case(1)
                            <img src="{{$vv[$src]}}">
                            @break
                        @case(2)
                            <audio controls><source src="{{$vv[$src]}}"/></audio>
                            <p>{{$vv[$src]}}</p>
                            @break
                        @case(3)
                            <video controls><source src="{{$vv[$src]}}"/></video>
                            <p>{{$vv[$src]}}</p>
                            @break
                        @case(4)
                            <div class="file-name">{{$vv[$src]}}</div>
                            @break
                    @endswitch()
                </div>
            @endforeach
        </div>
        <input type="hidden" class="kit-data" name="{{$name}}" value="{{$value ?? ''}}"/>
        <input type="file" class="select-files" data-type="{{$uploadType}}" data-max="{{$max}}" data-min="{{$min}}" multiple {!! $attributes !!}/>
    </div>

</div>
</div>
