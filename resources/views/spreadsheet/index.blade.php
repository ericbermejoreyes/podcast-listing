@extends('shared.base')

@section('title', 'Podcast Listing')

@section('content')
    <!--Add buttons to initiate auth sequence and sign out-->
    <div id="spreadsheet-authorization">
        <div>
            <h2 class="bold">User Authorization</h2><br>
            <p class="ta-left">Before you can proceed, this App requires your permission to give access to <br><span class="bold">Google Spreadsheets API</span> by clicking the button below</p>
            <button id="authorize-button" class="g-button mb-1 fill">Authorize</button>
            <p class="ta-left ts-italic">"<span class="bold">NOTE:</span> you can revoke this later on at anytime by clicking the "Sign Out" button at the top right corner of the page"</p>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/main.js') }}"></script>
@endsection