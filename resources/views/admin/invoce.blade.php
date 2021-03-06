@include('adminPartial.nav')
<title>Invoice | Japcom</title>
        <!-- Sidebar Area End Here -->
        <div class="dashboard-content-one">
            <!-- Breadcubs Area Start Here -->
            <div class="breadcrumbs-area">
                <h3>Customers</h3>
                <ul>
                    <li>
                        <a href="{{url('admin')}}">Home</a>
                    </li>
                    <li>All Invoices</li>
                </ul>
            </div>
            <!-- Breadcubs Area End Here -->
            <!-- Student Table Area Start Here -->
            <div class="card height-auto">
                <div class="card-body">
                    <div class="heading-layout1">
                        <div class="item-title">
                            <h3>All Quotations</h3>
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
                    <div class="page-content container">
                        <div class="page-header text-blue-d2">
                            <div class="page-tools">
                                <div class="action-buttons">
                                    <a class="btn bg-white btn-light mx-1px text-95" href="#" data-title="Print">
                                        <i class="mr-1 fa fa-print text-primary-m1 text-120 w-2"></i>
                                        Print
                                    </a>
                                    <a class="btn bg-white btn-light mx-1px text-95" href="#" data-title="PDF">
                                        <i class="mr-1 fa fa-file-pdf-o text-danger-m1 text-120 w-2"></i>
                                        Export
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="container px-0">
                            <div class="row mt-4">
                                <div class="col-12 col-lg-10 offset-lg-1">
                                    <!-- .row -->
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div>
                                                <span class="text-sm text-grey-m2 align-middle" style="padding-left: 70px"><img src="img/jp.png" alt="logo">
                                                <span class="text-600 text-110 text-blue align-middle"><br style="color:blue">Japcom Networks Limited</span>
                                            </div>
                                        </div>
                                        <!-- /.col -->

                                        <div class="text-95 col-sm-6 align-self-start d-sm-flex justify-content-end">
                                            <hr class="d-sm-none" />
                                            <div class="text-grey-m2" style="text-align: right">
                                                <div class="mt-1 mb-2 text-secondary-m1 text-600 text-125" style="color: black;font-size: 50px">
                                                    Invoice
                                                </div>

                                                <div class="my-2"></i> <span class="text-600 text-90" style="color: black">JAPCOM NETWORKS</div>
                                                <div class="my-2"></i> <span class="text-600 text-90">Nairobi, Nairobi Area 00100<br></div>
                                                <div class="my-2"></i> <span class="text-600 text-90">0729381059/0717749765/0729444986</div>
                                                <div class="my-2"></i> <span class="text-600 text-90" >sales@jnl.co.ke</div>

                                            </div>
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    <hr class="row brc-default-l1 mx-n1 mb-4" />

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div>
                                                <span class="text-sm text-grey-m2 align-middle">BILL TO:</span>
                                                <span class="text-600 text-110 text-blue align-middle"  ><br>Ndegwa</span>
                                            </div>
                                        </div>
                                        <!-- /.col -->

                                        <div class="text-95 col-sm-6 align-self-start d-sm-flex justify-content-end">
                                            <hr class="d-sm-none" />
                                            <div class="text-grey-m2">
                                                <div class="mt-1 mb-2 text-secondary-m1 text-600 text-125">
                                                    Invoice Number :0166
                                                </div>

                                                <div class="my-2"></i> <span class="text-600 text-90">Invoice Date:</span> June 9,2021</div>
                                                <div class="my-2"></i> <span class="text-600 text-90">Payment Due:</span> June 9,2021</div>
                                                <div class="my-2"></i> <span class="text-600 text-90" style="color: black"><b>Amount Due (KES):</b></span> <b style="color: black">SH3500.00</b></div>

                                            </div>
                                        </div>
                                        <!-- /.col -->
                                    </div>

                                    <div class="mt-4">
                                        <div class="row text-600 text-white bgc-default-tp1 py-25">
                                            <div class="d-none d-sm-block col-1">#</div>
                                            <div class="col-9 col-sm-5" style="color: black">Description</div>
                                            <div class="d-none d-sm-block col-4 col-sm-2" style="color: black">Qty</div>
                                            <div class="d-none d-sm-block col-sm-2" style="color: black">Unit Price</div>
                                            <div class="col-2" style="color: black">Amount</div>
                                        </div>

                                        <div class="text-95 text-secondary-d3">
                                            <div class="row mb-2 mb-sm-0 py-25">
                                                <div class="d-none d-sm-block col-1">1</div>
                                                <div class="col-9 col-sm-5">Domain registration</div>
                                                <div class="d-none d-sm-block col-2">2</div>
                                                <div class="d-none d-sm-block col-2 text-95">$10</div>
                                                <div class="col-2 text-secondary-d2">$20</div>
                                            </div>

                                            <div class="row mb-2 mb-sm-0 py-25 bgc-default-l4">
                                                <div class="d-none d-sm-block col-1">2</div>
                                                <div class="col-9 col-sm-5">Web hosting</div>
                                                <div class="d-none d-sm-block col-2">1</div>
                                                <div class="d-none d-sm-block col-2 text-95">$15</div>
                                                <div class="col-2 text-secondary-d2">$15</div>
                                            </div>

                                            <div class="row mb-2 mb-sm-0 py-25">
                                                <div class="d-none d-sm-block col-1">3</div>
                                                <div class="col-9 col-sm-5">Software development</div>
                                                <div class="d-none d-sm-block col-2">--</div>
                                                <div class="d-none d-sm-block col-2 text-95">$1,000</div>
                                                <div class="col-2 text-secondary-d2">$1,000</div>
                                            </div>

                                            <div class="row mb-2 mb-sm-0 py-25 bgc-default-l4">
                                                <div class="d-none d-sm-block col-1">4</div>
                                                <div class="col-9 col-sm-5">Consulting</div>
                                                <div class="d-none d-sm-block col-2">1 Year</div>
                                                <div class="d-none d-sm-block col-2 text-95">$500</div>
                                                <div class="col-2 text-secondary-d2">$500</div>
                                            </div>
                                        </div>

                                        <div class="row border-b-2 brc-default-l2"></div>

                                        <!-- or use a table instead -->
                                        <!--
                                <div class="table-responsive">
                                    <table class="table table-striped table-borderless border-0 border-b-2 brc-default-l1">
                                        <thead class="bg-none bgc-default-tp1">
                                            <tr class="text-white">
                                                <th class="opacity-2">#</th>
                                                <th>Description</th>
                                                <th>Qty</th>
                                                <th>Unit Price</th>
                                                <th width="140">Amount</th>
                                            </tr>
                                        </thead>

                                        <tbody class="text-95 text-secondary-d3">
                                            <tr></tr>
                                            <tr>
                                                <td>1</td>
                                                <td>Domain registration</td>
                                                <td>2</td>
                                                <td class="text-95">$10</td>
                                                <td class="text-secondary-d2">$20</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                -->

                                        <div class="row mt-3">
                                            <div class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0"></div>

                                            <div class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                                <div class="row my-2">
                                                    <div class="col-7 text-right" style="color: black">
                                                        Total
                                                    </div>
                                                    <div class="col-5">
                                                        <span class="text-120 text-secondary-d1">SH2,250.00</span>
                                                    </div>
                                                </div>

                                                <div class="row my-2 align-items-center bgc-primary-l3 p-2">
                                                    <div class="col-7 text-right" style="color: black">
                                                        <b>Amount Due (KES):</b>
                                                    </div>
                                                    <div class="col-5">
                                                        <span><b style="font-size: 18px">SH2,475.00</b></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <span class="text-secondary-d1 text-105"><b style="color: black">Notes / Terms</b><br>payment to<br>JAPCOM NETWORKS</span>
                                        </div>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <span class="text-secondary-d1 text-105" style="padding-left: 300px">We deliver as promised</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Student Table Area End Here -->
            <footer class="footer-wrap-layout1">
                <div class="copyright">?? Copyrights <a href="#">akkhor</a> 2019. All rights reserved. Designed by <a
                        href="#">PsdBosS</a></div>
            </footer>
        </div>
    </div>
    <!-- Page Area End Here -->
</div>
<style>
    body{
        margin-top:20px;
        color: #484b51;
    }
    .text-secondary-d1 {
        color: #728299!important;
    }
    .page-header {
        margin: 0 0 1rem;
        padding-bottom: 1rem;
        padding-top: .5rem;
        border-bottom: 1px dotted #e2e2e2;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-pack: justify;
        justify-content: space-between;
        -ms-flex-align: center;
        align-items: center;
    }
    .page-title {
        padding: 0;
        margin: 0;
        font-size: 1.75rem;
        font-weight: 300;
    }
    .brc-default-l1 {
        border-color: #dce9f0!important;
    }

    .ml-n1, .mx-n1 {
        margin-left: -.25rem!important;
    }
    .mr-n1, .mx-n1 {
        margin-right: -.25rem!important;
    }
    .mb-4, .my-4 {
        margin-bottom: 1.5rem!important;
    }

    hr {
        margin-top: 1rem;
        margin-bottom: 1rem;
        border: 0;
        border-top: 1px solid rgba(0,0,0,.1);
    }

    .text-grey-m2 {
        color: #888a8d!important;
    }

    .text-success-m2 {
        color: #86bd68!important;
    }

    .font-bolder, .text-600 {
        font-weight: 600!important;
    }

    .text-110 {
        font-size: 110%!important;
    }
    .text-blue {
        color: black!important;
    }
    .pb-25, .py-25 {
        padding-bottom: .75rem!important;
    }

    .pt-25, .py-25 {
        padding-top: .75rem!important;
    }
    .bgc-default-tp1 {
        background-color: rgba(8,238,235,255)!important;
    }
    .bgc-default-l4, .bgc-h-default-l4:hover {
        background-color: #f3f8fa!important;
    }
    .page-header .page-tools {
        -ms-flex-item-align: end;
        align-self: flex-end;
    }

    .btn-light {
        color: #757984;
        background-color: #f5f6f9;
        border-color: #dddfe4;
    }
    .w-2 {
        width: 1rem;
    }

    .text-120 {
        font-size: 120%!important;
    }
    .text-primary-m1 {
        color: #4087d4!important;
    }

    .text-danger-m1 {
        color: #dd4949!important;
    }
    .text-blue-m2 {
        color: #68a3d5!important;
    }
    .text-150 {
        font-size: 150%!important;
    }
    .text-60 {
        font-size: 60%!important;
    }
    .text-grey-m1 {
        color: #7b7d81!important;
    }
    .align-bottom {
        vertical-align: bottom!important;
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


<!-- Mirrored from www.radiustheme.com/demo/html/psdboss/akkhor/akkhor/all-student.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Jun 2021 10:35:18 GMT -->
</html>
