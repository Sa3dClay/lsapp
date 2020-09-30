@extends('layouts.app')

@section('content')
    <div class="jumbotron my-4 py-4 text-center">

        @if(count($notifications) > 0)
            <table class="table" id="reload">
                <h2>Notifications</h2>

                @foreach ($notifications as $notification)
                    <tr>
                        <td class="float-left">
                            {{ $notification->message }}

                            @if($notification->updated_at == $notification->created_at)
                                <i class="far fa-bell new-notification"></i>
                            @endif
                        </td>

                        <td class="float-right">{{ $notification->created_at }}<p><small>Type: {{$notification->type}}</small><p></td>
                    </tr>
                @endforeach
            </table>
        @else
            <h3>No actions occurred</h3>
        @endif

    </div>

    @include('notifications.reload')
@endsection
