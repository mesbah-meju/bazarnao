@extends('frontend.layouts.app')

@section('content')

    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-start">
                @include('frontend.inc.user_side_nav')
                <div class="aiz-user-panel">
                    <div class="aiz-titlebar mt-2 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h1 class="h3">{{ translate('Referral Link') }}</h1>
                            </div>
                        </div>
                    </div>
                   

                    <div class="row">
                            @php
                                $referral_code_url = URL::to('/referr-link/').'/'.Auth::user()->id;
                            @endphp
                            <div class="col">
                                <div class="card">
                                    <div class="form-box-content p-3">
                                        <div class="form-group">
                                            <textarea id="referral_code_url" class="form-control" readonly type="text" >{{$referral_code_url}}</textarea>
                                        </div>
                                        <button type=button id="ref-cpurl-btn" class="btn btn-primary pull-left" data-attrcpy="{{translate('Copied')}}" onclick="copyToClipboard('url')" >{{translate('Copy Url')}}</button>
                                    </div>
									 <div class="row no-gutters mt-4">
                                <div class="col-sm-2">
                                    <div class="opacity-50 my-2">{{ translate('Share')}}:</div>
                                </div>
                                <div class="col-sm-10">

<a target="_self" href="mailto:?subject=Bazarna&body={{$referral_code_url}}" class="jssocials-share-link"><i class="lar la-envelope jssocials-share-logo"></i></a>
</div>
<div class="jssocials-share jssocials-share-twitter">
<a target="_blank" href="https://twitter.com/share?url={{$referral_code_url}}&amp;text=Bazarna" class="jssocials-share-link"><i class="lab la-twitter jssocials-share-logo"></i></a>
</div>
<div class="jssocials-share jssocials-share-facebook">
<a target="_blank" href="https://facebook.com/sharer/sharer.php?u={{$referral_code_url}}" class="jssocials-share-link"><i class="lab la-facebook-f jssocials-share-logo"></i></a>
</div>
<div class="jssocials-share jssocials-share-linkedin">
<a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&amp;url={{$referral_code_url}}" class="jssocials-share-link"><i class="lab la-linkedin-in jssocials-share-logo"></i></a>
</div>
<div class="jssocials-share jssocials-share-whatsapp">
<a target="_self" href="whatsapp://send?text={{$referral_code_url}}" class="jssocials-share-link"><i class="lab la-whatsapp jssocials-share-logo"></i></a>
</div>
<div class="jssocials-share jssocials-share-linkedin">
<a class="jssocials-share-link" target="_blank" href="https://www.facebook.com/dialog/send?app_id=207766518608&display=popup&link={{$referral_code_url}}?%2Fwebsite-tools%2Fsocial-share-buttons&utm_medium=facebook_messenger&utm_source=socialnetwork&redirect_uri={{$referral_code_url}}"><i class="lab la-facebook-messenger jssocials-share-logo"></i></a>
</div>
                                </div>
                            </div>
                                </div>
                            </div>
                        </div>
                   
                    
                    
                </div>
            </div>
        </div>
    </section>
@endsection


@section('script')
    <script>
        function copyToClipboard(btn){
            // var el_code = document.getElementById('referral_code');
            var el_url = document.getElementById('referral_code_url');
            // var c_b = document.getElementById('ref-cp-btn');
            var c_u_b = document.getElementById('ref-cpurl-btn');

            // if(btn == 'code'){
            //     if(el_code != null && c_b != null){
            //         el_code.select();
            //         document.execCommand('copy');
            //         c_b .innerHTML  = c_b.dataset.attrcpy;
            //     }
            // }

            if(btn == 'url'){
                if(el_url != null && c_u_b != null){
                    el_url.select();
                    document.execCommand('copy');
                    c_u_b .innerHTML  = c_u_b.dataset.attrcpy;
                }
            }
        }

        function show_affiliate_withdraw_modal(){
            $('#affiliate_withdraw_modal').modal('show');
        }
    </script>
@endsection
