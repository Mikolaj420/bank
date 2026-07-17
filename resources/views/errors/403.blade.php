@extends('errors.layout')

@section('code', '403')
@section('heading', 'Brak uprawnień')
@section('message', $exception->getMessage() ?: 'Nie masz uprawnień do wyświetlenia tej strony.')
