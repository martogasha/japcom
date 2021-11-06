@include('adminPartial.nav')
<title>Customers List | Japcom</title>
<!------ Include the above in your HEAD tag ---------->

        <!-- Sidebar Area End Here -->
        <div class="dashboard-content-one">
            <!-- Breadcubs Area Start Here -->
            <div class="breadcrumbs-area">
                <h3>Customers</h3>
                <ul>
                    <li>
                        <a href="{{url('admin')}}">Home</a>
                    </li>
                    <li>All Customers</li>
                </ul>
            </div>
            <!-- Breadcubs Area End Here -->
            <!-- Student Table Area Start Here -->
            <div class="card height-auto">
                <div class="card-body">
                    <div class="heading-layout1">
                        <div class="item-title">
                            <h3>All Customer Data</h3>
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
                    <div class="row-fluid">
                        <div class="col-lg-12 col-12 form-group">
                            <label>Search</label>
                            <input type="text" placeholder="Search" class="form-control" id="myInput">
                        </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Status</th>
                                <th>Balance</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Package</th>
                                <th>Amount</th>
                                <th>Payment Date</th>
                                <th>Due Date</th>
                                <th>Condition</th>
                            </tr>
                            </thead>
                            <tbody id="myTable">
                            @foreach($customers as $customer)
                            <tr>
                                @if($customer->balance>0 && $customer->balance<$customer->package_amount)
                                    <td class="badge badge-pill badge-warning d-block mg-t-8">Pending</td>
                                @else
                                    @if($customer->balance<=0)
                                        <td class="badge badge-pill badge-success d-block mg-t-8">Paid</td>
                                    @else
                                        @if($customer->package_amount<=$customer->balance)
                                            <td class="badge badge-pill badge-danger d-block mg-t-8">UnPaid</td>
                                        @endif
                                    @endif
                                @endif
                                @if($customer->balance<=0)
                                    <td><b style="color: green">Ksh: {{$customer->balance}}</b></td>
                                @else
                                    <td><b style="color: red">Ksh: {{$customer->balance}}</b></td>

                                @endif
                                <td>{{$customer->first_name}} {{$customer->last_name}}</td>
                                <td>{{$customer->location}}</td>
                                <td>{{$customer->bandwidth}} Mbps</td>
                                    @if($customer->amount!=0)
                                <td>Ksh: {{$customer->amount}}</td>
                                    @else
                                        <td><span class="badge badge-danger">Not Paid</span></td>

                                    @endif
                                    @if($customer->payment_date==0)
                                <td><span class="badge badge-danger">Not Paid</span></td>
                                    @else
                                        <td>{{date('d/m/Y', strtotime($customer->payment_date))}}</td>
                                    @endif
                                    @if($customer->due_date==0)
                                        <td><span class="badge badge-danger">Not Paid</span>
                                        </td>
                                    @else
                                        <td>{{$customer->due_date}}</td>
                                    @endif
                                <td>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                           aria-expanded="false">
                                            <span class="flaticon-more-button-of-three-dots"></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{url('customerDetail',$customer->id)}}"><i
                                                    class="fas fa-times text-orange-red"></i>View</a>
                                            <a class="dropdown-item view" href="#updateDueDate" data-toggle="modal" data-target="#updateDueDate" id="{{$customer->id}}"><i
                                                    class="fas fa-cogs text-dark-pastel-green" ></i>Edit Due</a>


                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach

                                                    </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Student Table Area End Here -->
            <footer class="footer-wrap-layout1">
                <div class="copyright">© Copyrights <a href="#">akkhor</a> 2019. All rights reserved. Designed by <a
                        href="#">PsdBosS</a></div>
            </footer>
        </div>
    </div>
    <!-- Page Area End Here -->
</div>
<!-- Modal -->
<div class="modal fade" id="updateDueDate" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Update Due Date</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-lg-12 col-12 form-group">
                        <div class="form-group" id="basic">
                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateTimeButton">Save changes</button>
            </div>
        </div>
    </div>
</div>
<style>
    .select2-container .select2-selection--single{
        height:34px !important;
    }
    .select2-container--default .select2-selection--single{
        border: 1px solid #ccc !important;
        border-radius: 0px !important;
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
<!-- Scroll Up Js -->
<script src="js/jquery.scrollUp.min.js"></script>
<!-- Data Table Js -->
<script src="js/jquery.dataTables.min.js"></script>
<!-- Custom Js -->
<script src="js/main.js"></script>
</body>

<script>
    $(document).ready(function(){
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
<!-- Mirrored from www.radiustheme.com/demo/html/psdboss/akkhor/akkhor/all-student.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Jun 2021 10:35:18 GMT -->
</html>
