<?php $__env->startSection('content'); ?>
<div class="container-fluid bg-no-overlay">
    <video autoplay muted loop id="myVideo">
        <source src="../video/bg.mp4" type="video/mp4">
        <source src="../video/bg.ogg" type="video/ogg">
        <source src="../video/bg.webm" type="video/webm">
        Your browser does not support HTML5 video.
    </video>
    <div class="container searchpad">
        <div class="row text-center">
            <h1>Passionate People From Everywhere</h1>
            <br><br>
            <form action="search">
                <div class="col-md-3"></div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="icon-addon addon-sm" style="border: 1px solid #fff;border-radius: 5px;">
                            <input type="text" name="name" placeholder="Search" class="form-control"
                                   style="background: transparent;color:#fff;" id="name">
                            <label for="name" class="glyphicon glyphicon-search" rel="tooltip" title="name"
                                   style="color: white;"></label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="icon-addon addon-sm" style="border: 1px solid #fff;border-radius: 5px;">
                            <input type="text" name="city" placeholder="City" class="form-control"
                                   style="background: transparent;color:#fff;" id="city">
                            <label for="city" class="glyphicon glyphicon-search" rel="tooltip" title="city"
                                   style="color: white;"></label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="submit" class="search-btn" value="Search"></input>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="row" style="padding: 30px;">
            <div class="col-md-4 text-center">
                <div class="box" style="min-height: 350px;">
                    <div class="box-content">
                        <h1 class="tag-title"><i class="glyphicon glyphicon-search" style="color:#000000;"></i></h1>
                        <h3>Find Services</h3>
                        <p>Easily find local barbers, lawyers, drivers and more self-employed professionals that will
                            best suit your needs. View their profile , read customer reviews and create great business
                            relationships .</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="box" style="min-height: 350px;">
                    <div class="box-content">
                        <h1 class="tag-title"><span class="glyphicon glyphicon-time"></span></h1>
                        <h3>24/7 in Services</h3>
                        <p>View availability and simply book an appointment at anytime of the day . Receive
                            notifications by email and on the website, reminding you all of your affairs.</p>

                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="box" style="min-height: 350px;">
                    <div class="box-content">
                        <h1 class="tag-title"><span class="glyphicon glyphicon-heart"></span></h1>
                        <h3>Favourites</h3>
                        <p>Create your own contact list by following your favourite professionals. Stay updated and
                            immediately receive all of their updates on your timeline .</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- LOGIN MODAL -->
<?php echo $__env->make('modals.login', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<!-- SIGNUP MODAL -->
<?php echo $__env->make('modals.signup', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<!-- MESSAGE MODAL -->
<?php echo $__env->make('modals.message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<script src="js/home.js" type="text/javascript"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.default', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>