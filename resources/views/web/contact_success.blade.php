@extends('web.master.master')

@section('content')

    <div class="container p-5">
        <h2 class="text-center text-front">Sua mensagem foi enviada com sucesso! Em breve entraremos em contato.</h2>
        <p class="text-front text-center"><a href="{{ url()->previous() }}">... Continuar navegando!</a></p>
    </div>

@endsection
