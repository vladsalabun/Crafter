@empty($param['old_value']) @php $param['old_value'] = null @endphp @endisset
@isset($param['name'])
<input type="hidden"{{''}}@isset($param['name']) name="{{$param['name']}}"{{''}}@endisset{{''}}@isset($param['value']) value="{{$param['value']}}"{{''}}@endisset{{''}}@isset($param['id']) id="{{$param['id']}}"{{''}}@endisset{{''}}>
@endisset
@empty($param['name'])
Enter, please, the name of the field
@endisset
