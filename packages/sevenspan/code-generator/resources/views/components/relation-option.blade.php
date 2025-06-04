@props(['relationType'=>''])

<option value="">Select Relation</option>
@php
use Sevenspan\CodeGenerator\Library\Helper;
$relations = Helper::getRelation();
@endphp

@foreach ($relations as $key => $label)
<option value="{{ $key }}" {{ $key===$relationType ? 'selected' : '' }}>
    {{ $label }}
</option>
@endforeach