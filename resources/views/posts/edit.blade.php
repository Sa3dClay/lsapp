@extends('layouts.app')

@section('content')

    <div class="container py-4">
        <a href="{{ url('/posts'. '/' . $post->id) }}" class="btn btn-success mybtn">Go back</a>

        <h1 class="text-center hpc">Edit Post</h1>
        {!! Form::open([
            'action' => ['PostsController@update', $post->id],
            'id' => 'editForm',
            'method' => 'POST',
            'files' => true
        ]) !!}
            {{ csrf_field() }}

            <div class="form-group">
                {{ Form::label('title', 'Title', ['class' => 'control-label']) }}
                {{ Form::text('title', $post->title, ['class' => 'form-control', 'placeholder' => 'Title', 'required']) }}
            </div>

            <div class="form-group">
                {{ Form::label('category', 'Category', ['class' => 'control-label']) }}
                {{ Form::select('category', array(
                    'info' => 'Information',
                    'cs' => 'Computer science & IT',
                    'pd' => 'Problem discussion',
                    'love' => 'Love',
                    'marketing' =>'Marketing',
                    'social' => 'Social media',
                    'news' => 'News',
                    'other' => 'Other'
                    ),'other', ['class' => 'form-control']
                ) }}
            </div>

            <div class="form-group">
                {{ Form::label('body', 'Body', ['class' => 'control-label']) }}
                {{ Form::textarea('body', $post->body, ['class' => 'form-control', 'id'=>'CKEditor', 'rows'=>'4', 'placeholder' => 'Body', 'required']) }}
            </div>

            <a class="btn btn-sm btn-success hidden" data-toggle="collapse" href="#upload" role="button" aria-expanded="false" aria-controls="upload">
                Upload Image
            </a>
            <a class="btn btn-sm btn-danger" data-toggle="collapse" href="#select" role="button" aria-expanded="false" aria-controls="select">
                Select Image
            </a>

            <div class="form-group collapse" id="upload">
                <div class="custom-file ">
                    <p>Upload your image</p>
                    {{ Form::file('image') }}
                    <i class="fas fa-upload fa-2x"></i>
                </div>
                
                <small>Note: If you uploaded an image then selected an image, the selected image will be discarded</small>
            </div>

            <?php
            $directory = "uploads/*.*";
            $images = glob($directory);
            ?>

            <div class="row justify-content-center imgSelect collapse" id="select">
                @foreach($images as $image)
                    <?php
                    $splName = explode('/', $image);
                    $imgName = $splName[1];
                    ?>

                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <label>
                            {{ Form::radio('image_select', $imgName) }}
                            <img src="{{ asset('/' . $image) }}" class="img-fluid img-thumbnail" />
                        </label>
                    </div>
                @endforeach
            </div>

            {{ Form::hidden('_method', 'PUT') }}

            <button type="button" id="submitEdit" class="btn btn-sm btn-primary">Update</button>
        {!! Form::close() !!}
    </div>

    {{-- JS --}}
    <script src="https://cdn.ckeditor.com/4.14.0/full/ckeditor.js"></script>

    <script>
        CKEDITOR.replace( 'CKEditor' );

        $(function() {
            const x =  new RegExp("[\x00-\x80]+");

            $input = $('#title')
            
            if( $input.val() ) {
                var isAscii = x.test($input.val().charAt(0));
                // console.log($input.val().charAt(0), isAscii)

                if(isAscii) {
                    $input.css("direction", "ltr");
                    $input.css("text-align", "left")
                } else {
                    $input.css("direction", "rtl");
                    $input.css("text-align", "right")
                }
            }

            $('#title').on('keyup', function () {
                $this = $(this);

                if( $this.val() ) {
                    var isAscii = x.test($this.val().charAt(0));
                    // console.log(isAscii)

                    if(isAscii) {
                        $this.css("direction", "ltr");
                        $this.css("text-align", "left")
                    } else {
                        $this.css("direction", "rtl");
                        $this.css("text-align", "right")
                    }
                }
            })

            // prevent redundant requests
            $(function(){
                $("#submitEdit").on('click', function (e) {
                    e.preventDefault()

                    $("#submitEdit").attr("disabled", true)
                    $("#editForm").submit()
                });
            });
        })
    </script>

@endsection
