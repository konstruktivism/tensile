@extends('mail.base')

@section('header')
    {{ __('Login via e-mail') }}
@endsection

@section('body')
    {{ __('Please click the button below to login in to your account.') }}
@endsection

@section('button_url')
    {{ $url }}
@endsection

@section('button_text')
    {{ __('Login') }}
@endsection
