@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto mt-10 text-center">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <h2 class="text-xl font-bold mb-2">✅ Оплата прошла успешно!</h2>
            <p>Ваша Premium подписка активирована.</p>
            <a href="{{ route('converter') }}" class="mt-4 inline-block bg-green-600 text-white px-4 py-2 rounded">Вернуться в редактор</a>
        </div>
    </div>
@endsection
