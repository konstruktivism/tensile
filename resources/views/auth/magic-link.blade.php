@extends('layouts.app')

@section('content')
    <form method="POST" action="{{ route('login.magic.send') }}">
        @csrf
        <input type="email" name="email" required>
        <button type="submit">Send Magic Link</button>
    </form>
@endsection
