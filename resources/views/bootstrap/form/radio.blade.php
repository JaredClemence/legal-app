<div class="{{isset($box_width)?$box_width:''}}">
    <fieldset>
        <legend>{{$radio_group_label}}</legend>
@foreach($options as $key=>$option)
<div class="form-check">
  @if($option->is_checked)
  <input class="form-check-input" type="radio" name="{{$name}}" id="{{$name}}_{{$key}}" value="{{$option->value}}" checked>
  @else
  <input class="form-check-input" type="radio" name="{{$name}}" id="{{$name}}_{{$key}}" value="{{$option->value}}" >
  @endif
  <label class="form-check-label" for="{{$name}}_{{$key}}">
    {{$option->label_text}}
  </label>
</div>
@endforeach
    </fieldset>
</div>