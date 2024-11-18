@extends('errors.minimal')

@php
  $code = '500';
  $message = 'Wystąpił błąd serwera.';
  $description = 'Wystąpił nieoczekiwany problem po stronie serwera, który uniemożliwił przetworzenie Twojego żądania. Prosimy spróbować ponownie później.';
@endphp
