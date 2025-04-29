<?php

use App\Models\Dimond;
?>

@extends('layouts.admin')
@section('content')

<style>
  li.active.tab a {
    background-color: black !important;
  }
</style>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <h4>HR DIAMONDS List
          <div class="card-action">
            <a href="/admin/hr-export" class="btn btn-success">Export Slip</a>
          </div>
        </h4>
      </div>

      <div id="exTab2">

        <!-- Search Field -->
        <form method="GET" action="{{ route('admin.hrDimond') }}">
          <input type="text" name="search" placeholder="Search Diamond..." value="{{ request()->search }}" />
          <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <br />

        <ul class="nav nav-tabs">
          <li class="tab {{ request('tab') == 'repair' ? 'active' : '' }}">
            <a href="{{ route('admin.hrDimond', ['tab' => 'repair', 'search' => request()->search]) }}" class="btn btn-primary">
              <span style="font-size:16px">Repair</span>
              <span class="badge bg-danger text-white rounded-pill" style="font-size:15px">{{ count($repairDimonds) }}</span>
            </a>
          </li>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <li class="tab {{ request('tab') == 'delivered' ? 'active' : '' }}">
            <a href="{{ route('admin.hrDimond', ['tab' => 'delivered', 'search' => request()->search]) }}" class="btn btn-primary">
              <span style="font-size:16px">Delivered</span>
              <span class="badge bg-danger text-white rounded-pill" style="font-size:15px">{{ count($deliveredDimonds) }}</span>
            </a>
          </li>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <li class="tab {{ request('tab') == 'completed' ? 'active' : '' }}">
            <a href="{{ route('admin.hrDimond', ['tab' => 'completed', 'search' => request()->search]) }}" class="btn btn-primary">
              <span style="font-size:16px">Completed</span>
              <span class="badge bg-danger text-white rounded-pill" style="font-size:15px">{{ count($completedDimonds) }}</span>
            </a>
          </li>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <li class="tab {{ request('tab') == 'processing' ? 'active' : '' }}">
            <a href="{{ route('admin.hrDimond', ['tab' => 'processing', 'search' => request()->search]) }}" class="btn btn-primary">
              <span style="font-size:16px">Processing</span>
              <span class="badge bg-danger text-white rounded-pill" style="font-size:15px">{{ count($processingDimonds) }}</span>
            </a>
          </li>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <li class="tab {{ request('tab') == 'pending' ? 'active' : '' }}">
            <a href="{{ route('admin.hrDimond', ['tab' => 'pending', 'search' => request()->search]) }}" class="btn btn-primary">
              <span style="font-size:16px">Pending</span>
              <span class="badge bg-danger text-white rounded-pill" style="font-size:15px">{{ count($pendingDimonds) }}</span>
            </a>
          </li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane" id="5">
            <!-- <h3>Repair Dimond List</h3> -->
            <div class="table-responsive">
              <table id="repairDimonds" class="table align-items-center table-flush table-borderless ">
                <thead>
                  <tr>
                    <th>Show</th>
                    <th>Party Name</th>
                    <th>Dimond Name</th>
                    <th>Weight</th>
                    <th>Barcode</th>
                    <th>Shap</th>
                    <th>clarity</th>
                    <th>color</th>
                    <th>cut</th>
                    <th>polish</th>
                    <th>symmetry</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    @foreach($repairDimonds as $repairdimond)
                    <?php
                    $dimond = Dimond::where('id', $repairdimond->dimonds_id)->first();
                    ?>
                    <td>
                      <a href="{{route('admin.dimond.show', $dimond->barcode_number)}}"><i class="fa fa-eye" style="color:white;font-size:15px;background-color:rgba(255, 255, 255, 0.25);padding:8px;"></i></a>
                    </td>
                    <td>{{$dimond->parties->party_code}}</td>
                    <td>{{$dimond->dimond_name}}</td>
                    <td>{{$dimond->weight}}</td>
                    <td>{!! $dimond->barcode_number !!}</td>
                    <td>{{$dimond->shape}}</td>
                    <td>{{$dimond->clarity}}</td>
                    <td>{{$dimond->color}}</td>
                    <td>{{$dimond->cut}}</td>
                    <td>{{$dimond->polish}}</td>
                    <td>{{$dimond->symmetry}}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane" id="4">
            <!-- <h3>Delivered Dimond List</h3> -->
            <div class="table-responsive">
              <table id="deliveredDimonds" class="table align-items-center table-flush table-borderless ">
                <thead>
                  <tr>
                    <th>Repair</th>
                    <th>Slip</th>
                    <th>Party Name</th>
                    <th>Date</th>
                    <th>Dimond Name</th>
                    <th>Weight</th>
                    <th>Barcode</th>
                    <th>Shap</th>
                    <th>clarity</th>
                    <th>color</th>
                    <th>cut</th>
                    <th>polish</th>
                    <th>symmetry</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    @foreach($deliveredDimonds as $dimond)
                    <td><a href="/admin/repair/{{$dimond->id}}" class="btn btn-info">Repair</a></td>
                    <td><a href="/admin/print-slipe/{{$dimond->id}}" class="btn btn-primary" target="_blank">Print</a></td>
                    <td>{{$dimond->parties->party_code}}</td>
                    <td>{{ \Carbon\Carbon::parse($dimond->delevery_date)->format('d-m-Y') }}</td>
                    <td>{{$dimond->dimond_name}}</td>
                    <td>{{$dimond->weight}}</td>
                    <td>{!! $dimond->barcode_number !!}</td>
                    <td>{{$dimond->shape}}</td>
                    <td>{{$dimond->clarity}}</td>
                    <td>{{$dimond->color}}</td>
                    <td>{{$dimond->cut}}</td>
                    <td>{{$dimond->polish}}</td>
                    <td>{{$dimond->symmetry}}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane active" id="1">
            <!-- <h3>Completed Dimond List</h3> -->
            <div class="table-responsive">
              <table id="completedDimonds" class="table align-items-center table-flush table-borderless ">
                <thead>
                  <tr>
                    <th>Slip</th>
                    <th>Party Name</th>
                    <th>Dimond Name</th>
                    <th>Weight</th>
                    <th>Barcode</th>
                    <th>Shap</th>
                    <th>clarity</th>
                    <th>color</th>
                    <th>cut</th>
                    <th>polish</th>
                    <th>symmetry</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    @foreach($completedDimonds as $dimond)
                    <td><a href="/admin/print-slipe/{{$dimond->id}}" class="btn btn-primary" target="_blank">Print</a></td>
                    <td>{{$dimond->parties->party_code}}</td>
                    <td>{{$dimond->dimond_name}}</td>
                    <td>{{$dimond->weight}}</td>
                    <td>{!! $dimond->barcode_number !!}</td>
                    <td>{{$dimond->shape}}</td>
                    <td>{{$dimond->clarity}}</td>
                    <td>{{$dimond->color}}</td>
                    <td>{{$dimond->cut}}</td>
                    <td>{{$dimond->polish}}</td>
                    <td>{{$dimond->symmetry}}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane" id="2">
            <!-- <h3>Processing Dimond List</h3> -->
            <div class="table-responsive">
              <table id="processingDimonds" class="table align-items-center table-flush table-borderless ">
                <thead>
                  <tr>
                    <th>Party Name</th>
                    <th>Dimond Name</th>
                    <th>Weight</th>
                    <th>Barcode</th>
                    <th>Shap</th>
                    <th>clarity</th>
                    <th>color</th>
                    <th>cut</th>
                    <th>polish</th>
                    <th>symmetry</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    @foreach($processingDimonds as $dimond)
                    <td>{{$dimond->parties->party_code}}</td>
                    <td>{{$dimond->dimond_name}}</td>
                    <td>{{$dimond->weight}}</td>
                    <td>{!! $dimond->barcode_number !!}</td>
                    <td>{{$dimond->shape}}</td>
                    <td>{{$dimond->clarity}}</td>
                    <td>{{$dimond->color}}</td>
                    <td>{{$dimond->cut}}</td>
                    <td>{{$dimond->polish}}</td>
                    <td>{{$dimond->symmetry}}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane" id="3">
            <!-- <h3>Pending Dimond List</h3> -->
            <div class="table-responsive">
              <table id="pendingDimonds" class="table align-items-center table-flush table-borderless ">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Party Name</th>
                    <th>Dimond Name</th>
                    <th>Weight</th>
                    <th>Barcode</th>
                    <th>Shap</th>
                    <th>clarity</th>
                    <th>color</th>
                    <th>cut</th>
                    <th>polish</th>
                    <th>symmetry</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    @foreach($pendingDimonds as $dimond)
                    <td>{{ \Carbon\Carbon::parse($dimond->created_at)->format('d-m-Y') }}</td>
                    <td>{{$dimond->parties->party_code}}</td>
                    <td>{{$dimond->dimond_name}}</td>
                    <td>{{$dimond->weight}}</td>
                    <td>{!! $dimond->barcode_number !!}</td>
                    <td>{{$dimond->shape}}</td>
                    <td>{{$dimond->clarity}}</td>
                    <td>{{$dimond->color}}</td>
                    <td>{{$dimond->cut}}</td>
                    <td>{{$dimond->polish}}</td>
                    <td>{{$dimond->symmetry}}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div><!--End Row-->

@endsection

@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    let activeTab = new URLSearchParams(window.location.search).get("tab");
    if (activeTab) {
      document.querySelectorAll(".tab").forEach(tab => tab.classList.remove("active"));
      document.querySelector(`.tab a[href*="${activeTab}"]`).parentElement.classList.add("active");
    }
  });
</script>
@endsection