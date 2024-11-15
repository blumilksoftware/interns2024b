@extends('errors.minimal')

@php
  $code = '404';
  $message = 'Strona o podanym adresie nie istnieje.';
  $link = ['url' => '/', 'text' => 'Wróć na stronę główną'];
@endphp
