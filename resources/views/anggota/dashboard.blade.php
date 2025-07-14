@extends('layouts.anggota-app')

@section('content')
<div class="p-4 mt-14">
    <h1 class="text-2xl font-bold">Selamat Datang, {{ auth()->user()->name }}!</h1>
</div>
@endsection
