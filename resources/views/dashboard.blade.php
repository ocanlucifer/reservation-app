@extends('layouts.app')

@section('content')
    <h1>Dashboard</h1>
    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {!! session('error') !!}
        </div>
    @endif
    <div class="container mt-5">
        <h3>Selamat Datang, {{ Auth::user()->name }}</h3>
        {{-- <h4>Menu yang Dapat Diakses:</h4> --}}
        {{-- <ul>
            @foreach ($accessibleRoutes as $route)
                <li><a href="{{ url($route) }}">{{ $route }}</a></li>
            @endforeach
        </ul> --}}
    </div>
@endsection
