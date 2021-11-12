@include('adminPartial.nav')
<title>Admin | Japcom</title>
        <!-- Sidebar Area End Here -->
        <div class="dashboard-content-one">
            <!-- Breadcubs Area Start Here -->
            <div class="breadcrumbs-area">
                <h3>Admin Dashboard</h3>
                <ul>
                    <li>
                        <a href="index.html">Home</a>
                    </li>
                    <li>Admin</li>
                </ul>
            </div>
            @include('flash-message');
            <!-- Breadcubs Area End Here -->
            <!-- Dashboard summery Start Here -->
            @if(is_null($notice))
            <div class="row gutters-20">
                <h5>Published at:</h5>
                <div class="col-xl-12 col-sm-12 col-12">
                    <div class="dashboard-summery-one mg-b-20">
                        <div class="row align-items-center">
                            @if(\Illuminate\Support\Facades\Auth::user()->role==0)
                            <button class="btn btn-success" data-toggle="modal" data-target="#exampleModal">Add Notice</button>
                            @endif
                                <div class="col-6" id="pot">
                                <h4 style="color: red"><b>Notice Board</b></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
                <div class="row gutters-20">
                    <h5>Published at:<b>{{$notice->date}}</b></h5>
                    <div class="col-xl-12 col-sm-12 col-12">
                        @if(\Illuminate\Support\Facades\Auth::user()->role==0)
                            <button class="btn btn-success" data-toggle="modal" data-target="#exampleModal">Add Notice</button>
                            <form action="{{url('deleteNotice',$notice->id)}}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-danger">Remove Notice</button>
                            </form>
                        @endif
                        <div class="dashboard-summery-one mg-b-20">
                            <div class="row align-items-center">
                                <div class="col-6" id="pot">
                                    <h4 style="color: red"><b>{{$notice->message}}</b></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endif
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Post Notice</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{route('notice')}}" method="post">
                            @csrf
                        <div class="modal-body">
                                <div class="form-group">
                                    <label for="message-text" class="col-form-label">Message:</label>
                                    <textarea class="form-control" id="message-text" name="message"></textarea>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Send Notice</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <style>
                #pot {
                    bottom: 15%;
                    position: absolute;
                    -webkit-animation: linear infinite;
                    -webkit-animation-name: run;
                    -webkit-animation-duration: 20s;
                }
                @-webkit-keyframes run {
                    0% {
                        left: 0;
                    }
                    50% {
                        left: 100%;
                    }
                    100% {
                        left: 0;
                    }
                }
            </style>
            <div class="row gutters-20">
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card dashboard-card-seven">
                        <div class="social-media bg-fb hover-fb">
                            <div class="media media-none--lg">
                                <div class="media-body space-sm">
                                    <h6 class="item-title">Customers</h6>
                                </div>
                            </div>
                            <div class="social-like">{{\App\Models\User::where('role',2)->count()}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card dashboard-card-seven">
                        <div class="social-media bg-twitter hover-twitter">
                            <div class="media media-none--lg">

                                <div class="media-body space-sm">
                                    <h6 class="item-title">Users</h6>
                                </div>
                            </div>
                            <div class="social-like">{{\App\Models\User::where('role',1)->count()}}</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Dashboard summery End Here -->
            <!-- Dashboard Content Start Here -->
            <div class="row gutters-20" id="scrollToReports">
                    <div class="col-12 col-xl-4 col-3-xxxl">
                        <div class="card dashboard-card-two pd-b-20">
                            <div class="card-body">
                                <div class="heading-layout1">
                                    <div class="item-title" id="hideMonth">
                                        <h3><b>{{\Carbon\Carbon::now()->format('F-Y')}}</b> Net Income</h3>
                                    </div>
                                    <div class="item-title" id="showMonth">
                                    </div>
                                    <div class="dropdown">
                                        <div class="row">

                                        </div>
                                    </div>
                                </div>
                                <div id="basic">
                                <div class="row w-row" id="report">
                                    <div class="basic-column w-col w-col-3">
                                        <div class="tag-wrapper">
                                            <div class="number-card number-card-content1">
                                                <h6  class="number-card-number">KSH<br> <b style="font-size: 20px">{{$net}}</b>   </h6>
                                                <div class="number-card-divider"></div>
                                                <div class="number-card-progress-wrapper">
                                                    <div class="tagline number-card-currency">NET INCOME</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="basic-column w-col w-col-3">
                                        <div class="tag-wrapper">
                                            <div class="number-card number-card-content2">
                                                <h6 class="number-card-number">KSH<br> <b style="font-size: 20px">{{$mpesa}}</b></h6>
                                                <div class="number-card-divider"></div>
                                                <div class="number-card-progress-wrapper">
                                                    <div class="tagline number-card-currency">MPESA INCOME</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="basic-column w-col w-col-3">
                                        <div class="tag-wrapper">
                                            <div class="number-card number-card-content3">
                                                <h6 class="number-card-number">kSH<br> <b style="font-size: 20px">{{$cash}}</b></h6>
                                                <div class="number-card-divider"></div>
                                                <div class="number-card-progress-wrapper">
                                                    <div class="tagline number-card-currency">CASH INCOME</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="basic-column w-col w-col-3">
                                        <div class="tag-wrapper">
                                            <div class="number-card number-card-content4">
                                                <h6 class="number-card-number">KSH<br> <b style="font-size: 20px">{{$expense}}</b></h6>
                                                <div class="number-card-divider"></div>
                                                <div class="number-card-progress-wrapper">
                                                    <div class="tagline number-card-currency">EXPENSES INCURRED</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6 col-3-xxxl">
                        <div class="card dashboard-card-three pd-b-20">
                            <div class="card-body">
                                <div class="heading-layout1">
                                    <div class="item-title">
                                        <h3>Customer</h3>
                                    </div>
                                    <div class="dropdown">
                                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                                           aria-expanded="false">...</a>

                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#"><i
                                                    class="fas fa-times text-orange-red"></i>Close</a>
                                            <a class="dropdown-item" href="#"><i
                                                    class="fas fa-cogs text-dark-pastel-green"></i>Edit</a>
                                            <a class="dropdown-item" href="#"><i
                                                    class="fas fa-redo-alt text-orange-peel"></i>Refresh</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="doughnut-chart-wrap">
                                    <canvas id="student-doughnut-chart" width="100" height="300"></canvas>
                                </div>
                                <div class="student-report">
                                    <div class="student-count pseudo-bg-blue">
                                        <h4 class="item-title">Paid Customers</h4>
                                        <div class="item-number">45,000</div>
                                    </div>
                                    <div class="student-count pseudo-bg-yellow">
                                        <h4 class="item-title">Unpaid Customers</h4>
                                        <div class="item-number">1,05,000</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6 col-4-xxxl">
                        <div class="card dashboard-card-four pd-b-20">
                            <div class="card-body">
                                <div class="heading-layout1">
                                    <div class="item-title">
                                        <h3>Event Calender</h3>
                                    </div>
                                    <div class="dropdown">
                                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                                           aria-expanded="false">...</a>

                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#"><i
                                                    class="fas fa-times text-orange-red"></i>Close</a>
                                            <a class="dropdown-item" href="#"><i
                                                    class="fas fa-cogs text-dark-pastel-green"></i>Edit</a>
                                            <a class="dropdown-item" href="#"><i
                                                    class="fas fa-redo-alt text-orange-peel"></i>Refresh</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="calender-wrap">
                                    <div id="fc-calender" class="fc-calender"></div>
                                </div>
                            </div>
                        </div>
                    </div>

            </div>
            <!-- Dashboard Content End Here -->
            <!-- Social Media Start Here -->
            <!-- Social Media End Here -->
            <!-- Footer Area Start Here -->
            <footer class="footer-wrap-layout1">
                <div class="copyright">© Copyrights <a href="#">Japcom Networks</a> 2021. All rights reserved. Designed by <a
                        href="#">Japcom Networks</a></div>
            </footer>
            <!-- Footer Area End Here -->
        </div>
    </div>
    <!-- Page Area End Here -->
</div>
<style>

    h1{
        font-size:2em;
        margin:.67em 0;
    }
    *{
        -webkit-box-sizing:border-box;
        -moz-box-sizing:border-box;
        box-sizing:border-box;
        font-family: Roboto, sans-serif;
    }
    h1{
        font-weight:bold;
        margin-bottom:10px;
    }
    h1{
        font-size:38px;
        line-height:44px;
        margin-top:20px;
    }
    .w-row:before,.w-row:after{
        content:" ";
        display:table;
    }
    .w-row:after{
        clear:both;
    }
    .w-col{
        position:relative;
        float:left;
        width:100%;
        min-height:1px;
        padding-left:10px;
        padding-right:10px;
    }
    .w-col-3{
        width:25%;
    }
    @media screen and (max-width:767px){
        .w-row{
            margin-left:0;
            margin-right:0;
        }
        .w-col{
            width:100%;
            left:auto;
            right:auto;
        }
    }
    @media screen and (max-width:479px){
        .w-col{
            width:100%;
        }
    }
    h1{
        margin-top:15px;
        margin-bottom:15px;
        font-size:42px;
        line-height:54px;
        font-weight:400;
    }
    .divider{
        height:1px;
        margin-top:20px;
        margin-bottom:15px;
        background-color:#eee;
    }
    .style-label{
        color:#bebebe;
        font-size:12px;
        line-height:20px;
        font-weight:500;
        text-transform:uppercase;
    }
    .tag-wrapper{
        margin-top:35px;
        margin-bottom:35px;
        padding-right:5px;
        padding-left:5px;
    }
    .row{
        margin-bottom:50px;
    }
    .number-card-number{
        margin-top:0px;
        margin-bottom:0px;
        color:#fff;
        font-weight:300;
    }
    .tagline{
        font-size:12px;
        font-weight:500;
        letter-spacing:2px;
        text-transform:uppercase;
    }
    .tagline.number-card-currency{
        color:#fff;
    }
    .basic-column{
        padding-right:5px;
        padding-left:5px;
    }
    .number-card{
        padding:22px 30px;
        border-radius:8px;
        background-image:-webkit-linear-gradient(270deg, #1991eb, #1991eb);
        background-image:linear-gradient(180deg, #1991eb, #1991eb);
    }
    .number-card.number-card-content3{
        background-image:-webkit-linear-gradient(270deg, #ed629a, #c850c0);
        background-image:linear-gradient(180deg, #ed629a, #c850c0);
    }
    .number-card.number-card-content4{
        background-image:-webkit-linear-gradient(270deg, #ff8308, #fd4f00);
        background-image:linear-gradient(180deg, #ff8308, #fd4f00);
    }
    .number-card.number-card-content2{
        display:block;
        background-image:-webkit-linear-gradient(270deg, #17cec4, #17cec4 0%, #08aeea);
        background-image:linear-gradient(180deg, #17cec4, #17cec4 0%, #08aeea);
        color:#333;
    }
    .number-card.number-card-content1{
        background-image:-webkit-linear-gradient(270deg, #7042bf, #3023ae);
        background-image:linear-gradient(180deg, #7042bf, #3023ae);
    }
    .number-card-progress-wrapper{
        display:-webkit-box;
        display:-webkit-flex;
        display:-ms-flexbox;
        display:flex;
        -webkit-box-pack:justify;
        -webkit-justify-content:space-between;
        -ms-flex-pack:justify;
        justify-content:space-between;
    }
    .number-card-divider{
        height:1px;
        margin-top:10px;
        margin-bottom:14px;
        background-color:hsla(0, 0%, 100%, .15);
    }
    .number-card-dollars{
        color:hsla(0, 0%, 100%, .8);
        font-size:16px;
        line-height:24px;
    }
    .number-card-progress{
        color:#fff;
        text-align:right;
    }
    @media (max-width: 991px){
        .number-card-number{
            font-size:30px;
        }
        .number-card{
            padding-top:12px;
            padding-bottom:16px;
        }
    }

</style>
<!-- jquery-->
<script src="js/jquery-3.3.1.min.js"></script>
<!-- Plugins js -->
<script src="js/plugins.js"></script>
<!-- Popper js -->
<script src="js/popper.min.js"></script>
<!-- Bootstrap js -->
<script src="js/bootstrap.min.js"></script>
<!-- Counterup Js -->
<script src="js/jquery.counterup.min.js"></script>
<!-- Moment Js -->
<script src="js/moment.min.js"></script>
<!-- Waypoints Js -->
<script src="js/jquery.waypoints.min.js"></script>
<!-- Scroll Up Js -->
<script src="js/jquery.scrollUp.min.js"></script>
<!-- Full Calender Js -->
<script src="js/fullcalendar.min.js"></script>
<!-- Chart Js -->
<script src="js/Chart.min.js"></script>
<!-- Custom Js -->
<script src="js/main.js"></script>

</body>
<script>
    $('#month').on('change',function () {
        $('#report').hide();
        $month = $(this).val();
        $year = $('#year').val();
        $.ajax({
            type:"get",
            url:"{{url('ajax')}}",
            data:{'yeah':$year,'month':$month},
            success:function (data) {
               $('#basic').html(data);
                $.ajax({
                    type:"get",
                    url:"{{url('showMonth')}}",
                    data:{'yeah':$year,'month':$month},
                    success:function (data) {
                        $('#hideMonth').hide();
                        $('#showMonth').html(data);
                    },
                    error:function (error) {
                        console.log(error)
                        alert('error')

                    }

                });
                $('html, body').animate({
                    scrollTop: $("#scrollToReports").offset().top
                }, 500);
            },
            error:function (error) {
                console.log(error)
                alert('error')

            }

        });
    });
</script>


<!-- Mirrored from www.radiustheme.com/demo/html/psdboss/akkhor/akkhor/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Jun 2021 10:34:59 GMT -->
</html>
