@include('adminPartial.nav')
<title>{{$user->first_name}} {{$user->last_name}} Statements - Japcom Networks</title>
        <!-- Sidebar Area End Here -->
        <div class="dashboard-content-one">
            <!-- Breadcubs Area Start Here -->
            <div class="breadcrumbs-area">
                <h3>{{$user->first_name}} {{$user->last_name}} Statements</h3>
                <ul>
                    <li>
                        <a href="index.html">Home</a>
                    </li>
                    <li>{{$user->first_name}} {{$user->last_name}}</li>
                </ul>
            </div>
            <!-- Breadcubs Area End Here -->
            <!-- Teacher Table Area Start Here -->
            <div class="card height-auto">
                <div class="card-body">
                    <div class="heading-layout1">
                        <div class="item-title">
                            <h3>Statements for <b style="color: red;">{{$user->first_name}} {{$user->last_name}}</b></h3>
                        </div>
                        <div class="dropdown">
                            <a class="dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-expanded="false">...</a>

                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#"><i class="fas fa-times text-orange-red"></i>Close</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-cogs text-dark-pastel-green"></i>Edit</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-redo-alt text-orange-peel"></i>Refresh</a>
                            </div>
                        </div>
                    </div>
                    <form class="mg-b-20">
                        <div class="row gutters-8">
                            <div class="col-4-xxxl col-xl-4 col-lg-3 col-12 form-group">
                                <label>Paid Months *</label>
                                <select class="select2" id="paid_months">
                                    <option value="1">Unpaid</option>
                                    <option value="2">Paid</option>
                                </select>
                            </div>
                            <div class="col-4-xxxl col-xl-4 col-lg-3 col-12 form-group">
                                <input type="text" placeholder="Search by Name ..." class="form-control">
                            </div>
                            <div class="col-4-xxxl col-xl-3 col-lg-3 col-12 form-group">
                                <input type="text" placeholder="Search by Phone ..." class="form-control">
                            </div>
                            <div class="col-1-xxxl col-xl-2 col-lg-3 col-12 form-group">
                                <button type="submit" class="fw-btn-fill btn-gradient-yellow">SEARCH</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table display data-table text-nowrap">
                            <thead>
                            <tr>
                                <th>Month</th>
                                <th>Invoice/Payment</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Balance</th>
                                <th>Action</th>
                                <th>usage_time</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($invoices as $invoice)
                            <tr>
                                <td>
                                    <span class="badge badge-primary">{{date("m",strtotime($invoice->invoice_date))}}</span>

                                </td>

                                <td>Invoice
                                    <hr>
                                    <p class="text-muted mb-0">Paid</p>
                                </td>
                                <td>Ksh {{$invoice->amount}}
                                    <hr>
                                    @if(!is_null($invoice->cash_id))
                                    <p class="text-muted mb-0">Ksh: {{$invoice->cash->amount}}</p>
                                    @else
                                        <span class="badge badge-danger">Not Paid</span>
                                    @endif

                                </td>
                                <td>{{$invoice->invoice_date}}
                                    <hr>
                                    @if(!is_null($invoice->cash_id))
                                    <p class="text-muted mb-0">{{$invoice->cash->date}}</p>
                                    @else
                                        <span class="badge badge-danger">Not Paid</span>

                                    @endif

                                </td>
                                @if($invoice->status==0)
                                <td><span class="badge badge-danger"><b>Ksh: {{$invoice->balance}}</b></span>
                                    @else
                                    <td><span class="badge badge-success">Paid</span></td>

                                    @endif
                                </td>
                                <td>
                                    <a href="{{url('receipt',$invoice->id)}}"> <button class="btn btn-primary">Receipt</button></a>
                                    <a href="{{url('invoicePayment',$invoice->id)}}"> <button class="btn btn-info">View Payments</button></a>
                                </td>
                                    <td>{{$invoice->usage_time}}</td>

                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Teacher Table Area End Here -->
            <footer class="footer-wrap-layout1">
                <div class="copyright">© Copyrights <a href="#">akkhor</a> 2019. All rights reserved. Designed by <a href="#">PsdBosS</a></div>
            </footer>
        </div>
    </div>
    <!-- Page Area End Here -->
</div>
<!-- jquery-->
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<!-- Plugins js -->
<script src="{{asset('js/plugins.js')}}"></script>
<!-- Popper js -->
<script src="{{asset('js/popper.min.js')}}"></script>
<!-- Bootstrap js -->
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<!-- Scroll Up Js -->
<script src="{{asset('js/jquery.scrollUp.min.js')}}"></script>
<!-- Data Table Js -->
<script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
<!-- Custom Js -->
<script src="{{asset('js/main.js')}}"></script>

</body>
<script>
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    const d = new Date();
    document.write("The current month is " + monthNames[d.getMonth()]);
</script>

<!-- Mirrored from www.radiustheme.com/demo/html/psdboss/akkhor/akkhor/all-book.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Jun 2021 10:36:40 GMT -->
</html>
