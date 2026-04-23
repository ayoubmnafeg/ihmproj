@extends('layouts.app')

@section('title', 'Post')

@section('content')
<livewire:post-page :publication-id="$publicationId" />
@endsection
