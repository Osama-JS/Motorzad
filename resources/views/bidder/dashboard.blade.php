@extends('layouts.bidder')

@section('title', __('Bidder Dashboard'))

@section('content')
<div id="dashboard-container">
    @include('bidder.dashboard.partials.content')
</div>
@endsection
