@extends('errors.layout')

@section('code', '404')
@section('heading', 'Nie znaleziono')
@section('message', $exception->getMessage() ?: 'Strona lub zasób, którego szukasz, nie istnieje.')
