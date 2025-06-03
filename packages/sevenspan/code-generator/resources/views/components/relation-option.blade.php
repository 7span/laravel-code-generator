@props(['relationType'=>''])

<option value="">Select Relation</option>
@php
$relations = [
    'one_to_one' => 'One to One',
    'one_to_many' => 'One to Many',
    'many_to_many' => 'Many to Many',
    'has_one_through' => 'Has One Through',
    'has_many_through' => 'Has Many Through',
    'one_to_one_polymorphic' => 'One To One (Polymorphic)',
    'one_to_many_polymorphic' => 'One To Many (Polymorphic)',
    'many_to_many_polymorphic' => 'Many To Many (Polymorphic)',
];
@endphp

@foreach ($relations as $key => $label)
<option value="{{ $key }}" {{ $key === $relationType ? 'selected' : '' }}>
    {{ $label }}
</option>
@endforeach