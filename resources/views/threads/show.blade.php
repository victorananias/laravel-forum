@extends('layouts.app')

@section('content')
<thread-view inline-template :initial-replies-count="{{ $thread->replies_count }}">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-dark text-light">
                        <div class="level">
                            <span class="flex">
                                <a class="font-weight-bold" href="/profiles/{{ $thread->creator->name }}">{{ $thread->creator->name }}</a> publicou:
                                {{ $thread->title }}
                            </span>
                            @can('update', $thread)
                                <form action="{{ $thread->path() }}" method="POST">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="btn btn-link">Delete</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        {{ $thread->body }}
                    </div>
                </div>

                <replies :data="{{ $thread->replies }}" @removed="repliesCount--"></replies>

                @if(auth()->check())
                    <form action="{{ $thread->path().'/replies' }}" method="POST">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <textarea class="form-control" name="body" rows="5" 
                                placeholder="Tem algo a dizer?"></textarea>
                        </div>
                        <button type="submit" class="btn btn-dark float-right">Responder</button>
                    </form>
                @else 
                    <p class="text-center"><a href="{{ route('login') }}">Entre</a> para participar da discução.</p>
                @endif

            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        Essa thread foi publicada {{ $thread->created_at->diffForHumans() }} por 
                        <a href="/profiles/{{ $thread->creator->name }}">{{ $thread->creator->name }}</a> 
                        e atualmente possui <span v-text="repliesCount"></span> {{ str_plural('resposta', $thread->replies_count) }}.
                    </div>
                </div>
            </div>
        </div>
    </div>
</thread-view>
<flash message="{{ session('aviso') }}"></flash>
@endsection
