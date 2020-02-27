@php($selected = 'settings')
@extends('layouts.app')
@section('head_title','')
@section('icon', 'fas fa-school')

@section('title', 'Classes')

@section('title_menu')
    {{-- Nếu là admin thì hiển thị --}}

    <span class="title_menu_item"><a href="{{ route('lops.create') }}"><i class="fa fa-plus color11"></i>Add class</a></span>

@endsection

@section('content')

<div class="col-12">
  <p>
    {{-- Total submissions in queue: {{ $queue->count() }} --}}
  </p>
  <p>
    {# <a href="#" class="shj_act" id="pause"><i class="fa fa-pause"></i> Pause</a> | #}
    <a href="#" class="shj_act btn btn-primary" id="spawn" data-toggle="tooltip" data-placement="right" title="A queue processor process is spawned every time there is a submission or rejudging request. You can manually spawn one with this link"><i class="fa fa-play"></i> Spawn new queue process </a>
    <a href="#" class="shj_act btn btn-danger" id="empty_queue"  data-toggle="tooltip" data-placement="right" title="Empty the queue, all queue processor process should exit on their own, leaving submission in PENDING state"><i class="fa fa-times-circle"></i> Empty Queue</a>
  </p>
  <table class="wecode_table table table-striped table-bordered">
    <thead class="thead-dark">
    <tr>
      <th>id</th>
      <th>Submit ID</th>
      <th>Usename</th>
      <th>Assignment</th>
      <th>Problem</th>
      <th>Type (judge/rejudge)</th>
      <th>Process PID</th>
      <th><i class="fas fa-toolbox"></i></th>
    </tr>
    </thead>
      @foreach ($queue as $item)
      <tr>
        <td>{{ $item->id }}</td>
        <td>{{ $item->submission_id }}</td>
        <td>{{ $item->submission->username }}</td>
        <td>{{ $item->submission->assignment->id }} (<span>{{ $item->submission->assignment->name }}</span>)</td>
        <td>{{ $item->submission->problem->id }}</td>
        <td>{{ $item->type }}</td>
        <td>{{ $item->process_id }}</td>
        <td>
          @if ($item->process_id)
            <a href="#" class="shj_act btn btn-danger" id="unlock/{{ $item->id }}"  data-toggle="tooltip" data-placement="right" title="Unlock this queue item, allow it to be processed. Should only be used if its processor process has terminated somehow. MUST DOUBLE CHECK BEFORE USE"><i class="fas fa-lock-open"></i></a>
          @endif
        </td>
      </tr>
      @endforeach
  </table>
</div>
@endsection