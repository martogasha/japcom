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
                    <form action="{{url('filterInvoice')}}" method="post">
                        @csrf
                        <input type="hidden" value="{{$user->id}}" name="user_id">
                        <div class="row gutters-8">
                            <div class="col-4-xxxl col-xl-4 col-lg-3 col-12 form-group">
                                <div class="form-group">
                                    <label for="dob">Start Date *</label>
                                    <input type="date" class="form-control" name="start_date"/>
                                </div>                            </div>
                             <div class="col-4-xxxl col-xl-3 col-lg-3 col-12 form-group">
                                <div class="form-group">
                                    <label for="dob">End Date *</label>
                                    <input type="date" class="form-control" name="end_date"/>
                                </div>
                            </div>
                            <div class="col-4-xxxl col-xl-3 col-lg-3 col-12 form-group">
                                <button type="submit" class="fw-btn-fill btn-gradient-yellow">SEARCH</button>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                    <h4>Unpaid</h4>
                    <h4 style="padding-left: 20px">Paid</h4>
                    </div>
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
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($invoices as $invoice)
                                @if($invoice->status==2)
                            <tr>
                                <td>
                                    <span class="badge badge-primary">{{\Carbon\Carbon::parse($invoice->invoice_date)->format('F Y')}}</span>
                                </td>


                                <td>Invoice
                                    <hr>
                                    <p class="text-muted mb-0">Payment</p>
                                </td>
                                    <td>Ksh {{$invoice->amount}}
                                    <hr>
                                    @if(!is_null($invoice->payment_id))
                                    <p class="text-muted mb-0">Ksh: {{$invoice->payment->amount}}</p>
                                    @else
                                        <span class="badge badge-danger">Not Paid</span>
                                    @endif

                                </td>
                                <td>{{$invoice->invoice_date}}
                                    <hr>
                                    @if(!is_null($invoice->payment_id))
                                    <p class="text-muted mb-0">{{$invoice->payment->date}}</p>
                                    @else
                                        <span class="badge badge-danger">Not Paid</span>

                                    @endif

                                </td>
                                @if($invoice->status==0)
                                <td><span class="badge badge-danger"><b>Carried Forward</b></span>
                                    @else
                                    <td><span class="badge badge-success">Carried Forward</span></td>

                                </td>
                                @endif

                                <td>
                                    <a href="{{url('invoicePayment',$invoice->id)}}"> <button class="btn btn-info">View Payments</button></a>
                                </td>

                            </tr>
                                @else
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary">{{\Carbon\Carbon::parse($invoice->invoice_date)->format('F Y')}}</span>
                                        </td>

                                        <td>Invoice
                                            <hr>
                                            <p class="text-muted mb-0">Payment</p>
                                        </td>
                                        <td>Ksh {{$invoice->amount}}
                                            <hr>
                                            @if(!is_null($invoice->payment_id))
                                                    <p class="text-muted mb-0">Ksh: {{$invoice->payment->amount}}</p>
                                            @else
                                                <span class="badge badge-danger">Not Paid</span>
                                            @endif

                                        </td>
                                        <td>{{$invoice->invoice_date}}
                                            <hr>
                                            @if(!is_null($invoice->payment_id))
                                                <p class="text-muted mb-0">{{$invoice->payment->date}}</p>
                                            @else
                                                <span class="badge badge-danger">Not Paid</span>

                                            @endif

                                        </td>
                                        @if($invoice->status==0)
                                            <td><span class="badge badge-danger"><b>Ksh: {{$invoice->balance}}</b></span>
                                        @else
                                            <td><span class="badge badge-success">Paid</span></td>

                                            </td>
                                        @endif

                                        <td>
                                                <a href="{{url('invoicePayment',$invoice->id)}}"> <button class="btn btn-info">View Payments</button></a>
                                            </td>

                                    </tr>
                                @endif
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
    const today = new Date()
    today.toLocaleString('default', { month: 'long' })
</script>

<!-- Mirrored from www.radiustheme.com/demo/html/psdboss/akkhor/akkhor/all-book.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Jun 2021 10:36:40 GMT -->
</html>
