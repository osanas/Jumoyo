var picker = undefined;
var serviceInfo = undefined;

$(function() {
    getPortfolio($("#bookbtn").attr("data"));

    $("div[contenteditable=true]").on("paste", function (e) {
        e.stopPropagation();
        e.preventDefault();
        var toAdd = e.originalEvent.clipboardData.getData("text/plain");
        document.execCommand("insertHTML", false, toAdd);
    });

    $(".post-preview").on("click", function (e) {
        e.preventDefault();
        // Load Post
        var request = $.getJSON("/post/" + $(this).data("postId"));

        request.done(function (data) {
            displayComments(data[1]);
           
            // Change modal content
            $("#view-post #postTitle").text(data[0].title);
            $("#view-post #serviceName").text(data[0].name);
            $("#view-post #postText").text(data[0].text);
            
            if (data[0].url) {
                var video = $('<iframe width="560" height="315" src="https://www.youtube.com/embed/' + data[0].url + ' " frameborder="0" allow="encrypted-media" allowfullscreen></iframe>');
                $("#view-post #postText").append("<br>");
                $("#view-post #postText").append(video);
                
                // Remove video on modal close, to prevent ongoing audio 
                $("#view-post").on("hidden.bs.modal", function (e) {
                    $("#view-post #postText").find(video).remove();
                });
            }

            if (data[0].image) {
                var image = $('<div><img src="/' + data[0].image + '"></div>');
                $("#view-post #postText").append("<br>");
                $("#view-post #postText").append(image);
            }
            $("#view-post .post-icons a[data-type]")[0].dataset.type = "like";
            $("#view-post .post-icons a[data-type]").removeData("type");
            $("#view-post .post-icons a[data-likes]")[0].dataset.likes = data[0].like_count;
            $("#view-post .post-icons a[data-likes]").removeData("likes");
            $("#view-post .post-icons a[data-post-id]")[0].dataset.postId = data[0].post_id;
            $("#view-post .post-icons a[data-post-id]").removeData("postId");
            
            $(".comment-composer .postPostComment[data-post-id]")[0].dataset.postId = data[0].post_id;
            $(".comment-composer .postPostComment[data-post-id]").removeData("postId");
            
            // Show modal
            $("#view-post").modal();
        });

        request.fail(function() {
            alert("You must be logged in to view a post.");
        });
    });

    $(".post-icons > a").on("click", function (e) {
        e.preventDefault();
        if (this.dataset.type == "like") {
            var request = $.post(
                "/post/" + this.dataset.postId, 
                {
                    "action": "like"
                }
            );
            var element = this;

            request.done(function(likeResult) {
                if (likeResult.currentStatus) {
                    $(element).children().removeClass("fa-thumbs-o-up").addClass("fa-thumbs-up");
                    element.dataset.likes++;
                } else {
                    $(element).children().removeClass("fa-thumbs-up").addClass("fa-thumbs-o-up");
                    element.dataset.likes--;
                }
            });
        }
    });

    $(".postPostComment").on("click", function (e) {
        e.preventDefault();
        if ($(".comment-composer-text").text().length == 0) {
            return;
        }
        var text = $(".comment-composer-text")[0].innerText.trim();
        var request = $.post(
            "/post/" + this.dataset.postId, {
                "action": "comment",
                "comment": {
                    "text": text
                } 
            }
        );

        request.done(function(data) {
            $(".comment-composer-text").text("");
            displayComments(data);
        });
    });

    var start_time = start.split(" ");
    var end_time = end.split(" ");

    if(start_time.length !== 7)
        start_time.splice(0, 1);
    if(end_time.length !== 7)
        end_time.splice(0, 1);

    for(var i = 0; i < days.length; i++) {
        var jour = "";
        switch(i) {
            case 0:
                jour = "SUN";
                break;
            case 1:
                jour = "MON";
                break;
            case 2:
                jour = "TUE";
                break;
            case 3:
                jour = "WED";
                break;
            case 4:
                jour = "THU";
                break;
            case 5:
                jour = "FRI";
                break;
            case 6:
                jour = "SAT";
                break;
        }

        var debut = msToTime(parseInt(start_time[i]));
        var fin = msToTime(parseInt(end_time[i]));

        var template = '<div id="business-hours" class="right-sub-main">' +
            '               <span class="right-sub-left">' + jour + '</span>' +
            '               <span class="right-sub-right">' + debut + ' - ' + fin + '</span>' +
            '           </div>' +
            '           <div class="clearfix"></div>';

        if(days.charAt(i) !== "0")
            $("#timeDiv").append($(template));
    }

    $("form#bookForm").on("submit", function(e){
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax
        ({
            type: "POST",
            url: '/book',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            success: function (response) {
                alert("Your booking was successful.");
                location.reload();
            },
            error: function (response) {
                alert("There was an error while trying to book.");
            }
        });
    });

    $("#bookbtn").click(function(){
        var eyed = $(this).attr("data");
        $.ajax
        ({
            type: "GET",
            url: '/service',
            contentType: 'application/json',
            data: {"id": eyed},
            success: function (response) {
                var data = JSON.parse(response);
                serviceInfo = data;
                var days = [];
                for (var i = 0; i < data.days.length; i++) {
                    if (data.days.charAt(i) === '0') {
                        days.push(i + 1);
                    }
                }

                var input = $('.date-input').pickadate({
                    container: '#date-picker',
                    min: new Date(),
                    disable: days,
                    selectYears: true,
                    selectMonths: true
                });
                picker = input.pickadate('picker');

                $('.date-input').off('click focus');

                $('#date').on('click', function (e) {
                    if (picker.get('open')) {
                        picker.close()
                    } else {
                        picker.open()
                    }

                    e.stopPropagation()
                });

                $('#date').on('change', function(){
                    getBookings(picker, eyed);
                });
            }
        });
        $('#pickup').modal();
    });

    $("#reviewBtn").click(function(){
        $("#reviewModel").modal();
    });

    $("#fav").click(function(){
        var id = serviceInfo.service_id;
        var location = "/services/favorite/" + id;
        window.location = location;
    });

    $("#unfav").click(function(){
        var id = serviceInfo.service_id;
        var location = "/services/unfavorite/" + id;
        window.location = location;
    });

    $("#date").on("change", function(){
        var day = picker.get('select').obj.getDay();
        parseTime(day, serviceInfo.start_time, serviceInfo.end_time, serviceInfo.days);
    });

    $("#confirmDetailsBtn").click(function(){
        if(picker.get('select') !== null) {
            var id = serviceInfo.service_id;
            var date = picker.get('select').obj.getTime();
            var debut = parseInt($("#start").find(":selected").val());
            var end = parseInt($("#end").find(":selected").val());
            var startIsBad = $("#start").find("option[value='" + debut + "']").prop("disabled");
            var endIsBad = $("#end").find("option[value='" + end + "']").prop("disabled");

            if (debut >= end || startIsBad || endIsBad) {
                $("#dispError").text("Invalid time.");
            }
            else {
                $("#dispError").text("");

                $("#confirmationDate").val(date);
                $("#confirmationStart").val(debut);
                $("#confirmationEnd").val(end);
                $("#Idservice").val(id);

                var ddd = new Date(date);
                var d = new Date(debut);
                var e = new Date(end);

                $("#confirm-service").text(serviceInfo.services_name + " by " + serviceInfo.business_name);
                $("#confirm-address").text(serviceInfo.address + ", " + serviceInfo.city + ", " + serviceInfo.postal_code + ", " + serviceInfo.country);
                $("#confirm-datetime").text(new Date(date).toDateString());
                $("#confirm-servicename").text(serviceInfo.services_name);
                $("#confirm-time").text(msToTime(debut) + " to " + msToTime(end));

                var total = 0;
                if(serviceInfo.price_hourly != 0) {
                    var hours = Math.abs( d - e ) / 36e5;
                    total = hours * serviceInfo.price_hourly + serviceInfo.price;
                }
                else
                    total = serviceInfo.price;
                $("#confirm-price").text(total + "$");
                $("#price").val(total);
                $("#confirmdetail").modal();
            }
        }
        else {
            $("#dispError").text("Please select a date.");
        }
    });
});

function getPortfolio(id) {
    $.ajax
    ({
        type: "GET",
        url: '/api/portfolio',
        contentType: 'application/json',
        data: { "id": id },
        success: function(response) {
            var data = JSON.parse(response);
            if(data.length <= 1)
                if(data.length === 0 || !data[0].name)
                    $('.carousel-inner').append('<div>There are no pictures in the portfolio</div>');
            $.each(data, function(index, value){
                if(value.name !== null) {
                    var template = '<div class="item">';
                    if($('.carousel-inner .item').length === 0)
                        template = '<div class="item active">';
                    template = template +
                        '<img src="/' + value.name + '" class="img-responsive"></div>';
                    $('.carousel-inner').append(template);
                }
                else if(value.url !== null) {
                    var template = '<div class="item">';
                    if(index === 0)
                        template = '<div class="item active">';
                    value.url = value.url.split("=")[1];
                    template = template +
                    '<iframe width="560" height="315" src="https://www.youtube.com/embed/' + value.url +
                        '" frameborder="0" allow="encrypted-media" allowfullscreen></iframe>';
                    $('.carousel-inner').append(template);
                }
            });

            return response;
        }
    });
}

function getBookings(picka, id) {
    $.ajax
    ({
        type: "GET",
        url: '/api/business/bookings',
        contentType: 'application/json',
        data: { "id": id },
        success: function(response) {
            var data = JSON.parse(response);
            $.each(data, function(key, value) {
                var fin = parseInt(value.end);
                var debut = parseInt(value.start);

                if(parseInt(value.date) === picker.get('select').obj.getTime()) {
                    while (debut < fin) {
                        $("#start").find("option[value='" + debut + "']").attr("disabled", true);
                        $("#end").find("option[value='" + debut + "']").attr("disabled", true);
                        debut = debut + 1800000;
                    }
                }
            });
            return response;
        }
    });
}

function parseTime(day, start, end, days) {
    $("#start").empty();
    $("#end").empty();
    var start_time = start.split(" ");
    var end_time = end.split(" ");

    if(start_time.length !== 7)
        start_time.splice(0, 1);
    if(end_time.length !== 7)
        end_time.splice(0, 1);

    var startMs = parseInt(start_time[day]);
    var endMs = parseInt(end_time[day]);


    while(startMs !== (endMs + 1800000)) {
        var template = '<option value="' + startMs + '">' + msToTime(startMs) + '</option>';
        $("#start").append(template);
        $("#end").append(template);
        startMs = startMs + 1800000;
    }
}

function msToTime(s) {
    var date = new Date(s);
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = "am";

    if(hours === 0)
        hours = 12;
    else if(hours >= 12) {
        if(hours !== 12)
            hours = hours - 12;
        ampm = "pm";
    }

    if(minutes === 0)
        minutes = "00";

    return hours + ":" + minutes + ampm;
}

function displayComments(data) {
    $(".comments").empty();
    if (data.length == 0) {
        $(".comments").append("<p>No comments</p>");
    } else {
        data.forEach(function(element){
            var cmtimage = '';
            if(element.image !== null){
                cmtimage = "/"+element.image;
            }else{
                cmtimage = '/img/review-icon.png';
            }
            var tempDiv = $('<div class="single-comment"></div>');
            var commenter = $('<div class="commenter"></div>');
            var imageDiv = $('<div class="review-profile"><img src="'+ cmtimage+ '" id="cmt-img" class="img-rounded"></div>')
            $(commenter).text(element.first_name + " " + element.last_name + " ");
            commenter.append('<span class="comment-time">haha</span>');


            tempDiv.append(imageDiv);
            tempDiv.append(commenter);
            tempDiv.append('<p></p>');

            //TODO : Convert time to local time
            $(tempDiv).find(".comment-time").text(convertTime(element.date_time));
            $(tempDiv).find("p").text(element.text);
            $(".comments").append(tempDiv);
        });
    }
}

function convertTime(time) {
    time = time.replace(/-/g, "/");
    var d = new Date(time + " UTC");
    return d.toLocaleString('en-CA');
}