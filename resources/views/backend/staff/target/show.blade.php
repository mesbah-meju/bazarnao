@extends('layouts.admin')
@section('content')
    <style>
        th,
        td {
            padding: 3px !important
        }
    </style>
    <div class="card">
        <div class="card-header">
            Yearly Target for {{$entry[0]->year_title}}
            <a href="jabascript:" onclick="javascript:printDiv('printdiv')" class="btn btn-success pull-right">Print</a>
        </div>

        <div class="card-body">
           
            <div class="table-responsive" id="printdiv">
                <div class="col-md-4 col-md-offset-3 text-center" style="width:100%;margin:0 auto;text-align:center !important;margin-bottom:10px;">
                    <h2>Target for {{$entry[0]->year_title}}</h2>    
                    @if(!empty($entry[0]->city->name))
                        <b>City Corporation : {{$entry[0]->city->name}}</b>
                        <br>
                    @endif
                   
                    
                    @if(!empty($entry[0]->year_title))
                        <b>Year : {{$entry[0]->year_title}}</b>
                        <br>
                    @endif
                </div>
                <table class=" table table-bordered table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th width="10">
                                SL
                            </th>
                            <th>
                                Product
                            </th>
                            <th>
                                Target Quantity 
                            </th>
                            <th>
                                Remarks
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $total = 0;
                        @endphp
                        @foreach ($entry as $key => $item)
                        @php 
                            $total+= $item->qty;
                        @endphp
                            <tr id="data_{{ $item->id }}" data-entry-id="{{ $item->id }}">
                                <td>
                                    {{$key+1}}
                                </td>

                                <td>
                                    {{ $item->product->name ?? '' }}
                                </td>

                                <td style="text-align:right">
                                    {{ number_format($item->qty,2) }}
                                </td>

                                <td>
                                    &nbsp;    
                                </td>

                            </tr>
                        @endforeach
                        <tr>
                            <th colspan="2" style="text-align:right">Total</th>
                            <th style="text-align:right">{{ number_format($total,2) }}</th>
                            <th>&nbsp;</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@section('scripts')
    @parent
    <script>
 
    function printDiv(divID) {
        //Get the HTML of div
        var divElements = document.getElementById(divID).innerHTML;
        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;
        //Reset the page's HTML with div's HTML only
        document.body.innerHTML = 
          "<html><head><title></title><style>th,td {padding: 2px !important}</style></head><body>" + 
          divElements + "</body>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;

    }
    </script>
@endsection
@endsection
