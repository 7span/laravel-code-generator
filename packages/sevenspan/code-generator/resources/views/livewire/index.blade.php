@extends('code-generator::components.layouts.app')

@section('header')
<x-code-generator::header />
@endsection

@section('content')
<div class='flex pl-32 pr-32 pt-8 w-screen relative'>
 <x-code-generator::sidebar />
  <div class="flex-grow bg-white shadow-lg shadow-black/5 rounded-lg p-6 border border-grey-200 ">
    <livewire:code-generator::rest-api />
  </div>
</div>
@endsection