@extends('layouts.app')

@section('content')

    <div class="container">
        <a href="{{ url('/posts') }}" class="btn btn-success mybtn">Go back</a>
        
        <div class="post">
            <h2>{{$post->title}}</h2>
            <p>{!!$post->body!!}</p>
            @if(isset($post->image_name))
                <div class="post-img">
                    <img src="{{ asset('/uploads' . '/' . $post->image_name) }}" class="img-fluid" />
                    <span class="sp1"></span>
                    <span class="sp2"></span>
                    <span class="sp3"></span>
                    <span class="sp4"></span>
                    <i class="fab fa-staylinked fa-2x"></i>
                </div>
            @endif
            <hr>
            <small>Written on {{$post->created_at}} by {{$post->user->name}}</small>
        </div>
        
        @if(isset(auth()->user()->id))
            <div class="float-left">
                @if(isset($like))
                    {{-- {!! Form::open(['action' => ['PostsController@dislike', $like->id], 'method' => 'POST']) !!}
                        {{ Form::submit('Dislike', ['class' => 'btn btn-danger']) }}
                    {!! Form::close() !!} --}}

                    <button class="btn btn-danger" id="dislike_post_ajax">
                        <i class="far fa-thumbs-down"></i> Dislike
                    </button>

                    <button class="hidden btn btn-primary" id="like_post_ajax">
                        <i class="far fa-thumbs-up"></i> Like
                    </button>

                    <span class="hidden" id="like_id">{{ $like->id }}</span>
                @else
                    {{-- {!! Form::open(['action' => ['PostsController@like', $post->id], 'method' => 'POST']) !!}   
                        {{ Form::submit('Like', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!} --}}

                    <button class="btn btn-primary" id="like_post_ajax">
                        <i class="far fa-thumbs-up"></i> Like
                    </button>

                    <button class="hidden btn btn-danger" id="dislike_post_ajax">
                        <i class="far fa-thumbs-down"></i> Dislike
                    </button>

                    <span class="hidden" id="like_id"></span>
                @endif

                @if(isset($likes))
                    @if(count($likes) > 0)
                        <small role="button" class="btn btn-sm" id="count_likes" data-toggle="modal" data-target="#likesModal">
                            <span>{{ count($likes) }}</span> like this post
                        </small>
                    @else
                        <small role="button" class="btn btn-sm" id="count_likes" data-toggle="modal" data-target="#likesModal">
                            {{-- no comments --}}
                        </small>
                    @endif
                @endif
            </div>

            {{-- STR Likes Modal --}}
            <div class="modal fade" id="likesModal">
                <div class="modal-dialog modal-sm modal-dialog-scrollable modal-dialog-centered">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Who like this post</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    
                        <div class="modal-body">
                            <ul id="likers_list" class="list-group list-group-flush">
                                {{-- placed by js --}}
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
            {{-- END Likes Modal --}}

            {{-- STR Comments --}}
            <div class="float-right">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#commentsModal">
                    <i class="far fa-comment"></i> Comment
                </button>
            </div>

            {{-- Comments Modal --}}

            <div class="modal fade" id="commentsModal">
                <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                    @include('components.comments')
                </div>
            </div>
            {{-- END Comments --}}

            <div class="clearfix"></div><hr />
            
            <div class="mar-bot-20">
                @if(auth()->user()->id == $post->user_id)
                    <a href="/posts/{{$post->id}}/edit" class="btn btn-success">Edit</a>

                    {!! Form::open(['action' => ['PostsController@destroy', $post->id], 'method' => 'POST', 'class' => 'float-right']) !!}
                        {{ Form::hidden('_method', 'DELETE') }}
                        {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                    {!! Form::close() !!}
                @endif
            </div>
        @endif
    </div>

    {{-- JS --}}

    <script src="http://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=">
    </script>

    <script>
        $(function () {
            var post_id = {{ $post->id }}

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            // str like request
            $("#like_post_ajax").on('click', function (event) {
                event.preventDefault();
                // console.log(post_id)

                $.ajax({
                    url: "{{ url('/post/like') }}",
                    method: 'post',
                    data: {
                        post_id
                    },
                    success: function(response) {
                        // console.log(response)
                        $('#like_post_ajax').hide()
                        $('#dislike_post_ajax').show()

                        $("#like_id").text(response.like.id)
                        
                        var likes = response.likes
                        if(likes.length > 0) {
                            $("#count_likes").text(likes.length + ' like this post')
                        } else {
                            $("#count_likes").text('')
                        }
                    }
                })
            })
            // end like request

            // str dislike request
            $("#dislike_post_ajax").on('click', function (event) {
                event.preventDefault();
                
                var like_id = $('#like_id').text()
                // console.log(like_id)

                $.ajax({
                    url: "{{ url('/post/dislike') }}",
                    method: 'post',
                    data: {
                        like_id,
                        post_id
                    },
                    success: function(response) {
                        // console.log(response)
                        $('#like_post_ajax').show()
                        $('#dislike_post_ajax').hide()

                        var likes = response.likes
                        if(likes.length > 0) {
                            $("#count_likes").text(likes.length + ' like this post')
                        } else {
                            $("#count_likes").text('')
                        }
                    }
                })
            })
            // end dislike request

            // str getWhoLike request
            $("#count_likes").on('click', function (event) {
                event.preventDefault();

                $.ajax({
                    url: "{{ url('/post/getWhoLike') }}",
                    method: 'get',
                    data: {
                        post_id
                    },
                    success: (response) => {
                        console.log(response)

                        var likers = response.likers

                        if(likers && likers.length>0) {
                            $('#likers_list').text('')
                            
                            $.each(likers, (i, liker) => {
                                $('#likers_list').append(`
                                    <li class="list-group-item">` + liker.user_name + `</li>
                                `)
                            })
                        }
                    },
                    error: (error) => {
                        console.log(error)
                    }
                })
            })
            // end getWhoLike request
        })
    </script>

@endsection
