@extends('layout.Layout')

@section('title', 'Tickets')

@section('contenido')

    <script>
        const app = Vue.createApp({
            data() {
                return {
                    tickets: null,
                }
            },
            methods: {

            },
        })
    </script>
@endsection