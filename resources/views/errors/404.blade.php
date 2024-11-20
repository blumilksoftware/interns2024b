@extends('errors.minimal')

@php
  $code = '404';
  $message = 'Strona nie istnieje';
  $description = 'Przepraszamy, nie mogliśmy znaleźć strony, której szukasz.';
  $link = ['url' => '/', 'text' => 'Wróć na stronę główną'];
@endphp
