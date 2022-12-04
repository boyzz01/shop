@extends('layouts.admin')

@section('styles')
    <link href="{{ asset('assets/admin/css/jquery-ui.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    <div class="content-area">

        <div class="mr-breadcrumb">
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="heading">{{ __('Edit Link') }} <a class="add-btn"
                            href="{{ route('admin-olshoplink-index') }}"><i class="fas fa-arrow-left"></i>
                            {{ __('Back') }}</a></h4>
                    <ul class="links">
                        <li>
                            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
                        </li>
                        <li>
                            <a href="javascript:;">{{ __('Settings') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('admin-olshoplink-index') }}">{{ __('Social Links') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('admin-olshoplink-edit', $data->id) }}">{{ __('Edit') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


        <div class="add-product-content1 add-product-content2">
            <div class="row">
                <div class="col-lg-12">
                    <div class="product-description">
                        <div class="body-area">

                            <div class="gocover"
                                style="background: url({{ asset('assets/images/' . $gs->admin_loader) }}) no-repeat scroll center center rgba(45, 45, 45, 0.5);">
                            </div>

                            @include('alerts.admin.form-both')

                            <form id="geniusform" action="{{ route('admin-olshoplink-update', $data->id) }}" method="POST"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="left-area">
                                            <h4 class="heading">URL *</h4>
                                            <p class="sub-heading"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <input type="text" class="input-field" name="link" placeholder="URL"
                                            required="" value="{{ $data->link }}">
                                    </div>
                                </div>

                                {{-- 
												<div class="row">
														<div class="col-lg-4">
															<div class="left-area">
																<h4 class="heading">{{ __('Icon') }} *</h4>
															</div>
														</div>

														<div class="col-lg-7 d-flex">
															<i class="" id="icn"></i>
															<input type="text" id="icons" class="input-field" name="icon" placeholder="{{ __('Social Icon') }}" required="" value="{{ $data->icon }}">
															   
														</div>
												</div> --}}
                                <input type="hidden" id="icons" class="input-field" name="icon"
                                    placeholder="{{ __('Social Icon') }}" required="" value="{{ $data->icon }}">


                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="left-area">

                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <button class="addProductSubmit-btn" type="submit">{{ __('Save') }}</button>
                                    </div>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/admin/js/iconpicker.js') }}"></script>

    <script>
        $("#icons").autocomplete({
            source: icons,
            select: function(event, ui) {
                var label = ui.item.label;
                var value = ui.item.value;
                $('#icn').prop('class', value);
            }
        })

        $('#icons').on('change', function() {
            $('#icn').prop('class', $(this).val());
        })
    </script>
@endsection
