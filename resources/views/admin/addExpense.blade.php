@include('adminPartial.nav')
<title>Add Expense | Japcom</title>
        <!-- Sidebar Area End Here -->
        <div class="dashboard-content-one">
            <!-- Breadcubs Area Start Here -->
            <div class="breadcrumbs-area">
                <h3>Add Expense</h3>
                <ul>
                    <li>
                        <a href="{{url('admin')}}">Home</a>
                    </li>
                    <li>Add Expense</li>
                </ul>
            </div>
            <!-- Breadcubs Area End Here -->
            <!-- Add New Teacher Area Start Here -->
            <div class="card height-auto">
                <div class="card-body">
                    <div class="heading-layout1">
                        <div class="item-title">
                            <h3>Add Expense</h3>
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
                    <form action="{{url('storeExpense')}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-xl-3 col-lg-6 col-12 form-group">
                                <label>Expense Details</label>
                                <input type="text" name="details" placeholder="" class="form-control">
                            </div>
                            <div class="col-xl-3 col-lg-6 col-12 form-group">
                                <label>Amount</label>
                                <input type="text" name="amount" placeholder="" class="form-control">
                            </div>

                            <div class="col-xl-3 col-lg-6 col-12 form-group">
                                <label>Date *</label>
                                <input type="text" name=date placeholder="dd/mm/yyyy" class="form-control air-datepicker">
                                <i class="far fa-calendar-alt"></i>
                            </div>

                            <div class="col-12 form-group mg-t-8">
                                <button type="submit" class="btn-fill-lg btn-gradient-yellow btn-hover-bluedark">Save</button>
                                <button type="reset" class="btn-fill-lg bg-blue-dark btn-hover-yellow">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Add New Teacher Area End Here -->
            <footer class="footer-wrap-layout1">
                <div class="copyright">© Copyrights <a href="#">akkhor</a> 2019. All rights reserved. Designed by <a href="#">PsdBosS</a></div>
            </footer>
        </div>
    </div>
    <!-- Page Area End Here -->
</div>
<!-- jquery-->
<script src="js/jquery-3.3.1.min.js"></script>
<!-- Plugins js -->
<script src="js/plugins.js"></script>
<!-- Popper js -->
<script src="js/popper.min.js"></script>
<!-- Bootstrap js -->
<script src="js/bootstrap.min.js"></script>
<!-- Select 2 Js -->
<script src="js/select2.min.js"></script>
<!-- Date Picker Js -->
<script src="js/datepicker.min.js"></script>
<!-- Smoothscroll Js -->
<script src="js/jquery.smoothscroll.min.html"></script>
<!-- Scroll Up Js -->
<script src="js/jquery.scrollUp.min.js"></script>
<!-- Custom Js -->
<script src="js/main.js"></script>

</body>
<script>
    alert('ok')
</script>


<!-- Mirrored from www.radiustheme.com/demo/html/psdboss/akkhor/akkhor/add-teacher.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Jun 2021 10:36:38 GMT -->
</html>
