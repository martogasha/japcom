@include('adminPartial.nav')
<title>Quotation | Japcom</title>
        <!-- Sidebar Area End Here -->
        <div class="dashboard-content-one">
            <!-- Breadcubs Area Start Here -->
            <div class="breadcrumbs-area">
                <h3>Quotation</h3>
                <ul>
                    <li>
                        <a href="{{url('admin')}}">Home</a>
                    </li>
                    <li>Quotation</li>
                </ul>
            </div>
            @include('flash-message')
            <!-- Breadcubs Area End Here -->
            <!-- Add New Teacher Area Start Here -->
            <div class="card height-auto">
                <div class="card-body">
                    <div class="heading-layout1">
                        <div class="item-title">
                            <h3>Create Quotation</h3>
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
                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-12 form-group">
                            <label>Name of Customer *</label>
                            <input type="text" value="{{$estimate->name}}" class="form-control" id="customer_name">
                            <input type="hidden" value="{{$estimate->id}}" id="estimateId">
                        </div>
                            <div class="col-xl-3 col-lg-6 col-12 form-group">
                                <div class="form-group">
                                    <label for="dob">Estimate Date *</label>
                                    <input type="text" name=date value="{{$estimate->estimate_date}}" class="form-control air-datepicker" id="estimated_date">
                                    <i class="far fa-calendar-alt"></i>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-12 form-group">
                                <div class="form-group">
                                    <label for="dob">Expiry Date *</label>
                                        <input type="text" name=date value="{{$estimate->expiry_date}}" class="form-control air-datepicker" id="expiry_date">
                                    <i class="far fa-calendar-alt"></i>
                                </div>
                            </div>
                    </div>

                        <div class="row">
                        <div class="col-xl-3 col-lg-12 col-12 form-group">
                            <label>Product *</label>
                            <select class="select2" id="product_name">
                                <option>Select product</option>
                            @foreach($products as $product)
                                <option value="{{$product->id}}">{{$product->name}}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-xl-3 col-lg-12 col-12 form-group">
                            <label>Amount *</label>
                            <div id="amountDiv">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-12 col-12 form-group">
                            <label>Quantity *</label>
                            <select class="select2" id="quantity">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                            <button type="submit" class="btn btn-success" id="storeQuotationButton">Add</button>
                        </div>
                    <div class="table-responsive">
                        <table class="table display data-table text-nowrap">
                            <thead>
                            <tr>

                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($ducts as $product)
                            <tr>
                                <td>{{$product->name}}</td>
                                <td>{{$product->quantity}}</td>
                                <td>Ksh: {{$product->amount}}</td>
                                <td>Ksh: {{$product->amount*$product->quantity}}</td>

                                <td>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                           aria-expanded="false">
                                            <span class="flaticon-more-button-of-three-dots"></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item view" id="{{$product->id}}" href="#editModal" data-toggle="modal">Edit</a>
                                            <a class="dropdown-item delete" id="{{$product->id}}">Remove</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 form-group mg-t-8">
                        <div class="row">
                        <a href="{{url('quotes',$quote->id)}}"> <button type="submit" class="btn-fill-lg btn-gradient-yellow btn-hover-bluedark">Save</button></a>
                        <form action="{{url('deleteQ', $quote->id)}}" method="post">
                            @csrf
                            <button type="submit" style="margin-left: 15px" class="btn-fill-lg bg-blue-dark btn-hover-yellow">Remove</button>
                        </form>
                        </div>
                    </div>
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
<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <form action="{{route('editQProduct')}}" method="post">
        @csrf
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Edit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="basic1">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
    </form>
</div>
<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle" style="color: red">ARE YOU SURE</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="deleteP">DELETE</button>
                </div>
            </div>
        </div>
</div>

<!-- jquery-->
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<!-- Plugins js -->
<script src="{{asset('js/plugins.js')}}"></script>
<!-- Popper js -->
<script src="{{asset('js/popper.min.js')}}"></script>
<!-- Bootstrap js -->
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<!-- Select 2 Js -->
<script src="{{asset('js/select2.min.js')}}"></script>
<!-- Date Picker Js -->
<script src="{{asset('js/datepicker.min.js')}}"></script>
<!-- Smoothscroll Js -->
<script src="{{(asset('js/jquery.smoothscroll.min.html'))}}"></script>
<!-- Scroll Up Js -->
<script src="{{asset('js/jquery.scrollUp.min.js')}}"></script>
<!-- Custom Js -->
<script src="{{asset('js/main.js')}}"></script>

</body>
<script>
    $(document).on('click','.view',function () {
       $value = $(this).attr('id');
        $.ajax({
            type:"get",
            url:"{{url('getQProducts')}}",
            data:{'id':$value},
            success:function (data) {
                $('#basic1').html(data);
            },
            error:function (error) {
                console.log(error)
                alert('error')

            }

        });
    });
    $('#product_name').on('change',function () {
        $value = $(this).val();
        $.ajax({
            type:"get",
            url:"{{url('getAmount')}}",
            data:{'id':$value},
            success:function (data) {
                $('#amountDiv').html(data);
            },
            error:function (error) {
                console.log(error)
                alert('error')

            }

        });
    });
    $(document).on('click','.delete',function () {
        $value = $(this).attr('id');
        $.ajax({
            type:"get",
            url:"{{url('deletePro')}}",
            data:{'id':$value},
            success:function (data) {
                alert('Deleted')
                location.reload();
            },
            error:function (error) {
                console.log(error)
                alert('error')

            }

        });
    });
    $('#storeQuotationButton').click(function () {
       var customer_name = $('#customer_name').val();
       var estimated_date = $('#estimated_date').val();
       var expiry_date = $('#expiry_date').val();
       var product_name = $('#product_name').val();
       var quantity = $('#quantity').val();
       var amount = $('#amount').val();
       var id = $('#estimateId').val();
        $.ajax({
            type:"get",
            url:"{{url('storeQuotation')}}",
            data:{'customer_name':customer_name,'estimated_date':estimated_date,'expiry_date':expiry_date,'product_name':product_name,'quantity':quantity,'amount':amount,'id':id},
            success:function (data) {
                location.reload();
            },
            error:function (error) {
                console.log(error)
                alert('error')

            }

        });
    });
</script>

<!-- Mirrored from www.radiustheme.com/demo/html/psdboss/akkhor/akkhor/add-teacher.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 16 Jun 2021 10:36:38 GMT -->
</html>
