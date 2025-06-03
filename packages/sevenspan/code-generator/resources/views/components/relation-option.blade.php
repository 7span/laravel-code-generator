@props(['relationType'=>''])

<option value="">Select Relation</option>
@php
use Sevenspan\CodeGenerator\Helper\RelationHelper;
$relations = RelationHelper::getRelation();
@endphp

@foreach ($relations as $key => $label)
<option value="{{ $key }}" {{ $key===$relationType ? 'selected' : '' }}>
    {{ $label }}
</option>
@endforeach