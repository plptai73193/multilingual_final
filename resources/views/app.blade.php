@php
   use App\Libs\Cafe24\Cafe24Token;
   use Illuminate\Support\Facades\DB;
   
@endphp

@extends('layout.master')
@section('table')

<?php 
$mall_params = base64_decode($mall_params);
$mall_params = json_decode($mall_params, true);
$mall_id = $mall_params["mall_id"];
$mall_langs = $mall_params["mall_langs"];
// dd($mall_langs);
?>
   <div class="card">
      <div class="card-body">
         <h2 class="text-center p-3">Multilingual Tool</h2>
         <form method="GET" id="search_form">
            <div class="search_outer">
               <div class="input-group mb-3">
                  <div class="input-group mb-3 search_content">
                     <div class="search_criteria">
                        <select class="form-select" id="inputGroupSelect01">
                           <option value="page_name">Page</option>
                           <option value="selector">Selector</option>
                           <option value="language">Language</option>
                           <option value="input_text">Text</option>
                        </select>
                     </div>
                     <div class="search_input">
                        <input type="text" class="form-control" placeholder="Search..." aria-describedby="basic-addon1">
                        <input type="hidden" name="cafe_mall_id" value="{{ $mall_id }}">
                     </div>
                  </div>
               </div>
            </div>
         </form>
         
         
         <form id="app_form" action="{{ route('mall.text') }}" method="POST">
               @csrf
               <input id="cafe24_mall_id" type="hidden" name="cafe24_mall_id" value="{{ $mall_id }}">
               <table class="table app_table table-striped table-hover">
                  <thead class="table-dark">
                     <tr>
                        <th scope="col">Page</th>
                        <th scope="col">Selector</th>
                        <th scope="col">Language</th>
                        <th scope="col">Text</th>
                        <th scope="col">Is Placeholder</th>
                        <th scope="col">Action</th>
                     </tr>
                  </thead>
                  <tbody></tbody>
               </table>
               <nav class="pagination_outer" aria-label="Page navigation example">
                  <ul class="pagination app_pagination"></ul>
               </nav>
               <div class="button-group text-right">
                  <button class="btn btn-primary app_more_row">Add more row</button>
                  <button type="submit" class="btn btn-warning app_save">Save</button>
               </div>
         </form>
      </div>
   </div>
@endsection
<datalist id="datalistOptions"></datalist>
@section('temp')
    <table id="temp">
       <tbody>
         <tr>
            <td>
               <input type="text" name="page_name[]" class="form-control page" list="datalistOptions" placeholder="Type to search..." data-pageurl="">
            </td>
            <td>
               <div class="input-group flex-nowrap">
                  <input type="text" class="form-control selector" placeholder="Selector" aria-describedby="addon-wrapping" name="selector[]">
               </div>
            </td>
            <td>
               <select class="form-select language" name="language[]">
                  @foreach ($mall_langs as $lang)
                        <option value="{{$lang['lang_code']}}">{{$lang['shop_name']}}</option>
                  @endforeach
               </select>
            </td>
            <td>
               <div class="input-group flex-nowrap">
                  <input type="text" class="form-control input_text" placeholder="Translate Text" aria-describedby="addon-wrapping" name="input_text[]">
               </div>
            </td>
            <td>
               <select class="form-select table_checkbox" name="is_placeholder[]">
                  <option value="0">No</option>
                  <option value="1">Yes</option>
            </select>
            </td>
            <td>
               <span class="delete_icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Click to remove"><i class="far fa-trash-alt"></i></span>
               <input type="hidden" name="row_id[]" class="row_id" value="0">
            </td>
         </tr>
      </tbody>
       
   </table>
   <div id="temp_lang"></div>
   <div id="temp_holder"></div>
@endsection
