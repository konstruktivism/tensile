@extends('mail.base')

@section('header')
    {{ __('Verify Email Address') }}
@endsection

@section('body')
    {{ __('Please click the button below to verify your email address.') }}
@endsection

@section('button_url')
    {{ $url }}
@endsection

@section('button_text')
    {{ __('Verify Account') }}
@endsection

@section('footer_image')
    {{ asset('images/icon-alert.png') }}
@endsection

@section('footer')
    {{ __('If you did not create an account, no further action is required.') }}
@endsection
