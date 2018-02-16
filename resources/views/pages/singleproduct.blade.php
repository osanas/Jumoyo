@extends('layouts.default')

@section('content')
@include('includes.searchbar')
<div class="container-fluid single-product-details">
    <div class="container">
        <div class="col-md-8 col-xs-12">
            <div class=" single-content-left">
                <div class="single-heading list">
                    <h4 class="personnameclass">{{ $service->business_name }}</h4>
                    <span class="stars">
                        @if(empty($reviews))
                            <i class="fa fa-star-o" aria-hidden="true"></i>
                            <i class="fa fa-star-o" aria-hidden="true"></i>
                            <i class="fa fa-star-o" aria-hidden="true"></i>
                            <i class="fa fa-star-o" aria-hidden="true"></i>
                            <i class="fa fa-star-o" aria-hidden="true"></i>
                        @else
                            @for($i = 1; $i <= $avgStars; $i++)
                                <i class="fa fa-star" aria-hidden="true"></i>
                            @endfor
                            @if(fmod($avgStars,1) != 0)
                                <i class="fa fa-star-half" aria-hidden="true"></i>
                            @endif
                        @endif
                        <span>({{ count($reviews) }})</span>
                    </span>
                </div>
                @if(session()->get('id'))
                    @if(\App\Gateways\ClientGateway::isFav($service->service_id))
                        <div class="single-follow">
                            <a href="/service/favorite/{{$service->service_id}}">
                                <span class="glyphicon glyphicon-heart-empty"
                                      style="font-size: 200%"></span>
                            </a>
                        </div>
                    @else
                        <div class="single-follow">
                            <a href="/service/unfavorite/{{$service->service_id}}">
                                <span class="glyphicon glyphicon-heart"
                                      style="color: red; font-size: 200%"></span>
                            </a>
                        </div>
                    @endif
                @endif
            </div>
            <div class="clearfix"></div>
            <div class="single-image">
                @if($service->image_url)
                    <img src="{{ URL::asset($service->image_url) }}" class="img-responsive" style="width: 100%;">
                @else
                    <img src="{{ URL::asset('img/services/default.png') }}" class="img-responsive" style="width: 100%;">
                @endif
            </div>
        </div>
        <div class="col-md-4 col-xs-12" style="margin-top: 10%;">
            <div class=" right-content">
                <div class="right-details">
                    <i class="fa fa-location-arrow" aria-hidden="true"></i>
                    <span>{{ $service->address }}, {{ $service->city }}, {{ $service->state }}
                            , {{ $service->country }}</span>
                    <p>{{ $service->description }}</p>
                </div>
                <div class="right-details">
                    <i class="fa fa-phone" aria-hidden="true"></i>
                    <span id="clickseeno">{{ $service->mobile }}</span>
                </div>

                <div class="right-details">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                    <span id="clickseeemail">{{ $service->email }}</span>
                </div>

                <div id="timeDiv" class="right-details">
                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                    <span> Business Hour</span>
                </div>

                <div class="right-details">
                    <i class="fa fa-user" aria-hidden="true"></i>
                    <span>Service</span>
                    <div class="right-sub-main">
                        <span class="right-sub-left">{{ $service->services_name }}</span>
                        @if($service->price != 0 && $service->price_hourly != 0)
                        <span class="right-sub-right">{{ $service->price }}$ | {{ $service->price_hourly }}
                                    $/hour</span>
                        @elseif($service->price_hourly != 0)
                        <span class="right-sub-right">{{ $service->price_hourly }}$/hour</span>
                        @else
                        <span class="right-sub-right">{{ $service->price }}$</span>
                        @endif
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="right-details">
                    @if(!session()->get('id')) Please log in to book. @endif
                    <a href="#">
                        <button @if(!session()->get('id')) disabled @endif
                            id="bookbtn" class="btn btn-primary bookbtn bootbutton"
                            data="{{$service->service_id}}">Book Service
                        </button>
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid work-carousel">
    <div class="container">
        <div class="col-md-12">
            <h2> Posts </h2>
            @foreach ($data as $post)
            <div class="widget bg-white overflow-hidden post post-preview" data-post-id="{{$post->post_id}}">
                <span class="widget-title">{{ $post->title }}</span>
                <p id="serviceName">{{ $post->name }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
<div class="container-fluid work-carousel">
    <div class="container">
        <div class="col-xs-12">
            <h2> Portfolio </h2>
        </div>
        <div class="col-xs-12 carousel-slider">
            <div class="carousel-logo">
                <div class="carousel slide" id="myCarousel">
                    <div class="carousel-inner" style="margin: 0 auto; max-width: 50%;">
                    </div>
                </div>
                <div class="carousel-btns">
                    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                    </a>
                    <a class="right carousel-control" href="#myCarousel" data-slide="next">
                        <i class="glyphicon glyphicon-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="container-fluid work-carousel">
    <div class="container">
        <div class="col-xs-12">
            <h2> Reviews </h2>
            @if(session()->get('id'))
            <div class="add-review">
                <button  id="reviewBtn" class="btn-primary bookbtn addreview" type="button"
                         data-serviceId="{{ $service->service_id }}"> Add Review
                </button>
            </div>
            @endif
        </div>
        <div class="clearfix"></div>
        @if(count($reviews) === 0)
        <div>There are no reviews yet.</div>
        @endif
        @foreach($reviews as $value)
        <div class="col-xs-12 single-review">
            <div class="review-profile">
                @if($value->image !== null)
                <img class="img-rounded" src="{{URL::asset($value->image)}}">
                @else
                <img class="img-rounded" src="{{URL::asset('/img/review-icon.png')}}">
                @endif
            </div>
            <div class="reviewer">
                <h4>{{ $value->first_name }}</h4>
                <span class="stars">
                            @for($i = 1; $i <= $value->stars; $i++)
                                <i class="fa fa-star" aria-hidden="true"></i>
                            @endfor

                            @if(fmod($value->stars,1) != 0)
                                <i class="fa fa-star-half" aria-hidden="true"></i>
                            @endif
                        </span>
                <p>{{ $value->text }}</p>
                <span>{{ $value->date_time }}</span>
                @if(session('id') == $value->reviewer_id)
                <span><br><a href="/delete/{{ $value->review_id }}" id="deleteReview">Delete</a></span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- LOGIN MODAL -->
@include('modals.login')

<!-- SIGNUP MODAL -->
@include('modals.signup')

<!-- BOOK MODAL -->
@include('modals.book')

<!-- BOOKDETAILS MODAL -->
@include('modals.bookdetails')

<!-- BOOKCONF MODAL -->
@include('modals.confirmdetails')

<!-- REVIEW MODAL -->
@include('modals.review')

<!-- POST MODAL -->
@include('modals.viewpost')

<script src="{{ URL::asset('js/singleProduct.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('js/review.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    days = '{{ $service->days }}';
    start = '{{ $service->start_time }}';
    end = '{{ $service->end_time }}';
</script>
@stop
