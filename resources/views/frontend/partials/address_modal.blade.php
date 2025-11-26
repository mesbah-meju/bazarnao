<div class="modal fade" id="new-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-default" role="form" action="{{ route('addresses.store') }}" method="POST">
                @csrf
                <div class="modal-body c-scrollbar-light">
                    <div class="p-3">
                         <div class="row">
                            <div class="col-md-2">
                            <label>Area</label>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <select class="form-control aiz-selectpicker rounded-0" data-live-search="true" data-placeholder="{{ translate('Select your area') }}" name="area_id" required>
                                        <option value="">{{ translate('Select your area') }}</option>
                                        @foreach (App\Models\Area::get() as $key => $area)
                                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Block/Sector')}}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3 rounded-0"  name="block_sector" value="" >
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>Road No</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3 rounded-0"  name="road_no" value="" >
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>House/Flat No</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3 rounded-0"  name="house_flat_no" value="" >
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>Floor No</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3 rounded-0"  name="floor_no" value="" >
                            </div>
                        </div>

                        @if (get_setting('google_map') == 1)
                            <div class="row mt-3 mb-3">
                                <input id="searchInput" class="controls" type="text" placeholder="{{translate('Enter a location')}}">
                                <div id="map"></div>
                                <ul id="geoData">
                                    <li style="display: none;">Full Address: <span id="location"></span></li>
                                    <li style="display: none;">Postal Code: <span id="postal_code"></span></li>
                                    <li style="display: none;">Country: <span id="country"></span></li>
                                    <li style="display: none;">Latitude: <span id="lat"></span></li>
                                    <li style="display: none;">Longitude: <span id="lon"></span></li>
                                </ul>
                            </div>
                            <div class="row">
                                <div class="col-md-2" id="">
                                    <label for="exampleInputuname">{{ translate('Longitude')}}</label>
                                </div>
                                <div class="col-md-10" id="">
                                    <input type="text" class="form-control mb-3 rounded-0" id="longitude" name="longitude" readonly="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2" id="">
                                    <label for="exampleInputuname">{{ translate('Latitude')}}</label>
                                </div>
                                <div class="col-md-10" id="">
                                    <input type="text" class="form-control mb-3 rounded-0" id="latitude" name="latitude" readonly="">
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Phone')}}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="number" class="form-control mb-3 rounded-0" placeholder="018" name="phone" value="" required>
                            </div>
                        </div>
                          <div class="row">
                            <div class="col-md-2">
                                <label>Note</label>
                            </div>
                            <div class="col-md-10">
                                <textarea class="form-control mb-3 rounded-0" placeholder="type note if any suggetion" rows="2" name="suggetion"></textarea>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary rounded-0 w-150px">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Address Modal -->
<div class="modal fade" id="edit-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body c-scrollbar-light" id="edit_modal_body">

            </div>
        </div>
    </div>
</div>

@section('script')
    <script type="text/javascript">
        function add_new_address(){
            $('#new-address-modal').modal('show');
        }

        function edit_address(address) {
            var url = '{{ route("addresses.edit", ":id") }}';
            url = url.replace(':id', address);
            
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                success: function (response) {
                    $('#edit_modal_body').html(response.html);
                    $('#edit-address-modal').modal('show');
                    AIZ.plugins.bootstrapSelect('refresh');

                    @if (get_setting('google_map') == 1)
                        var lat     = -33.8688;
                        var long    = 151.2195;

                        if(response.data.address_data.latitude && response.data.address_data.longitude) {
                            lat     = parseFloat(response.data.address_data.latitude);
                            long    = parseFloat(response.data.address_data.longitude);
                        }

                        initialize(lat, long, 'edit_');
                    @endif
                }
            });
        }
        
        $(document).on('change', '[name=area_id]', function() {
            var area_id = $(this).val();
            get_location(area_id);
        });

     
        function get_location(area_id){
            $('[name="location"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-location')}}",
                type: 'POST',
                data: {
                    area_id  : area_id
                },
                success: function (response){
                    
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="location_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }
    </script>

    
    @if (get_setting('google_map') == 1)
        @include('frontend.partials.google_map')
    @endif
@endsection