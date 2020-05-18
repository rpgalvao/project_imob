@component('mail::message')

# Novo contato

<p>Contato: {{ $name }}</p>
<p>E-mail: {{ $email }}</p>
<p>Telefone: {{ $cell }}</p>
<p>Mensagem</p>
<p>{{ $message }}</p>

<p><small>Esse e-mail é enviado automaticamente pelo sistema</small></p>

@endcomponent
